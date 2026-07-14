<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRefundRequest;
use App\Mail\RefundStatusMail;
use App\Models\Order;
use App\Models\RefundRequest;
use Illuminate\Support\Facades\Auth;

class RefundController extends Controller
{
    /** Hiển thị form yêu cầu hoàn tiền */
    public function create(Order $order)
    {
        $this->authorizeOrder($order);
        return view('pages.order.refund-create', compact('order'));
    }

    /** Khách gửi yêu cầu hoàn tiền — Bước 1 trong quy trình */
    public function store(StoreRefundRequest $request, Order $order)
    {
        $this->authorizeOrder($order);

        $evidencePaths = [];
        if ($request->hasFile('evidence_images')) {
            foreach ($request->file('evidence_images') as $file) {
                $evidencePaths[] = $file->store('refunds/evidence', 'public');
            }
        }

        // Chỉ cần thông tin ngân hàng nếu đơn thanh toán COD (không thanh toán qua cổng)
        $needBankInfo = $order->payment_method === 'cod';

        RefundRequest::create([
            'order_id'             => $order->id,
            'user_id'              => Auth::id(),
            'reason'                => $request->reason,
            'reason_detail'         => $request->reason_detail,
            'evidence_images'       => $evidencePaths ?: null,
            'bank_name'             => $needBankInfo ? $request->bank_name : null,
            'bank_account_number'   => $needBankInfo ? $request->bank_account_number : null,
            'bank_account_holder'   => $needBankInfo ? $request->bank_account_holder : null,
            'refund_amount'         => $order->total_amount, // mặc định hoàn toàn bộ
            'status'                => RefundRequest::STATUS_PENDING,
        ]);

        return redirect()->route('orders.show', $order)
            ->with('success', 'Yêu cầu hoàn tiền đã được gửi. Chúng tôi sẽ xử lý trong thời gian sớm nhất!');
    }

    /** Chỉ chủ đơn mới được yêu cầu hoàn tiền, và đơn phải đã thanh toán, chưa có yêu cầu nào trước đó */
    private function authorizeOrder(Order $order): void
    {
        abort_if($order->user_id !== Auth::id(), 403);

        abort_if(
            $order->order_status !== Order::STATUS_COMPLETED,
            403,
            'Chỉ có thể yêu cầu hoàn tiền cho đơn hàng đã hoàn thành.'
        );

        abort_if(
            $order->refundRequest()->exists(),
            403,
            'Đơn hàng này đã có yêu cầu hoàn tiền.'
        );
    }
}