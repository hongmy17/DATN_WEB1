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
        // Kiểm tra đơn hàng thuộc về user đang đăng nhập
        if ($order->user_id !== Auth::id()) {
            abort(403);
        }

        $order->load(['items', 'coupon']);

        return view('pages.order.show', compact('order'));
    }

    // Yêu cầu hủy đơn hàng
    public function cancel(Order $order)
    {
        // Kiểm tra đơn hàng thuộc về user đang đăng nhập
        if ($order->user_id !== Auth::id()) {
            abort(403);
        }

        // Chỉ hủy được khi đang ở trạng thái "Chờ xác nhận"
        if ($order->order_status !== 0) {
            return back()->with('error', 'Không thể hủy đơn hàng này!');
        }

        $order->update(['order_status' => 4]);

        return back()->with('success', 'Đã hủy đơn hàng thành công!');
    }
}