<?php

namespace App\Http\Controllers;

use App\Models\Coupon;
use Illuminate\Http\Request;

class CouponController extends Controller
{
    public function apply(Request $request)
    {
        $request->validate([
            'coupon_code' => 'required|string|max:50',
            'sub_total'   => 'required|numeric|min:0',
        ], [
            'coupon_code.required' => 'Vui lòng nhập mã giảm giá.',
            'sub_total.required'   => 'Thiếu thông tin tổng đơn hàng.',
        ]);

        $coupon = Coupon::where('coupon_code', $request->coupon_code)->first();

        if (! $coupon) {
            return response()->json([
                'success' => false,
                'message' => 'Mã giảm giá không tồn tại.',
            ]);
        }

        $errorMessage = $coupon->validate((float) $request->sub_total);
        if ($errorMessage !== null) {
            return response()->json([
                'success' => false,
                'message' => $errorMessage,
            ]);
        }

        $discount = $coupon->calcDiscount((float) $request->sub_total);
        $total    = $request->sub_total - $discount;

        session([
            'coupon_code'     => $coupon->coupon_code,
            'coupon_id'       => $coupon->id,
            'discount_amount' => $discount,
        ]);

        $discountLabel = $coupon->type === Coupon::TYPE_PERCENT
            ? $coupon->value . '%'
            : number_format($coupon->value, 0, ',', '.') . '₫';

        $responseData = [
            'success'         => true,
            'message'         => 'Áp mã thành công! Bạn được giảm ' . number_format($discount, 0, ',', '.') . '₫.',
            'coupon_code'     => $coupon->coupon_code,
            'discount_amount' => $discount,
            'discount_label'  => $discountLabel,
            'total'           => $total,
            'type'            => $coupon->type,
            'value'           => $coupon->value,
        ];

        if ($coupon->type === Coupon::TYPE_PERCENT && $coupon->max_discount) {
            $responseData['max_discount'] = $coupon->max_discount;
        }

        return response()->json($responseData);
    }

    public function remove()
    {
        session()->forget(['coupon_code', 'coupon_id', 'discount_amount']);

        return response()->json([
            'success' => true,
            'message' => 'Đã hủy mã giảm giá.',
        ]);
    }
}