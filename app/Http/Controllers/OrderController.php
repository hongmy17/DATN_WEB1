<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    // Xem chi tiết đơn hàng
    public function show(Order $order)
    {
        if ($order->user_id !== Auth::id()) abort(403);
        $order->load(['items', 'coupon']);
        return view('pages.order.show', compact('order'));
    }

    // Yêu cầu hủy đơn hàng
    public function cancel(Request $request, Order $order)
    {
        if ($order->user_id !== Auth::id()) abort(403);

        // Chỉ hủy được khi đang ở trạng thái "Chờ xác nhận"
        if ($order->order_status !== 0) {
            return back()->with('error', 'Không thể hủy đơn hàng này vì đơn hàng đã được xác nhận!');
        }

        $request->validate([
            'cancel_reason' => 'required|string',
        ]);

        // Hủy ngay lập tức
        $order->update([
            'order_status'  => 4,
            'cancel_reason' => $request->cancel_reason,
        ]);

        return back()->with('success', 'Đơn hàng đã được hủy thành công!');
    }
}
