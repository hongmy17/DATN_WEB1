@extends('layouts.app')
@section('title', 'Quên mật khẩu — Nexus Store')
@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/pages/atuh.css') }}">
@endpush

@section('content')
    <div class="forgot-wrap">
        <div class="forgot-card">
            <a href="{{ url('/') }}"
                style="display:flex;align-items:center;justify-content:center;gap:4px;font-family:var(--font-display);font-size:22px;font-weight:700;color:var(--ink);margin-bottom:28px">
                Nexus<span style="color:var(--accent)">.</span>
            </a>

            <div class="step-dots">
                <div class="step-dot-item active" id="d1"></div>
                <div class="step-dot-item" id="d2"></div>
                <div class="step-dot-item" id="d3"></div>
            </div>

            {{-- BƯỚC 1: Nhập email --}}
            <div id="step1">
                <h1 style="font-family:var(--font-display);font-size:22px;font-weight:700;margin-bottom:6px">Quên mật khẩu?
                </h1>
                <p style="font-size:14px;color:var(--ink-3);margin-bottom:24px">Nhập email đã đăng ký. Chúng tôi sẽ gửi mã
                    xác nhận.</p>

                <div class="form-group">
                    <label class="form-label">Email</label>
                    <div class="input-with-icon">
                        <svg class="input-icon" width="15" height="15" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="1.8" stroke-linecap="round">
                            <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z" />
                            <polyline points="22,6 12,13 2,6" />
                        </svg>
                        <input type="email" id="resetEmail" class="form-control" placeholder="email@example.com">
                    </div>
                </div>

                <button onclick="goStep2()" class="btn btn-primary btn-full btn-lg">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2" stroke-linecap="round">
                        <line x1="22" y1="2" x2="11" y2="13" />
                        <polygon points="22 2 15 22 11 13 2 9 22 2" />
                    </svg>
                    Gửi mã xác nhận
                </button>

                <p style="text-align:center;font-size:13px;color:var(--ink-muted);margin-top:20px">
                    <a href="{{ url('tai-khoan') }}"
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

            {{-- BƯỚC 2: Nhập OTP --}}
            <div id="step2" style="display:none">
                <h1 style="font-family:var(--font-display);font-size:22px;font-weight:700;margin-bottom:6px">Nhập mã xác
                    nhận</h1>
                <p style="font-size:14px;color:var(--ink-3);margin-bottom:24px">Mã 6 số đã được gửi đến <strong
                        id="sentTo" style="color:var(--ink)"></strong></p>

                <div class="form-group">
                    <label class="form-label">Mã OTP</label>
                    <div class="otp-inputs">
                        @for ($i = 1; $i <= 6; $i++)
                            <input type="text" class="otp-input" maxlength="1" id="otp{{ $i }}"
                                oninput="otpNext(this,{{ $i }})">
                        @endfor
                    </div>
                </div>

                <button onclick="goStep3()" class="btn btn-primary btn-full btn-lg mt-8">Xác nhận mã</button>
                <p style="text-align:center;font-size:13px;color:var(--ink-muted);margin-top:14px">
                    Không nhận được mã?
                    <button onclick="Toast.show('Đã gửi lại mã OTP!','success')"
                        style="color:var(--accent);font-size:13px;font-weight:500;background:none;border:none;cursor:pointer;padding:0">Gửi
                        lại</button>
                </p>
            </div>

            {{-- BƯỚC 3: Đặt lại mật khẩu --}}
            <div id="step3" style="display:none">
                <h1 style="font-family:var(--font-display);font-size:22px;font-weight:700;margin-bottom:6px">Tạo mật khẩu
                    mới</h1>
                <p style="font-size:14px;color:var(--ink-3);margin-bottom:24px">Mật khẩu mới phải có ít nhất 8 ký tự.</p>

                <div class="form-group">
                    <label class="form-label">Mật khẩu mới</label>
                    <div class="input-with-icon">
                        <svg class="input-icon" width="15" height="15" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="1.8" stroke-linecap="round">
                            <rect x="3" y="11" width="18" height="11" rx="2" />
                            <path d="M7 11V7a5 5 0 0 1 10 0v4" />
                        </svg>
                        <input type="password" class="form-control" placeholder="Ít nhất 8 ký tự">
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">Xác nhận mật khẩu mới</label>
                    <div class="input-with-icon">
                        <svg class="input-icon" width="15" height="15" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="1.8" stroke-linecap="round">
                            <rect x="3" y="11" width="18" height="11" rx="2" />
                            <path d="M7 11V7a5 5 0 0 1 10 0v4" />
                        </svg>
                        <input type="password" class="form-control" placeholder="Nhập lại mật khẩu mới">
                    </div>
                </div>

                <button onclick="submitReset()" class="btn btn-accent btn-full btn-lg">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2" stroke-linecap="round">
                        <polyline points="20 6 9 17 4 12" />
                    </svg>
                    Đặt lại mật khẩu
                </button>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function updateDots(n) {
            [1, 2, 3].forEach(i => {
                document.getElementById('d' + i).classList.toggle('active', i <= n);
            });
        }

        function showStep(n) {
            [1, 2, 3].forEach(i => document.getElementById('step' + i).style.display = i === n ? '' : 'none');
            updateDots(n);
        }

        function goStep2() {
            const email = document.getElementById('resetEmail').value.trim();
            if (!email) {
                Toast.show('Vui lòng nhập email', 'error');
                return;
            }
            document.getElementById('sentTo').textContent = email;
            Toast.show('Mã OTP đã được gửi!', 'success');
            showStep(2);
            setTimeout(() => document.getElementById('otp1').focus(), 100);
        }

        function goStep3() {
            Toast.show('Xác nhận thành công!', 'success');
            showStep(3);
        }

        function otpNext(el, idx) {
            el.value = el.value.replace(/\D/, '');
            if (el.value && idx < 6) document.getElementById('otp' + (idx + 1)).focus();
        }

        function submitReset() {
            Toast.show('Đặt lại mật khẩu thành công!', 'success');
            setTimeout(() => window.location.href = '{{ url('tai-khoan') }}', 1400);
        }
        Object.assign(window, {
            goStep2,
            goStep3,
            otpNext,
            submitReset
        });
    </script>
@endpush
