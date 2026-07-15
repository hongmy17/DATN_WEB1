<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class PasswordResetLinkController extends Controller
{
    /**
     * Display the password reset link request view.
     */
    public function create(): View
    {
        return view('pages.auth.forgot');
    }

    /**
     * Handle an incoming password reset link request.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        Password::sendResetLink($request->only('email'));

        // FIX: dự án không có file lang (__($status) sẽ hiện chữ thô kiểu "passwords.sent"),
        // nên hardcode tiếng Việt trực tiếp — đồng bộ với cách các message khác trong dự án.
        //
        // FIX bảo mật: LUÔN hiện đúng 1 câu chung này bất kể email có tồn tại hay không.
        // Không kiểm tra $status để phân nhánh báo lỗi khác nhau — nếu không sẽ để lộ
        // được email nào có đăng ký tài khoản hay không (dò email hàng loạt), khác gì
        // với cách đăng nhập cố tình không tiết lộ email có tồn tại hay không.
        return back()->with('success', 'Nếu email này có tồn tại trong hệ thống, chúng tôi đã gửi link đặt lại mật khẩu. Vui lòng kiểm tra hộp thư (và cả mục Spam).');
    }
}   