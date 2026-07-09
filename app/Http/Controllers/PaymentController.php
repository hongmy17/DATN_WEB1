<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Payment;
use App\Models\ProductVariant;
use App\Services\VNPayService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    public function __construct(private VNPayService $vnpay) {}

    /* ═══════════════════════════════════════════════════════════
       1. TẠO URL THANH TOÁN VNPAY
       POST /thanh-toan/vnpay/create
       Body: { order_id }
    ═══════════════════════════════════════════════════════════ */
    public function createVNPay(Request $request)
    {
        $request->validate(['order_id' => 'required|integer|exists:orders,id']);

        $order = Order::findOrFail($request->order_id);

        // Chỉ cho phép chính chủ tạo URL
        if ($order->user_id !== auth()->id()) {
            return response()->json(['success' => false, 'message' => 'Không có quyền.'], 403);
        }

        // Chỉ cho phép đơn đang chờ thanh toán
        if ($order->order_status !== Order::STATUS_PENDING) {
            return response()->json(['success' => false, 'message' => 'Đơn hàng không hợp lệ.'], 422);
        }

        // Tạo bản ghi payment ở trạng thái chờ
        Payment::updateOrCreate(
            ['order_id' => $order->id, 'payment_gateway' => 'VNPay', 'status' => 0],
            [
                'amount'           => $order->total_amount,
                'gateway_response' => null,
            ]
        );

        $url = $this->vnpay->createPaymentUrl(
            orderId:   $order->id,
            amount:    $order->total_amount,
            orderInfo: "Thanh toan don hang NX-{$order->id}",
            clientIp:  $request->ip(),
        );

        return response()->json(['success' => true, 'payment_url' => $url]);
    }

    /* ═══════════════════════════════════════════════════════════
       2. RETURN URL — User quay lại sau khi thanh toán
       GET /thanh-toan/vnpay/return
       VNPay redirect user về đây với kết quả
    ═══════════════════════════════════════════════════════════ */
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
            // Thanh toán thành công — xử lý trong handleSuccess
            $this->handleSuccess($order, $data);
            return redirect()->route('orders.index')
                ->with('success', 'Thanh toán thành công! Đơn hàng NX-' . str_pad($order->id, 6, '0', STR_PAD_LEFT) . ' đã được xác nhận.')
                ->with('clear_cart', true);
        }

        // Thanh toán thất bại / bị hủy
        $this->handleFailed($order, $data);
        return redirect()->route('checkout.index')
            ->with('error', 'Thanh toán không thành công. Mã lỗi: ' . $responseCode);
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
            // Cập nhật trạng thái đơn hàng → Đang xử lý
            if ($order->order_status === Order::STATUS_PENDING) {
                $order->update(['order_status' => Order::STATUS_CONFIRMED]);

                // Trừ tồn kho
                $order->load('items');
                foreach ($order->items as $item) {
                    ProductVariant::where('id', $item->variant_id)
                        ->where('manage_stock', true)
                        ->decrement('stock_quantity', $item->quantity);
                }
            }

            // Ghi / cập nhật bảng payments
            $rawTxnNo = $data['vnp_TransactionNo'] ?? null;
            Payment::updateOrCreate(
                ['order_id' => $order->id, 'payment_gateway' => 'VNPay'],
                [
                    'transaction_code' => ($rawTxnNo && $rawTxnNo !== '0') ? $rawTxnNo : null,
                    'amount'           => $order->total_amount,
                    'status'           => 1, // thành công
                    'gateway_response' => $data,
                    'paid_at'          => now(),
                ]
            );

            // FIX: giỏ hàng chỉ được xóa khi thanh toán VNPay thành công thật sự
            if ($order->user_id) {
                \App\Models\CartItem::where('user_id', $order->user_id)->delete();
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
        // Giữ nguyên order_status = PENDING để user có thể thử lại
    }
}