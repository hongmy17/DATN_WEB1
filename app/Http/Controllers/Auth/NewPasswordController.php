<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class NewPasswordController extends Controller
{
    /**
     * Display the password reset view.
     */
    public function create(Request $request): View
    {
        return view('pages.auth.reset', ['request' => $request]);
    }

    /**
     * Handle an incoming new password request.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'token' => ['required'],
            'email' => ['required', 'email'],
            // FIX: trước đây dùng Rules\Password::defaults() (mặc định chỉ yêu cầu tối
            // thiểu 8 ký tự) — YẾU HƠN quy tắc mật khẩu lúc đăng ký (RegisterRequest),
            // khiến người dùng có thể đặt lại thành mật khẩu yếu hơn cả lúc tạo tài
            // khoản. Đồng bộ lại cho đúng 1 chuẩn duy nhất trong toàn hệ thống.
            'password' => [
                'required', 'confirmed',
                Rules\Password::min(8)->letters()->mixedCase()->numbers()->uncompromised(),
            ],
        ], [
            'password.required' => 'Vui lòng nhập mật khẩu mới.',
            'password.confirmed' => 'Xác nhận mật khẩu không khớp.',
            'password.min' => 'Mật khẩu phải có ít nhất 8 ký tự.',
            'password.letters' => 'Mật khẩu phải chứa ít nhất 1 chữ cái.',
            'password.mixed_case' => 'Mật khẩu phải có cả chữ hoa và chữ thường.',
            'password.numbers' => 'Mật khẩu phải chứa ít nhất 1 chữ số.',
            'password.uncompromised' => 'Mật khẩu này đã từng xuất hiện trong các vụ rò rỉ dữ liệu, vui lòng chọn mật khẩu khác.',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user) use ($request) {
                $user->forceFill([
                    'password' => Hash::make($request->password),
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($user));
            }
        );

        // FIX: hardcode tiếng Việt — dự án không có file lang, __($status) sẽ hiện
        // chữ thô kiểu "passwords.token" thay vì câu tiếng Việt tử tế.
        if ($status == Password::PASSWORD_RESET) {
            return redirect()->route('login')->with('success', 'Đặt lại mật khẩu thành công! Vui lòng đăng nhập bằng mật khẩu mới.');
        }

        $message = match ($status) {
            Password::INVALID_TOKEN => 'Link đặt lại mật khẩu không hợp lệ hoặc đã hết hạn. Vui lòng yêu cầu gửi lại.',
            Password::INVALID_USER  => 'Không tìm thấy tài khoản với email này.',
            default => 'Có lỗi xảy ra, vui lòng thử lại.',
        };

        return back()->withInput($request->only('email'))->withErrors(['email' => $message]);
    }
}