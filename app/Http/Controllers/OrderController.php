<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    /** Xem chi tiết đơn hàng */
    public function show(Order $order)
    {
        if ($order->user_id !== Auth::id()) {
            abort(403);
        }

        $order->load(['items', 'coupon', 'refundRequest']);

        return view('pages.order.show', compact('order'));
    }

    /**
     * Khách hàng hủy / yêu cầu hủy đơn hàng.
     *
     * LUỒNG MỚI — hai nhánh tùy theo đơn đã được shop xác nhận hay chưa:
     *
     *   Chờ xác nhận (0) ──► Hủy NGAY (4)
     *      Shop chưa động tới đơn, chưa trừ kho, chưa đóng gói → không cần duyệt.
     *
     *   Đã xác nhận (1) ──► Gửi YÊU CẦU HỦY (6), chờ admin duyệt
     *      Shop đã trừ kho và có thể đã đóng gói. Nếu cho khách tự hủy, shop
     *      không kiểm soát được hàng đã xuất. Admin sẽ bấm "Duyệt hủy" hoặc
     *      "Từ chối hủy" trong trang quản lý đơn hàng.
     *
     *   Các trạng thái khác (đang giao, hoàn thành, đã hủy...) ──► không cho hủy.
     *
     * Đây chính là lý do tồn tại của hằng số STATUS_CANCEL_REQUESTED = 6, vốn
     * đã được khai báo từ trước nhưng chưa có nơi nào gán giá trị cho nó.
     */
    public function cancel(Request $request, Order $order)
    {
        if ($order->user_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'cancel_reason' => 'required|string|max:255',
        ]);

        $status = (int) $order->order_status;

        // ── Nhánh 1: đơn chưa được xác nhận → hủy ngay ──────────────────────
        if (in_array($status, [Order::STATUS_PENDING, Order::STATUS_AWAITING_PAYMENT], true)) {
            $order->update([
                'order_status'  => Order::STATUS_CANCELLED,
                'cancel_reason' => $request->cancel_reason,
            ]);

            return back()->with('success', 'Đơn hàng đã được hủy thành công!');
        }

        // ── Nhánh 2: đơn đã xác nhận → gửi yêu cầu, chờ shop duyệt ──────────
        if ($status === Order::STATUS_CONFIRMED) {
            $order->update([
                'order_status'  => Order::STATUS_CANCEL_REQUESTED,
                'cancel_reason' => $request->cancel_reason,
            ]);

            return back()->with(
                'success',
                'Đã gửi yêu cầu hủy đơn. Shop sẽ phản hồi trong vòng 24 giờ.'
            );
        }

        // ── Nhánh 3: các trạng thái còn lại ─────────────────────────────────
        if ($status === Order::STATUS_CANCEL_REQUESTED) {
            return back()->with('error', 'Yêu cầu hủy của bạn đang được shop xem xét.');
        }

        return back()->with(
            'error',
            'Không thể hủy đơn hàng ở trạng thái "' . $order->statusLabel() . '".'
        );
    }
}
