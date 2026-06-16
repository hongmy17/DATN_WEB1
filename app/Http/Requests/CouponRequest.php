<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * [FILE MỚI] Request validate cho endpoint áp mã giảm giá.
 */
class CouponRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'coupon_code' => ['required', 'string', 'max:50'],
            'sub_total'   => ['required', 'numeric', 'min:0'],
        ];
    }

    public function messages(): array
    {
        return [
            'coupon_code.required' => 'Vui lòng nhập mã giảm giá.',
            'coupon_code.max'      => 'Mã giảm giá không được vượt quá 50 ký tự.',
            'sub_total.required'   => 'Thiếu thông tin tổng đơn hàng.',
            'sub_total.numeric'    => 'Tổng đơn hàng không hợp lệ.',
            'sub_total.min'        => 'Tổng đơn hàng không được âm.',
        ];
    }
}