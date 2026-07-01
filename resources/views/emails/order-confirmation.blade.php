<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: #F7F6F3;
            margin: 0;
            padding: 0;
            color: #181614;
        }

        .wrap {
            max-width: 600px;
            margin: 0 auto;
            padding: 32px 16px;
        }

        .card {
            background: #ffffff;
            border-radius: 16px;
            overflow: hidden;
            border: 1px solid #ECEAE5;
        }

        .header {
            background: #181614;
            padding: 32px 28px;
            text-align: center;
        }

        .header .logo {
            font-size: 22px;
            font-weight: 700;
            color: #ffffff;
            letter-spacing: -.3px;
        }

        .header .logo span {
            color: #C8522A;
        }

        .body {
            padding: 32px 28px;
        }

        .check-circle {
            width: 56px;
            height: 56px;
            background: #EAF5EF;
            border-radius: 50%;
            margin: 0 auto 16px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        h1 {
            font-size: 20px;
            margin: 0 0 8px;
            text-align: center;
        }

        .sub {
            font-size: 14px;
            color: #6B6459;
            text-align: center;
            margin: 0 0 28px;
        }

        .order-code {
            background: #FBF0EB;
            border: 1.5px dashed #C8522A;
            border-radius: 10px;
            padding: 12px 16px;
            text-align: center;
            margin-bottom: 24px;
        }

        .order-code .label {
            font-size: 11px;
            color: #C8522A;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .05em;
        }

        .order-code .code {
            font-size: 18px;
            font-weight: 700;
            color: #181614;
            margin-top: 2px;
        }

        table.items {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        table.items th {
            text-align: left;
            font-size: 11px;
            color: #9C9488;
            text-transform: uppercase;
            letter-spacing: .04em;
            padding: 8px 0;
            border-bottom: 1.5px solid #ECEAE5;
        }

        table.items td {
            padding: 12px 0;
            border-bottom: 1px solid #ECEAE5;
            font-size: 13.5px;
            vertical-align: top;
        }

        .item-name {
            font-weight: 500;
        }

        .item-variant {
            font-size: 12px;
            color: #9C9488;
            margin-top: 2px;
        }

        .text-right {
            text-align: right;
        }

        .summary {
            background: #F7F6F3;
            border-radius: 10px;
            padding: 16px 18px;
            margin-bottom: 24px;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            font-size: 13.5px;
            padding: 4px 0;
        }

        .summary-row.total {
            font-size: 16px;
            font-weight: 700;
            border-top: 1px solid #ECEAE5;
            margin-top: 8px;
            padding-top: 10px;
        }

        .addr-box {
            background: #F7F6F3;
            border-radius: 10px;
            padding: 16px 18px;
            margin-bottom: 24px;
            font-size: 13.5px;
            line-height: 1.7;
        }

        .addr-box .label {
            font-size: 11px;
            font-weight: 700;
            color: #9C9488;
            text-transform: uppercase;
            margin-bottom: 6px;
        }

        .footer {
            text-align: center;
            padding: 24px 28px;
            font-size: 12px;
            color: #9C9488;
        }

        .btn {
            display: inline-block;
            background: #C8522A;
            color: #fff;
            text-decoration: none;
            padding: 12px 28px;
            border-radius: 99px;
            font-size: 14px;
            font-weight: 600;
        }
    </style>
</head>

<body>
    <div class="wrap">
        <div class="card">
            <div class="header">
                <div class="logo">Nexus<span>.</span></div>
            </div>

            <div class="body">
                <div class="check-circle">
                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#2D7A52"
                        stroke-width="2.5" stroke-linecap="round">
                        <polyline points="20 6 9 17 4 12" />
                    </svg>
                </div>

                <h1>Cảm ơn bạn đã đặt hàng!</h1>
                <p class="sub">Xin chào {{ $order->receiver_name }}, đơn hàng của bạn đã được tiếp nhận.</p>

                <div class="order-code">
                    <div class="label">Mã đơn hàng</div>
                    <div class="code">{{ $orderCode }}</div>
                </div>

                <table class="items">
                    <thead>
                        <tr>
                            <th>Sản phẩm</th>
                            <th class="text-right">SL</th>
                            <th class="text-right">Thành tiền</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($order->items as $item)
                            <tr>
                                <td>
                                    <div class="item-name">{{ $item->product_name }}</div>
                                    <div class="item-variant">{{ $item->variant_description }}</div>
                                </td>
                                <td class="text-right">{{ $item->quantity }}</td>
                                <td class="text-right">{{ number_format($item->total_price, 0, ',', '.') }}₫</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <div class="summary">
                    <div class="summary-row">
                        <span>Tạm tính</span>
                        <span>{{ number_format($order->subtotal, 0, ',', '.') }}₫</span>
                    </div>
                    @if ($order->discount_amount > 0)
                        <div class="summary-row">
                            <span>Giảm giá{{ $order->coupon ? ' (' . $order->coupon->coupon_code . ')' : '' }}</span>
                            <span>-{{ number_format($order->discount_amount, 0, ',', '.') }}₫</span>
                        </div>
                    @endif
                    <div class="summary-row">
                        <span>Vận chuyển</span>
                        <span style="color:#2D7A52">Miễn phí</span>
                    </div>
                    <div class="summary-row total">
                        <span>Tổng cộng</span>
                        <span>{{ number_format($order->total_amount, 0, ',', '.') }}₫</span>
                    </div>
                </div>

                <div class="addr-box">
                    <div class="label">Giao đến</div>
                    <strong>{{ $order->receiver_name }}</strong> — {{ $order->receiver_phone }}<br>
                    {{ $order->shipping_address }}
                </div>

                <div style="text-align:center">
                    <a href="{{ url('don-hang') }}" class="btn">Xem đơn hàng của tôi</a>
                </div>
            </div>

            <div class="footer">
                Hóa đơn chi tiết đính kèm dưới dạng PDF.<br>
                © {{ date('Y') }} Nexus Store. Mọi thắc mắc liên hệ support@nexusstore.vn
            </div>
        </div>
    </div>
</body>

</html>
