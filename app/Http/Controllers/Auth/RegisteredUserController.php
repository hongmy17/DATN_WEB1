<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Hiển thị form đăng ký.
     */
    public function create(): View|RedirectResponse
    {
        if (Auth::check()) {
            return redirect()->route('home');
        }

        return view('pages.auth.register');
    }

    /**
     * Xử lý đăng ký tài khoản mới.
     * Validation được xử lý hoàn toàn trong RegisterRequest.
     */
    public function store(RegisterRequest $request): RedirectResponse
    {
        $user = User::create([
            'code'     => 'USR' . strtoupper(substr(uniqid(), -7)),
            'name'     => trim($request->name),
            'email'    => $request->email,
            'phone'    => $request->phone,
            'password' => Hash::make($request->password),
        ]);

        event(new Registered($user));

        Auth::login($user);

        return redirect()->route('home')
            ->with('success', 'Đăng ký thành công! Chào mừng bạn đến với Nexus Store');
    }
}
