@extends('layouts.app')
@section('title', 'Khuyến mãi — Nexus Store')

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/pages/promotions.css') }}">
@endpush

@section('content')
    <div class="container section">

        {{-- Breadcrumb --}}
        <div class="breadcrumb mb-32">
            <a href="{{ url('/') }}">Trang chủ</a>
            <span class="breadcrumb__sep">›</span>
            <span class="breadcrumb__current">Khuyến mãi</span>
        </div>

        {{-- ── HERO BANNER ──────────────────────── --}}
        <div class="promo-hero">
            {{-- Background decoration --}}
            <div class="promo-hero__glow promo-hero__glow--1"></div>
            <div class="promo-hero__glow promo-hero__glow--2"></div>

            <div class="promo-hero__content">
                {{-- Badge (SVG thay emoji 🔥) --}}
                <div class="promo-hero__badge">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2" stroke-linecap="round">
                        <path d="M8.5 14.5A2.5 2.5 0 0 0 11 12c0-1.38-.5-2-1-3-1.072-2.143-.224-4.054 2-6
                                 .5 2.5 2 4.9 4 6.5 2 1.6 3 3.5 3 5.5a7 7 0 1 1-14 0c0-1.153.433-2.294 1-3
                                 a2.5 2.5 0 0 0 2.5 2.5z" />
                    </svg>
                    Ưu đãi độc quyền
                </div>

                <h1 class="promo-hero__title">
                    FLASH SALE<br>
                    <span class="promo-hero__title-red">Giảm đến 40%</span>
                </h1>

                {{-- Đếm ngược --}}
                <div class="promo-countdown" id="bigCountdown">
                    <div class="promo-countdown__item">
                        <div class="promo-countdown__num" id="cd-h">06</div>
                        <div class="promo-countdown__label">Giờ</div>
                    </div>
                    <div class="promo-countdown__sep">:</div>
                    <div class="promo-countdown__item">
                        <div class="promo-countdown__num" id="cd-m">30</div>
                        <div class="promo-countdown__label">Phút</div>
                    </div>
                    <div class="promo-countdown__sep">:</div>
                    <div class="promo-countdown__item">
                        <div class="promo-countdown__num" id="cd-s">00</div>
                        <div class="promo-countdown__label">Giây</div>
                    </div>
                </div>

                <a href="{{ url('san-pham') }}" class="promo-hero__btn">
                    Mua ngay
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2.5" stroke-linecap="round">
                        <line x1="5" y1="12" x2="19" y2="12" />
                        <polyline points="12 5 19 12 12 19" />
                    </svg>
                </a>
            </div>
        </div>

        {{-- ── PROMO CARDS ─────────────────────── --}}
        {{--
        Mỗi card có:
        - Màu nền đồng bộ với theme đỏ (#E30019) và các màu phụ
        - SVG icon thay hoàn toàn emoji (yêu cầu của thầy)
        - Hover effect scale nhẹ
    --}}
        <div class="promo-grid">

            {{-- Card 1: Flash Sale --}}
            <div class="promo-card pc-1">
                <div class="promo-card__icon">
                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="1.8" stroke-linecap="round">
                        <polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2" />
                    </svg>
                </div>
                <div class="promo-card__title">Giảm đến 40%</div>
                <div class="promo-card__desc">Laptop & Gaming</div>
                <a href="{{ url('san-pham?cat=laptop') }}" class="promo-card__link">
                    Xem ngay
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2.5" stroke-linecap="round">
                        <line x1="5" y1="12" x2="19" y2="12" />
                        <polyline points="12 5 19 12 12 19" />
                    </svg>
                </a>
            </div>

            {{-- Card 2: iPhone --}}
            <div class="promo-card pc-2">
                <div class="promo-card__icon">
                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="1.8" stroke-linecap="round">
                        <path d="M12 18h.01M8 21h8a1 1 0 0 0 1-1V4a1 1 0 0 0-1-1H8
                                 a1 1 0 0 0-1 1v16a1 1 0 0 0 1 1z" />
                    </svg>
                </div>
                <div class="promo-card__title">iPhone 15 Series</div>
                <div class="promo-card__desc">Tặng kèm ốp lưng</div>
                <a href="{{ url('san-pham?cat=phone') }}" class="promo-card__link">
                    Xem ngay
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2.5" stroke-linecap="round">
                        <line x1="5" y1="12" x2="19" y2="12" />
                        <polyline points="12 5 19 12 12 19" />
                    </svg>
                </a>
            </div>

            {{-- Card 3: Trả góp --}}
            <div class="promo-card pc-3">
                <div class="promo-card__icon">
                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="1.8" stroke-linecap="round">
                        <rect x="1" y="4" width="22" height="16" rx="2" />
                        <line x1="1" y1="10" x2="23" y2="10" />
                    </svg>
                </div>
                <div class="promo-card__title">Trả Góp 0%</div>
                <div class="promo-card__desc">12 tháng, không thế chấp</div>
                <a href="{{ url('san-pham') }}" class="promo-card__link">
                    Xem ngay
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2.5" stroke-linecap="round">
                        <line x1="5" y1="12" x2="19" y2="12" />
                        <polyline points="12 5 19 12 12 19" />
                    </svg>
                </a>
            </div>

            {{-- Card 4: Mua 1 tặng 1 --}}
            <div class="promo-card pc-4">
                <div class="promo-card__icon">
                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="1.8" stroke-linecap="round">
                        <polyline points="20 12 20 22 4 22 4 12" />
                        <rect x="2" y="7" width="20" height="5" />
                        <line x1="12" y1="22" x2="12" y2="7" />
                        <path d="M12 7H7.5a2.5 2.5 0 0 1 0-5C11 2 12 7 12 7z" />
                        <path d="M12 7h4.5a2.5 2.5 0 0 0 0-5C13 2 12 7 12 7z" />
                    </svg>
                </div>
                <div class="promo-card__title">Mua 1 Tặng 1</div>
                <div class="promo-card__desc">Phụ kiện Apple</div>
                <a href="{{ url('san-pham?cat=accessory') }}" class="promo-card__link">
                    Xem ngay
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2.5" stroke-linecap="round">
                        <line x1="5" y1="12" x2="19" y2="12" />
                        <polyline points="12 5 19 12 12 19" />
                    </svg>
                </a>
            </div>

            {{-- Card 5: Free Ship --}}
            <div class="promo-card pc-5">
                <div class="promo-card__icon">
                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="1.8" stroke-linecap="round">
                        <path d="M1 3h15v13H1z" />
                        <path d="M16 8h4l3 3v4h-7V8z" />
                        <circle cx="5.5" cy="18.5" r="2.5" />
                        <circle cx="18.5" cy="18.5" r="2.5" />
                    </svg>
                </div>
                <div class="promo-card__title">Free Ship</div>
                <div class="promo-card__desc">Đơn từ 500.000₫</div>
                <a href="{{ url('san-pham') }}" class="promo-card__link">
                    Xem ngay
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2.5" stroke-linecap="round">
                        <line x1="5" y1="12" x2="19" y2="12" />
                        <polyline points="12 5 19 12 12 19" />
                    </svg>
                </a>
            </div>

            {{-- Card 6: VIP --}}
            <div class="promo-card pc-6">
                <div class="promo-card__icon">
                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="1.8" stroke-linecap="round">
                        <polygon
                            points="12 2 15.09 8.26 22 9.27 17 14.14
                                     18.18 21.02 12 17.77 5.82 21.02
                                     7 14.14 2 9.27 8.91 8.26 12 2" />
                    </svg>
                </div>
                <div class="promo-card__title">Tích Điểm VIP</div>
                <div class="promo-card__desc">Đổi điểm lấy quà</div>
                <a href="{{ url('san-pham') }}" class="promo-card__link">
                    Xem ngay
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2.5" stroke-linecap="round">
                        <line x1="5" y1="12" x2="19" y2="12" />
                        <polyline points="12 5 19 12 12 19" />
                    </svg>
                </a>
            </div>
        </div>

        {{-- ── SẢN PHẨM FLASH SALE ─────────────── --}}
        <div style="margin-bottom:28px">
            <div class="section-eyebrow">Flash Sale</div>
            <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px">
                <h2 class="section-title">Sản phẩm đang giảm giá</h2>
                <a href="{{ url('san-pham') }}"
                    style="font-size:13.5px;color:var(--accent);font-weight:600;
                      display:flex;align-items:center;gap:5px;text-decoration:none">
                    Xem tất cả
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2.5" stroke-linecap="round">
                        <line x1="5" y1="12" x2="19" y2="12" />
                        <polyline points="12 5 19 12 12 19" />
                    </svg>
                </a>
            </div>
        </div>

        <div class="grid-4">
            @php
                $saleProducts = [
                    [
                        'id' => 1,
                        'brand' => 'Apple',
                        'name' => 'MacBook Pro M3 Pro',
                        'price' => 42990000,
                        'old' => 48490000,
                        'badge' => '-14%',
                        'icon' => 'M2 3h20v14H2zM8 21h8M12 17v4',
                    ],
                    [
                        'id' => 2,
                        'brand' => 'Apple',
                        'name' => 'iPhone 15 Pro Max',
                        'price' => 32990000,
                        'old' => 36990000,
                        'badge' => '-11%',
                        'icon' => 'M12 18h.01M8 21h8a1 1 0 0 0 1-1V4a1 1 0 0 0-1-1H8a1 1 0 0 0-1 1v16a1 1 0 0 0 1 1z',
                    ],
                    [
                        'id' => 5,
                        'brand' => 'Apple',
                        'name' => 'iPad Pro M2',
                        'price' => 28990000,
                        'old' => 32990000,
                        'badge' => '-10%',
                        'icon' => 'M18 3H6a1 1 0 0 0-1 1v16a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V4a1 1 0 0 0-1-1zM12 17h.01',
                    ],
                    [
                        'id' => 6,
                        'brand' => 'Sony',
                        'name' => 'Sony WH-1000XM5',
                        'price' => 8490000,
                        'old' => 9990000,
                        'badge' => '-15%',
                        'icon' =>
                            'M3 18v-6a9 9 0 0 1 18 0v6M21 19a2 2 0 0 1-2 2h-1a2 2 0 0 1-2-2v-3a2 2 0 0 1 2-2h3zM3 19a2 2 0 0 0 2 2h1a2 2 0 0 0 2-2v-3a2 2 0 0 0-2-2H3z',
                    ],
                ];
            @endphp

            @foreach ($saleProducts as $p)
                <div class="product-card reveal">
                    <div class="product-card__thumb">
                        <div class="product-card__badges">
                            <span class="badge badge-sale">{{ $p['badge'] }}</span>
                        </div>

                        {{-- Nút yêu thích: SVG, không emoji ♥ --}}
                        <button class="product-card__wish" data-wish-id="{{ $p['id'] }}"
                            data-wish-name="{{ $p['name'] }}" data-wish-price="{{ $p['price'] }}"
                            data-wish-slug="" aria-label="Yêu thích">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="1.8" stroke-linecap="round">
                                <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06
                                     a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78
                                     1.06-1.06a5.5 5.5 0 0 0 0-7.78z" />
                            </svg>
                        </button>

                        {{-- Ảnh placeholder SVG, không emoji 💻📱📟🎧 --}}
                        <div class="product-card__img" style="display:flex;align-items:center;justify-content:center">
                            <svg width="68" height="68" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width=".7" stroke-linecap="round" style="color:#C5C3BC">
                                <path d="{{ $p['icon'] }}" />
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
                            <a href="{{ url('san-pham') }}" class="btn btn-primary">Xem</a>
                        </div>
                    </div>

                    <div class="product-card__body">
                        <div class="product-card__brand">{{ $p['brand'] }}</div>
                        <div class="product-card__name">{{ $p['name'] }}</div>
                        <div class="product-card__rating">
                            <div class="product-card__stars">
                                @for ($s = 1; $s <= 5; $s++)
                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="#F59E0B"
                                        stroke="#F59E0B" stroke-width="1">
                                        <polygon
                                            points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2" />
                                    </svg>
                                @endfor
                            </div>
                        </div>
                        <div class="product-card__price">
                            <span class="product-card__price-current">
                                {{ number_format($p['price'], 0, ',', '.') }}₫
                            </span>
                            <span class="product-card__price-old">
                                {{ number_format($p['old'], 0, ',', '.') }}₫
                            </span>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

    </div>
@endsection

@push('scripts')
    <script>
        // ── Đếm ngược thời gian flash sale ────────────────────────────────
        (function() {
            const end = new Date();
            end.setHours(end.getHours() + 6, 30, 0, 0);

            function pad(n) {
                return String(n).padStart(2, '0');
            }

            function tick() {
                const diff = Math.max(0, end - new Date());
                const h = Math.floor(diff / 3600000);
                const m = Math.floor((diff % 3600000) / 60000);
                const s = Math.floor((diff % 60000) / 1000);

                const hEl = document.getElementById('cd-h');
                const mEl = document.getElementById('cd-m');
                const sEl = document.getElementById('cd-s');

                if (hEl) hEl.textContent = pad(h);
                if (mEl) mEl.textContent = pad(m);
                if (sEl) sEl.textContent = pad(s);

                if (diff > 0) requestAnimationFrame(() => setTimeout(tick, 1000));
            }

            tick();
        })();
    </script>
@endpush
