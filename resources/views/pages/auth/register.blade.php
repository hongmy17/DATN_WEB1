@extends('layouts.app')

@section('title', 'Đăng ký - Nexus Store')

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
    max-width: 500px;
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
.field-error {
    color: #ef4444;
    font-size: 12px;
    margin-top: 5px;
    display: flex;
    align-items: center;
    gap: 4px;
}
.form-control.is-invalid {
    border-color: #ef4444 !important;
}
.form-control.is-valid {
    border-color: #22c55e !important;
}
.password-strength {
    height: 4px;
    border-radius: 2px;
    margin-top: 8px;
    background: var(--border);
    overflow: hidden;
}
.password-strength__bar {
    height: 100%;
    border-radius: 2px;
    transition: width 0.3s, background 0.3s;
    width: 0%;
}
.password-hint {
    font-size: 11px;
    color: var(--ink-3);
    margin-top: 4px;
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
.form-row-2 {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 14px;
}
@media (max-width: 480px) {
    .form-row-2 { grid-template-columns: 1fr; }
    .auth-card { padding: 28px 20px; }
}
</style>
@endpush

@section('content')
<div class="auth-wrap min-h-screen flex items-center justify-center py-12 bg-gray-100">
    <div class="auth-card w-full max-w-md bg-white border border-gray-200 rounded-2xl p-8 shadow-lg">

        {{-- Logo --}}
        <div class="auth-logo">
            <em style="font-style:normal;background:var(--ink);color:var(--bg);width:38px;height:38px;border-radius:10px;display:inline-flex;align-items:center;justify-content:center;font-size:18px;margin-right:8px">N</em>
            Nexus Store
        </div>

        {{-- Tabs --}}
            <div class="auth-tabs">
            <a href="{{ route('login') }}" class="auth-tab">Đăng Nhập</a>
            <a href="{{ route('register') }}" class="auth-tab active">Đăng Ký</a>
        </div>

        {{-- Lỗi chung --}}
        @if ($errors->any())
            <div class="alert-error">Vui lòng kiểm tra lại thông tin bên dưới.</div>
        @endif

        {{-- Form đăng ký --}}
        <form method="POST" action="{{ route('register.store') }}" id="registerForm" novalidate>
            @csrf

            {{-- Họ tên --}}
            <div class="form-group">
                <label class="form-label" for="name">Họ và tên <span style="color:#ef4444">*</span></label>
                <div class="input-icon">
                    <span class="input-icon__icon"></span>
                    <input
                        type="text"
                        class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 px-3 py-2 @error('name') border-red-500 @enderror"
                        id="name"
                        name="name"
                        value="{{ old('name') }}"
                        placeholder="Nguyễn Văn A"
                        autocomplete="name"
                        autofocus
                        required
                    >
                </div>
                @error('name')
                    <div class="field-error">{{ $message }}</div>
                @enderror
            </div>

            {{-- Email --}}
            <div class="form-group">
                <label class="form-label" for="email">Email <span style="color:#ef4444">*</span></label>
                <div class="input-icon">
                    <span class="input-icon__icon"></span>
                    <input
                        type="email"
                        class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 px-3 py-2 @error('email') border-red-500 @enderror"
                        id="email"
                        name="email"
                        value="{{ old('email') }}"
                        placeholder="email@gmail.com"
                        autocomplete="email"
                        required
                    >
                </div>
                @error('email')
                    <div class="field-error">{{ $message }}</div>
                @enderror
            </div>

            {{-- Số điện thoại --}}
            <div class="form-group">
                <label class="form-label" for="phone">Số điện thoại</label>
                <div class="input-icon">
                    <span class="input-icon__icon"></span>
                    <input
                        type="tel"
                        class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 px-3 py-2 @error('phone') border-red-500 @enderror"
                        id="phone"
                        name="phone"
                        value="{{ old('phone') }}"
                        placeholder="0901 234 567"
                        autocomplete="tel"
                    >
                </div>
                @error('phone')
                    <div class="field-error">{{ $message }}</div>
                @enderror
            </div>

            {{-- Mật khẩu --}}
            <div class="form-group">
                <label class="form-label" for="password">Mật khẩu <span style="color:#ef4444">*</span></label>
                <div class="input-icon">
                    <span class="input-icon__icon"></span>
                    <input
                        type="password"
                        class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 px-3 py-2 @error('password') border-red-500 @enderror"
                        id="password"
                        name="password"
                        placeholder="Ít nhất 8 ký tự"
                        autocomplete="new-password"
                        required
                        oninput="checkStrength(this.value)"
                    >
                </div>
                <div class="password-strength">
                    <div class="password-strength__bar" id="strengthBar"></div>
                </div>
                <div class="password-hint" id="strengthText">Nhập mật khẩu để kiểm tra độ mạnh</div>
                @error('password')
                    <div class="field-error">{{ $message }}</div>
                @enderror
            </div>

            {{-- Xác nhận mật khẩu --}}
            <div class="form-group">
                <label class="form-label" for="password_confirmation">Xác nhận mật khẩu <span style="color:#ef4444">*</span></label>
                <div class="input-icon">
                    <span class="input-icon__icon"></span>
                    <input
                        type="password"
                        class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 px-3 py-2"
                        id="password_confirmation"
                        name="password_confirmation"
                        placeholder="Nhập lại mật khẩu"
                        autocomplete="new-password"
                        required
                    >
                </div>
            </div>

            {{-- Điều khoản --}}
            <div class="form-group mb-16">
                <label class="checkbox-wrap" style="align-items:flex-start;gap:10px">
                    <input
                        type="checkbox"
                        name="terms"
                        id="terms"
                        value="1"
                        {{ old('terms') ? 'checked' : '' }}
                        required
                    >
                    <span style="font-size:13px">
                        Tôi đồng ý với
                        <a href="#" style="color:var(--accent)">điều khoản dịch vụ</a>
                        và
                        <a href="#" style="color:var(--accent)">chính sách bảo mật</a>
                    </span>
                </label>
                @error('terms')
                    <div class="field-error" style="margin-left:24px">{{ $message }}</div>
                @enderror
            </div>

            {{-- Nút đăng ký --}}
            <button type="submit" class="btn btn-primary btn-full btn-lg" id="registerBtn">
                Tạo Tài Khoản
            </button>

            <p class="text-center mt-16" style="font-size:13px;color:var(--ink-muted)">
                Đã có tài khoản?
                <a href="{{ route('login') }}" style="color:var(--accent);font-weight:600">Đăng nhập</a>
            </p>
        </form>

    </div>
</div>
@endsection

@push('scripts')
<script>
function checkStrength(value) {
    const bar = document.getElementById('strengthBar');
    const text = document.getElementById('strengthText');
    let score = 0;
    if (value.length >= 8)  score++;
    if (/[A-Z]/.test(value)) score++;
    if (/[a-z]/.test(value)) score++;
    if (/[0-9]/.test(value)) score++;
    if (/[^A-Za-z0-9]/.test(value)) score++;

    const levels = [
        { pct: '0%',   color: '#e5e7eb', label: 'Nhập mật khẩu để kiểm tra độ mạnh' },
        { pct: '25%',  color: '#ef4444', label: 'Rất yếu — quá ngắn' },
        { pct: '50%',  color: '#f97316', label: 'Yếu — thêm chữ hoa / số' },
        { pct: '75%',  color: '#eab308', label: 'Trung bình — thêm ký tự đặc biệt' },
        { pct: '90%',  color: '#22c55e', label: 'Mạnh' },
        { pct: '100%', color: '#16a34a', label: 'Rất mạnh' },
    ];

    const lvl = value.length === 0 ? levels[0] : levels[Math.min(score, 5)];
    bar.style.width  = lvl.pct;
    bar.style.background = lvl.color;
    text.textContent = lvl.label;
}

document.getElementById('registerForm').addEventListener('submit', function(e) {
    const btn = document.getElementById('registerBtn');
    btn.disabled = true;
    btn.textContent = 'Đang tạo tài khoản...';
});
</script>
@endpush
