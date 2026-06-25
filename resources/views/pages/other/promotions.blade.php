@extends('layouts.app')

@section('title', 'Khuyến mãi - Nexus Store')

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/pages/promotions.css') }}">
@endpush

@section('content')
    <div class="container section">
        <div class="breadcrumb mb-32">
            <a href="{{ url('/') }}">Trang chủ</a>
            <span class="breadcrumb__sep">›</span>
            <span class="breadcrumb__current">Khuyến Mãi</span>
        </div>

        <div class="promo-hero">
            <div class="section-header__eyebrow" style="color:rgba(255,255,255,.5);display:inline-flex">🔥 Ưu đãi độc quyền
            </div>
            <h1 class="display-1" style="color:#fff;">FLASH SALE<br><span style="color:var(--accent)">Giảm đến 40%</span>
            </h1>
            <div id="bigCountdown" style="display:flex;justify-content:center;gap:10px;margin:24px 0"></div>
            <a href="{{ url('san-pham') }}" class="btn btn-accent btn-xl">Mua ngay →</a>
        </div>

        <div class="promo-grid">
            <div class="promo-card pc-1">
                <div class="promo-card-icon">⚡</div>
                <div class="promo-card-title">Giảm đến 40%</div>
                <div class="promo-card-desc">Laptop & Gaming</div>
            </div>
            <div class="promo-card pc-2">
                <div class="promo-card-icon">📱</div>
                <div class="promo-card-title">iPhone 15 Series</div>
                <div class="promo-card-desc">Tặng kèm ốp lưng</div>
            </div>
            <div class="promo-card pc-3">
                <div class="promo-card-icon">💳</div>
                <div class="promo-card-title">Trả Góp 0%</div>
                <div class="promo-card-desc">12 tháng, không thế chấp</div>
            </div>
            <div class="promo-card pc-4">
                <div class="promo-card-icon">🎁</div>
                <div class="promo-card-title">Mua 1 Tặng 1</div>
                <div class="promo-card-desc">Phụ kiện Apple</div>
            </div>
            <div class="promo-card pc-5">
                <div class="promo-card-icon">🚀</div>
                <div class="promo-card-title">Free Ship</div>
                <div class="promo-card-desc">Đơn từ 500.000₫</div>
            </div>
            <div class="promo-card pc-6">
                <div class="promo-card-icon">💎</div>
                <div class="promo-card-title">Tích Điểm VIP</div>
                <div class="promo-card-desc">Đổi điểm lấy quà</div>
            </div>
        </div>

        <div class="grid-4">
            <div class="product-card">
                <div class="product-card__thumb">
                    <div class="product-card__badges"><span class="badge badge-sale">-14%</span></div>
                    <button class="product-card__wish" data-wish-id="1">♥</button>
                    <div class="product-card__img">💻</div>
                </div>
                <div class="product-card__body">
                    <div class="product-card__brand">Apple</div>
                    <div class="product-card__name">MacBook Pro M3 Pro</div>
                    <div class="product-card__price">42.990.000₫</div>
                </div>
            </div>
            <div class="product-card">
                <div class="product-card__thumb">
                    <div class="product-card__badges"><span class="badge badge-sale">-11%</span></div>
                    <button class="product-card__wish" data-wish-id="2">♥</button>
                    <div class="product-card__img">📱</div>
                </div>
                <div class="product-card__body">
                    <div class="product-card__brand">Apple</div>
                    <div class="product-card__name">iPhone 15 Pro Max</div>
                    <div class="product-card__price">32.990.000₫</div>
                </div>
            </div>
            <div class="product-card">
                <div class="product-card__thumb">
                    <div class="product-card__badges"><span class="badge badge-sale">-10%</span></div>
                    <button class="product-card__wish" data-wish-id="5">♥</button>
                    <div class="product-card__img">📟</div>
                </div>
                <div class="product-card__body">
                    <div class="product-card__brand">Apple</div>
                    <div class="product-card__name">iPad Pro M2</div>
                    <div class="product-card__price">28.990.000₫</div>
                </div>
            </div>
            <div class="product-card">
                <div class="product-card__thumb">
                    <div class="product-card__badges"><span class="badge badge-sale">-15%</span></div>
                    <button class="product-card__wish" data-wish-id="6">♥</button>
                    <div class="product-card__img">🎧</div>
                </div>
                <div class="product-card__body">
                    <div class="product-card__brand">Sony</div>
                    <div class="product-card__name">Sony WH-1000XM5</div>
                    <div class="product-card__price">8.490.000₫</div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        const endTime = new Date();
        endTime.setHours(endTime.getHours() + 6, 30);
        initCountdown(endTime, 'bigCountdown');
        initWishBtns();
    </script>
@endpush
