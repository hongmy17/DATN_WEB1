@extends('layouts.app')
@section('title', 'Đơn hàng của tôi — Nexus Store')
@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/pages/order.css') }}">
@endpush
@section('content')
    <div class="container">
        <div class="account-layout">
            {{-- NAV --}}
            <div class="account-nav">
                <a href="{{ route('profile.edit') }}" class="account-nav-link">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="1.8" stroke-linecap="round">
                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" />
                        <circle cx="12" cy="7" r="4" />
                    </svg>
                    Tài khoản
                </a>
                <a href="{{ url('don-hang') }}" class="account-nav-link active">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="1.8" stroke-linecap="round">
                        <path d="M9 11l3 3L22 4" />
                        <path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11" />
                    </svg>
                    Đơn hàng
                </a>
                <a href="{{ url('dia-chi') }}" class="account-nav-link">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="1.8" stroke-linecap="round">
                        <path d="M21 10c0 7-9 13-9 13S3 17 3 10a9 9 0 0 1 18 0z" />
                        <circle cx="12" cy="10" r="3" />
                    </svg>
                    Địa chỉ
                </a>
                <a href="{{ url('yeu-thich') }}" class="account-nav-link">
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
                <h1 class="h1 mb-24">Đơn hàng của tôi</h1>

                @forelse($orders ?? [] as $order)
                    <div class="order-card">
                        <div class="order-head">
                            <span class="order-code">NX-{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}</span>
                            @php
                                $isExpired = $order->isPaymentExpired();
                                $statusMap = [
                                    0 => 'status-pending',
                                    1 => 'status-confirmed',
                                    2 => 'status-shipping',
                                    3 => 'status-delivered',
                                    4 => 'status-cancelled',
                                    5 => $isExpired ? 'status-expired' : 'status-awaiting',
                                ];
                            @endphp
                            <span
                                class="status {{ $statusMap[$order->order_status] ?? '' }}">{{ $order->statusLabel() }}</span>
                            <span
                                style="font-size:12px;color:var(--ink-muted);margin-left:auto">{{ $order->created_at->format('d/m/Y H:i') }}</span>
                        </div>

                        <div class="order-items-list">
                            @foreach ($order->items as $item)
                                <div class="order-item-row">
                                    <div class="order-item-img">
                                        @if ($item->product_thumbnail)
                                            <img src="{{ asset('storage/' . $item->product_thumbnail) }}"
                                                alt="{{ $item->product_name }}"
                                                style="width:100%;height:100%;object-fit:contain"
                                                onerror="this.style.display='none'">
                                        @else
                                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                                stroke="currentColor" stroke-width="1" stroke-linecap="round">
                                                <rect x="2" y="3" width="20" height="14" rx="2" />
                                                <line x1="8" y1="21" x2="16" y2="21" />
                                                <line x1="12" y1="17" x2="12" y2="21" />
                                            </svg>
                                        @endif
                                    </div>
                                    <div>
                                        <div class="order-item-name">{{ $item->product_name }}</div>
                                        <div class="order-item-variant">{{ $item->variant_description }} ×
                                            {{ $item->quantity }}</div>
                                    </div>
                                    <div class="order-item-price">{{ number_format($item->total_price, 0, ',', '.') }}₫
                                    </div>
                                </div>
                            @endforeach
                            <div style="display:flex;justify-content:space-between;align-items:center;width:100%">
                                <span style="font-size:13px;color:var(--ink-muted)">Tổng tiền:</span>
                                <div class="order-total">{{ number_format($order->total_amount, 0, ',', '.') }}₫</div>
                            </div>
                        </div>

                        <div class="order-foot">
                            <div style="font-size:13px;color:var(--ink-muted);display:flex;align-items:center;gap:6px">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="1.8" stroke-linecap="round">
                                    <path d="M21 10c0 7-9 13-9 13S3 17 3 10a9 9 0 0 1 18 0z" />
                                    <circle cx="12" cy="10" r="3" />
                                </svg>
                                {{ $order->shipping_address }}
                            </div>
                            <div style="display:flex;align-items:center;gap:10px;margin-left:auto">
                                <div class="order-total">{{ number_format($order->total_amount, 0, ',', '.') }}₫</div>
                                <a href="{{ route('orders.show', $order) }}" class="btn btn-outline btn-sm">
                                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none"
                                        stroke="currentColor" stroke-width="2" stroke-linecap="round">
                                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" />
                                        <circle cx="12" cy="12" r="3" />
                                    </svg>
                                    Xem chi tiết
                                </a>
                            </div>
                        </div>

                        @if ($order->order_status === \App\Models\Order::STATUS_AWAITING_PAYMENT)
                            <div style="padding:0 20px 16px">
                                <button type="button" class="btn btn-primary btn-sm js-retry-payment"
                                    data-order-id="{{ $order->id }}">
                                    {{ $isExpired ? 'Thanh toán lại' : 'Tiếp tục thanh toán' }}
                                </button>
                            </div>
                        @endif
                    </div>
                @empty
                    <div
                        style="text-align:center;padding:80px 24px;background:var(--bg-alt);border:1px solid var(--border-soft);border-radius:var(--r-xl)">
                        <svg width="56" height="56" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="1" stroke-linecap="round" style="color:var(--border);margin:0 auto 14px">
                            <path d="M9 11l3 3L22 4" />
                            <path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11" />
                        </svg>
                        <p style="font-size:15px;font-weight:600;margin-bottom:6px">Chưa có đơn hàng nào</p>
                        <p style="font-size:13.5px;color:var(--ink-3);margin-bottom:20px">Mua sắm ngay để có đơn hàng đầu
                            tiên!</p>
                        <a href="{{ url('san-pham') }}" class="btn btn-primary">Khám phá sản phẩm</a>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
    @include('pages.order._retry_payment_script')
@endsection
