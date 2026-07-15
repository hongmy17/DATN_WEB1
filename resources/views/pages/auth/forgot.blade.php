@extends('layouts.app')
@section('title', 'Quên mật khẩu — Nexus Store')
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
                    <line x1="17" y1="10" x2="10" y2="22" stroke="#fff" stroke-width="1.6" stroke-linecap="round" />
                    <line x1="17" y1="10" x2="24" y2="22" stroke="#fff" stroke-width="1.6" stroke-linecap="round" />
                    <line x1="10" y1="22" x2="24" y2="22" stroke="#fff" stroke-width="1.6" stroke-linecap="round" />
                </svg>
                <span>Nexus<span style="color:var(--accent)"></span></span>
            </a>

            <h1 style="font-family:var(--font-display);font-size:22px;font-weight:700;margin-bottom:6px">Quên mật khẩu?</h1>
            <p style="font-size:14px;color:var(--ink-3);margin-bottom:24px">Nhập email đã đăng ký. Chúng tôi sẽ gửi cho bạn
                1 link để đặt lại mật khẩu.</p>

            @if ($errors->any())
                <div style="background:#fee2e2;color:#991b1b;padding:12px;border-radius:8px;margin-bottom:16px">
                    <ul style="margin:0;padding-left:18px">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('password.email') }}">
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

                <button type="submit" class="btn btn-primary btn-full btn-lg">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2" stroke-linecap="round">
                        <line x1="22" y1="2" x2="11" y2="13" />
                        <polygon points="22 2 15 22 11 13 2 9 22 2" />
                    </svg>
                    Gửi link đặt lại mật khẩu
                </button>
            </form>

            <p style="text-align:center;font-size:13px;color:var(--ink-muted);margin-top:20px">
                <a href="{{ route('login') }}"
                    style="color:var(--accent);font-weight:500;display:inline-flex;align-items:center;gap:5px">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2.5" stroke-linecap="round">
                        <line x1="19" y1="12" x2="5" y2="12" />
                        <polyline points="12 19 5 12 12 5" />
                    </svg>
                    Quay lại đăng nhập
                </a>
            </p>
        </div>
    </div>
@endsection