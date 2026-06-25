@extends('layouts.app')
@section('title', 'Đăng nhập — Nexus Store')
@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/pages/auth.css') }}">
@endpush
@section('content')
    <div class="auth-wrap">
        <div class="auth-side">
            <div class="auth-side__orb" style="width:500px;height:500px;top:-100px;right:-150px;"></div>
            <div class="auth-side__orb" style="width:300px;height:300px;bottom:50px;left:-80px;opacity:.4;"></div>
            <div class="auth-side__content">
                <div class="auth-side__logo">Nexus<span>.</span></div>
                <h2 class="auth-side__quote">Công nghệ trong tầm tay, trải nghiệm không giới hạn.</h2>
                <p class="auth-side__sub">Hàng nghìn sản phẩm chính hãng, giao hàng siêu nhanh, bảo hành chính hãng.</p>
            </div>
        </div>
        <div class="auth-main">
            <div class="auth-form-box">
                <h1 class="auth-form-title">Chào mừng trở lại</h1>
                <p class="auth-form-sub">Đăng nhập để tiếp tục mua sắm</p>

                @if (session('status'))
                    <div
                        style="background:var(--green-light);color:var(--green);padding:12px 16px;border-radius:var(--r-md);font-size:13px;margin-bottom:20px;border:1px solid rgba(45,122,82,.2)">
                        {{ session('status') }}
                    </div>
                @endif

                <div class="social-login">
                    {{-- Google: có SocialAuthController thật → dùng link redirect --}}
                    <a href="{{ route('social.redirect', 'google') }}" class="social-btn">
                        <svg width="16" height="16" viewBox="0 0 24 24">
                            <path fill="#4285F4"
                                d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" />
                            <path fill="#34A853"
                                d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" />
                            <path fill="#FBBC05"
                                d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" />
                            <path fill="#EA4335"
                                d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" />
                        </svg>
                        Google
                    </a>
                    {{-- Facebook: chưa cài → giữ thông báo --}}
                    <button class="social-btn" onclick="Toast.show('Facebook login chưa được cài đặt','info')">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="#1877F2">
                            <path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z" />
                        </svg>
                        Facebook
                    </button>
                </div>

                <div class="divider-text mb-24">hoặc đăng nhập bằng email</div>

                <form method="POST" action="{{ route('login') }}">
                    @csrf
                    <div class="form-group">
                        <label class="form-label">Email</label>
                        <div class="input-with-icon">
                            <svg class="input-icon" width="15" height="15" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="1.8" stroke-linecap="round">
                                <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z" />
                                <polyline points="22,6 12,13 2,6" />
                            </svg>
                            <input type="email" name="email" value="{{ old('email') }}"
                                class="form-control {{ $errors->has('email') ? 'is-error' : '' }}"
                                placeholder="email@example.com" required autofocus>
                        </div>
                        @error('email')
                            <p class="form-error">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label" style="display:flex;justify-content:space-between">
                            Mật khẩu
                            @if (Route::has('password.request'))
                                <a href="{{ route('password.request') }}"
                                    style="font-size:12px;color:var(--accent);font-weight:500;text-transform:none;letter-spacing:0">Quên
                                    mật khẩu?</a>
                            @endif
                        </label>
                        <div class="input-with-icon">
                            <svg class="input-icon" width="15" height="15" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="1.8" stroke-linecap="round">
                                <rect x="3" y="11" width="18" height="11" rx="2" />
                                <path d="M7 11V7a5 5 0 0 1 10 0v4" />
                            </svg>
                            <input type="password" name="password"
                                class="form-control {{ $errors->has('password') ? 'is-error' : '' }}" placeholder="••••••••"
                                required>
                        </div>
                        @error('password')
                            <p class="form-error">{{ $message }}</p>
                        @enderror
                    </div>
                    <div style="display:flex;align-items:center;gap:8px;margin-bottom:24px">
                        <input type="checkbox" id="remember" name="remember"
                            style="width:15px;height:15px;accent-color:var(--accent);cursor:pointer">
                        <label for="remember" style="font-size:13.5px;color:var(--ink-2);cursor:pointer">Ghi nhớ đăng
                            nhập</label>
                    </div>
                    <button type="submit" class="btn btn-primary btn-full btn-lg">Đăng nhập</button>
                </form>
                <p style="text-align:center;font-size:13.5px;color:var(--ink-3);margin-top:24px">
                    Chưa có tài khoản? <a href="{{ route('register') }}" style="color:var(--accent);font-weight:600">Đăng
                        ký ngay</a>
                </p>
            </div>
        </div>
    </div>
@endsection
