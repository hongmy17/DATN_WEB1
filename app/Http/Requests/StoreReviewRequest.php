<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreReviewRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'order_item_id' => ['required', 'exists:order_items,id'],
            'rating'        => ['required', 'integer', 'min:1', 'max:5'],
            'comment'       => ['nullable', 'string', 'max:2000'],
            'images'        => ['nullable', 'array', 'max:5'],
            'images.*'      => ['image', 'max:3072'], // tối đa 3MB/ảnh
        ];
    }

    public function messages(): array
    {
        return [
            'order_item_id.required' => 'Vui lòng chọn sản phẩm cần đánh giá.',
            'order_item_id.exists'   => 'Sản phẩm không hợp lệ.',
            'rating.required'        => 'Vui lòng chọn số sao.',
            'rating.min'             => 'Số sao tối thiểu là 1.',
            'rating.max'             => 'Số sao tối đa là 5.',
            'images.max'             => 'Tối đa 5 ảnh.',
            'images.*.image'         => 'File phải là ảnh.',
            'images.*.max'           => 'Mỗi ảnh tối đa 3MB.',
        ];
    }
}
