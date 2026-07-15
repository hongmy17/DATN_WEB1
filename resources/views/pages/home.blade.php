@extends('layouts.app')
@section('title', 'Nexus Store — Công nghệ đỉnh cao')
@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/pages/home.css') }}">
    <style>
        /* ── HERO v3 ─────────────────────────────── */
        .hero-v3 {
            background: linear-gradient(135deg, #0F172A 0%, #16213E 50%, #2563EB 100%);
            padding: 60px 0 0;
            overflow: hidden;
            position: relative;
        }

        .hero-v3__glow {
            position: absolute;
            border-radius: 50%;
            pointer-events: none;
            background: radial-gradient(circle, rgba(37, 99, 235, .18) 0%, transparent 70%);
        }

        .hero-v3__grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 48px;
            align-items: center;
            padding-bottom: 56px;
        }

        .hero-v3__badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: rgba(245, 158, 11, .15);
            border: 1px solid rgba(245, 158, 11, .3);
            color: #F59E0B;
            padding: 5px 14px;
            border-radius: 99px;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: .08em;
            text-transform: uppercase;
            margin-bottom: 20px;
        }

        .hero-v3__badge-dot {
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: #F59E0B;
            animation: pulse 2s ease-in-out infinite;
        }

        @keyframes pulse {

            0%,
            100% {
                opacity: 1;
                transform: scale(1)
            }

            50% {
                opacity: .5;
                transform: scale(.8)
            }
        }

        .hero-v3__title {
            font-family: var(--font-display);
            font-size: clamp(28px, 4vw, 52px);
            font-weight: 700;
            color: #fff;
            line-height: 1.1;
            letter-spacing: -1px;
            margin-bottom: 16px;
        }

        .hero-v3__title-red {
            color: #F59E0B;
        }

        .hero-v3__sub {
            font-size: 15px;
            color: rgba(255, 255, 255, .55);
            line-height: 1.75;
            margin-bottom: 28px;
            max-width: 420px;
        }

        .hero-v3__btns {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
        }

        .hero-v3__btn-primary {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: #F59E0B;
            color: #fff;
            padding: 13px 28px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            text-decoration: none;
            transition: .2s;
            border: none;
            cursor: pointer;
        }

        .hero-v3__btn-primary:hover {
            background: #D97706;
        }

        .hero-v3__btn-outline {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: rgba(255, 255, 255, .08);
            color: #fff;
            padding: 13px 28px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 500;
            text-decoration: none;
            transition: .2s;
            border: 1px solid rgba(255, 255, 255, .18);
        }

        .hero-v3__btn-outline:hover {
            background: rgba(255, 255, 255, .14);
        }

        .hero-v3__stats {
            display: flex;
            gap: 32px;
            margin-top: 40px;
            padding-top: 32px;
            border-top: 1px solid rgba(255, 255, 255, .08);
            flex-wrap: wrap;
        }

        .hero-v3__stat-num {
            font-size: 22px;
            font-weight: 700;
            color: #fff;
        }

        .hero-v3__stat-label {
            font-size: 12px;
            color: rgba(255, 255, 255, .4);
            margin-top: 2px;
        }

        /* Feature cards bên phải hero */
        .hero-v3__features {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
        }

        .hero-v3__feat {
            background: rgba(255, 255, 255, .06);
            border: 1px solid rgba(255, 255, 255, .09);
            border-radius: 12px;
            padding: 18px;
            backdrop-filter: blur(8px);
            transition: .2s;
        }

        .hero-v3__feat:hover {
            background: rgba(255, 255, 255, .1);
        }

        .hero-v3__feat-icon {
            width: 36px;
            height: 36px;
            border-radius: 8px;
            background: rgba(37, 99, 235, .18);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #60A5FA;
            margin-bottom: 10px;
        }

        .hero-v3__feat-title {
            font-size: 13px;
            font-weight: 600;
            color: #fff;
            margin-bottom: 3px;
        }

        .hero-v3__feat-sub {
            font-size: 11px;
            color: rgba(255, 255, 255, .45);
        }

        /* Promo strip */
        .promo-strip {
            background: #F59E0B;
            padding: 10px 0;
            margin-top: 0;
        }

        .promo-strip__inner {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 40px;
            flex-wrap: wrap;
        }

        .promo-strip__item {
            display: flex;
            align-items: center;
            gap: 7px;
            font-size: 13px;
            font-weight: 500;
            color: #fff;
            white-space: nowrap;
        }

        @media(max-width:900px) {
            .hero-v3__grid {
                grid-template-columns: 1fr;
            }

            .hero-v3__features {
                display: none;
            }
        }
    </style>
