@extends('layouts.app')
@section('title', 'Chi tiết đơn hàng — Nexus Store')
@push('styles')
<link rel="stylesheet" href="{{ asset('assets/css/pages/order.css') }}">
@endpush

@section('content')
<div class="container">
  <div class="account-layout">
    {{-- NAV --}}
    <div class="account-nav">
      <a href="{{ route('profile.edit') }}" class="account-nav-link">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
        Tài khoản
      </a>
      <a href="{{ url('don-hang') }}" class="account-nav-link active">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"><path d="M9 11l3 3L22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg>
        Đơn hàng
      </a>
      <a href="{{ url('dia-chi') }}" class="account-nav-link">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"><path d="M21 10c0 7-9 13-9 13S3 17 3 10a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
        Địa chỉ
      </a>
      <a href="{{ url('yeu-thich') }}" class="account-nav-link">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg>
        Yêu thích
      </a>
    </div>

    {{-- CONTENT --}}
    <div>
      <div style="display:flex;align-items:center;gap:12px;margin-bottom:24px">
        <a href="{{ route('orders.index') }}" style="display:flex;align-items:center;gap:6px;font-size:13px;color:var(--ink-muted)">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
          Quay lại đơn hàng
        </a>
      </div>

      <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:24px">
        <h1 class="h1">Đơn hàng NX-{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}</h1>
        @php
          $statusMap   = [0=>'status-pending',1=>'status-confirmed',2=>'status-shipping',3=>'status-delivered',4=>'status-cancelled'];
          $statusLabel = [0=>'Chờ xác nhận',1=>'Đã xác nhận',2=>'Đang giao',3=>'Hoàn thành',4=>'Đã huỷ'];
        @endphp
        <span class="status {{ $statusMap[$order->order_status] ?? '' }}">
          {{ $statusLabel[$order->order_status] ?? '' }}
        </span>
      </div>

      {{-- TIMELINE TRẠNG THÁI --}}
      <div class="order-card" style="margin-bottom:20px;padding:24px">
        <div style="font-weight:700;font-size:14px;margin-bottom:16px;display:flex;align-items:center;gap:8px">
          <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
          Theo dõi trạng thái
        </div>
        <div style="display:flex;align-items:center;justify-content:space-between;position:relative">
          <div style="position:absolute;top:16px;left:0;right:0;height:2px;background:var(--border-soft);z-index:0"></div>
          @php
            $steps = [
              ['label' => 'Chờ xác nhận', 'status' => 0],
              ['label' => 'Đã xác nhận',  'status' => 1],
              ['label' => 'Đang giao',     'status' => 2],
              ['label' => 'Hoàn thành',    'status' => 3],
            ];
          @endphp
          @foreach($steps as $step)
          @php
            $done    = $order->order_status >= $step['status'] && $order->order_status != 4;
            $current = $order->order_status == $step['status'];
          @endphp
          <div style="display:flex;flex-direction:column;align-items:center;gap:8px;z-index:1;flex:1">
            <div style="width:32px;height:32px;border-radius:50%;display:flex;align-items:center;justify-content:center;
              background:{{ $done ? 'var(--accent)' : 'var(--surface)' }};
              border:2px solid {{ $done ? 'var(--accent)' : 'var(--border)' }};
              {{ $current ? 'box-shadow:0 0 0 4px rgba(200,82,42,.15)' : '' }}">
              @if($done)
              <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="3" stroke-linecap="round"><polyline points="20 6 9 17 4 12"/></svg>
              @endif
            </div>
            <span style="font-size:11px;font-weight:{{ $current ? '700' : '500' }};color:{{ $done ? 'var(--accent)' : 'var(--ink-muted)' }};text-align:center">
              {{ $step['label'] }}
            </span>
          </div>
          @endforeach

          @if($order->order_status == 4)
          <div style="position:absolute;top:4px;right:0;background:var(--red-light);color:var(--red);border-radius:var(--r-lg);padding:4px 10px;font-size:12px;font-weight:600">
            Đã huỷ
          </div>
          @endif
        </div>
      </div>

      {{-- THÔNG TIN GIAO HÀNG --}}
      <div class="order-card" style="margin-bottom:20px;padding:24px">
        <div style="font-weight:700;font-size:14px;margin-bottom:12px;display:flex;align-items:center;gap:8px">
          <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M21 10c0 7-9 13-9 13S3 17 3 10a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
          Thông tin giao hàng
        </div>
        <p style="font-size:14px;font-weight:600">{{ $order->receiver_name }} — {{ $order->receiver_phone }}</p>
        <p style="font-size:13px;color:var(--ink-muted);margin-top:4px">{{ $order->shipping_address }}</p>
        @if($order->note)
        <p style="font-size:13px;color:var(--ink-muted);margin-top:4px">Ghi chú: {{ $order->note }}</p>
        @endif
      </div>

      {{-- SẢN PHẨM --}}
      <div class="order-card" style="margin-bottom:20px;padding:24px">
        <div style="font-weight:700;font-size:14px;margin-bottom:12px;display:flex;align-items:center;gap:8px">
          <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>
          Sản phẩm đã đặt
        </div>
        <div class="order-items-list">
          @foreach($order->items as $item)
          <div class="order-item-row">
            <div class="order-item-img">
              <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round"><rect x="2" y="3" width="20" height="14" rx="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/></svg>
            </div>
            <div style="flex:1">
              <div class="order-item-name">{{ $item->product_name }}</div>
              <div class="order-item-variant">{{ $item->variant_description }} × {{ $item->quantity }}</div>
            </div>
            <div class="order-item-price">{{ number_format($item->total_price ?? $item->unit_price * $item->quantity, 0, ',', '.') }}₫</div>
          </div>
          @endforeach
        </div>
      </div>

      {{-- TỔNG TIỀN --}}
      <div class="order-card" style="margin-bottom:20px;padding:24px">
        <div style="font-weight:700;font-size:14px;margin-bottom:12px;display:flex;align-items:center;gap:8px">
          <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><rect x="1" y="4" width="22" height="16" rx="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>
          Thông tin thanh toán
        </div>
        <div style="display:flex;justify-content:space-between;font-size:14px;padding:6px 0;border-bottom:1px solid var(--border-soft)">
  <span style="color:var(--ink-muted)">Phương thức thanh toán</span>
  <span style="font-weight:600">Thanh toán khi nhận hàng (COD)</span>
