<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Payment;
use App\Models\ProductVariant;
use App\Services\VNPayService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Mail\OrderConfirmationMail;
use Illuminate\Support\Facades\Mail;

class PaymentController extends Controller
{
    public function __construct(private VNPayService $vnpay) {}

    /* ═══════════════════════════════════════════════════════════
        1. TẠO URL THANH TOÁN VNPAY
        POST /thanh-toan/vnpay/create
        Body: { order_id }
        ═══════════════════════════════════════════════════════════ */
    /* ═══════════════════════════════════════════════════════════
       1. TẠO / TIẾP TỤC URL THANH TOÁN VNPAY
       POST /thanh-toan/vnpay/create
       Body: { order_id }
       Dùng cho nút "Tiếp tục thanh toán" / "Thanh toán lại" ở trang
       đơn hàng — khi khách bị gián đoạn (tắt trình duyệt, mất mạng...)
       và quay lại sau, hoặc phiên VNPay cũ đã hết hạn (15 phút).
    ═══════════════════════════════════════════════════════════ */
    public function createVNPay(Request $request)
    {
        $request->validate(['order_id' => 'required|integer|exists:orders,id']);

        $order = Order::findOrFail($request->order_id);

        // Chỉ cho phép chính chủ tạo URL
        if ($order->user_id !== auth()->id()) {
            return response()->json(['success' => false, 'message' => 'Không có quyền.'], 403);
        }

        // FIX: đơn VNPay chưa thanh toán mang trạng thái STATUS_AWAITING_PAYMENT
        // (không phải STATUS_PENDING — đó là trạng thái "chờ xác nhận" của đơn
        // COD / đơn đã thanh toán xong). Check sai status khiến endpoint này
        // trước giờ luôn trả lỗi 422 nên chưa từng được gắn vào giao diện.
        if ($order->payment_method !== 'vnpay' || $order->order_status !== Order::STATUS_AWAITING_PAYMENT) {
            return response()->json(['success' => false, 'message' => 'Đơn hàng không hợp lệ hoặc đã được xử lý.'], 422);
        }

        // Reset lại đúng bản ghi payment cũ của đơn này về trạng thái chờ
        // (trước đây match theo cả 'status' => 0 nên nếu payment cũ đang ở
        // status=2/thất bại thì không match được, updateOrCreate() sẽ tạo
        // THÊM 1 dòng payment mới thay vì cập nhật — tích luỹ rác qua mỗi lần retry).
        Payment::updateOrCreate(
            ['order_id' => $order->id, 'payment_gateway' => 'VNPay'],
            [
                'amount'           => $order->total_amount,
                'status'           => Payment::STATUS_PENDING,
                'transaction_code' => null,
                'gateway_response' => null,
            ]
        );

        $url = $this->vnpay->createPaymentUrl(
            orderId: $order->id,
            amount: $order->total_amount,
            orderInfo: "Thanh toan don hang NX-{$order->id}",
            clientIp: $request->ip(),
        );

        return response()->json(['success' => true, 'payment_url' => $url]);
    }

    /* ═══════════════════════════════════════════════════════════
       2. RETURN URL — User quay lại sau khi thanh toán
       GET /thanh-toan/vnpay/return
       VNPay redirect user về đây với kết quả
    ═══════════════════════════════════════════════════════════ */
    public function result(Order $order)
    {
        abort_if($order->user_id !== Auth::id(), 403);

        $order->load('payment');
        $payment = $order->payment;

        $status = match (true) {
            $payment && (int) $payment->status === Payment::STATUS_SUCCESS => 'success',
            $payment && (int) $payment->status === Payment::STATUS_FAILED  => 'failed',
            (int) $order->order_status === Order::STATUS_AWAITING_PAYMENT  => 'pending',
            default                                                        => 'success',
        };

        return view('pages.payment.result', compact('order', 'payment', 'status'));
    }
    public function returnVNPay(Request $request)
    {
        $data = $request->all();

        // Verify chữ ký
        if (!$this->vnpay->verifySignature($data)) {
            return redirect()->route('checkout.index')
                ->with('error', 'Chữ ký không hợp lệ.');
        }

        $orderId    = $this->vnpay->parseOrderId($data['vnp_TxnRef']);
        $responseCode = $data['vnp_ResponseCode'] ?? '99';
        $order      = Order::find($orderId);

        if (!$order) {
            return redirect()->route('home')->with('error', 'Không tìm thấy đơn hàng.');
        }

        if ($responseCode === '00') {
            $this->handleSuccess($order, $data);
            return redirect()->route('payment.result', $order)->with('clear_cart', true);
        }

        $this->handleFailed($order, $data);
        return redirect()->route('payment.result', $order);
    }

