@extends('layouts.app')

@section('title', 'Đăng nhập - Nexus Store')

@push('styles')
<style>
.auth-wrap {
    min-height: calc(100vh - 200px);
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 40px 16px;
}
.auth-card {
    width: 100%;
    max-width: 460px;
    background: var(--bg-alt);
    border: 1px solid var(--border-soft);
    border-radius: var(--r-2xl);
    padding: 40px;
    box-shadow: var(--shadow-lg);
}
.auth-logo {
    text-align: center;
    margin-bottom: 28px;
    font-family: var(--font-display);
    font-size: 26px;
    font-weight: 800;
}
.auth-tabs {
    display: flex;
    background: var(--surface);
    border-radius: var(--r-lg);
    padding: 4px;
    margin-bottom: 28px;
    gap: 4px;
}
.auth-tab {
    flex: 1;
    padding: 10px;
    text-align: center;
    border-radius: var(--r-md);
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    transition: var(--transition);
    color: var(--ink-3);
    background: transparent;
    border: none;
    text-decoration: none;
    display: inline-block;
}
.auth-tab.active {
    background: var(--bg-alt);
    color: var(--ink);
    box-shadow: var(--shadow-xs);
}
.social-btn {
    width: 100%;
    padding: 12px;
    border-radius: var(--r-lg);
    background: var(--surface);
    border: 1.5px solid var(--border);
    color: var(--ink);
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    transition: var(--transition);
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    margin-bottom: 10px;
}
.social-btn:hover {
    border-color: var(--ink);
    background: var(--surface-2);
}
.field-error {
    color: #ef4444;
    font-size: 12px;
    margin-top: 5px;
    display: flex;
    align-items: center;
    gap: 4px;
}
.form-footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
}
.divider--text {
    text-align: center;
    margin: 20px 0 24px;
    color: var(--ink-3);
    font-size: 13px;
}
.form-control.is-invalid {
    border-color: #ef4444 !important;
}
.alert-error {
    background: #fef2f2;
    border: 1px solid #fecaca;
    border-radius: var(--r-lg);
    padding: 12px 16px;
    margin-bottom: 20px;
    color: #dc2626;
    font-size: 14px;
}
.alert-success {
    background: #f0fdf4;
    border: 1px solid #bbf7d0;
    border-radius: var(--r-lg);
    padding: 12px 16px;
    margin-bottom: 20px;
    color: #16a34a;
    font-size: 14px;
}
.lockout-timer {
    text-align: center;
    padding: 16px;
    background: #fff7ed;
    border: 1px solid #fed7aa;
    border-radius: var(--r-lg);
    color: #ea580c;
    font-size: 14px;
    margin-bottom: 16px;
}
.attempts-warning {
    font-size: 12px;
    color: #f59e0b;
    margin-top: 4px;
}
</style>
@endpush

@section('content')
<div class="auth-wrap">
    <div class="auth-card">

        {{-- Logo --}}
        <div class="auth-logo">
            <em style="font-style:normal;background:var(--ink);color:var(--bg);width:38px;height:38px;border-radius:10px;display:inline-flex;align-items:center;justify-content:center;font-size:18px;margin-right:8px">N</em>
            Nexus Store
        </div>

        {{-- Tabs --}}
        <div class="auth-tabs">
            <a href="{{ route('login') }}" class="auth-tab active">Đăng Nhập</a>
            <a href="{{ route('register') }}" class="auth-tab">Đăng Ký</a>
        </div>

        {{-- Thông báo thành công (VD: sau đăng ký) --}}
        @if (session('success'))
              <div class="alert-success">{{ session('success') }}</div>
        @endif

        {{-- Thông báo lỗi chung --}}
        @if ($errors->any() && !$errors->has('email'))
              <div class="alert-error">Vui lòng kiểm tra lại thông tin đăng nhập.</div>
        @endif

        {{-- Thông báo rate limit / lockout --}}
        @if ($errors->has('email') && str_contains($errors->first('email'), 'giây'))
            <div class="lockout-timer">
                    {{ $errors->first('email') }}
            </div>
        @endif

        {{-- Form đăng nhập --}}
        <form method="POST" action="{{ route('login.store') }}" id="loginForm" novalidate>
            @csrf

            {{-- Đăng nhập mạng xã hội --}}
                    <a href="{{ route('social.redirect', 'google') }}" class="social-btn w-full" style="text-decoration: none; display: flex; align-items: center; justify-content: center;">
                        Tiếp tục với Google
                    </a>
            <div class="divider--text">hoặc đăng nhập bằng email</div>

            {{-- Email --}}
            <div class="form-group">
                <label class="form-label" for="email">Email</label>
                <div class="input-icon">
                    <span class="input-icon__icon"></span>
                    <input
                        type="email"
                        class="form-control @error('email') is-invalid @enderror"
                        id="email"
                        name="email"
                        value="{{ old('email') }}"
                        placeholder="email@gmail.com"
                        autocomplete="email"
                        autofocus
                        required
                    >
                </div>
                @error('email')
                    @if (!str_contains($message, 'giây'))
                        <div class="field-error">{{ $message }}</div>
                    @endif
                @enderror
            </div>

            {{-- Mật khẩu --}}
            <div class="form-group">
                <label class="form-label" for="password">Mật khẩu</label>
                <div class="input-icon">
                    <span class="input-icon__icon"></span>
                    <input
                        type="password"
                        class="form-control @error('password') is-invalid @enderror"
                        id="password"
                        name="password"
                        placeholder="••••••••"
                        autocomplete="current-password"
                        required
                    >
                </div>
                @error('password')
                    <div class="field-error">{{ $message }}</div>
                @enderror
            </div>

            {{-- Ghi nhớ + quên mật khẩu --}}
            <div class="form-footer">
                <label class="checkbox-wrap">
                    <input type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                    <span>Ghi nhớ đăng nhập</span>
                </label>
                <a href="{{ route('password.request') }}" style="font-size:13px;color:var(--accent)">Quên mật khẩu?</a>
            </div>

            {{-- Nút đăng nhập --}}
            <button type="submit" class="btn btn-primary btn-full btn-lg" id="loginBtn">
                Đăng Nhập
            </button>

            <p class="text-center mt-16" style="font-size:13px;color:var(--ink-muted)">
                Chưa có tài khoản?
                <a href="{{ route('register') }}" style="color:var(--accent);font-weight:600">Đăng ký ngay</a>
            </p>
        </form>

    </div>
</div>
@endsection

@push('scripts')
<script>
document.getElementById('loginForm').addEventListener('submit', function(e) {
    const btn = document.getElementById('loginBtn');
    btn.disabled = true;
    btn.textContent = 'Đang đăng nhập...';
});
</script>
@endpush