</div>
        @if($order->discount_amount > 0)
        <div style="display:flex;justify-content:space-between;font-size:14px;padding:6px 0;border-bottom:1px solid var(--border-soft)">
          <span style="color:var(--ink-muted)">Giảm giá {{ $order->coupon ? '('.$order->coupon->coupon_code.')' : '' }}</span>
          <span style="color:var(--green)">-{{ number_format($order->discount_amount, 0, ',', '.') }}₫</span>
        </div>
        @endif
        <div style="display:flex;justify-content:space-between;font-size:14px;padding:6px 0;border-bottom:1px solid var(--border-soft)">
          <span style="color:var(--ink-muted)">Phí vận chuyển</span>
          <span style="color:var(--green)">Miễn phí</span>
        </div>
        <div style="display:flex;justify-content:space-between;font-size:18px;font-weight:800;padding:12px 0 0">
          <span>Tổng cộng</span>
          <span style="color:var(--accent)">{{ number_format($order->total_amount, 0, ',', '.') }}₫</span>
        </div>
      </div>

      {{-- NÚT HỦY ĐƠN --}}
      @if($order->order_status === 0)
      <div style="margin-top:8px">
        <form action="{{ route('orders.cancel', $order) }}" method="POST"
          onsubmit="return confirm('Bạn có chắc muốn hủy đơn hàng này?')">
          @csrf
          <button type="submit" class="btn btn-ghost"
            style="color:var(--red);border-color:var(--red)">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
            Yêu cầu hủy đơn
          </button>
        </form>
      </div>
      @endif

      @if(session('success'))
      <div style="margin-top:16px;padding:12px 16px;background:var(--green-light);color:var(--green);border-radius:var(--r-lg);font-size:14px;font-weight:600">
        {{ session('success') }}
      </div>
      @endif

      @if(session('error'))
      <div style="margin-top:16px;padding:12px 16px;background:var(--red-light);color:var(--red);border-radius:var(--r-lg);font-size:14px;font-weight:600">
        {{ session('error') }}
      </div>
      @endif
    </div>
  </div>
</div>
@endsection