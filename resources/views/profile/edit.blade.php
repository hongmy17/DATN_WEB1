@extends('layouts.app')
@section('title', 'Tài khoản — Nexus Store')
@push('styles')
    <style>
        .account-layout {
            display: grid;
            grid-template-columns: 220px 1fr;
            gap: 24px;
            padding: 40px 0 80px;
        }

        .account-nav {
            background: var(--bg-alt);
            border: 1px solid var(--border-soft);
            border-radius: var(--r-xl);
            padding: 16px;
            position: sticky;
            top: 88px;
        }

        .account-nav-link {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 12px;
            border-radius: var(--r-md);
            font-size: 14px;
            color: var(--ink-2);
            transition: var(--t);
            margin-bottom: 2px;
        }

        .account-nav-link:hover {
            background: var(--surface);
            color: var(--ink);
        }

        .account-nav-link.active {
            background: var(--ink);
            color: #fff;
            font-weight: 500;
        }

        .profile-card {
            background: var(--bg-alt);
            border: 1px solid var(--border-soft);
            border-radius: var(--r-xl);
            padding: 28px;
            margin-bottom: 20px;
        }

        .profile-card-title {
            font-size: 16px;
            font-weight: 700;
            color: var(--ink);
            margin-bottom: 4px;
        }

        .profile-card-sub {
            font-size: 13px;
            color: var(--ink-3);
            margin-bottom: 24px;
        }

        .alert-success {
            background: var(--green-light);
            color: var(--green);
            border: 1px solid rgba(45, 122, 82, .2);
            border-radius: var(--r-md);
            padding: 12px 16px;
            font-size: 13px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .alert-danger {
            background: var(--red-light);
            color: var(--red);
            border: 1px solid rgba(185, 28, 28, .2);
            border-radius: var(--r-md);
            padding: 12px 16px;
            font-size: 13px;
            margin-bottom: 20px;
        }

        .form-row-2 {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 14px;
        }

        .danger-zone {
            border: 1.5px solid var(--red-light);
            border-radius: var(--r-xl);
            padding: 28px;
            background: var(--red-light);
        }

        .danger-zone .profile-card-title {
            color: var(--red);
        }

        @media(max-width:768px) {
            .account-layout {
                grid-template-columns: 1fr;
            }

            .account-nav {
                position: static;
            }

            .form-row-2 {
                grid-template-columns: 1fr;
            }
        }
    </style>
@endpush

@section('content')
    <div class="container">
        <div class="account-layout">

            {{-- SIDEBAR NAV --}}
            <div class="account-nav">
                <a href="{{ route('profile.edit') }}" class="account-nav-link active">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="1.8" stroke-linecap="round">
                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" />
                        <circle cx="12" cy="7" r="4" />
                    </svg>
                    Tài khoản
                </a>
                <a href="{{ route('orders.index') }}" class="account-nav-link">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="1.8" stroke-linecap="round">
                        <path d="M9 11l3 3L22 4" />
                        <path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11" />
                    </svg>
                    Đơn hàng
                </a>
                <a href="{{ route('addresses.index') }}" class="account-nav-link">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="1.8" stroke-linecap="round">
                        <path d="M21 10c0 7-9 13-9 13S3 17 3 10a9 9 0 0 1 18 0z" />
                        <circle cx="12" cy="10" r="3" />
                    </svg>
                    Địa chỉ
                </a>
                <a href="{{ route('wishlist') }}" class="account-nav-link">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="1.8" stroke-linecap="round">
                        <path
                            d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z" />
                    </svg>
                    Yêu thích
                </a>
            </div>

            {{-- CONTENT --}}
            <div>
                <h1 class="h1 mb-24">Tài khoản của tôi</h1>

                {{-- ── THÔNG TIN CÁ NHÂN ── --}}
                <div class="profile-card">
                    <div class="profile-card-title">Thông tin cá nhân</div>
                    <div class="profile-card-sub">Cập nhật tên và địa chỉ email của bạn.</div>

                    @if (session('status') === 'profile-updated')
                        <div class="alert-success">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2.5" stroke-linecap="round">
                                <polyline points="20 6 9 17 4 12" />
                            </svg>
                            Đã cập nhật thông tin thành công!
                        </div>
                    @endif

                    @if ($errors->any() && !$errors->updatePassword->any() && !$errors->userDeletion->any())
                        <div class="alert-danger">
                            @foreach ($errors->all() as $e)
                                <p>{{ $e }}</p>
                            @endforeach
                        </div>
                    @endif

                    <form method="POST" action="{{ route('profile.update') }}">
                        @csrf @method('patch')
                        <div class="form-row-2">
                            <div class="form-group">
                                <label class="form-label">Họ và tên</label>
                                <input type="text" name="name" value="{{ old('name', $user->name) }}"
                                    class="form-control {{ $errors->has('name') ? 'is-error' : '' }}" required>
                                @error('name')
                                    <p class="form-error">{{ $message }}</p>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label class="form-label">Email</label>
                                <input type="email" name="email" value="{{ old('email', $user->email) }}"
                                    class="form-control {{ $errors->has('email') ? 'is-error' : '' }}" required>
                                @error('email')
                                    <p class="form-error">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                       @if (in_array('phone', $user->getFillable()))
                            <div class="form-group">
                                <label class="form-label">Số điện thoại</label>
                                <input type="text" name="phone" value="{{ old('phone', $user->phone) }}"
                                    class="form-control">
                            </div>
                        @endif
                        <button type="submit" class="btn btn-primary">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2" stroke-linecap="round">
                                <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z" />
                                <polyline points="17 21 17 13 7 13 7 21" />
                            </svg>
                            Lưu thay đổi
                        </button>
                    </form>
                </div>

                {{-- ── ĐỔI MẬT KHẨU ── --}}
                <div class="profile-card">
                    <div class="profile-card-title">Đổi mật khẩu</div>
                    <div class="profile-card-sub">Đảm bảo mật khẩu mới có độ dài ít nhất 8 ký tự, bao gồm chữ hoa, chữ
                        thường và số.</div>

                    @if (session('status') === 'password-updated')
                        <div class="alert-success">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2.5" stroke-linecap="round">
                                <polyline points="20 6 9 17 4 12" />
                            </svg>
                            Đã đổi mật khẩu thành công!
                        </div>
                    @endif

                    @if ($errors->updatePassword->any())
                        <div class="alert-danger">
                            @foreach ($errors->updatePassword->all() as $e)
                                <p>{{ $e }}</p>
                            @endforeach
                        </div>
                    @endif

                    <form method="POST" action="{{ route('password.update') }}">
                        @csrf @method('put')
                        <div class="form-group">
                            <label class="form-label">Mật khẩu hiện tại</label>
                            <div class="input-with-icon">
                                <svg class="input-icon" width="15" height="15" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round">
                                    <rect x="3" y="11" width="18" height="11" rx="2" />
                                    <path d="M7 11V7a5 5 0 0 1 10 0v4" />
                                </svg>
                                <input type="password" name="current_password"
                                    class="form-control {{ $errors->updatePassword->has('current_password') ? 'is-error' : '' }}"
                                    placeholder="••••••••">
                            </div>
                        </div>
                        <div class="form-row-2">
                            <div class="form-group">
                                <label class="form-label">Mật khẩu mới</label>
                                <div class="input-with-icon">
                                    <svg class="input-icon" width="15" height="15" viewBox="0 0 24 24"
                                        fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round">
                                        <rect x="3" y="11" width="18" height="11" rx="2" />
                                        <path d="M7 11V7a5 5 0 0 1 10 0v4" />
                                    </svg>
                                    <input type="password" name="password"
                                        class="form-control {{ $errors->updatePassword->has('password') ? 'is-error' : '' }}"
                                        placeholder="Ví dụ: Nexus@2026">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Xác nhận mật khẩu mới</label>
                                <div class="input-with-icon">
                                    <svg class="input-icon" width="15" height="15" viewBox="0 0 24 24"
                                        fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round">
                                        <rect x="3" y="11" width="18" height="11" rx="2" />
                                        <path d="M7 11V7a5 5 0 0 1 10 0v4" />
                                    </svg>
                                    <input type="password" name="password_confirmation" class="form-control"
                                        placeholder="••••••••">
                                </div>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2" stroke-linecap="round">
                                <rect x="3" y="11" width="18" height="11" rx="2" />
                                <path d="M7 11V7a5 5 0 0 1 10 0v4" />
                            </svg>
                            Đổi mật khẩu
                        </button>
                    </form>
                </div>

                {{-- ── XOÁ TÀI KHOẢN ── --}}
                <div class="danger-zone">
                    <div class="profile-card-title">Xoá tài khoản</div>
                    <div class="profile-card-sub" style="color:var(--red);opacity:.8">Sau khi xoá, toàn bộ dữ liệu sẽ bị
                        xoá vĩnh viễn và không thể khôi phục.</div>

                    @if ($errors->userDeletion->any())
                        <div class="alert-danger">
                            @foreach ($errors->userDeletion->all() as $e)
                                <p>{{ $e }}</p>
                            @endforeach
                        </div>
                    @endif

                    <button onclick="document.getElementById('deleteModal').classList.add('open')"
                        class="btn btn-danger btn-sm">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2" stroke-linecap="round">
                            <polyline points="3 6 5 6 21 6" />
                            <path d="M19 6l-1 14H6L5 6" />
                            <path d="M10 11v6M14 11v6" />
                        </svg>
                        Xoá tài khoản vĩnh viễn
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL XOÁ TÀI KHOẢN --}}
    <div class="modal-overlay" id="deleteModal">
        <div class="modal">
            <div class="modal__head">
                <span class="modal__title" style="color:var(--red)">Xác nhận xoá tài khoản</span>
                <button class="modal__close" onclick="document.getElementById('deleteModal').classList.remove('open')">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2.5" stroke-linecap="round">
                        <line x1="18" y1="6" x2="6" y2="18" />
                        <line x1="6" y1="6" x2="18" y2="18" />
                    </svg>
                </button>
            </div>
            <div class="modal__body">
                <p style="font-size:14px;color:var(--ink-3);margin-bottom:20px">Nhập mật khẩu hiện tại để xác nhận xoá tài
                    khoản. Hành động này <strong>không thể hoàn tác</strong>.</p>
                <form method="POST" action="{{ route('profile.destroy') }}">
                    @csrf @method('delete')
                    <div class="form-group">
                        <label class="form-label">Mật khẩu xác nhận</label>
                        <input type="password" name="password" class="form-control" placeholder="Nhập mật khẩu của bạn"
                            required autofocus>
                    </div>
                    <div style="display:flex;gap:10px;margin-top:4px">
                        <button type="submit" class="btn btn-danger flex-1">Xoá vĩnh viễn</button>
                        <button type="button" onclick="document.getElementById('deleteModal').classList.remove('open')"
                            class="btn btn-ghost flex-1">Huỷ</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
