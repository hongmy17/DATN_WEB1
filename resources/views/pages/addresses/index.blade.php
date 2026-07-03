@extends('layouts.app')
@section('title', 'Địa chỉ — Nexus Store')
@push('styles')
<link rel="stylesheet" href="{{ asset('assets/css/pages/addresses.css') }}">
@endpush
@section('content')
<div class="container">
  <div class="account-layout">
    <div class="account-nav">
      <a href="{{ route('profile.edit') }}" class="account-nav-link">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
        Tài khoản
      </a>
      <a href="{{ url('don-hang') }}" class="account-nav-link">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"><path d="M9 11l3 3L22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg>
        Đơn hàng
      </a>
      <a href="{{ url('dia-chi') }}" class="account-nav-link active">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"><path d="M21 10c0 7-9 13-9 13S3 17 3 10a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
        Địa chỉ
      </a>
      <a href="{{ url('yeu-thich') }}" class="account-nav-link">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg>
        Yêu thích
      </a>
    </div>

    <div>
      <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:24px">
        <h1 class="h1">Địa chỉ giao hàng</h1>
        <a href="{{ route('addresses.create') }}" class="btn btn-primary btn-sm">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
          Thêm địa chỉ
        </a>
      </div>

      <div class="addr-grid">
        @foreach($addresses as $addr)
        <div class="addr-card {{ $addr->is_default ? 'default' : '' }}">
          @if($addr->is_default)
          <div style="position:absolute;top:14px;right:14px"><span class="badge badge-info">Mặc định</span></div>
          @endif
          <div class="addr-card__head">
            <div>
              <div class="addr-card__name">{{ $addr->receiver_name }}</div>
              <div style="font-size:13px;color:var(--ink-3);margin-top:2px">{{ $addr->receiver_phone }}</div>
            </div>
          </div>
          <div class="addr-card__info">
            {{ $addr->address_detail }},<br>
            {{ $addr->ward }} {{ $addr->district }}<br>
            {{ $addr->province }}
          </div>
          <div style="display:flex;gap:8px;margin-top:14px">
            <a href="{{ route('addresses.edit',$addr->id) }}" class="btn btn-outline btn-sm">
              <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
              Sửa
            </a>
            @if(!$addr->is_default)
            <form method="POST" action="{{ route('addresses.destroy',$addr->id) }}" onsubmit="return confirm('Xoá địa chỉ này?')">
              @csrf @method('DELETE')
              <button type="submit" class="btn btn-danger btn-sm">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14H6L5 6"/></svg>
                Xoá
              </button>
            </form>
            @endif
          </div>
        </div>
        @endforeach

        <a href="{{ route('addresses.create') }}" class="addr-add">
          <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="16"/><line x1="8" y1="12" x2="16" y2="12"/></svg>
          <span style="font-size:14px;font-weight:500">Thêm địa chỉ mới</span>
        </a>
      </div>
    </div>
  </div>
</div>
@endsection
