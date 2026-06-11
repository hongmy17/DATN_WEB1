<?php

namespace App\Http\Controllers;

use App\Models\Coupon;
use Illuminate\Http\Request;

class CouponController extends Controller
{
    // Kiểm tra và áp mã giảm giá
    public function apply(Request $request)
    {
        $request->validate([
            'coupon_code' => 'required|string',
            'sub_total'   => 'required|numeric|min:0',
        ]);

        $coupon = Coupon::where('coupon_code', $request->coupon_code)->first();

        if (!$coupon) {
            return response()->json([
                'success' => false,
                'message' => 'Mã giảm giá không tồn tại!',
            ]);
        }

        if (!$coupon->isValid($request->sub_total)) {
            return response()->json([
                'success' => false,
                'message' => 'Mã giảm giá không hợp lệ hoặc đã hết hạn!',
            ]);
        }

        $discount = $coupon->calcDiscount($request->sub_total);
        $total    = $request->sub_total - $discount;

        // Lưu vào session
        session([
            'coupon_code'     => $coupon->coupon_code,
            'coupon_id'       => $coupon->id,
            'discount_amount' => $discount,
        ]);

        return response()->json([
            'success'         => true,
            'message'         => 'Áp mã thành công!',
            'coupon_code'     => $coupon->coupon_code,
            'discount_amount' => $discount,
            'total'           => $total,
            'type'            => $coupon->type,
            'value'           => $coupon->value,
        ]);
    }

    // Hủy mã giảm giá
    public function remove()
    {
        session()->forget(['coupon_code', 'coupon_id', 'discount_amount']);

        return response()->json([
            'success' => true,
            'message' => 'Đã hủy mã giảm giá!',
        ]);
    }
}