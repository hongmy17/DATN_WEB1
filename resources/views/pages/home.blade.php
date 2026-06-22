@extends('layouts.app')

@section('title', 'Trang chủ - Nexus Store')

@section('content')

    @push('styles')
        <style>
            /* ── HERO ─────────────────────────────── */
            .hero {
                min-height: calc(100vh - 68px);
                display: grid;
                grid-template-columns: 1fr 1fr;
                align-items: center;
                gap: 60px;
                padding: 80px 0;
            }

            .hero__eyebrow {
                display: inline-flex;
                align-items: center;
                gap: 8px;
                padding: 6px 16px;
                border-radius: var(--r-full);
                border: 1.5px solid var(--border);
                background: var(--bg-alt);
                font-size: 12px;
                font-weight: 700;
                color: var(--ink-3);
                letter-spacing: 1px;
                text-transform: uppercase;
                margin-bottom: 24px;
            }

            .hero__eyebrow span {
                width: 8px;
                height: 8px;
                border-radius: 50%;
                background: var(--accent);
                animation: pulse 1.8s infinite;
            }

            @keyframes pulse {

                0%,
                100% {
                    transform: scale(1);
                    opacity: 1
                }

                50% {
                    transform: scale(1.4);
                    opacity: .6
                }
            }

            .hero__title {
                margin-bottom: 24px;
                color: var(--ink);
            }

            .hero__title em {
                font-style: normal;
                color: var(--accent);
            }

            .hero__desc {
                font-size: 17px;
                color: var(--ink-3);
                line-height: 1.8;
                margin-bottom: 36px;
                max-width: 480px;
            }

            .hero__cta {
                display: flex;
                gap: 14px;
                flex-wrap: wrap;
                align-items: center;
            }

            .hero__stats {
                display: flex;
                gap: 40px;
                margin-top: 48px;
                padding-top: 40px;
                border-top: 1px solid var(--border-soft);
            }

            .hero__stat-num {
                font-family: var(--font-display);
                font-size: 30px;
                font-weight: 800;
                color: var(--ink);
                line-height: 1;
            }

            .hero__stat-label {
                font-size: 13px;
                color: var(--ink-muted);
                margin-top: 4px;
            }

            .hero__visual {
                position: relative;
            }

            .hero__grid {
                display: grid;
                grid-template-columns: 1fr 1fr;
                gap: 14px;
            }

            .hero__card {
                background: var(--bg-alt);
                border: 1px solid var(--border-soft);
                border-radius: var(--r-xl);
                padding: 24px 18px;
                text-align: center;
                transition: all .3s var(--ease-out);
                cursor: pointer;
            }

            .hero__card:hover {
                box-shadow: var(--shadow-md);
                transform: translateY(-3px);
                border-color: var(--accent);
            }

            .hero__card.featured {
                grid-column: 1/-1;
                background: linear-gradient(135deg, #FBF0EB, #FFF7F3);
                border-color: rgba(200, 82, 42, .2);
            }

            .hero__card-emoji {
                font-size: 52px;
                line-height: 1;
                margin-bottom: 10px;
            }

            .hero__card-name {
                font-size: 13px;
                font-weight: 700;
                color: var(--ink);
                margin-bottom: 3px;
            }

            .hero__card-price {
                font-size: 12px;
                color: var(--accent);
                font-weight: 600;
            }

            .hero__float {
                position: absolute;
                background: var(--bg-alt);
                border: 1px solid var(--border);
                border-radius: var(--r-lg);
                padding: 10px 14px;
                display: flex;
                align-items: center;
                gap: 8px;
                box-shadow: var(--shadow-md);
                font-size: 12px;
                font-weight: 600;
                color: var(--ink);
                animation: bob 3s ease-in-out infinite;
            }

            .hero__float--1 {
                top: -16px;
                right: 24px;
                animation-delay: 0s;
            }

            .hero__float--2 {
                bottom: 8px;
                left: -20px;
                animation-delay: 1.2s;
            }

            @keyframes bob {

                0%,
                100% {
                    transform: translateY(0)
                }

                50% {
                    transform: translateY(-8px)
                }
            }

            /* ── BRANDS ──────────────────────────────── */
            .brands {
                overflow: hidden;
                padding: 28px 0;
                border-top: 1px solid var(--border-soft);
                border-bottom: 1px solid var(--border-soft);
                background: var(--bg-alt);
            }

            .brands__track {
                display: flex;
                gap: 56px;
                align-items: center;
                width: max-content;
                animation: scroll 22s linear infinite;
            }

            .brands__track:hover {
                animation-play-state: paused;
            }

            .brands__item {
                font-family: var(--font-display);
                font-size: 17px;
                font-weight: 700;
                color: var(--ink-muted);
                white-space: nowrap;
                transition: var(--transition);
            }

            .brands__item:hover {
                color: var(--accent);
            }

            @keyframes scroll {
                to {
                    transform: translateX(-50%);
                }
            }

            /* ── CATEGORIES ─────────────────────────── */
            .cat-grid {
                display: grid;
                grid-template-columns: repeat(6, 1fr);
                gap: 14px;
            }

            .cat-card {
                background: var(--bg-alt);
                border: 1px solid var(--border-soft);
                border-radius: var(--r-xl);
                padding: 22px 14px;
                text-align: center;
                transition: all .3s var(--ease-out);
                cursor: pointer;
                text-decoration: none;
            }

            .cat-card:hover {
                background: var(--ink);
                border-color: var(--ink);
                transform: translateY(-3px);
                box-shadow: var(--shadow-md);
            }

            .cat-card:hover .cat-card__name,
            .cat-card:hover .cat-card__count {
                color: #fff;
            }

            .cat-card__icon {
                font-size: 36px;
                line-height: 1;
                margin-bottom: 10px;
            }

            .cat-card__name {
                font-size: 13px;
                font-weight: 700;
                color: var(--ink);
                margin-bottom: 3px;
                transition: var(--transition);
            }

            .cat-card__count {
                font-size: 11px;
                color: var(--ink-muted);
                transition: var(--transition);
            }

            /* ── FLASH SALE BAR ─────────────────────── */
            .flash-bar {
                background: var(--ink);
                border-radius: var(--r-xl);
                padding: 20px 28px;
                display: flex;
                align-items: center;
                justify-content: space-between;
                flex-wrap: wrap;
                gap: 16px;
                margin-bottom: 32px;
            }

            .flash-bar__title {
                font-family: var(--font-display);
                font-size: 20px;
                font-weight: 800;
                color: #fff;
                display: flex;
                align-items: center;
                gap: 10px;
            }

            #flashCountdown {
                display: flex;
                align-items: center;
                gap: 6px;
            }

            .cd__unit {
                background: rgba(255, 255, 255, .12);
                border-radius: var(--r-md);
                padding: 8px 12px;
                text-align: center;
                min-width: 52px;
            }

            .cd__unit b {
                display: block;
                font-family: var(--font-display);
                font-size: 22px;
                font-weight: 800;
                color: #fff;
                line-height: 1;
            }

            .cd__unit small {
                font-size: 9px;
                text-transform: uppercase;
                letter-spacing: 1px;
                color: rgba(255, 255, 255, .6);
            }

            .cd__sep {
                font-size: 20px;
                font-weight: 800;
                color: rgba(255, 255, 255, .4);
            }

            /* ── BANNERS ─────────────────────────────── */
            .banner-grid {
                display: grid;
                grid-template-columns: 3fr 2fr;
                gap: 18px;
            }

            .banner {
                border-radius: var(--r-2xl);
                padding: 40px;
                position: relative;
                overflow: hidden;
                cursor: pointer;
                min-height: 240px;
                display: flex;
                flex-direction: column;
                justify-content: flex-end;
                transition: var(--transition);
            }

            .banner:hover {
                transform: translateY(-3px);
                box-shadow: var(--shadow-lg);
            }

            .banner__bg-emoji {
                position: absolute;
                right: 24px;
                top: 50%;
                transform: translateY(-50%);
                font-size: 110px;
                opacity: .5;
                transition: var(--transition);
            }

            .banner:hover .banner__bg-emoji {
                transform: translateY(-55%) scale(1.1);
                opacity: .7;
            }

            .banner--1 {
                background: linear-gradient(135deg, #0F1923 0%, #1A3A5C 100%);
            }

            .banner--2 {
                background: linear-gradient(135deg, #2D1515 0%, #6B2020 100%);
            }

            .banner__tag {
                display: inline-block;
                padding: 4px 12px;
                border-radius: var(--r-full);
                font-size: 11px;
                font-weight: 700;
                letter-spacing: 1px;
                text-transform: uppercase;
                margin-bottom: 10px;
            }

            .banner--1 .banner__tag {
                background: rgba(79, 130, 247, .2);
                color: #93c5fd;
            }

            .banner--2 .banner__tag {
                background: rgba(200, 82, 42, .25);
                color: #fca97e;
            }

            .banner__title {
                font-family: var(--font-display);
                font-size: 26px;
                font-weight: 800;
                color: #fff;
                line-height: 1.2;
                margin-bottom: 8px;
            }

            .banner__sub {
                font-size: 14px;
                color: rgba(255, 255, 255, .6);
                margin-bottom: 18px;
            }

            .banner__cta {
                display: inline-flex;
                align-items: center;
                gap: 6px;
                font-size: 14px;
                font-weight: 700;
                color: #fff;
                transition: var(--transition);
            }

            .banner__cta:hover {
                gap: 10px;
            }

            /* ── WHY US ─────────────────────────────── */
            .why-grid {
                display: grid;
                grid-template-columns: repeat(4, 1fr);
                gap: 20px;
            }

            .why-card {
                padding: 28px;
                border-radius: var(--r-xl);
                border: 1px solid var(--border-soft);
                background: var(--bg-alt);
                transition: var(--transition);
            }

            .why-card:hover {
                border-color: var(--accent);
                transform: translateY(-2px);
                box-shadow: var(--shadow-sm);
            }

            .why-icon {
                font-size: 36px;
                margin-bottom: 16px;
            }

            .why-title {
                font-size: 16px;
                font-weight: 700;
                color: var(--ink);
                margin-bottom: 8px;
            }

            .why-desc {
                font-size: 14px;
                color: var(--ink-3);
                line-height: 1.7;
            }

            /* ── TESTIMONIALS ────────────────────────── */
            .testi-grid {
                display: grid;
                grid-template-columns: repeat(3, 1fr);
                gap: 20px;
            }

            .testi-card {
                background: var(--bg-alt);
                border: 1px solid var(--border-soft);
                border-radius: var(--r-xl);
                padding: 24px;
                transition: var(--transition);
            }

            .testi-card:hover {
                box-shadow: var(--shadow-md);
                transform: translateY(-2px);
            }

            .testi-stars {
                color: var(--yellow);
                font-size: 14px;
                margin-bottom: 12px;
            }

            .testi-text {
                font-size: 14px;
                color: var(--ink-3);
                line-height: 1.75;
                margin-bottom: 18px;
                font-style: italic;
            }

            .testi-user {
                display: flex;
                align-items: center;
                gap: 10px;
            }

            .testi-avatar {
                width: 38px;
                height: 38px;
                border-radius: 50%;
                background: linear-gradient(135deg, var(--accent), #C8522A);
                display: flex;
                align-items: center;
                justify-content: center;
                font-weight: 700;
                font-size: 14px;
                color: #fff;
            }

            .testi-name {
                font-weight: 600;
                font-size: 14px;
                color: var(--ink);
            }

            .testi-job {
                font-size: 12px;
                color: var(--ink-muted);
            }

            @media(max-width:1024px) {
                .hero {
                    grid-template-columns: 1fr;
                    gap: 40px;
                }

                .hero__visual {
                    order: -1;
                }

                .cat-grid {
                    grid-template-columns: repeat(3, 1fr);
                }

                .banner-grid {
                    grid-template-columns: 1fr;
                }

                .why-grid {
                    grid-template-columns: repeat(2, 1fr);
                }

                .testi-grid {
                    grid-template-columns: 1fr;
                }
            }

            @media(max-width:768px) {
                .cat-grid {
                    grid-template-columns: repeat(3, 1fr);
                }

                .hero__stats {
                    gap: 24px;
                    flex-wrap: wrap;
                }

                .flash-bar {
                    flex-direction: column;
                    align-items: flex-start;
                }
            }
        </style>
    @endpush

    <div class="container">
        <div class="hero">
            <div class="hero__content">
                <div class="hero__eyebrow"><span></span> Flash Sale — Giảm đến 40% hôm nay</div>
                <h1 class="display-1 hero__title">Công nghệ<br><em>đỉnh cao,</em><br>giá tốt nhất.</h1>
                <p class="hero__desc">Laptop, điện thoại, tablet chính hãng 100%. Bảo hành 12 tháng, đổi trả 7 ngày, giao
                    hàng trong ngày.</p>
                <div class="hero__cta">
                    <a href="{{ url('san-pham') }}" class="btn btn-primary btn-lg">Mua sắm ngay →</a>
                    <a href="{{ url('khuyen-mai') }}" class="btn btn-outline btn-lg">Xem khuyến mãi</a>
                </div>
                <div class="hero__stats">
                    <div>
                        <div class="hero__stat-num">50K+</div>
                        <div class="hero__stat-label">Khách hàng</div>
                    </div>
                    <div>
                        <div class="hero__stat-num">5.000+</div>
                        <div class="hero__stat-label">Sản phẩm</div>
                    </div>
                    <div>
                        <div class="hero__stat-num">4.9★</div>
                        <div class="hero__stat-label">Đánh giá</div>
                    </div>
                </div>
            </div>
            <div class="hero__visual">
                <div class="hero__float hero__float--1">🔥 Bán chạy #1</div>
                <div class="hero__float hero__float--2">✅ Chính hãng 100%</div>
                <div class="hero__grid">
                    <a href="{{ url('chi-tiet?id=1') }}" class="hero__card featured">
                        <div class="hero__card-emoji">💻</div>
                        <div class="hero__card-name">MacBook Pro M3 Pro</div>
                        <div class="hero__card-price">42.990.000₫</div>
                    </a>
                    <a href="{{ url('chi-tiet?id=2') }}" class="hero__card">
                        <div class="hero__card-emoji">📱</div>
                        <div class="hero__card-name">iPhone 15 Pro Max</div>
                        <div class="hero__card-price">32.990.000₫</div>
                    </a>
                    <a href="{{ url('chi-tiet?id=5') }}" class="hero__card">
                        <div class="hero__card-emoji">📟</div>
                        <div class="hero__card-name">iPad Pro M2</div>
                        <div class="hero__card-price">28.990.000₫</div>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="brands">
        <div class="brands__track">
            <span class="brands__item">Apple</span><span class="brands__item">Samsung</span><span
                class="brands__item">Dell</span>
            <span class="brands__item">ASUS</span><span class="brands__item">Sony</span><span class="brands__item">LG</span>
            <span class="brands__item">HP</span><span class="brands__item">Lenovo</span><span
                class="brands__item">Bose</span>
            <span class="brands__item">Logitech</span><span class="brands__item">Microsoft</span><span
                class="brands__item">Razer</span>
            <span class="brands__item">Apple</span><span class="brands__item">Samsung</span><span
                class="brands__item">Dell</span>
            <span class="brands__item">ASUS</span><span class="brands__item">Sony</span><span class="brands__item">LG</span>
            <span class="brands__item">HP</span><span class="brands__item">Lenovo</span><span
                class="brands__item">Bose</span>
            <span class="brands__item">Logitech</span><span class="brands__item">Microsoft</span><span
                class="brands__item">Razer</span>
        </div>
    </div>

    <section class="section">
        <div class="container">
            <div class="section-header section-header--center">
                <div class="section-header__eyebrow">Danh Mục</div>
                <h2 class="display-2 section-header__title">Tìm theo <span class="text-accent">loại sản phẩm</span></h2>
            </div>
            <div class="cat-grid">
                <a href="{{ url('san-pham?cat=laptop') }}" class="cat-card">
                    <div class="cat-card__icon">💻</div>
                    <div class="cat-card__name">Laptop</div>
                    <div class="cat-card__count">234 sản phẩm</div>
                </a>
                <a href="{{ url('san-pham?cat=phone') }}" class="cat-card">
                    <div class="cat-card__icon">📱</div>
                    <div class="cat-card__name">Điện Thoại</div>
                    <div class="cat-card__count">456 sản phẩm</div>
                </a>
                <a href="{{ url('san-pham?cat=tablet') }}" class="cat-card">
                    <div class="cat-card__icon">📟</div>
                    <div class="cat-card__name">Máy Tính Bảng</div>
                    <div class="cat-card__count">128 sản phẩm</div>
                </a>
                <a href="{{ url('san-pham?cat=watch') }}" class="cat-card">
                    <div class="cat-card__icon">⌚</div>
                    <div class="cat-card__name">Smartwatch</div>
                    <div class="cat-card__count">89 sản phẩm</div>
                </a>
                <a href="{{ url('san-pham?cat=audio') }}" class="cat-card">
                    <div class="cat-card__icon">🎧</div>
                    <div class="cat-card__name">Tai Nghe</div>
                    <div class="cat-card__count">167 sản phẩm</div>
                </a>
                <a href="{{ url('san-pham?cat=accessory') }}" class="cat-card">
                    <div class="cat-card__icon">🖱️</div>
                    <div class="cat-card__name">Phụ Kiện</div>
                    <div class="cat-card__count">678 sản phẩm</div>
                </a>
            </div>
        </div>
    </section>

    <section class="section" style="padding-top:0">
        <div class="container">
            <div class="flash-bar">
                <div class="flash-bar__title">⚡ Flash Sale</div>
                <div id="flashCountdown"></div>
                <a href="{{ url('khuyen-mai') }}" class="btn btn-sm"
                    style="background:rgba(255,255,255,.15);color:#fff;border:1px solid rgba(255,255,255,.2)">Xem tất cả
                    →</a>
            </div>
            <div class="grid-4">
                <!-- Sản phẩm 1 -->
                <div class="product-card">
                    <div class="product-card__thumb">
                        <div class="product-card__badges"><span class="badge badge-sale">-14%</span></div>
                        <button class="product-card__wish" data-wish-id="1" data-wish-name="MacBook Pro">♥</button>
                        <div class="product-card__img">💻</div>
                        <div class="product-card__actions">
                            <button class="btn btn-ghost btn-sm" onclick="Toast.show('Xem nhanh','info')">👁 Xem
                                nhanh</button>
                            <button class="btn btn-primary btn-sm"
                                onclick="Cart.add({id:1,name:'MacBook Pro',price:42990000,img:'💻'})">+ Giỏ hàng</button>
                        </div>
                    </div>
                    <div class="product-card__body">
                        <div class="product-card__brand">Apple</div>
                        <div class="product-card__name"><a href="{{ url('chi-tiet?id=1') }}">MacBook Pro 14" M3 Pro</a>
                        </div>
                        <div class="product-card__rating"><span class="product-card__stars">★★★★★</span><span
                                class="product-card__reviews">(234)</span></div>
                        <div class="product-card__price"><span class="product-card__price-current">42.990.000₫</span><span
                                class="product-card__price-old">49.990.000₫</span></div>
                    </div>
                </div>
                <!-- Sản phẩm 2 -->
                <div class="product-card">
                    <div class="product-card__thumb">
                        <div class="product-card__badges"><span class="badge badge-sale">-11%</span></div>
                        <button class="product-card__wish" data-wish-id="2" data-wish-name="iPhone 15 Pro Max">♥</button>
                        <div class="product-card__img">📱</div>
                        <div class="product-card__actions">
                            <button class="btn btn-ghost btn-sm" onclick="Toast.show('Xem nhanh','info')">👁 Xem
                                nhanh</button>
                            <button class="btn btn-primary btn-sm"
                                onclick="Cart.add({id:2,name:'iPhone 15 Pro Max',price:32990000,img:'📱'})">+ Giỏ
                                hàng</button>
                        </div>
                    </div>
                    <div class="product-card__body">
                        <div class="product-card__brand">Apple</div>
                        <div class="product-card__name"><a href="{{ url('chi-tiet?id=2') }}">iPhone 15 Pro Max 256GB</a>
                        </div>
                        <div class="product-card__rating"><span class="product-card__stars">★★★★★</span><span
                                class="product-card__reviews">(567)</span></div>
                        <div class="product-card__price"><span class="product-card__price-current">32.990.000₫</span><span
                                class="product-card__price-old">36.990.000₫</span></div>
                    </div>
                </div>
                <!-- Sản phẩm 3 -->
                <div class="product-card">
                    <div class="product-card__thumb">
                        <div class="product-card__badges"><span class="badge badge-hot">Hot 🔥</span></div>
                        <button class="product-card__wish" data-wish-id="6" data-wish-name="Sony WH-1000XM5">♥</button>
                        <div class="product-card__img">🎧</div>
                        <div class="product-card__actions">
                            <button class="btn btn-ghost btn-sm" onclick="Toast.show('Xem nhanh','info')">👁 Xem
                                nhanh</button>
                            <button class="btn btn-primary btn-sm"
                                onclick="Cart.add({id:6,name:'Sony WH-1000XM5',price:8490000,img:'🎧'})">+ Giỏ
                                hàng</button>
                        </div>
                    </div>
                    <div class="product-card__body">
                        <div class="product-card__brand">Sony</div>
                        <div class="product-card__name"><a href="{{ url('chi-tiet?id=6') }}">Sony WH-1000XM5</a></div>
                        <div class="product-card__rating"><span class="product-card__stars">★★★★★</span><span
                                class="product-card__reviews">(1.203)</span></div>
                        <div class="product-card__price"><span class="product-card__price-current">8.490.000₫</span><span
                                class="product-card__price-old">9.990.000₫</span></div>
                    </div>
                </div>
                <!-- Sản phẩm 4 -->
                <div class="product-card">
                    <div class="product-card__thumb">
                        <div class="product-card__badges"><span class="badge badge-new">Mới</span></div>
                        <button class="product-card__wish" data-wish-id="8" data-wish-name="Apple Watch S9">♥</button>
                        <div class="product-card__img">⌚</div>
                        <div class="product-card__actions">
                            <button class="btn btn-ghost btn-sm" onclick="Toast.show('Xem nhanh','info')">👁 Xem
                                nhanh</button>
                            <button class="btn btn-primary btn-sm"
                                onclick="Cart.add({id:8,name:'Apple Watch S9',price:11990000,img:'⌚'})">+ Giỏ hàng</button>
                        </div>
                    </div>
                    <div class="product-card__body">
                        <div class="product-card__brand">Apple</div>
                        <div class="product-card__name"><a href="{{ url('chi-tiet?id=8') }}">Apple Watch Series 9</a>
                        </div>
                        <div class="product-card__rating"><span class="product-card__stars">★★★★★</span><span
                                class="product-card__reviews">(892)</span></div>
                        <div class="product-card__price"><span class="product-card__price-current">11.990.000₫</span><span
                                class="product-card__price-old">13.990.000₫</span></div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="section" style="padding-top:0">
        <div class="container">
            <div class="banner-grid">
                <a href="{{ url('san-pham?cat=laptop') }}" class="banner banner--1">
                    <div class="banner__bg-emoji">💻</div>
                    <div class="banner__tag">Laptop</div>
                    <h3 class="banner__title">MacBook & Gaming<br>Laptop Cao Cấp</h3>
                    <p class="banner__sub">Hiệu năng M3 Pro — thiết kế mỏng nhẹ</p>
                    <span class="banner__cta">Khám phá ngay →</span>
                </a>
                <a href="{{ url('san-pham?cat=phone') }}" class="banner banner--2">
                    <div class="banner__bg-emoji">📱</div>
                    <div class="banner__tag">Smartphone</div>
                    <h3 class="banner__title">iPhone 15<br>Pro Series</h3>
                    <p class="banner__sub">Chip A17 Pro · Camera 48MP</p>
                    <span class="banner__cta">Mua ngay →</span>
                </a>
            </div>
        </div>
    </section>

    <section class="section">
        <div class="container">
            <div class="d-flex justify-between align-center mb-32" style="flex-wrap:wrap;gap:16px">
                <div>
                    <div class="section-header__eyebrow" style="display:inline-flex;margin-bottom:8px">Bán Chạy</div>
                    <h2 class="display-2" style="margin:0">Sản phẩm <span class="text-accent">nổi bật</span></h2>
                </div>
            </div>
            <div class="grid-4">
                <!-- MacBook -->
                <div class="product-card">
                    <div class="product-card__thumb">
                        <div class="product-card__badges"><span class="badge badge-hot">Hot 🔥</span></div>
                        <button class="product-card__wish" data-wish-id="1" data-wish-name="MacBook Pro">♥</button>
                        <div class="product-card__img">💻</div>
                        <div class="product-card__actions">
                            <button class="btn btn-primary btn-sm"
                                onclick="Cart.add({id:1,name:'MacBook Pro',price:42990000,img:'💻'})">+ Giỏ hàng</button>
                        </div>
                    </div>
                    <div class="product-card__body">
                        <div class="product-card__brand">Apple</div>
                        <div class="product-card__name"><a href="{{ url('chi-tiet?id=1') }}">MacBook Pro 14" M3 Pro</a>
                        </div>
                        <div class="product-card__price"><span class="product-card__price-current">42.990.000₫</span>
                        </div>
                    </div>
                </div>
                <!-- iPhone -->
                <div class="product-card">
                    <div class="product-card__thumb">
                        <div class="product-card__badges"><span class="badge badge-sale">-11%</span></div>
                        <button class="product-card__wish" data-wish-id="2" data-wish-name="iPhone 15 Pro Max">♥</button>
                        <div class="product-card__img">📱</div>
                        <div class="product-card__actions">
                            <button class="btn btn-primary btn-sm"
                                onclick="Cart.add({id:2,name:'iPhone 15 Pro Max',price:32990000,img:'📱'})">+ Giỏ
                                hàng</button>
                        </div>
                    </div>
                    <div class="product-card__body">
                        <div class="product-card__brand">Apple</div>
                        <div class="product-card__name"><a href="{{ url('chi-tiet?id=2') }}">iPhone 15 Pro Max 256GB</a>
                        </div>
                        <div class="product-card__price"><span class="product-card__price-current">32.990.000₫</span><span
                                class="product-card__price-old">36.990.000₫</span></div>
                    </div>
                </div>
                <!-- iPad -->
                <div class="product-card">
                    <div class="product-card__thumb">
                        <div class="product-card__badges"><span class="badge badge-sale">-10%</span></div>
                        <button class="product-card__wish" data-wish-id="5" data-wish-name="iPad Pro">♥</button>
                        <div class="product-card__img">📟</div>
                        <div class="product-card__actions">
                            <button class="btn btn-primary btn-sm"
                                onclick="Cart.add({id:5,name:'iPad Pro',price:28990000,img:'📟'})">+ Giỏ hàng</button>
                        </div>
                    </div>
                    <div class="product-card__body">
                        <div class="product-card__brand">Apple</div>
                        <div class="product-card__name"><a href="{{ url('chi-tiet?id=5') }}">iPad Pro 12.9" M2</a></div>
                        <div class="product-card__price"><span class="product-card__price-current">28.990.000₫</span><span
                                class="product-card__price-old">32.000.000₫</span></div>
                    </div>
                </div>
                <!-- Sony -->
                <div class="product-card">
                    <div class="product-card__thumb">
                        <div class="product-card__badges"><span class="badge badge-hot">Hot 🔥</span></div>
                        <button class="product-card__wish" data-wish-id="6" data-wish-name="Sony WH-1000XM5">♥</button>
                        <div class="product-card__img">🎧</div>
                        <div class="product-card__actions">
                            <button class="btn btn-primary btn-sm"
                                onclick="Cart.add({id:6,name:'Sony WH-1000XM5',price:8490000,img:'🎧'})">+ Giỏ
                                hàng</button>
                        </div>
                    </div>
                    <div class="product-card__body">
                        <div class="product-card__brand">Sony</div>
                        <div class="product-card__name"><a href="{{ url('chi-tiet?id=6') }}">Sony WH-1000XM5</a></div>
                        <div class="product-card__price"><span class="product-card__price-current">8.490.000₫</span><span
                                class="product-card__price-old">9.990.000₫</span></div>
                    </div>
                </div>
            </div>
            <div class="text-center mt-32">
                <a href="{{ url('san-pham') }}" class="btn btn-outline btn-lg">Xem tất cả sản phẩm →</a>
            </div>
        </div>
    </section>

    <section class="section" style="background:var(--surface)">
        <div class="container">
            <div class="section-header section-header--center">
                <div class="section-header__eyebrow">Cam Kết</div>
                <h2 class="display-2 section-header__title">Tại sao chọn <span class="text-accent">Nexus?</span></h2>
            </div>
            <div class="why-grid">
                <div class="why-card">
                    <div class="why-icon">🛡️</div>
                    <div class="why-title">100% Chính Hãng</div>
                    <div class="why-desc">Tất cả sản phẩm có tem chính hãng, hóa đơn VAT.</div>
                </div>
                <div class="why-card">
                    <div class="why-icon">🚀</div>
                    <div class="why-title">Giao Hàng Nhanh</div>
                    <div class="why-desc">Giao trong ngày tại TP.HCM & Hà Nội.</div>
                </div>
                <div class="why-card">
                    <div class="why-icon">🔄</div>
                    <div class="why-title">Đổi Trả 7 Ngày</div>
                    <div class="why-desc">Đổi trả dễ dàng trong 7 ngày nếu lỗi.</div>
                </div>
                <div class="why-card">
                    <div class="why-icon">💬</div>
                    <div class="why-title">Hỗ Trợ 24/7</div>
                    <div class="why-desc">Đội ngũ tư vấn online 24/7, luôn sẵn sàng.</div>
                </div>
            </div>
        </div>
    </section>

    <section class="section">
        <div class="container">
            <div class="section-header section-header--center">
                <div class="section-header__eyebrow">Đánh Giá</div>
                <h2 class="display-2 section-header__title">Khách hàng <span class="text-accent">nói gì</span></h2>
            </div>
            <div class="testi-grid">
                <div class="testi-card">
                    <div class="testi-stars">★★★★★</div>
                    <p class="testi-text">"MacBook Pro M3 giá tốt, giao hàng nhanh, seal nguyên hộp."</p>
                    <div class="testi-user">
                        <div class="testi-avatar">NA</div>
                        <div>
                            <div class="testi-name">Ngọc Anh</div>
                            <div class="testi-job">Designer</div>
                        </div>
                    </div>
                </div>
                <div class="testi-card">
                    <div class="testi-stars">★★★★★</div>
                    <p class="testi-text">"iPhone 15 Pro Max về đúng hẹn, staff tư vấn nhiệt tình."</p>
                    <div class="testi-user">
                        <div class="testi-avatar">HD</div>
                        <div>
                            <div class="testi-name">Hữu Đức</div>
                            <div class="testi-job">Kỹ sư IT</div>
                        </div>
                    </div>
                </div>
                <div class="testi-card">
                    <div class="testi-stars">★★★★★</div>
                    <p class="testi-text">"Dịch vụ hậu mãi tốt, đổi máy mới ngay khi lỗi."</p>
                    <div class="testi-user">
                        <div class="testi-avatar">MQ</div>
                        <div>
                            <div class="testi-name">Minh Quân</div>
                            <div class="testi-job">Freelancer</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="section" style="padding-top:0">
        <div class="container">
            <div
                style="background:var(--ink);border-radius:var(--r-2xl);padding:64px;text-align:center;position:relative;overflow:hidden">
                <div
                    style="position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);font-size:400px;opacity:.03;pointer-events:none">
                    N</div>
                <div class="section-header__eyebrow"
                    style="color:rgba(255,255,255,.5);margin-bottom:16px;display:inline-flex">Nâng cấp ngay</div>
                <h2 class="display-2" style="color:#fff;margin-bottom:14px">Sẵn sàng nâng cấp <em
                        style="font-style:normal;color:var(--accent)">thiết bị?</em></h2>
                <p style="font-size:16px;color:rgba(255,255,255,.6);margin-bottom:36px">Hơn 5.000 sản phẩm công nghệ chính
                    hãng giá tốt nhất thị trường</p>
                <div class="d-flex gap-16" style="justify-content:center;flex-wrap:wrap">
                    <a href="{{ url('san-pham') }}" class="btn btn-accent btn-xl">Mua sắm ngay</a>
                    <a href="{{ url('lien-he') }}" class="btn btn-xl"
                        style="background:rgba(255,255,255,.1);color:#fff;border:1px solid rgba(255,255,255,.2)">Tư vấn
                        miễn phí</a>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
    <script>
        const flashEnd = new Date();
        flashEnd.setHours(flashEnd.getHours() + 5, 30);
        initCountdown(flashEnd, 'flashCountdown');
        initReveal();
        initWishBtns();
    </script>
@endpush