@endpush

@section('content')

    {{-- ── HERO ─────────────────────────────── --}}
    <section class="hero-v3">
        <div class="hero-v3__glow" style="width:600px;height:600px;top:-200px;right:-100px;"></div>
        <div class="hero-v3__glow" style="width:300px;height:300px;bottom:0;left:-50px;opacity:.6;"></div>

        <div class="container">
            <div class="hero-v3__grid">
                {{-- LEFT: Text --}}
                <div>
                    <div class="hero-v3__badge">
                        <span class="hero-v3__badge-dot"></span>
                        Công nghệ chính hãng
                    </div>

                    <h1 class="hero-v3__title">
                        Giá tốt nhất —<br>
                        <span class="hero-v3__title-red">Chất lượng đỉnh</span>
                    </h1>

                    <p class="hero-v3__sub">
                        Hàng nghìn sản phẩm Apple, Samsung, Sony chính hãng.
                        Bảo hành toàn quốc, giao nhanh 2H nội thành.
                    </p>

                    <div class="hero-v3__btns">
                        <a href="{{ url('san-pham') }}" class="hero-v3__btn-primary">
                            Mua sắm ngay
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2.5" stroke-linecap="round">
                                <line x1="5" y1="12" x2="19" y2="12" />
                                <polyline points="12 5 19 12 12 19" />
                            </svg>
                        </a>
                        <a href="{{ url('khuyen-mai') }}" class="hero-v3__btn-outline">
                            Xem khuyến mãi
                        </a>
                    </div>

                    <div class="hero-v3__stats">
                        @foreach ([['10K+', 'Sản phẩm'], ['50K+', 'Khách hàng'], ['99%', 'Hài lòng']] as $s)
                            <div>
                                <div class="hero-v3__stat-num">{{ $s[0] }}</div>
                                <div class="hero-v3__stat-label">{{ $s[1] }}</div>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- RIGHT: Feature cards (SVG thuần, không emoji) --}}
                <div class="hero-v3__features">
                    @php
                        $feats = [
                            [
                                'title' => 'Giao hàng nhanh',
                                'sub' => 'Nội thành 2 giờ',
                                'path' =>
                                    'M1 3h15v13H1zM16 8h4l3 3v4h-7V8zM5.5 21a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5zM18.5 21a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5z',
                            ],
                            [
                                'title' => 'Bảo hành chính hãng',
                                'sub' => '12–24 tháng',
                                'path' => 'M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z',
                            ],
                            [
                                'title' => 'Đổi trả dễ dàng',
                                'sub' => '30 ngày',
                                'path' => 'M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8 M3 3v5h5',
                            ],
                            [
                                'title' => 'Trả góp 0%',
                                'sub' => 'Qua thẻ tín dụng',
                                'path' => 'M1 4h22v16H1z M1 10h22',
                            ],
                        ];
                    @endphp

                    @foreach ($feats as $f)
                        <div class="hero-v3__feat">
                            <div class="hero-v3__feat-icon">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="1.8" stroke-linecap="round">
                                    <path d="{{ $f['path'] }}" />
                                </svg>
                            </div>
                            <div class="hero-v3__feat-title">{{ $f['title'] }}</div>
                            <div class="hero-v3__feat-sub">{{ $f['sub'] }}</div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Promo strip (SVG thay emoji) --}}
        <div class="promo-strip">
            <div class="container">
                <div class="promo-strip__inner">
                    @php $promos = [['Flash Sale mỗi ngày 12h', 'M13 2L3 14h9l-1 8 10-12h-9l1-8z'], ['Miễn phí ship từ 500K', 'M1 3h15v13H1zM16 8h4l3 3v4h-7V8z M5.5 21a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5zM18.5 21a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5z'], ['Quà tặng kèm chính hãng', 'M20 12v10H4V12 M22 7H2v5h20V7z M12 22V7 M12 7H7.5a2.5 2.5 0 0 1 0-5C11 2 12 7 12 7z M12 7h4.5a2.5 2.5 0 0 0 0-5C13 2 12 7 12 7z'], ['Bảo hành tận nơi', 'M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z M9 12l2 2 4-4']]; @endphp
                    @foreach ($promos as $p)
                        <div class="promo-strip__item">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2" stroke-linecap="round">
                                <path d="{{ $p[1] }}" />
                            </svg>
                            {{ $p[0] }}
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
                <svg width="4" height="4" viewBox="0 0 4 4" style="color:var(--border-soft);flex-shrink:0">
                    <circle cx="2" cy="2" r="2" fill="currentColor" />
                </svg>
            @endforeach
        </div>
    </div>

    {{-- ── CATEGORIES ──────────────────────── --}}
    <section class="section" style="padding-top:36px">
        <div class="container">
            <div class="section-header section-header-center reveal" style="text-align:center">
                <div class="section-eyebrow">Danh mục</div>
                <h2 class="section-title">Khám phá theo danh mục</h2>
            </div>

            <div class="cat-grid">
                @forelse($categories as $cat)
                    <a href="{{ route('products.index', ['categories' => [$cat->id]]) }}" class="cat-tile reveal">
                        <div class="cat-tile__icon">
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="1.7" stroke-linecap="round">
                                <path d="{{ $cat->icon_path }}" />
                            </svg>
                        </div>
                        <div class="cat-tile__name">{{ $cat->name }}</div>
                        <div class="cat-tile__count">
                            {{ $cat->products_count > 0 ? $cat->products_count . ' sản phẩm' : 'Đang cập nhật' }}
                        </div>
                    </a>
                @empty
                    <p style="grid-column:1/-1;text-align:center;color:var(--ink-muted)">Chưa có danh mục.</p>
                @endforelse
            </div>
        </div>
    </section>

    {{-- ── PROMO BANNERS ───────────────────── --}}
    <section class="section" style="padding-top:0">
        <div class="container">
            <div class="promo-grid">
                {{-- Card đen: sản phẩm BÁN CHẠY NHẤT (30 ngày) --}}
                @isset($topProduct)
                    <a href="{{ route('products.show', $topProduct->slug) }}" class="promo-card promo-card--dark"
                        style="text-decoration:none">
                        <div class="promo-card__body">
                            <div class="promo-card__eyebrow">
                                {{ ($topProduct->sold_count ?? 0) > 0 ? 'Bán chạy nhất' : $topProduct->category?->name ?? 'Nổi bật' }}
                            </div>
                            <h3 class="promo-card__title">{{ $topProduct->name }}</h3>
                            <span class="btn btn-accent" style="width:fit-content">Mua ngay</span>
                        </div>
                        <div class="promo-card__media">
                            @if ($topProduct->thumbnail)
                                <img src="{{ asset('storage/' . $topProduct->thumbnail) }}" alt="{{ $topProduct->name }}"
                                    onerror="this.style.display='none'">
                            @else
                                <svg width="120" height="120" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width=".5" stroke-linecap="round" style="color:rgba(255,255,255,.4)">
                                    <path
                                        d="{{ $topProduct->category?->icon_path ?? 'M20 7H4a2 2 0 0 0-2 2v10a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2z' }}" />
                                </svg>
                            @endif
                        </div>
                    </a>
                @else
                    <a href="{{ route('products.index') }}" class="promo-card promo-card--dark"
                        style="text-decoration:none">
                        <div class="promo-card__body">
                            <div class="promo-card__eyebrow">Khám phá ngay</div>
                            <h3 class="promo-card__title">Phụ kiện công nghệ<br>chính hãng giá tốt</h3>
                            <span class="btn btn-accent" style="width:fit-content">Xem sản phẩm</span>
                        </div>
                    </a>
                @endisset

                {{-- Card cam: sản phẩm FLASH SALE giảm % SÂU NHẤT đang thật sự active --}}
                @isset($flashSaleProduct)
                    <a href="{{ route('products.show', $flashSaleProduct->slug) }}" class="promo-card promo-card--accent"
                        style="text-decoration:none">
                        <div class="promo-card__body">
                            <div class="promo-card__eyebrow">Flash Sale hôm nay</div>
                            <h3 class="promo-card__title">Giảm
                                {{ $flashSaleDiscountPercent }}%<br>{{ $flashSaleProduct->name }}</h3>
                            <span class="btn promo-card__cta-white" style="width:fit-content">Xem ngay</span>
                        </div>
                        <div class="promo-card__media">
                            @if ($flashSaleProduct->thumbnail)
                                <img src="{{ asset('storage/' . $flashSaleProduct->thumbnail) }}"
                                    alt="{{ $flashSaleProduct->name }}" onerror="this.style.display='none'">
                            @else
                                <svg width="120" height="120" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width=".5" stroke-linecap="round" style="color:rgba(255,255,255,.45)">
                                    <path
                                        d="{{ $flashSaleProduct->category?->icon_path ?? 'M20 7H4a2 2 0 0 0-2 2v10a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2z' }}" />
                                </svg>
                            @endif
                        </div>
                    </a>
                @else
                    <a href="{{ url('khuyen-mai') }}" class="promo-card promo-card--accent" style="text-decoration:none">
                        <div class="promo-card__body">
                            <div class="promo-card__eyebrow">Flash Sale hôm nay</div>
                            <h3 class="promo-card__title">Ưu đãi mỗi ngày<br>đừng bỏ lỡ</h3>
                            <span class="btn promo-card__cta-white" style="width:fit-content">Xem ngay</span>
                        </div>
                    </a>
                @endisset
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
                    <button class="tab-pill active" onclick="switchTab(this,'all')" data-cat="all">
                        Tất cả
                    </button>
                    @if (isset($tabCategories) && $tabCategories->isNotEmpty())
                        @foreach ($tabCategories as $tc)
                            @php $childIds = $tc->children->pluck('id')->implode(','); @endphp
                            <button class="tab-pill" onclick="switchTab(this,'{{ $childIds }}')"
                                data-cat="{{ $childIds }}">
                                {{ $tc->name }}
                                @if ($tc->products_count > 0)
                                    <span style="font-size:11px;opacity:.6">({{ $tc->products_count }})</span>
                                @endif
                            </button>
                        @endforeach
                    @endif
                </div>
            </div>

            <div class="grid-4">
                @forelse($featuredProducts as $p)
                    @php
                        $minPrice = $p->variants->min('price') ?? 0;
                        $oldPrice = $p->variants->max('compare_price');
                        $discount = $oldPrice && $oldPrice > $minPrice ? round((1 - $minPrice / $oldPrice) * 100) : 0;
                        $thumbnail = $p->thumbnail ? asset('storage/' . $p->thumbnail) : null;
                        $defVar = $p->variants->first();
                    @endphp
                    <div class="product-card reveal" data-cat="{{ $p->category_id }}">
                        <div class="product-card__thumb">
                            @if ($discount >= 5)
                                <div class="product-card__badges">
                                    <span class="badge badge-sale">-{{ $discount }}%</span>
                                </div>
                            @endif
                            <button class="product-card__wish" data-wish-id="{{ $p->id }}"
                                data-wish-name="{{ addslashes($p->name) }}" data-wish-price="{{ $minPrice }}"
                                data-wish-slug="{{ $p->slug }}" data-wish-img="{{ $p->thumbnail ?? '' }}"
                                aria-label="Yêu thích">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none"
                                    stroke="currentColor" stroke-width="1.8" stroke-linecap="round">
                                    <path
                                        d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06 a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z" />
                                </svg>
                            </button>
                            <div class="product-card__img" style="display:flex;align-items:center;justify-content:center">
                                @if ($thumbnail)
                                    <img src="{{ $thumbnail }}" alt="{{ $p->name }}"
                                        style="width:100%;height:100%;object-fit:contain;padding:12px"
                                        onerror="this.style.display='none'">
                                @else
                                    <svg width="72" height="72" viewBox="0 0 24 24" fill="none"
                                        stroke="currentColor" stroke-width=".7" stroke-linecap="round"
                                        style="color:#C5C3BC">
                                        <rect x="2" y="3" width="20" height="14" rx="2" />
                                        <line x1="8" y1="21" x2="16" y2="21" />
                                        <line x1="12" y1="17" x2="12" y2="21" />
                                    </svg>
                                @endif
                            </div>
                            <div class="product-card__actions">
                                <button class="btn btn-ghost"
                                    onclick="Cart.add({id:{{ $defVar?->id ?? $p->id }},name:'{{ addslashes($p->name) }}',price:{{ $minPrice }},img:'{{ $p->thumbnail ?? '' }}',slug:'{{ $p->slug }}'})">
                                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none"
                                        stroke="currentColor" stroke-width="2" stroke-linecap="round">
                                        <circle cx="9" cy="21" r="1" />
                                        <circle cx="20" cy="21" r="1" />
                                        <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6" />
                                    </svg>
                                    Giỏ hàng
                                </button>
                                <a href="{{ route('products.show', $p->slug) }}" class="btn btn-primary">Xem ngay</a>
                            </div>
                        </div>
                        <div class="product-card__body">
                            <div class="product-card__brand">{{ $p->category?->name ?? '' }}</div>
                            <div class="product-card__name"><a
                                    href="{{ route('products.show', $p->slug) }}">{{ $p->name }}</a></div>
                            <div class="product-card__price">
                                <span
                                    class="product-card__price-current">{{ number_format($minPrice, 0, ',', '.') }}₫</span>
                                @if ($oldPrice && $oldPrice > $minPrice)
                                    <span
                                        class="product-card__price-old">{{ number_format($oldPrice, 0, ',', '.') }}₫</span>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <p style="grid-column:1/-1;text-align:center;color:var(--ink-muted)">Chưa có sản phẩm.</p>
                @endforelse
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

    {{-- ── FEATURE STRIP ─────────────────────── --}}
    <section class="section" style="padding-top:0">
        <div class="container">
            <div class="feature-strip reveal">
                @php $features = [['path' => 'M1 3h15v13H1zM16 8h4l3 3v4h-7V8zM5.5 21a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5zM18.5 21a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5z', 'title' => 'Giao hàng toàn quốc', 'sub' => 'Miễn phí cho đơn từ 500K'], ['path' => 'M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z', 'title' => 'Bảo hành chính hãng', 'sub' => '12–24 tháng tùy sản phẩm'], ['path' => 'M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8 M3 3v5h5', 'title' => 'Đổi trả dễ dàng', 'sub' => '30 ngày không cần lý do'], ['path' => 'M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07A19.5 19.5 0 0 1 4.07 12a19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 3 1.18h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L7.09 8.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7a2 2 0 0 1 1.72 2.03z', 'title' => 'Hỗ trợ 24/7', 'sub' => 'Tư vấn & chăm sóc tận tâm']]; @endphp
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
    <section class="section" style="background:var(--surface);padding-top:56px;padding-bottom:56px">
        <div class="container">
            <div class="section-header section-header-center reveal" style="text-align:center">
                <div class="section-eyebrow">Khách hàng</div>
                <h2 class="section-title">Được tin dùng bởi hàng chục nghìn khách hàng</h2>
            </div>
            <div class="testimonials-grid">
                @php
                    // FIX: Review thật từ DB (được truyền từ home route)
                    // Fallback về dữ liệu mẫu phù hợp web bán phụ kiện nếu chưa có review thật
                    $reviewList =
                        isset($reviews) && $reviews->count() > 0
                            ? $reviews->map(
                                fn($r) => [
                                    'name' => $r->user?->name ?? 'Khách hàng',
                                    'role' => 'Khách hàng Nexus Store',
                                    'stars' => $r->rating,
                                    'text' => $r->comment,
                                ],
                            )
                            : collect([
                                [
                                    'name' => 'Nguyễn Thị Lan',
                                    'role' => 'Kỹ sư phần mềm',
                                    'stars' => 5,
                                    'text' =>
                                        'Mua Sony WH-1000XM5 tại Nexus, seal nguyên vẹn, giao siêu nhanh. Âm thanh tuyệt vời, chống ồn cực tốt. Tư vấn nhiệt tình. Sẽ ủng hộ dài dài!',
                                ],
                                [
                                    'name' => 'Trần Văn Minh',
                                    'role' => 'Nhà thiết kế đồ họa',
                                    'stars' => 5,
                                    'text' =>
                                        'Logitech MX Master 3S chính hãng, giá tốt hơn thị trường. Chuột cực mượt, click yên tĩnh, pin trâu 70 ngày. Đóng gói kỹ, giao đúng hẹn!',
                                ],
                                [
                                    'name' => 'Lê Thị Hương',
                                    'role' => 'Giáo viên',
                                    'stars' => 5,
                                    'text' =>
                                        'AirPods Pro 2 mua tại đây chính hãng Apple, hộp seal đầy đủ. Website chuyên nghiệp, đặt dễ, giao trong ngày. Chống ồn rất tốt khi dạy online!',
                                ],
                            ]);
                @endphp
                @foreach ($reviewList as $r)
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

    <script>
        function switchTab(btn, catIds) {
            // Cập nhật active tab
            document.querySelectorAll('.tab-pill').forEach(b => b.classList.remove('active'));
            btn.classList.add('active');

            const cards = document.querySelectorAll('.product-card[data-cat]');

            if (!catIds || catIds === 'all') {
                cards.forEach(c => c.style.display = '');
                return;
            }

            // FIX: parse childIds thành Set số nguyên để so sánh chính xác
            const allowedIds = new Set(
                catIds.split(',').map(id => parseInt(id.trim())).filter(Boolean)
            );

            cards.forEach(card => {
                // data-cat trên card là số nguyên (category_id của danh mục con)
                const cardCatId = parseInt(card.dataset.cat);
                card.style.display = allowedIds.has(cardCatId) ? '' : 'none';
            });

            // Nếu lọc xong không có SP nào → hiện tất cả
            const anyVisible = [...cards].some(c => c.style.display !== 'none');
            if (!anyVisible) {
                cards.forEach(c => c.style.display = '');
            }
        }
        window.switchTab = switchTab;
        window.filterProducts = switchTab;
    </script>
@endsection
