<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;

class SocialAuthController extends Controller
{
    public function redirect(string $provider)
    {
        if ($provider !== 'google') {
            return redirect()->route('login');
        }

        return Socialite::driver('google')->redirect();
    }

    public function callback(Request $request, string $provider): RedirectResponse
    {
        if ($provider !== 'google') {
            return redirect()->route('login');
        }

        try {
            $socialUser = Socialite::driver('google')->user();
        } catch (\Exception $e) {
            return redirect()->route('login')->with('error', 'Không thể đăng nhập bằng Google');
        }

        // Find or create user
        $user = User::where('email', $socialUser->getEmail())->first();

        if (! $user) {
            $user = User::create([
                'code' => 'USR' . strtoupper(substr(uniqid(), -7)),
                'name' => $socialUser->getName() ?? $socialUser->getNickname() ?? 'Người dùng',
                'email' => $socialUser->getEmail(),
                'phone' => null,
                'password' => Hash::make(bin2hex(random_bytes(10))),
            ]);
        }

        Auth::login($user);

        return redirect()->route('home');
    }
}
