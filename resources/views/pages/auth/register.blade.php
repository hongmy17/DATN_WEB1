@extends('layouts.app')
@section('title', 'Đăng ký — Nexus Store')
@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/pages/auth.css') }}">
@endpush
@section('content')
    <div class="auth-wrap">
        <div class="auth-card">
            <div class="auth-logo">
                <svg width="26" height="26" viewBox="0 0 34 34" fill="none">
                    <rect width="34" height="34" rx="8" fill="var(--accent)" />
                    <circle cx="17" cy="10" r="2.6" fill="#fff" />
                    <circle cx="10" cy="22" r="2.6" fill="#fff" />
                    <circle cx="24" cy="22" r="2.6" fill="#fff" />
                    <line x1="17" y1="10" x2="10" y2="22" stroke="#fff" stroke-width="1.6" stroke-linecap="round" />
                    <line x1="17" y1="10" x2="24" y2="22" stroke="#fff" stroke-width="1.6" stroke-linecap="round" />
                    <line x1="10" y1="22" x2="24" y2="22" stroke="#fff" stroke-width="1.6" stroke-linecap="round" />
                </svg>
                <span>Nexus<span></span></span>
            </div>

            <h1 class="auth-form-title">Tạo tài khoản</h1>
            <p class="auth-form-sub">Tham gia ngay để nhận ưu đãi độc quyền</p>

            <form method="POST" action="{{ route('register.store') }}">
                @csrf
                @if ($errors->any())
                    <div style="background:var(--red-light);color:var(--red);padding:12px;border-radius:var(--r-md);margin-bottom:16px">
                        <ul style="margin:0;padding-left:18px">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                <div class="form-group">
                    <label class="form-label">Họ và tên</label>
                    <div class="input-with-icon">
                        <svg class="input-icon" width="15" height="15" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="1.8" stroke-linecap="round">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" />
                            <circle cx="12" cy="7" r="4" />
                        </svg>
                        <input type="text" name="name" value="{{ old('name') }}"
                            class="form-control {{ $errors->has('name') ? 'is-error' : '' }}" placeholder="Nguyễn Văn A"
                            required autofocus>
                    </div>
                    @error('name')
                        <p class="form-error">{{ $message }}</p>
                    @enderror
                </div>

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
                            placeholder="email@example.com" required>
                    </div>
                    @error('email')
                        <p class="form-error">{{ $message }}</p>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Số điện thoại</label>
                    <div class="input-with-icon">
                        <svg class="input-icon" width="15" height="15" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="1.8" stroke-linecap="round">
                            <path
                                d="M22 16.92v3a2 2 0 0 1-2.18 2A19.8 19.8 0 0 1 3 5.18 2 2 0 0 1 5 3h3a2 2 0 0 1 2 1.72c.12.9.32 1.77.59 2.61a2 2 0 0 1-.45 2.11L9 10.59a16 16 0 0 0 4.41 4.41l1.15-1.15a2 2 0 0 1 2.11-.45c.84.27 1.71.47 2.61.59A2 2 0 0 1 22 16.92z" />
                        </svg>

                        <input type="text" name="phone" value="{{ old('phone') }}"
                            class="form-control {{ $errors->has('phone') ? 'is-error' : '' }}"
                            placeholder="0901234567">
                    </div>

                    @error('phone')
                        <p class="form-error">{{ $message }}</p>
                    @enderror
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Mật khẩu</label>
                        <div class="input-with-icon">
                            <svg class="input-icon" width="15" height="15" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="1.8" stroke-linecap="round">
                                <rect x="3" y="11" width="18" height="11" rx="2" />
                                <path d="M7 11V7a5 5 0 0 1 10 0v4" />
                            </svg>
                            <input type="password" name="password"
                                class="form-control {{ $errors->has('password') ? 'is-error' : '' }}"
                                placeholder="Tối thiểu 8 ký tự" required>
                        </div>
                        @error('password')
                            <p class="form-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label">Xác nhận mật khẩu</label>
                        <div class="input-with-icon">
                            <svg class="input-icon" width="15" height="15" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="1.8" stroke-linecap="round">
                                <rect x="3" y="11" width="18" height="11" rx="2" />
                                <path d="M7 11V7a5 5 0 0 1 10 0v4" />
                            </svg>
                            <input type="password" name="password_confirmation" class="form-control"
                                placeholder="Nhập lại mật khẩu" required>
                        </div>
                    </div>
                </div>

                <div style="display:flex;align-items:flex-start;gap:8px;margin:16px 0 24px">
                    <input type="checkbox" id="agree" name="terms" value="1" required
                        style="width:15px;height:15px;margin-top:2px;accent-color:var(--accent);cursor:pointer;flex-shrink:0">
                    <label for="agree" style="font-size:13px;color:var(--ink-2);cursor:pointer;line-height:1.5">
                        Tôi đồng ý với <a href="#" style="color:var(--accent)">Điều khoản dịch vụ</a> và <a
                            href="#" style="color:var(--accent)">Chính sách bảo mật</a> của Nexus Store.
                    </label>
                </div>

                <button type="submit" class="btn btn-primary btn-full btn-lg">Tạo tài khoản</button>
            </form>
            <p style="text-align:center;font-size:13.5px;color:var(--ink-3);margin-top:24px">
                Đã có tài khoản? <a href="{{ route('login') }}" style="color:var(--accent);font-weight:600">Đăng
                    nhập</a>
            </p>
        </div>
    </div>
@endsection