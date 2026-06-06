<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $credentials = [
            'email' => $request->email,
            'password' => $request->password,
            'status' => 1
        ];

        if (Auth::attempt($credentials)) {

            if (Auth::user()->role == 1) {
                return redirect('/admin');
            }

            return redirect('/');
        }

        return back()->with('error', 'Email hoặc mật khẩu không đúng');
    }

    public function logout()
    {
        Auth::logout();

        return redirect('/');
    }
}