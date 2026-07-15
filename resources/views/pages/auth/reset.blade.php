@extends('layouts.app')
@section('title', 'Đặt lại mật khẩu — Nexus Store')
@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/pages/auth.css') }}">
@endpush

@section('content')
    <div class="forgot-wrap">
        <div class="forgot-card">
            <a href="{{ url('/') }}"
                style="display:flex;align-items:center;justify-content:center;gap:10px;font-family:var(--font-display);font-size:22px;font-weight:700;color:var(--ink);margin-bottom:28px">
                <svg width="28" height="28" viewBox="0 0 34 34" fill="none">
                    <rect width="34" height="34" rx="8" fill="var(--accent)" />
                    <circle cx="17" cy="10" r="2.6" fill="#fff" />
                    <circle cx="10" cy="22" r="2.6" fill="#fff" />
                    <circle cx="24" cy="22" r="2.6" fill="#fff" />
                    <line x1="17" y1="10" x2="10" y2="22" stroke="#fff" stroke-width="1.6"
                        stroke-linecap="round" />
                    <line x1="17" y1="10" x2="24" y2="22" stroke="#fff" stroke-width="1.6"
                        stroke-linecap="round" />
                    <line x1="10" y1="22" x2="24" y2="22" stroke="#fff" stroke-width="1.6"
                        stroke-linecap="round" />
                </svg>
                <span>Nexus<span style="color:var(--accent)"></span></span>
            </a>

            <h1 style="font-family:var(--font-display);font-size:22px;font-weight:700;margin-bottom:6px">Tạo mật khẩu mới
            </h1>
            <p style="font-size:14px;color:var(--ink-3);margin-bottom:24px">Mật khẩu mới phải có ít nhất 8 ký tự, gồm cả
                chữ hoa, chữ thường và số.</p>

            @if ($errors->any())
                <div style="background:#fee2e2;color:#991b1b;padding:12px;border-radius:8px;margin-bottom:16px">
                    <ul style="margin:0;padding-left:18px">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('password.store') }}">
                @csrf
                <input type="hidden" name="token" value="{{ $request->route('token') }}">

                <div class="form-group">
                    <label class="form-label">Email</label>
                    <div class="input-with-icon">
                        <svg class="input-icon" width="15" height="15" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="1.8" stroke-linecap="round">
                            <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z" />
                            <polyline points="22,6 12,13 2,6" />
                        </svg>
                        <input type="email" name="email" value="{{ old('email', $request->email) }}"
                            class="form-control {{ $errors->has('email') ? 'is-error' : '' }}" required>
                    </div>
                    @error('email')
                        <p class="form-error">{{ $message }}</p>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Mật khẩu mới</label>
                    <div class="input-with-icon">
                        <svg class="input-icon" width="15" height="15" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="1.8" stroke-linecap="round">
                            <rect x="3" y="11" width="18" height="11" rx="2" />
                            <path d="M7 11V7a5 5 0 0 1 10 0v4" />
                        </svg>
                        <input type="password" name="password"
                            class="form-control {{ $errors->has('password') ? 'is-error' : '' }}"
                            placeholder="Ít nhất 8 ký tự" required>
                    </div>
                    @error('password')
                        <p class="form-error">{{ $message }}</p>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Xác nhận mật khẩu mới</label>
                    <div class="input-with-icon">
                        <svg class="input-icon" width="15" height="15" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="1.8" stroke-linecap="round">
                            <rect x="3" y="11" width="18" height="11" rx="2" />
                            <path d="M7 11V7a5 5 0 0 1 10 0v4" />
                        </svg>
                        <input type="password" name="password_confirmation" class="form-control"
                            placeholder="Nhập lại mật khẩu mới" required>
                    </div>
                </div>

                <button type="submit" class="btn btn-accent btn-full btn-lg">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2" stroke-linecap="round">
                        <polyline points="20 6 9 17 4 12" />
                    </svg>
                    Đặt lại mật khẩu
                </button>
            </form>
        </div>
    </div>
@endsection
