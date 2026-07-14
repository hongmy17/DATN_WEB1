<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; background:#F7F6F3; margin:0; padding:0; color:#181614; }
        .wrap { max-width:600px; margin:0 auto; padding:32px 16px; }
        .card { background:#fff; border-radius:16px; overflow:hidden; border:1px solid #ECEAE5; }
        .header { background:#181614; padding:32px 28px; text-align:center; }
        .header .logo { font-size:22px; font-weight:700; color:#fff; }
        .header .logo span { color:#C8522A; }
        .body { padding:32px 28px; }
        h1 { font-size:20px; margin:0 0 8px; text-align:center; }
        .sub { font-size:14px; color:#6B6459; text-align:center; margin:0 0 24px; }
        .order-code { background:#FBF0EB; border:1.5px dashed #C8522A; border-radius:10px; padding:12px 16px; text-align:center; margin-bottom:24px; }
        .order-code .code { font-size:18px; font-weight:700; color:#181614; }
        .box { background:#F7F6F3; border-radius:10px; padding:16px 18px; margin-bottom:16px; font-size:13.5px; }
        .footer { text-align:center; font-size:12px; color:#9C9488; padding:20px; }
    </style>
</head>
<body>
    <div class="wrap">
        <div class="card">
            <div class="header">
                <div class="logo">Nexus<span>.</span></div>
            </div>
            <div class="body">
                @if ($refund->status === \App\Models\RefundRequest::STATUS_REFUNDED)
                    <h1>Đã hoàn tiền thành công</h1>
                    <p class="sub">Chúng tôi đã hoàn tiền cho đơn hàng của bạn.</p>
                @else
                    <h1>Yêu cầu hoàn tiền bị từ chối</h1>
                    <p class="sub">Rất tiếc, yêu cầu hoàn tiền của bạn không được chấp nhận.</p>
                @endif

                <div class="order-code">
                    <div class="code">{{ $orderCode }}</div>
                </div>

                <div class="box">
                    <p><strong>Số tiền:</strong> {{ number_format($refund->refund_amount) }}₫</p>
                    @if ($refund->status === \App\Models\RefundRequest::STATUS_REJECTED)
                        <p><strong>Lý do từ chối:</strong> {{ $refund->reject_reason }}</p>
                    @else
                        <p><strong>Hoàn tất lúc:</strong> {{ $refund->refunded_at?->format('H:i d/m/Y') }}</p>
                        @if ($refund->receipt_image)
                            <p><strong>Biên lai:</strong>
                                <a href="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($refund->receipt_image) }}">
                                    Xem ảnh biên lai
                                </a>
                            </p>
                        @endif
                    @endif
                </div>

                <p style="font-size:13px;color:#6B6459">
                    Mọi thắc mắc vui lòng liên hệ bộ phận hỗ trợ của Nexus Store.
                </p>
            </div>
        </div>
        <div class="footer">© {{ date('Y') }} Nexus Store</div>
    </div>
</body>
</html>