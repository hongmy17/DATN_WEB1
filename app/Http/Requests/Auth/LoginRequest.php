<?php

namespace App\Http\Requests\Auth;

use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
            ],
            'password' => [
                'required',
                'string',
                'min:8',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'email.required'   => 'Vui lòng nhập địa chỉ email.',
            'email.email'      => 'Địa chỉ email không hợp lệ.',
            'email.max'        => 'Email không được vượt quá 255 ký tự.',
            'password.required' => 'Vui lòng nhập mật khẩu.',
            'password.min'     => 'Mật khẩu phải có ít nhất 8 ký tự.',
        ];
    }

    /**
     * Xác thực thông tin đăng nhập + kiểm tra rate limit.
     */
    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();

        if (! Auth::attempt($this->only('email', 'password'), $this->boolean('remember'))) {
            RateLimiter::hit($this->throttleKey(), 180);

            throw ValidationException::withMessages([
                'email' => __('Thông tin đăng nhập không chính xác.'),
            ]);
        }

        RateLimiter::clear($this->throttleKey());
    }

    /**
     * Kiểm tra xem request có bị rate limit hay không.
     * Giới hạn: 5 lần thất bại / 3 phút.
     */
    public function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'email' => 'Quá nhiều lần đăng nhập. Vui lòng thử lại sau ' . ceil($seconds / 60) . ' phút.',
        ]);
    }

    /**
     * Tạo khóa throttle dựa trên email + IP.
     */
    public function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->string('email')) . '|' . $this->ip());
    }
}
