<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <style>
        /*
        FIX QUAN TRỌNG: dompdf không hỗ trợ flexbox/grid như trình duyệt.
        Phải dùng table hoặc float để dàn layout — đây là giới hạn riêng
        của thư viện chuyển HTML→PDF, không phải lỗi code.
    */
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            color: #181614;
        }

        .header-table {
            width: 100%;
            margin-bottom: 24px;
        }

        .header-table td {
            vertical-align: top;
        }

        .logo {
            font-size: 22px;
            font-weight: bold;
            color: #181614;
        }

        .logo span {
            color: #C8522A;
        }

        .invoice-title {
            text-align: right;
        }

        .invoice-title h1 {
            font-size: 20px;
            margin: 0;
            color: #181614;
        }

        .invoice-title .code {
            font-size: 13px;
            color: #6B6459;
            margin-top: 4px;
        }

        .info-table {
            width: 100%;
            margin-bottom: 20px;
        }

        .info-table td {
            vertical-align: top;
            width: 50%;
            padding-right: 16px;
        }

        .info-box {
            background: #F7F6F3;
            border-radius: 6px;
            padding: 12px 14px;
        }

        .info-label {
            font-size: 9px;
            text-transform: uppercase;
            color: #9C9488;
            font-weight: bold;
            margin-bottom: 4px;
        }

        .info-value {
            font-size: 12px;
            line-height: 1.6;
        }

        table.items {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        table.items th {
            background: #181614;
            color: #fff;
            font-size: 10px;
            text-transform: uppercase;
            padding: 8px 10px;
            text-align: left;
        }

        table.items th.text-right,
        table.items td.text-right {
            text-align: right;
        }

        table.items td {
            padding: 8px 10px;
            border-bottom: 1px solid #ECEAE5;
            font-size: 11px;
        }

        table.items tr:nth-child(even) {
            background: #FAFAF9;
        }

        .summary-table {
            width: 280px;
            margin-left: auto;
        }

        .summary-table td {
            padding: 4px 0;
            font-size: 12px;
        }

        .summary-table td.label {
            color: #6B6459;
        }

        .summary-table td.value {
            text-align: right;
        }

        .summary-table tr.total td {
            font-size: 15px;
            font-weight: bold;
            border-top: 1.5px solid #181614;
            padding-top: 8px;
        }

        .footer {
            margin-top: 32px;
            padding-top: 16px;
            border-top: 1px solid #ECEAE5;
            text-align: center;
            font-size: 10px;
            color: #9C9488;
        }
    </style>
</head>

<body>

    <table class="header-table">
        <tr>
            <td>
                <div class="logo">Nexus<span>.</span></div>
                <div style="font-size:10px;color:#6B6459;margin-top:4px">
                    123 Nguyễn Huệ, Quận 1, TP.HCM<br>
                    support@nexusstore.vn — 1800 9999
                </div>
            </td>
            <td class="invoice-title">
                <h1>HÓA ĐƠN BÁN HÀNG</h1>
                <div class="code">{{ $orderCode }}</div>
                <div class="code">Ngày: {{ $order->created_at->format('d/m/Y H:i') }}</div>
            </td>
        </tr>
    </table>

    <table class="info-table">
        <tr>
            <td>
                <div class="info-box">
                    <div class="info-label">Khách hàng</div>
                    <div class="info-value">
                        <strong>{{ $order->receiver_name }}</strong><br>
                        SĐT: {{ $order->receiver_phone }}<br>
                        @if ($order->user)
                            Email: {{ $order->user->email }}
                        @endif
                    </div>
                </div>
            </td>
            <td>
                <div class="info-box">
                    <div class="info-label">Địa chỉ giao hàng</div>
                    <div class="info-value">{{ $order->shipping_address }}</div>
                </div>
            </td>
        </tr>
    </table>

    <table class="items">
        <thead>
            <tr>
                <th>Sản phẩm</th>
                <th>Phân loại</th>
                <th class="text-right">SL</th>
                <th class="text-right">Đơn giá</th>
                <th class="text-right">Thành tiền</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($order->items as $item)
                <tr>
                    <td>{{ $item->product_name }}</td>
                    <td>{{ $item->variant_description }}</td>
                    <td class="text-right">{{ $item->quantity }}</td>
                    <td class="text-right">{{ number_format($item->unit_price, 0, ',', '.') }}₫</td>
                    <td class="text-right">{{ number_format($item->total_price, 0, ',', '.') }}₫</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <table class="summary-table">
        <tr>
            <td class="label">Tạm tính</td>
            <td class="value">{{ number_format($order->subtotal, 0, ',', '.') }}₫</td>
        </tr>
        @if ($order->discount_amount > 0)
            <tr>
                <td class="label">Giảm giá{{ $order->coupon ? ' (' . $order->coupon->coupon_code . ')' : '' }}</td>
                <td class="value">-{{ number_format($order->discount_amount, 0, ',', '.') }}₫</td>
            </tr>
        @endif
        <tr>
            <td class="label">Phí vận chuyển</td>
            <td class="value">Miễn phí</td>
        </tr>
        <tr class="total">
            <td>Tổng cộng</td>
            <td class="value">{{ number_format($order->total_amount, 0, ',', '.') }}₫</td>
        </tr>
    </table>

    <div class="footer">
        Cảm ơn quý khách đã mua sắm tại Nexus Store!<br>
        Đây là hóa đơn điện tử, không cần đóng dấu hay chữ ký.
    </div>

</body>

</html>
