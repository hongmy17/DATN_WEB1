@extends('layouts.app')
@section('title', 'Nexus Store — Công nghệ đỉnh cao')
@push('styles')
<link rel="stylesheet" href="{{ asset('assets/css/pages/home.css') }}">
@endpush

@section('content')

    {{-- ── HERO ────────────────────────────── --}}
    <section class="hero">
        <div class="hero__grid"></div>
        <div class="hero__glow" style="width:800px;height:800px;top:-200px;right:-200px;"></div>
        <div class="hero__glow" style="width:400px;height:400px;bottom:-100px;left:100px;opacity:.5;"></div>

        <div class="container">
            <div class="hero__content">
                <div class="hero__eyebrow">
                    <span class="hero__eyebrow-dot"></span>
                    Bộ sưu tập mới — 2026
                </div>

                <h1 class="hero__title">
                    Công nghệ<br><span>đỉnh cao</span>,<br>giá tốt nhất.
                </h1>

                <p class="hero__sub">Hàng nghìn sản phẩm công nghệ chính hãng từ Apple, Samsung, Sony và hơn 50 thương hiệu
                    hàng đầu thế giới.</p>

                <div class="hero__cta">
                    <a href="{{ url('san-pham') }}" class="btn btn-accent btn-xl">
                        Khám phá ngay
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2.5" stroke-linecap="round">
                            <line x1="5" y1="12" x2="19" y2="12" />
                            <polyline points="12 5 19 12 12 19" />
                        </svg>
                    </a>
                    <a href="{{ url('khuyen-mai') }}" class="btn btn-outline btn-xl"
                        style="border-color:rgba(255,255,255,.2);color:rgba(255,255,255,.7)">
                        Xem khuyến mãi
                    </a>
                </div>

                <div class="hero__stats">
                    @php $stats = [['50K+','Khách hàng'],['10K+','Sản phẩm'],['500+','Thương hiệu'],['4.9','Điểm đánh giá']]; @endphp
                    @foreach ($stats as $s)
                        <div class="hero__stat">
                            <div class="hero__stat-num">{{ $s[0] }}</div>
                            <div class="hero__stat-label">{{ $s[1] }}</div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    {{-- ── BRANDS MARQUEE ─────────────────── --}}
    <div class="brands-bar">
        <div class="brands-track">
            @php $brs = ['Apple','Samsung','Sony','Dell','Asus','LG','Bose','Logitech','Microsoft','Google','OnePlus','Xiaomi','Apple','Samsung','Sony','Dell','Asus','LG','Bose','Logitech','Microsoft','Google','OnePlus','Xiaomi']; @endphp
            @foreach ($brs as $br)
                <span class="brand-name">{{ $br }}</span>
                <svg width="5" height="5" viewBox="0 0 5 5" style="color:var(--border-soft);flex-shrink:0">
                    <circle cx="2.5" cy="2.5" r="2.5" fill="currentColor" />
                </svg>
            @endforeach
        </div>
    </div>

    {{-- ── CATEGORIES ──────────────────────── --}}
    <section class="section">
        <div class="container">
            <div class="section-header section-header-center reveal" style="text-align:center">
                <div class="section-eyebrow">Danh mục</div>
                <h2 class="section-title">Khám phá theo danh mục</h2>
            </div>

            <div class="cat-grid">
                @php $cats = [['slug' => 'laptop', 'label' => 'Laptop', 'count' => '124 sản phẩm', 'path' => 'M2 3h20v14H2zM8 21h8M12 17v4'], ['slug' => 'phone', 'label' => 'Điện thoại', 'count' => '89 sản phẩm', 'path' => 'M12 18h.01M8 21h8a1 1 0 0 0 1-1V4a1 1 0 0 0-1-1H8a1 1 0 0 0-1 1v16a1 1 0 0 0 1 1z'], ['slug' => 'tablet', 'label' => 'Máy tính bảng', 'count' => '56 sản phẩm', 'path' => 'M18 3H6a1 1 0 0 0-1 1v16a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V4a1 1 0 0 0-1-1zM12 17h.01'], ['slug' => 'audio', 'label' => 'Tai nghe', 'count' => '78 sản phẩm', 'path' => 'M3 18v-6a9 9 0 0 1 18 0v6M21 19a2 2 0 0 1-2 2h-1a2 2 0 0 1-2-2v-3a2 2 0 0 1 2-2h3zM3 19a2 2 0 0 0 2 2h1a2 2 0 0 0 2-2v-3a2 2 0 0 0-2-2H3z'], ['slug' => 'watch', 'label' => 'Smartwatch', 'count' => '43 sản phẩm', 'path' => 'M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 14l-4-4h3V8h2v4h3z'], ['slug' => 'accessory', 'label' => 'Phụ kiện', 'count' => '200+ sản phẩm', 'path' => 'M12 22V8M5 12H2a10 10 0 0 0 20 0h-3']]; @endphp
                @foreach ($cats as $i => $cat)
                    <a href="{{ url('san-pham?cat=' . $cat['slug']) }}" class="cat-tile reveal"
                        style="--delay:{{ $i * 50 }}ms">
                        <div class="cat-tile__icon">
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="1.7" stroke-linecap="round">
                                <path d="{{ $cat['path'] }}" />
                            </svg>
                        </div>
                        <div class="cat-tile__name">{{ $cat['label'] }}</div>
                        <div class="cat-tile__count">{{ $cat['count'] }}</div>
                    </a>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ── PROMO BANNERS ───────────────────── --}}
    <section class="section" style="padding-top:0">
        <div class="container">
            <div class="promo-grid">
                <a href="{{ url('san-pham?cat=laptop') }}" class="promo-card promo-card--dark"
                    style="text-decoration:none">
                    <div class="promo-card__orb" style="width:300px;height:300px;top:-80px;right:-80px;"></div>
                    <div class="promo-card__orb" style="width:150px;height:150px;top:20px;right:60px;opacity:.5;"></div>
                    <div class="promo-card__product">
                        <svg width="220" height="220" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width=".5" stroke-linecap="round">
                            <rect x="2" y="3" width="20" height="14" rx="2" />
                            <line x1="8" y1="21" x2="16" y2="21" />
                            <line x1="12" y1="17" x2="12" y2="21" />
                        </svg>
                    </div>
                    <div class="promo-card__eyebrow">MacBook Pro</div>
                    <h3 class="promo-card__title">Hiệu năng M3 Pro<br>đột phá giới hạn</h3>
                    <span class="btn btn-accent btn-sm" style="width:fit-content">Mua ngay</span>
                </a>

                <a href="{{ url('khuyen-mai') }}" class="promo-card promo-card--accent" style="text-decoration:none">
                    <div class="promo-card__orb"
                        style="width:280px;height:280px;top:-60px;right:-60px;background:rgba(255,255,255,.08);"></div>
                    <div class="promo-card__product">
                        <svg width="180" height="180" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width=".6" stroke-linecap="round">
                            <path d="M12 18h.01M8 21h8a1 1 0 0 0 1-1V4a1 1 0 0 0-1-1H8a1 1 0 0 0-1 1v16a1 1 0 0 0 1 1z" />
                        </svg>
                    </div>
                    <div class="promo-card__eyebrow">Flash Sale hôm nay</div>
                    <h3 class="promo-card__title">Giảm đến 40%<br>điện thoại flagship</h3>
                    <span class="btn btn-sm"
                        style="background:rgba(255,255,255,.2);color:#fff;width:fit-content;border:none">Xem ngay</span>
                </a>
            </div>
        </div>
    </section>

    {{-- ── FEATURED PRODUCTS ───────────────── --}}
    <section class="section" style="padding-top:0">
        <div class="container">
            <div
                style="display:flex;align-items:center;justify-content:space-between;margin-bottom:24px;flex-wrap:wrap;gap:12px">
                <div>
                    <div class="section-eyebrow reveal">Nổi bật</div>
                    <h2 class="section-title reveal">Sản phẩm bán chạy</h2>
                </div>
                <div class="tabs-nav">
                    @foreach (['Tất cả', 'Laptop', 'Điện thoại', 'Tai nghe'] as $t)
                        <button class="tab-pill {{ $loop->first ? 'active' : '' }}"
                            onclick="switchTab(this)">{{ $t }}</button>
                    @endforeach
                </div>
            </div>

            <div class="grid-4">
                @php
                    $products = [
                        [
                            'id' => 1,
                            'name' => 'MacBook Pro 14" M3 Pro',
                            'brand' => 'Apple',
                            'price' => 42990000,
                            'old' => 48490000,
                            'badge' => '',
                        ],
                        [
                            'id' => 2,
                            'name' => 'iPhone 15 Pro Max',
                            'brand' => 'Apple',
                            'price' => 32990000,
                            'old' => 36990000,
                            'badge' => 'Hot',
                        ],
                        [
                            'id' => 3,
                            'name' => 'Samsung S24 Ultra',
                            'brand' => 'Samsung',
                            'price' => 29990000,
                            'old' => 33990000,
                            'badge' => '-11%',
                        ],
                        [
                            'id' => 6,
                            'name' => 'Sony WH-1000XM5',
                            'brand' => 'Sony',
                            'price' => 8490000,
                            'old' => 9990000,
                            'badge' => 'Best',
                        ],
                ]; @endphp

                @foreach ($products as $p)
                    <div class="product-card reveal">
                        <div class="product-card__thumb">
                            @if ($p['badge'])
                                <div class="product-card__badges">
                                    <span
                                        class="badge {{ str_starts_with($p['badge'], '-') ? 'badge-sale' : ($p['badge'] === 'Hot' ? 'badge-hot' : 'badge-best') }}">{{ $p['badge'] }}</span>
                                </div>
                            @endif
                            <button class="product-card__wish" data-wish-id="{{ $p['id'] }}"
                                data-wish-name="{{ $p['name'] }}" data-wish-price="{{ $p['price'] }}"
                                aria-label="Yêu thích">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none"
                                    stroke="currentColor" stroke-width="1.8" stroke-linecap="round">
                                    <path
                                        d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z" />
                                </svg>
                            </button>
                            <div class="product-card__img" style="display:flex;align-items:center;justify-content:center">
                                <svg width="72" height="72" viewBox="0 0 24 24" fill="none"
                                    stroke="currentColor" stroke-width=".7" stroke-linecap="round">
                                    <rect x="2" y="3" width="20" height="14" rx="2" />
                                    <line x1="8" y1="21" x2="16" y2="21" />
                                    <line x1="12" y1="17" x2="12" y2="21" />
                                </svg>
                            </div>
                            <div class="product-card__actions">
                                <button class="btn btn-ghost"
                                    onclick="Cart.add({id:{{ $p['id'] }},name:'{{ addslashes($p['name']) }}',price:{{ $p['price'] }},img:''})">
                                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none"
                                        stroke="currentColor" stroke-width="2" stroke-linecap="round">
                                        <circle cx="9" cy="21" r="1" />
                                        <circle cx="20" cy="21" r="1" />
                                        <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6" />
                                    </svg>
                                    Giỏ hàng
                                </button>
                                <a href="{{ route('products.show') }}" class="btn btn-primary">Xem ngay</a>
                            </div>
                        </div>
                        <div class="product-card__body">
                            <div class="product-card__brand">{{ $p['brand'] }}</div>
                            <div class="product-card__name"><a
                                    href="{{ route('products.show') }}">{{ $p['name'] }}</a></div>
                            <div class="product-card__rating">
                                <div class="product-card__stars">
                                    @for ($s = 1; $s <= 5; $s++)
                                        <svg width="12" height="12" viewBox="0 0 24 24"
                                            fill="{{ $s <= 5 ? '#F59E0B' : '#E5E3DE' }}"
                                            stroke="{{ $s <= 5 ? '#F59E0B' : '#E5E3DE' }}" stroke-width="1">
                                            <polygon
                                                points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2" />
                                        </svg>
                                    @endfor
                                </div>
                            </div>
                            <div class="product-card__price">
                                <span
                                    class="product-card__price-current">{{ number_format($p['price'], 0, ',', '.') }}₫</span>
                                @if ($p['old'])
                                    <span class="product-card__price-old">{{ number_format($p['old'], 0, ',', '.') }}₫</span>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div style="text-align:center;margin-top:36px">
                <a href="{{ url('san-pham') }}" class="btn btn-outline btn-lg">
                    Xem tất cả sản phẩm
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2" stroke-linecap="round">
                        <line x1="5" y1="12" x2="19" y2="12" />
                        <polyline points="12 5 19 12 12 19" />
                    </svg>
                </a>
            </div>
        </div>
    </section>

    {{-- ── FEATURE STRIP ───────────────────── --}}
    <section class="section" style="padding-top:0">
        <div class="container">
            <div class="feature-strip reveal">
                @php $features = [['path' => 'M1 3h15v13H1zM16 8h4l3 3v4h-7V8zM5.5 21a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5zM18.5 21a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5z', 'title' => 'Giao hàng toàn quốc', 'sub' => 'Miễn phí cho đơn từ 1 triệu'], ['path' => 'M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z', 'title' => 'Bảo hành chính hãng', 'sub' => '12 – 24 tháng tùy sản phẩm'], ['path' => 'M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z M9 22V12h6v10', 'title' => 'Đổi trả dễ dàng', 'sub' => '30 ngày không cần lý do'], ['path' => 'M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z', 'title' => 'Hỗ trợ 24/7', 'sub' => 'Tư vấn & chăm sóc tận tâm']]; @endphp
                @foreach ($features as $f)
                    <div class="feature-item">
                        <div class="feature-item__icon">
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="1.7" stroke-linecap="round">
                                <path d="{{ $f['path'] }}" />
                            </svg>
                        </div>
                        <div>
                            <div class="feature-item__title">{{ $f['title'] }}</div>
                            <div class="feature-item__sub">{{ $f['sub'] }}</div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ── TESTIMONIALS ────────────────────── --}}
    <section class="section" style="background:var(--surface);padding-top:64px;padding-bottom:64px">
        <div class="container">
            <div class="section-header section-header-center reveal" style="text-align:center">
                <div class="section-eyebrow">Khách hàng</div>
                <h2 class="section-title">Được tin dùng bởi hàng chục nghìn khách hàng</h2>
            </div>

            <div class="testimonials-grid">
                @php $reviews = [['name' => 'Nguyễn Thị Lan', 'role' => 'Kỹ sư phần mềm', 'stars' => 5, 'text' => 'Mua MacBook Pro tại Nexus, hàng chính hãng, seal mới hoàn toàn. Giao hàng đúng hẹn, tư vấn nhiệt tình. Sẽ tiếp tục ủng hộ!'], ['name' => 'Trần Văn Minh', 'role' => 'Nhà thiết kế đồ họa', 'stars' => 5, 'text' => 'iPhone 15 Pro Max mua tại đây giá tốt hơn nhiều so với các cửa hàng khác. Bảo hành uy tín, hỗ trợ sau bán hàng rất chu đáo.'], ['name' => 'Lê Thị Hương', 'role' => 'Giáo viên', 'stars' => 5, 'text' => 'Lần đầu mua đã tin tưởng ngay vì website chuyên nghiệp. Sản phẩm đúng mô tả, đóng gói cẩn thận. Rất hài lòng!']]; @endphp
                @foreach ($reviews as $r)
                    <div class="testimonial-card reveal">
                        <div class="testimonial-stars">
                            @for ($s = 1; $s <= 5; $s++)
                                <svg width="14" height="14" viewBox="0 0 24 24"
                                    fill="{{ $s <= $r['stars'] ? '#F59E0B' : '#E5E3DE' }}"
                                    stroke="{{ $s <= $r['stars'] ? '#F59E0B' : '#E5E3DE' }}" stroke-width="1">
                                    <polygon
                                        points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2" />
                                </svg>
                            @endfor
                        </div>
                        <p class="testimonial-text">"{{ $r['text'] }}"</p>
                        <div class="testimonial-author">
                            <div class="testimonial-avatar">{{ mb_substr($r['name'], 0, 1) }}</div>
                            <div>
                                <div class="testimonial-name">{{ $r['name'] }}</div>
                                <div class="testimonial-sub">{{ $r['role'] }}</div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ── CTA ─────────────────────────────── --}}
    <section class="section">
        <div class="container">
            <div class="cta-section">
                <div class="cta-section__content">
                    <h2 class="cta-section__title">Sẵn sàng nâng cấp thiết bị của bạn?</h2>
                    <p class="cta-section__sub">Hơn 10.000 sản phẩm chính hãng đang chờ bạn khám phá.</p>
                    <div style="display:flex;gap:12px;justify-content:center;flex-wrap:wrap">
                        <a href="{{ url('san-pham') }}" class="btn btn-accent btn-xl">
                            Mua sắm ngay
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2.5" stroke-linecap="round">
                                <line x1="5" y1="12" x2="19" y2="12" />
                                <polyline points="12 5 19 12 12 19" />
                            </svg>
                        </a>
                        <a href="{{ url('lien-he') }}" class="btn btn-xl"
                            style="background:rgba(255,255,255,.1);color:#fff;border:1px solid rgba(255,255,255,.2)">Liên
                            hệ tư vấn</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <script>
        function switchTab(btn) {
            document.querySelectorAll('.tab-pill').forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
        }
        window.switchTab = switchTab;
    </script>
@endsection
