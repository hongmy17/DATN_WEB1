<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreRefundRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'reason'                => ['required', 'string', 'in:defective,not_as_desc,not_received,changed_mind,other'],
            'reason_detail'         => ['nullable', 'string', 'max:2000'],
            'evidence_images'       => ['nullable', 'array', 'max:5'],
            'evidence_images.*'     => ['file', 'mimes:jpg,jpeg,png,mp4,mov', 'max:10240'], // ảnh hoặc video, tối đa 10MB/file
            'bank_name'             => ['required_if:need_bank_info,1', 'nullable', 'string', 'max:100'],
            'bank_account_number'   => ['required_if:need_bank_info,1', 'nullable', 'string', 'max:50'],
            'bank_account_holder'   => ['required_if:need_bank_info,1', 'nullable', 'string', 'max:100'],
        ];
    }

    public function messages(): array
    {
        return [
            'reason.required'              => 'Vui lòng chọn lý do hoàn tiền.',
            'bank_name.required_if'        => 'Vui lòng nhập tên ngân hàng.',
            'bank_account_number.required_if' => 'Vui lòng nhập số tài khoản.',
            'bank_account_holder.required_if' => 'Vui lòng nhập tên chủ tài khoản.',
        ];
    }
}