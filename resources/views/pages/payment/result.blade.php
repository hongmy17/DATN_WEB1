@extends('layouts.app')
@section('title', 'Kết quả thanh toán — Nexus Store')

@php
    $orderCode = 'NX-' . str_pad($order->id, 6, '0', STR_PAD_LEFT);
    $meta = [
        'success' => [
            'color' => '#1E8E5A',
            'bg' => '#EAF5EF',
            'title' => 'Thanh toán thành công!',
            'desc' => "Đơn hàng {$orderCode} đã được xác nhận.",
        ],
        'failed' => [
            'color' => '#C0392B',
            'bg' => '#FBEAEA',
            'title' => 'Thanh toán không thành công',
            'desc' => 'Giao dịch đã bị hủy hoặc bị từ chối.',
        ],
        'pending' => [
            'color' => '#B7791F',
            'bg' => '#FEF6E7',
            'title' => 'Đang chờ xác nhận thanh toán',
            'desc' => 'Nếu đơn quá hạn, bạn có thể thanh toán lại ở trang chi tiết đơn hàng.',
        ],
    ][$status];
@endphp

@section('content')
    <div class="container" style="padding:60px 16px;display:flex;justify-content:center">
        <div style="max-width:480px;width:100%;text-align:center">

            <div
                style="width:72px;height:72px;border-radius:50%;background:{{ $meta['bg'] }};display:flex;align-items:center;justify-content:center;margin:0 auto 20px">
                @if ($status === 'success')
                    <svg width="34" height="34" viewBox="0 0 24 24" fill="none" stroke="{{ $meta['color'] }}"
                        stroke-width="2.5" stroke-linecap="round">
                        <circle cx="12" cy="12" r="10" />
                        <polyline points="8 12 11 15 16 9" />
                    </svg>
                @elseif ($status === 'failed')
                    <svg width="34" height="34" viewBox="0 0 24 24" fill="none" stroke="{{ $meta['color'] }}"
                        stroke-width="2.5" stroke-linecap="round">
                        <circle cx="12" cy="12" r="10" />
                        <line x1="15" y1="9" x2="9" y2="15" />
                        <line x1="9" y1="9" x2="15" y2="15" />
                    </svg>
                @else
                    <svg width="34" height="34" viewBox="0 0 24 24" fill="none" stroke="{{ $meta['color'] }}"
                        stroke-width="2.5" stroke-linecap="round">
                        <circle cx="12" cy="12" r="10" />
                        <polyline points="12 6 12 12 16 14" />
                    </svg>
                @endif
            </div>

            <h1 style="font-size:22px;margin:0 0 8px">{{ $meta['title'] }}</h1>
            <p style="color:var(--ink-muted);font-size:14px;margin:0 0 24px">{{ $meta['desc'] }}</p>

            <div class="od-card" style="text-align:left;margin-bottom:24px">
                <div
                    style="display:flex;justify-content:space-between;padding:8px 0;border-bottom:1px solid var(--border-soft)">
                    <span style="color:var(--ink-muted);font-size:13px">Mã đơn hàng</span>
                    <strong>{{ $orderCode }}</strong>
                </div>
                <div style="display:flex;justify-content:space-between;padding:8px 0">
                    <span style="color:var(--ink-muted);font-size:13px">Số tiền</span>
                    <strong>{{ number_format($order->total_amount) }}₫</strong>
                </div>
            </div>

            <div style="display:flex;flex-direction:column;gap:10px">
                <a href="{{ route('orders.show', $order) }}" class="btn btn-outline">Xem chi tiết đơn hàng</a>
                <a href="{{ route('orders.index') }}" class="btn btn-outline">Xem lịch sử đơn hàng</a>
            </div>
        </div>
    </div>
@endsection