    /* ═══════════════════════════════════════════════════════════
       3. IPN — VNPay gọi ngầm để thông báo kết quả
       GET /thanh-toan/vnpay/ipn
       Phải trả về JSON { RspCode, Message } theo đúng spec VNPay
    ═══════════════════════════════════════════════════════════ */
    public function ipnVNPay(Request $request)
    {
        $data = $request->all();

        Log::channel('daily')->info('[VNPay IPN]', $data);

        // 1. Verify chữ ký
        if (!$this->vnpay->verifySignature($data)) {
            return response()->json(['RspCode' => '97', 'Message' => 'Invalid signature']);
        }

        // 2. Tìm đơn hàng
        $orderId = $this->vnpay->parseOrderId($data['vnp_TxnRef']);
        $order   = Order::find($orderId);

        if (!$order) {
            return response()->json(['RspCode' => '01', 'Message' => 'Order not found']);
        }

        // 3. Kiểm tra số tiền khớp
        $amountFromVNPay = (int) ($data['vnp_Amount'] ?? 0) / 100;
        if ((float) $order->total_amount !== (float) $amountFromVNPay) {
            return response()->json(['RspCode' => '04', 'Message' => 'Invalid amount']);
        }

        // 4. Kiểm tra đã xử lý chưa (tránh duplicate)
        $payment = Payment::where('order_id', $order->id)
            ->where('payment_gateway', 'VNPay')
            ->first();

        if ($payment && $payment->status === 1) {
            return response()->json(['RspCode' => '02', 'Message' => 'Order already confirmed']);
        }

        // 5. Xử lý kết quả
        $responseCode = $data['vnp_ResponseCode'] ?? '99';

        if ($responseCode === '00') {
            $this->handleSuccess($order, $data);
        } else {
            $this->handleFailed($order, $data);
        }

        return response()->json(['RspCode' => '00', 'Message' => 'Confirm success']);
    }

    /* ═══════════════════════════════════════════════════════════
       HELPER: Xử lý thanh toán thành công
    ═══════════════════════════════════════════════════════════ */
    private function handleSuccess(Order $order, array $data): void
    {
        DB::transaction(function () use ($order, $data) {
            // Lưu lại: đây có phải LẦN ĐẦU đơn được xác nhận không?
            // (quan trọng để không gửi mail 2 lần — giải thích bên dưới)
            $isFirstTimeConfirmed = (int) $order->order_status === Order::STATUS_AWAITING_PAYMENT;

            if ($isFirstTimeConfirmed) {
                // THAY ĐỔI QUAN TRỌNG: trước đây dòng này gán STATUS_CONFIRMED,
                // tức là đơn VNPay tự nhảy sang "Đã xác nhận" mà admin không hề
                // đụng vào — và với luồng tồn kho mới, điều đó sẽ TỰ TRỪ KHO,
                // vi phạm yêu cầu "chỉ trừ kho khi admin xác nhận".
                //
                // Nay: khách đã trả tiền (payments.status = 1 ghi ở ngay bên
                // dưới), nhưng đơn vẫn nằm ở "Chờ xác nhận" chờ admin duyệt —
                // đúng như cách Shopee/Tiki vận hành.
                //
                // Doanh thu KHÔNG bị mất: Order::scopeRevenue() đã được đổi sang
                // căn cứ vào bảng payments thay vì order_status.
                $order->update(['order_status' => Order::STATUS_PENDING]);
                $order->load('items');
            }

            $rawTxnNo = $data['vnp_TransactionNo'] ?? null;
            Payment::updateOrCreate(
                ['order_id' => $order->id, 'payment_gateway' => 'VNPay'],
                [
                    'transaction_code' => ($rawTxnNo && $rawTxnNo !== '0') ? $rawTxnNo : null,
                    'amount'           => $order->total_amount,
                    'status'           => 1,
                    'gateway_response' => $data,
                    'paid_at'          => now(),
                ]
            );

            if ($order->user_id) {
                \App\Models\CartItem::where('user_id', $order->user_id)->delete();
            }

            // FIX: gửi mail xác nhận cho đơn VNPay ngay sau khi thanh toán thành công
            if ($isFirstTimeConfirmed && $order->user && $order->user->email) {
                Mail::to($order->user->email)->send(new OrderConfirmationMail($order));
            }
        });
    }

    /* ═══════════════════════════════════════════════════════════
       HELPER: Xử lý thanh toán thất bại / bị hủy
    ═══════════════════════════════════════════════════════════ */
    private function handleFailed(Order $order, array $data): void
    {
        // FIX: khi hủy/thất bại, VNPay trả vnp_TransactionNo = "0" (không phải mã
        // giao dịch thật). Cột payments.transaction_code có ràng buộc unique, nên
        // lưu thẳng "0" sẽ gây lỗi "Duplicate entry '0'" ngay từ lần thất bại thứ 2
        // (của bất kỳ đơn hàng nào). Chỉ lưu mã giao dịch khi nó là mã thật (khác 0/null).
        $rawTxnNo = $data['vnp_TransactionNo'] ?? null;
        $transactionCode = ($rawTxnNo && $rawTxnNo !== '0') ? $rawTxnNo : null;

        Payment::updateOrCreate(
            ['order_id' => $order->id, 'payment_gateway' => 'VNPay'],
            [
                'transaction_code' => $transactionCode,
                'amount'           => $order->total_amount,
                'status'           => 2, // thất bại
                'gateway_response' => $data,
            ]
        );
        // Giữ nguyên order_status = STATUS_AWAITING_PAYMENT để user có thể
        // bấm "Tiếp tục thanh toán" / "Thanh toán lại" ở trang đơn hàng
    }
}
