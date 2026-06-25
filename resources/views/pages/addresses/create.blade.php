@extends('layouts.app')
@section('title', 'Thêm địa chỉ — Nexus Store')
@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/pages/addresses.css') }}">
@endpush
@section('content')
    <div class="container">
        <div class="addr-form-wrap">
            <div class="breadcrumb">
                <a href="{{ url('/') }}">Trang chủ</a>
                <span class="breadcrumb__sep">/</span>
                <a href="{{ url('dia-chi') }}">Địa chỉ</a>
                <span class="breadcrumb__sep">/</span>
                <span class="breadcrumb__current">Thêm mới</span>
            </div>

            <h1 class="h1 mb-24">Thêm địa chỉ mới</h1>

            <div class="addr-form-card">
                <form action="{{ route('addresses.store') }}" method="POST">
                    @csrf

                    <div class="form-row-2">
                        <div class="form-group">
                            <label class="form-label">Họ tên người nhận</label>
                            <input type="text" name="receiver_name" value="{{ old('receiver_name') }}"
                                class="form-control {{ $errors->has('receiver_name') ? 'is-error' : '' }}"
                                placeholder="Nguyễn Văn A">
                            @error('receiver_name')
                                <p class="form-error">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label class="form-label">Số điện thoại</label>
                            <input type="text" name="receiver_phone" value="{{ old('receiver_phone') }}"
                                class="form-control {{ $errors->has('receiver_phone') ? 'is-error' : '' }}"
                                placeholder="0900 000 000">
                            @error('receiver_phone')
                                <p class="form-error">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="form-row-2">
                        <div class="form-group">
                            <label class="form-label">Tỉnh / Thành phố</label>
                            <input type="text" name="province" value="{{ old('province') }}"
                                class="form-control {{ $errors->has('province') ? 'is-error' : '' }}"
                                placeholder="TP. Hồ Chí Minh">
                            @error('province')
                                <p class="form-error">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label class="form-label">Quận / Huyện</label>
                            <input type="text" name="district" value="{{ old('district') }}"
                                class="form-control {{ $errors->has('district') ? 'is-error' : '' }}" placeholder="Quận 1">
                            @error('district')
                                <p class="form-error">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Phường / Xã</label>
                        <input type="text" name="ward" value="{{ old('ward') }}"
                            class="form-control {{ $errors->has('ward') ? 'is-error' : '' }}"
                            placeholder="Phường Bến Nghé">
                        @error('ward')
                            <p class="form-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label">Địa chỉ chi tiết</label>
                        <textarea name="address_detail" rows="2"
                            class="form-control {{ $errors->has('address_detail') ? 'is-error' : '' }}" placeholder="Số nhà, tên đường...">{{ old('address_detail') }}</textarea>
                        @error('address_detail')
                            <p class="form-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div style="display:flex;align-items:center;gap:8px;margin-bottom:24px">
                        <input type="checkbox" name="is_default" value="1" id="isDefault"
                            {{ old('is_default') ? 'checked' : '' }}
                            style="width:16px;height:16px;accent-color:var(--accent);cursor:pointer">
                        <label for="isDefault" style="font-size:14px;color:var(--ink-2);cursor:pointer">Đặt làm địa chỉ mặc
                            định</label>
                    </div>

                    <div style="display:flex;gap:10px">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2" stroke-linecap="round">
                                <polyline points="20 6 9 17 4 12" />
                            </svg>
                            Lưu địa chỉ
                        </button>
                        <a href="{{ route('addresses.index') }}" class="btn btn-ghost btn-lg">Huỷ</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
