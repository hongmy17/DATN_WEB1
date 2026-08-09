@extends('layouts.app')
@section('title', 'Chi tiết đơn hàng — Nexus Store')

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/pages/order.css') }}">
    <style>

    </style>
@endpush

@section('content')
    <div class="container" style="padding-bottom:80px">
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
                <a href="{{ route('orders.index') }}" class="account-nav-link active">
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

                {{-- Back + Flash messages --}}
                <a href="{{ route('orders.index') }}"
                    style="display:inline-flex;align-items:center;gap:6px;font-size:13px;color:var(--ink-muted);margin-bottom:20px;text-decoration:none">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2" stroke-linecap="round">
                        <line x1="19" y1="12" x2="5" y2="12" />
                        <polyline points="12 19 5 12 12 5" />
                    </svg>
                    Quay lại đơn hàng
                </a>

                @if (session('success'))
                    <div
                        style="padding:12px 16px;background:var(--green-light);color:var(--green);border-radius:var(--r-md);font-size:14px;font-weight:600;margin-bottom:16px;display:flex;align-items:center;gap:8px">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2.5" stroke-linecap="round">
                            <polyline points="20 6 9 17 4 12" />
                        </svg>
                        {{ session('success') }}
                    </div>
                @endif

                @if (session('error'))
                    <div
                        style="padding:12px 16px;background:var(--red-light);color:var(--red);border-radius:var(--r-md);font-size:14px;font-weight:600;margin-bottom:16px;display:flex;align-items:center;gap:8px">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2.5" stroke-linecap="round">
                            <circle cx="12" cy="12" r="10" />
                            <line x1="12" y1="8" x2="12" y2="12" />
                            <line x1="12" y1="16" x2="12.01" y2="16" />
                        </svg>
                        {{ session('error') }}
                    </div>
                @endif

                {{-- ── HEADER ───────────────────────────────── --}}
                @php
                    $currentStatus = (int) $order->order_status;
                    $isAwaiting = $currentStatus === 5;
                    $isCancelReq = $currentStatus === 6;
                    $isCancelled = $currentStatus === 4;

                    $statusLabel = [
                        0 => 'Chờ xác nhận',
                        1 => 'Đã xác nhận',
                        2 => 'Đang giao',
                        3 => 'Hoàn thành',
                        4 => 'Đã hủy',
                        5 => 'Chờ thanh toán',
                        6 => 'Chờ xác nhận hủy',
                        7 => 'Đã hoàn tiền',
                    ];
                    $statusClass = [
                        0 => 'status-pending',
                        1 => 'status-confirmed',
                        2 => 'status-shipping',
                        3 => 'status-delivered',
                        4 => 'status-cancelled',
                        5 => 'status-pending',
                        6 => 'status-cancelled',
                        7 => 'status-delivered',
                    ];
                @endphp

                <div class="od-header">
                    <div>
                        <div class="od-code">
                            NX-{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}
                        </div>
                        <div class="od-date">
                            Đặt lúc {{ $order->created_at->format('H:i — d/m/Y') }}
                        </div>
                    </div>
                    <span class="status {{ $statusClass[$currentStatus] ?? '' }}">
                        {{ $statusLabel[$currentStatus] ?? 'Không xác định' }}
                    </span>
                </div>

                {{-- ── TIMELINE ──────────────────────────────── --}}
                <div class="od-card">
                    <div class="od-card-title">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2" stroke-linecap="round">
                            <circle cx="12" cy="12" r="10" />
                            <polyline points="12 6 12 12 16 14" />
                        </svg>
                        Trạng thái đơn hàng
                    </div>

                    {{-- Trạng thái đặc biệt: hủy / chờ thanh toán / chờ xác nhận hủy --}}
                    @if ($isCancelled || $isAwaiting || $isCancelReq)
                        <div style="margin-bottom:20px">
                            @if ($isCancelled)
                                <div class="status-special cancelled">
                                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none"
                                        stroke="currentColor" stroke-width="2.5" stroke-linecap="round">
                                        <circle cx="12" cy="12" r="10" />
                                        <line x1="15" y1="9" x2="9" y2="15" />
                                        <line x1="9" y1="9" x2="15" y2="15" />
                                    </svg>
                                    Đơn hàng đã bị hủy
                                    @if ($order->cancel_reason)
                                        <span style="font-weight:400;margin-left:4px">— {{ $order->cancel_reason }}</span>
                                    @endif
                                </div>
                            @elseif($isAwaiting)
                                <div class="status-special awaiting">
                                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none"
                                        stroke="currentColor" stroke-width="2.5" stroke-linecap="round">
                                        <circle cx="12" cy="12" r="10" />
                                        <polyline points="12 6 12 12 16 14" />
                                    </svg>
                                    Đang chờ thanh toán qua VNPay
                                    — vui lòng hoàn tất thanh toán để xác nhận đơn
                                </div>
                            @elseif($isCancelReq)
                                <div class="status-special cancel-req">
                                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none"
                                        stroke="currentColor" stroke-width="2.5" stroke-linecap="round">
                                        <circle cx="12" cy="12" r="10" />
                                        <line x1="12" y1="8" x2="12" y2="12" />
                                        <line x1="12" y1="16" x2="12.01" y2="16" />
                                    </svg>
                                    Yêu cầu hủy đang được xem xét — Admin sẽ phản hồi trong 24h
                                </div>
                            @endif
                        </div>
                    @endif

                    {{-- Timeline steps --}}
                    @php
                        $steps = [
                            ['label' => 'Chờ xác nhận', 'status' => 0],
                            ['label' => 'Đã xác nhận', 'status' => 1],
                            ['label' => 'Đang giao', 'status' => 2],
                            ['label' => 'Hoàn thành', 'status' => 3],
                        ];
                        // Tính % fill line
                        $normalStatus = min($currentStatus, 3);
                        $fillPercent = $isCancelled || $isAwaiting || $isCancelReq ? 0 : ($normalStatus / 3) * 100;
                    @endphp

                    <div class="timeline-wrap">
                        <div class="timeline-line"></div>
                        <div class="timeline-line-fill" style="width:{{ $fillPercent }}%"></div>

                        @foreach ($steps as $step)
                            @php
                                $done =
                                    !$isCancelled && !$isAwaiting && !$isCancelReq && $currentStatus >= $step['status'];
                                $current =
                                    $currentStatus === $step['status'] &&
                                    !$isCancelled &&
                                    !$isAwaiting &&
                                    !$isCancelReq;
                            @endphp
                            <div class="timeline-step">
                                <div class="timeline-dot {{ $done ? 'done' : ($current ? 'current' : '') }}">
                                    @if ($done)
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none"
                                            stroke="#fff" stroke-width="3" stroke-linecap="round">
                                            <polyline points="20 6 9 17 4 12" />
                                        </svg>
                                    @elseif($current)
                                        <div style="width:10px;height:10px;border-radius:50%;background:var(--accent)">
                                        </div>
                                    @endif
                                </div>
                                <span class="timeline-label {{ $done ? 'done' : ($current ? 'current' : '') }}">
                                    {{ $step['label'] }}
                                </span>
                            </div>
                        @endforeach
                    </div>

                    {{-- Nút tiếp tục thanh toán VNPay --}}
                    @if ($isAwaiting)
                        <div style="margin-top:20px;padding-top:16px;border-top:1px solid var(--border-soft)">
                            <button type="button" class="btn btn-primary" id="retryPayBtn"
                                data-order-id="{{ $order->id }}">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none"
                                    stroke="currentColor" stroke-width="2" stroke-linecap="round">
                                    <rect x="1" y="4" width="22" height="16" rx="2" />
                                    <line x1="1" y1="10" x2="23" y2="10" />
                                </svg>
                                Tiếp tục thanh toán VNPay
                            </button>
                            <span style="font-size:12px;color:var(--ink-muted);margin-left:10px">
                                Phiên thanh toán hết hạn sau 15 phút
                            </span>
                        </div>
                    @endif
                </div>

                {{-- ── THÔNG TIN GIAO HÀNG ──────────────────── --}}
                <div class="od-card">
                    <div class="od-card-title">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2" stroke-linecap="round">
                            <path d="M21 10c0 7-9 13-9 13S3 17 3 10a9 9 0 0 1 18 0z" />
                            <circle cx="12" cy="10" r="3" />
                        </svg>
                        Thông tin giao hàng
                    </div>
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px">
                        <div>
                            <div style="font-size:12px;color:var(--ink-muted);margin-bottom:4px">Người nhận</div>
                            <div style="font-size:14px;font-weight:600">{{ $order->receiver_name }}</div>
                            <div style="font-size:13px;color:var(--ink-2);margin-top:2px">{{ $order->receiver_phone }}
                            </div>
                        </div>
                        <div>
                            <div style="font-size:12px;color:var(--ink-muted);margin-bottom:4px">Địa chỉ giao hàng</div>
                            <div style="font-size:13px;color:var(--ink-2);line-height:1.6">{{ $order->shipping_address }}
                            </div>
                        </div>
                    </div>
                    @if ($order->note)
                        <div style="margin-top:14px;padding-top:14px;border-top:1px solid var(--border-soft)">
                            <div style="font-size:12px;color:var(--ink-muted);margin-bottom:4px">Ghi chú</div>
                            <div style="font-size:13px;color:var(--ink-2)">{{ $order->note }}</div>
                        </div>
                    @endif
                </div>

                {{-- ── SẢN PHẨM ─────────────────────────────── --}}
                <div class="od-card">
                    <div class="od-card-title">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2" stroke-linecap="round">
                            <path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z" />
                            <line x1="3" y1="6" x2="21" y2="6" />
                            <path d="M16 10a4 4 0 0 1-8 0" />
                        </svg>
                        Sản phẩm đã đặt ({{ $order->items->count() }} sản phẩm)
                    </div>

                    @foreach ($order->items as $item)
                        <div class="od-item">
                            <div class="od-item-img">
                                @if ($item->product_thumbnail)
                                    <img src="{{ asset('storage/' . $item->product_thumbnail) }}"
                                        alt="{{ $item->product_name }}" onerror="this.style.display='none'">
                                @else
                                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none"
                                        stroke="currentColor" stroke-width=".8" stroke-linecap="round">
                                        <rect x="2" y="3" width="20" height="14" rx="2" />
                                        <line x1="8" y1="21" x2="16" y2="21" />
                                        <line x1="12" y1="17" x2="12" y2="21" />
                                    </svg>
                                @endif
                            </div>

                            <div style="flex:1;min-width:0">
                                <div class="od-item-name">{{ $item->product_name }}</div>
                                <div class="od-item-variant">
                                    {{ $item->variant_description }}
                                    @if ($item->variant_sku)
                                        <span style="color:var(--border)">·</span>
                                        <span style="font-family:monospace;font-size:11px">{{ $item->variant_sku }}</span>
                                    @endif
                                </div>
                                <div style="font-size:12px;color:var(--ink-muted);margin-top:4px">
                                    {{ number_format($item->unit_price, 0, ',', '.') }}₫ × {{ $item->quantity }}
                                </div>
                            </div>

                            <div class="od-item-price">
                                {{ number_format($item->total_price ?? $item->unit_price * $item->quantity, 0, ',', '.') }}₫
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- ── THANH TOÁN ───────────────────────────── --}}
                <div class="od-card">
                    <div class="od-card-title">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2" stroke-linecap="round">
                            <rect x="1" y="4" width="22" height="16" rx="2" />
                            <line x1="1" y1="10" x2="23" y2="10" />
                        </svg>
                        Thông tin thanh toán
                    </div>

                    <div class="pay-row">
                        <span style="color:var(--ink-muted)">Phương thức</span>
                        <span style="font-weight:600">
                            {{ match ($order->payment_method ?? 'cod') {
                                'vnpay' => 'Ví điện tử VNPay',
                                'momo' => 'Ví MoMo',
                                'zalopay' => 'ZaloPay',
                                'bank_transfer' => 'Chuyển khoản ngân hàng',
                                default => 'Thanh toán khi nhận hàng (COD)',
                            } }}
                        </span>
                    </div>

                    <div class="pay-row">
                        <span style="color:var(--ink-muted)">Tạm tính</span>
                        <span>{{ number_format($order->subtotal ?? 0, 0, ',', '.') }}₫</span>
                    </div>

                    @if ($order->discount_amount > 0)
                        <div class="pay-row">
                            <span style="color:var(--ink-muted)">
                                Giảm giá
                                @if ($order->coupon_code)
                                    <span
                                        style="background:var(--green-light);color:var(--green);padding:1px 7px;border-radius:99px;font-size:11px;font-weight:700;margin-left:4px">
                                        {{ $order->coupon_code }}
                                    </span>
                                @endif
                            </span>
                            <span style="color:var(--green);font-weight:600">
                                -{{ number_format($order->discount_amount, 0, ',', '.') }}₫
                            </span>
                        </div>
                    @endif

                    <div class="pay-row">
                        <span style="color:var(--ink-muted)">Phí vận chuyển</span>
                        <span style="color:var(--green);font-weight:500">Miễn phí</span>
                    </div>

                    <div class="pay-row pay-row-total">
                        <span>Tổng cộng</span>
                        <span style="color:var(--accent)">
                            {{ number_format($order->total_amount, 0, ',', '.') }}₫
                        </span>
                    </div>
                </div>

                {{-- ── NÚT HÀNH ĐỘNG ───────────────────────── --}}
                @if ((int) $order->order_status === \App\Models\Order::STATUS_COMPLETED && !$order->refundRequest)
                    <a href="{{ route('refunds.create', $order) }}" class="btn btn-outline">
                        Yêu cầu hoàn tiền
                    </a>
                @elseif ($order->refundRequest)
                    <div
                        style="margin-top:12px;padding:12px 16px;background:var(--bg-alt);border-radius:var(--r-md);font-size:13px">
                        <strong>Trạng thái hoàn tiền:</strong> {{ $order->refundRequest->statusLabel() }}
                        @if ($order->refundRequest->status === \App\Models\RefundRequest::STATUS_REJECTED)
                            <div style="color:var(--red);margin-top:4px">
                                Lý do từ chối: {{ $order->refundRequest->reject_reason }}
                            </div>
                        @endif
                    </div>
                @endif
                @if ($currentStatus === \App\Models\Order::STATUS_PENDING)
                    <div style="display:flex;justify-content:flex-end;margin-top:4px">
                        <button type="button" onclick="openCancelModal()" class="btn btn-ghost"
                            style="color:var(--red);border-color:var(--red)">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2" stroke-linecap="round">
                                <circle cx="12" cy="12" r="10" />
                                <line x1="15" y1="9" x2="9" y2="15" />
                                <line x1="9" y1="9" x2="15" y2="15" />
                            </svg>
                            Yêu cầu hủy đơn
                        </button>
                    </div>

                    {{-- MODAL HỦY ĐƠN --}}
                    <div class="cancel-overlay" id="cancelModal">
                        <div class="cancel-modal">
                            <div class="cancel-modal-head">
                                <span style="font-size:16px;font-weight:700">Chọn lý do hủy đơn</span>
                                <button onclick="closeCancelModal()"
                                    style="background:none;border:none;cursor:pointer;padding:4px;color:var(--ink-muted)">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none"
                                        stroke="currentColor" stroke-width="2.5" stroke-linecap="round">
                                        <line x1="18" y1="6" x2="6" y2="18" />
                                        <line x1="6" y1="6" x2="18" y2="18" />
                                    </svg>
                                </button>
                            </div>

                            <div class="cancel-modal-body">
                                <div
                                    style="background:var(--amber-light);border-radius:var(--r-md);padding:12px 14px;margin-bottom:16px;display:flex;gap:10px;align-items:flex-start">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none"
                                        stroke="var(--amber)" stroke-width="2" stroke-linecap="round"
                                        style="flex-shrink:0;margin-top:1px">
                                        <circle cx="12" cy="12" r="10" />
                                        <line x1="12" y1="8" x2="12" y2="12" />
                                        <line x1="12" y1="16" x2="12.01" y2="16" />
                                    </svg>
                                    <p style="font-size:13px;color:var(--amber);line-height:1.6;margin:0">
                                        Đơn hàng sẽ được hủy ngay sau khi bạn xác nhận. Số lượng tồn kho sẽ được hoàn lại.
                                    </p>
                                </div>

                                <form action="{{ route('orders.cancel', $order) }}" method="POST" id="cancelForm">
                                    @csrf
                                    @php $reasons = ['Tôi muốn thay đổi địa chỉ / số điện thoại nhận hàng', 'Tôi muốn áp dụng mã giảm giá khác', 'Tôi muốn thay đổi sản phẩm (màu sắc, kích thước...)', 'Thủ tục thanh toán rắc rối', 'Tôi tìm được nơi mua rẻ hơn hoặc uy tín hơn', 'Tôi không còn nhu cầu mua nữa', 'Lý do khác']; @endphp

                                    @foreach ($reasons as $reason)
                                        <label class="cancel-reason-item">
                                            <input type="radio" name="cancel_reason" value="{{ $reason }}"
                                                onchange="document.getElementById('submitCancelBtn').disabled=false">
                                            <span class="cancel-reason-text">{{ $reason }}</span>
                                        </label>
                                    @endforeach

                                    <button type="submit" id="submitCancelBtn" disabled class="btn btn-danger btn-full"
                                        style="margin-top:20px;opacity:.5" onclick="this.style.opacity='1'"
                                        {{-- Kích hoạt style khi enabled --}}>
                                        Xác nhận hủy đơn
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endif

            </div>{{-- end content --}}
        </div>{{-- end account-layout --}}
    </div>{{-- end container --}}

    <script>
        function openCancelModal() {
            document.getElementById('cancelModal').style.display = 'flex';
            document.body.style.overflow = 'hidden';
        }

        function closeCancelModal() {
            document.getElementById('cancelModal').style.display = 'none';
            document.body.style.overflow = '';
        }
        // Đóng modal khi click ra ngoài
        document.getElementById('cancelModal')?.addEventListener('click', function(e) {
            if (e.target === this) closeCancelModal();
        });

        // Kích hoạt nút xác nhận khi chọn lý do
        document.querySelectorAll('input[name="cancel_reason"]').forEach(function(radio) {
            radio.addEventListener('change', function() {
                const btn = document.getElementById('submitCancelBtn');
                if (btn) {
                    btn.disabled = false;
                    btn.style.opacity = '1';
                }
            });
        });

        // Nút tiếp tục thanh toán VNPay
        const retryBtn = document.getElementById('retryPayBtn');
        if (retryBtn) {
            retryBtn.addEventListener('click', function() {
                this.disabled = true;
                this.textContent = 'Đang tạo phiên thanh toán...';
                fetch('{{ route('vnpay.create') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            order_id: this.dataset.orderId
                        })
                    })
                    .then(r => r.json())
                    .then(data => {
                        if (data.success && data.payment_url) {
                            window.location.href = data.payment_url;
                        } else {
                            Toast.show(data.message || 'Không thể tạo phiên thanh toán', 'error');
                            this.disabled = false;
                            this.textContent = 'Tiếp tục thanh toán VNPay';
                        }
                    })
                    .catch(() => {
                        Toast.show('Lỗi kết nối, thử lại sau', 'error');
                        this.disabled = false;
                        this.textContent = 'Tiếp tục thanh toán VNPay';
                    });
            });
        }

        window.openCancelModal = openCancelModal;
        window.closeCancelModal = closeCancelModal;
    </script>
@endsection
