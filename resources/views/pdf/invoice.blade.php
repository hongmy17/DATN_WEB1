<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title>Hóa đơn {{ $orderCode }}</title>
    <style>
        /*
     * QUAN TRỌNG: dompdf không hỗ trợ flexbox/grid.
     * Phải dùng <table> để dàn layout — đây là giới hạn
     * của thư viện, không phải lỗi code.
     */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            color: #1a1a1a;
            background: #fff;
            line-height: 1.5;
        }

        .page {
            padding: 32px 36px;
        }

        /* ── HEADER ───────────────────────── */
        .header-table {
            width: 100%;
            margin-bottom: 28px;
        }

        .header-table td {
            vertical-align: top;
        }

        .logo {
            font-size: 26px;
            font-weight: bold;
            color: #1a1a1a;
            letter-spacing: -0.5px;
        }

        .logo-dot {
            color: #E30019;
        }

        .company-info {
            font-size: 11px;
            color: #666;
            margin-top: 6px;
            line-height: 1.7;
        }

        .invoice-meta {
            text-align: right;
        }

        .invoice-title {
            font-size: 22px;
            font-weight: bold;
            color: #1a1a1a;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .invoice-code {
            font-size: 14px;
            font-weight: bold;
            color: #E30019;
            margin-top: 4px;
        }

        .invoice-date {
            font-size: 11px;
            color: #666;
            margin-top: 4px;
        }

        /* ── DIVIDER ──────────────────────── */
        .divider {
            height: 2px;
            background: #E30019;
            margin: 20px 0;
        }

        .divider-thin {
            height: 1px;
            background: #eee;
            margin: 14px 0;
        }

        /* ── INFO SECTION ─────────────────── */
        .info-table {
            width: 100%;
            margin-bottom: 24px;
        }

        .info-table td {
            vertical-align: top;
            width: 50%;
            padding-right: 16px;
        }

        .info-table td:last-child {
            padding-right: 0;
        }

        .info-box {
            background: #f8f8f8;
            border: 1px solid #eee;
            border-radius: 6px;
            padding: 14px 16px;
        }

        .info-box-title {
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: #999;
            margin-bottom: 8px;
        }

        .info-name {
            font-size: 13px;
            font-weight: bold;
            color: #1a1a1a;
            margin-bottom: 4px;
        }

        .info-detail {
            font-size: 11px;
            color: #555;
            line-height: 1.6;
        }

        /* ── STATUS BADGE ─────────────────── */
        .status-badge {
            display: inline-block;
            padding: 3px 10px;
            border-radius: 99px;
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .status-0 {
            background: #FEF9C3;
            color: #854D0E;
        }

        .status-1 {
            background: #DBEAFE;
            color: #1E40AF;
        }

        .status-2 {
            background: #FEF3C7;
            color: #92400E;
        }

        .status-3 {
            background: #DCFCE7;
            color: #166534;
        }

        .status-4 {
            background: #FEE2E2;
            color: #991B1B;
        }

        /* ── ITEMS TABLE ──────────────────── */
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .items-table th {
            background: #1a1a1a;
            color: #fff;
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            padding: 10px 12px;
            text-align: left;
        }

        .items-table th.text-center {
            text-align: center;
        }

        .items-table th.text-right {
            text-align: right;
        }

        .items-table td {
            padding: 10px 12px;
            font-size: 11px;
            border-bottom: 1px solid #f0f0f0;
            vertical-align: top;
        }

        .items-table tr:last-child td {
            border-bottom: none;
        }

        .items-table tr:nth-child(even) td {
            background: #fafafa;
        }

        .item-name {
            font-size: 12px;
            font-weight: bold;
            color: #1a1a1a;
            margin-bottom: 2px;
        }

        .item-variant {
            font-size: 10px;
            color: #888;
        }

        .item-sku {
            font-size: 10px;
            color: #aaa;
            font-style: italic;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        /* ── SUMMARY ──────────────────────── */
        .summary-table {
            width: 280px;
            margin-left: auto;
            margin-bottom: 28px;
        }

        .summary-table td {
            padding: 5px 0;
            font-size: 12px;
        }

        .summary-label {
            color: #666;
        }

        .summary-value {
            text-align: right;
            color: #1a1a1a;
        }

        .summary-discount {
            color: #16a34a;
        }

        .summary-total td {
            padding: 10px 0;
            border-top: 2px solid #1a1a1a;
            font-size: 16px;
            font-weight: bold;
        }

        .summary-total .summary-value {
            color: #E30019;
        }

        /* ── NOTE ─────────────────────────── */
        .note-box {
            background: #fffbeb;
            border: 1px solid #fde68a;
            border-radius: 6px;
            padding: 10px 14px;
            font-size: 11px;
            color: #78350f;
            margin-bottom: 24px;
        }

        .note-label {
            font-weight: bold;
            margin-bottom: 3px;
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        /* ── FOOTER ───────────────────────── */
        .invoice-footer {
            border-top: 1px solid #eee;
            padding-top: 16px;
            text-align: center;
            color: #aaa;
            font-size: 10px;
            line-height: 1.7;
        }

        /* ── COUPON ───────────────────────── */
        .coupon-badge {
            background: #DCFCE7;
            color: #166534;
            padding: 2px 8px;
            border-radius: 99px;
            font-size: 11px;
            font-weight: bold;
        }
    </style>
</head>

<body>
    <div class="page">

        {{-- ── HEADER ─────────────────────── --}}
        <table class="header-table">
            <tr>
                <td>
                    <div class="logo">Nexus<span class="logo-dot">.</span></div>
                    <div class="company-info">
                        123 Nguyễn Huệ, Quận 1, TP. Hồ Chí Minh<br>
                        Email: support@nexusstore.vn<br>
                        Hotline: 1800 9999
                    </div>
                </td>
                <td class="invoice-meta">
                    <div class="invoice-title">Hóa đơn bán hàng</div>
                    <div class="invoice-code">{{ $orderCode }}</div>
                    <div class="invoice-date">
                        Ngày đặt: {{ $order->created_at->format('d/m/Y H:i') }}
                    </div>
                    <div style="margin-top:8px">
                        @php
                            $statusLabels = [
                                0 => 'Chờ xác nhận',
                                1 => 'Đã xác nhận',
                                2 => 'Đang giao',
                                3 => 'Hoàn thành',
                                4 => 'Đã hủy',
                            ];
                            $status = (int) $order->order_status;
                        @endphp
                        <span class="status-badge status-{{ $status }}">
                            {{ $statusLabels[$status] ?? 'Không xác định' }}
                        </span>
                    </div>
                </td>
            </tr>
        </table>

        <div class="divider"></div>

        {{-- ── THÔNG TIN ────────────────────── --}}
        <table class="info-table">
            <tr>
                {{-- Người nhận --}}
                <td>
                    <div class="info-box">
                        <div class="info-box-title">Thông tin người nhận</div>
                        <div class="info-name">{{ $order->receiver_name }}</div>
                        <div class="info-detail">
                            SĐT: {{ $order->receiver_phone }}<br>
                            @if ($order->user)
                                Email: {{ $order->user->email }}
                            @endif
                        </div>
                    </div>
                </td>
                {{-- Địa chỉ giao hàng --}}
                <td>
                    <div class="info-box">
                        <div class="info-box-title">Địa chỉ giao hàng</div>
                        <div class="info-detail" style="line-height:1.8">
                            {{ $order->shipping_address }}
                        </div>
                    </div>
                </td>
            </tr>
        </table>

        {{-- ── GHI CHÚ ──────────────────────── --}}
        @if ($order->note)
            <div class="note-box">
                <div class="note-label">Ghi chú của khách</div>
                {{ $order->note }}
            </div>
        @endif

        {{-- ── DANH SÁCH SẢN PHẨM ────────────── --}}
        <table class="items-table">
            <thead>
                <tr>
                    <th style="width:5%">#</th>
                    <th style="width:40%">Sản phẩm</th>
                    <th style="width:20%">Phân loại</th>
                    <th class="text-center" style="width:10%">SL</th>
                    <th class="text-right" style="width:12%">Đơn giá</th>
                    <th class="text-right" style="width:13%">Thành tiền</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($order->items as $i => $item)
                    <tr>
                        <td class="text-center" style="color:#aaa">{{ $i + 1 }}</td>
                        <td>
                            <div class="item-name">{{ $item->product_name }}</div>
                            @if ($item->variant_sku)
                                <div class="item-sku">SKU: {{ $item->variant_sku }}</div>
                            @endif
                        </td>
                        <td class="item-variant">
                            {{ $item->variant_description ?: 'Mặc định' }}
                        </td>
                        <td class="text-center">{{ $item->quantity }}</td>
                        <td class="text-right">
                            {{ number_format($item->unit_price, 0, ',', '.') }}₫
                            @if ($item->compare_price && $item->compare_price > $item->unit_price)
                                <br><span style="font-size:10px;color:#aaa;text-decoration:line-through">
                                    {{ number_format($item->compare_price, 0, ',', '.') }}₫
                                </span>
                            @endif
                        </td>
                        <td class="text-right" style="font-weight:bold">
                            {{ number_format($item->total_price ?? $item->unit_price * $item->quantity, 0, ',', '.') }}₫
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        {{-- ── TỔNG TIỀN ────────────────────── --}}
        <table class="summary-table">
            <tr>
                <td class="summary-label">Tạm tính</td>
                <td class="summary-value">{{ number_format($order->subtotal ?? 0, 0, ',', '.') }}₫</td>
            </tr>
            @if ($order->discount_amount > 0)
                <tr>
                    <td class="summary-label">
                        Giảm giá
                        @if ($order->coupon_code)
                            <span class="coupon-badge">{{ $order->coupon_code }}</span>
                        @endif
                    </td>
                    <td class="summary-value summary-discount">
                        -{{ number_format($order->discount_amount, 0, ',', '.') }}₫
                    </td>
                </tr>
            @endif
            <tr>
                <td class="summary-label">Phí vận chuyển</td>
                <td class="summary-value" style="color:#16a34a">Miễn phí</td>
            </tr>
            <tr class="summary-total">
                <td class="summary-label">Tổng cộng</td>
                <td class="summary-value">{{ number_format($order->total_amount, 0, ',', '.') }}₫</td>
            </tr>
        </table>

        {{-- ── FOOTER ──────────────────────── --}}
        <div class="invoice-footer">
            Cảm ơn quý khách đã tin tưởng và mua sắm tại <strong>Nexus Store</strong>!<br>
            Hóa đơn này được xuất tự động bởi hệ thống. Mọi thắc mắc vui lòng liên hệ support@nexusstore.vn<br>
            © {{ date('Y') }} Nexus Store — Nhóm DATN, FPT Polytechnic
        </div>

    </div>
</body>

</html>
