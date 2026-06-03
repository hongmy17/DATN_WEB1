<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'min:2',
                'max:100',
                'regex:/^[\p{L}\s]+$/u', // Chỉ chữ cái và khoảng trắng (hỗ trợ tiếng Việt)
            ],
            'email' => [
                'required',
                'string',
                'email:rfc,dns',
                'max:255',
                'unique:users,email',
            ],
            'phone' => [
                'nullable',
                'string',
                'regex:/^(0[3|5|7|8|9])[0-9]{8}$/', // Số điện thoại Việt Nam
            ],
            'password' => [
                'required',
                'string',
                'confirmed',
                Password::min(8)
                    ->letters()
                    ->mixedCase()
                    ->numbers()
                    ->uncompromised(),
            ],
            'terms' => [
                'required',
                'accepted',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'       => 'Vui lòng nhập họ tên.',
            'name.min'            => 'Họ tên phải có ít nhất 2 ký tự.',
            'name.max'            => 'Họ tên không được vượt quá 100 ký tự.',
            'name.regex'          => 'Họ tên chỉ được chứa chữ cái và khoảng trắng.',
            'email.required'      => 'Vui lòng nhập địa chỉ email.',
            'email.email'         => 'Địa chỉ email không hợp lệ.',
            'email.max'           => 'Email không được vượt quá 255 ký tự.',
            'email.unique'        => 'Email này đã được sử dụng, vui lòng chọn email khác.',
            'phone.regex'         => 'Số điện thoại không hợp lệ (ví dụ: 0901234567).',
            'password.required'   => 'Vui lòng nhập mật khẩu.',
            'password.confirmed'  => 'Xác nhận mật khẩu không khớp.',
            'password.min'        => 'Mật khẩu phải có ít nhất 8 ký tự.',
            // Password rule messages (localized)
            'password.letters'    => 'Mật khẩu phải chứa ít nhất một chữ cái.',
            'password.mixed'      => 'Mật khẩu phải chứa ít nhất một chữ hoa và một chữ thường.',
            'password.mixedCase'  => 'Mật khẩu phải chứa ít nhất một chữ hoa và một chữ thường.',
            'password.numbers'    => 'Mật khẩu phải chứa ít nhất một chữ số.',
            'password.symbols'    => 'Mật khẩu phải chứa ít nhất một ký tự đặc biệt.',
            'password.uncompromised' => 'Mật khẩu xuất hiện trong danh sách rò rỉ — vui lòng chọn mật khẩu khác.',
            'terms.required'      => 'Vui lòng đồng ý với điều khoản dịch vụ.',
            'terms.accepted'      => 'Bạn phải chấp nhận điều khoản dịch vụ để tiếp tục.',
        ];
    }

    public function attributes(): array
    {
        return [
            'name'     => 'họ tên',
            'email'    => 'email',
            'phone'    => 'số điện thoại',
            'password' => 'mật khẩu',
            'terms'    => 'điều khoản',
        ];
    }
}
