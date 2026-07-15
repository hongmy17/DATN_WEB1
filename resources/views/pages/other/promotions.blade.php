@extends('layouts.app')
@section('title', 'Khuyến mãi — Nexus Store')

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/pages/products.css') }}">
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



        {{-- ── HERO ─────────────────────────────── --}}
        <div class="promo-hero">
            <div class="promo-hero__glow promo-hero__glow--1"></div>
            <div class="promo-hero__glow promo-hero__glow--2"></div>
            <div class="promo-hero__content">
                <div class="promo-hero__badge">
                    <span
                        style="width:6px;height:6px;border-radius:50%;background:currentColor;display:inline-block"></span>
                    Ưu đãi có hạn
                </div>
                <h1 class="promo-hero__title">
                    Săn deal <span class="promo-hero__title-red">giảm sốc</span><br>mỗi ngày
                </h1>

                {{-- Đếm ngược thật — khớp đúng id với script cuối trang --}}
                <div class="promo-countdown">
                    <div class="promo-countdown__item">
                        <div class="promo-countdown__num" id="cd-h">00</div>
                        <div class="promo-countdown__label">Giờ</div>
                    </div>
                    <div class="promo-countdown__sep">:</div>
                    <div class="promo-countdown__item">
                        <div class="promo-countdown__num" id="cd-m">00</div>
                        <div class="promo-countdown__label">Phút</div>
                    </div>
                    <div class="promo-countdown__sep">:</div>
                    <div class="promo-countdown__item">
                        <div class="promo-countdown__num" id="cd-s">00</div>
                        <div class="promo-countdown__label">Giây</div>
                    </div>
                </div>

                <a href="#flash-sale" class="promo-hero__btn">
                    Xem ưu đãi ngay
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2.5" stroke-linecap="round">
                        <line x1="12" y1="5" x2="12" y2="19" />
                        <polyline points="19 12 12 19 5 12" />
                    </svg>
                </a>
            </div>
        </div>

        {{-- ── MÃ GIẢM GIÁ ──────────────────────── --}}
        @if ($coupons->isNotEmpty())
            <div style="margin-bottom:20px">
                <div class="section-eyebrow">Ưu đãi</div>
                <h2 class="section-title">Mã giảm giá nổi bật</h2>
            </div>
            <div class="coupon-grid">
                @foreach ($coupons as $coupon)
                    @php
                        $valueLabel =
                            $coupon->type === \App\Models\Coupon::TYPE_PERCENT
                                ? rtrim(rtrim(number_format($coupon->value, 0), '0'), '.') . '%'
                                : number_format($coupon->value, 0, ',', '.') . 'đ';
                    @endphp
                    <div class="coupon-card">
                        <div class="coupon-card__value">
                            {{ $valueLabel }}
                            <span style="font-size:9px;font-weight:600;opacity:.8;margin-top:2px">GIẢM</span>
                        </div>
                        <div class="coupon-card__body">
                            <div class="coupon-card__code">{{ $coupon->coupon_code }}</div>
                            <div class="coupon-card__cond">
                                @if ($coupon->min_order_value > 0)
                                    Đơn từ {{ number_format($coupon->min_order_value, 0, ',', '.') }}đ
                                @else
                                    Không giới hạn giá trị đơn
                                @endif
                            </div>
                            <button type="button" class="coupon-card__copy"
                                onclick="navigator.clipboard.writeText('{{ $coupon->coupon_code }}');Toast.show('Đã sao chép mã {{ $coupon->coupon_code }}','success')">
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2" stroke-linecap="round">
                                    <rect x="9" y="9" width="13" height="13" rx="2" />
                                    <path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1" />
                                </svg>
                                Sao chép mã
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

        {{-- ── DANH MỤC ƯU ĐÃI ──────────────────── --}}
        <div style="margin-bottom:20px">
            <div class="section-eyebrow">Khám phá</div>
            <h2 class="section-title">Danh mục ưu đãi</h2>
        </div>
        <div class="promo-grid">
            <a href="{{ route('products.index') }}?sort=price_asc" class="promo-card pc-1">
                <div class="promo-card__icon">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="1.8" stroke-linecap="round">
                        <polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2" />
                    </svg>
                </div>
                <div class="promo-card__title">Flash Sale</div>
                <div class="promo-card__desc">Giá sốc, số lượng có hạn</div>
                <span class="promo-card__link">Xem ngay →</span>
            </a>
            <a href="{{ route('products.index') }}" class="promo-card pc-2">
                <div class="promo-card__icon">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="1.8" stroke-linecap="round">
                        <circle cx="12" cy="12" r="10" />
                        <path d="M12 6v6l4 2" />
                    </svg>
                </div>
                <div class="promo-card__title">Ưu đãi hôm nay</div>
                <div class="promo-card__desc">Chỉ áp dụng trong ngày</div>
                <span class="promo-card__link">Xem ngay →</span>
            </a>
            <a href="{{ route('products.index') }}?sort=newest" class="promo-card pc-5">
                <div class="promo-card__icon">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="1.8" stroke-linecap="round">
                        <path d="M12 2l3 7h7l-5.5 4.5L18 21l-6-4-6 4 1.5-7.5L2 9h7z" />
                    </svg>
                </div>
                <div class="promo-card__title">Hàng mới về</div>
                <div class="promo-card__desc">Cập nhật sản phẩm mới nhất</div>
                <span class="promo-card__link">Xem ngay →</span>
            </a>
        </div>


        {{-- ── SẢN PHẨM FLASH SALE ─────────────── --}}
        <div id="flash-sale" style="margin-bottom:28px;scroll-margin-top:90px">
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
            {{--
                FIX: Chỉ hiện sản phẩm có ÍT NHẤT 1 biến thể đang flash sale thật sự
                (is_sale_active), không phải cứ có compare_price cao hơn price là hiện.
                Tốt nhất nên lọc việc này ở Controller bằng whereHas() để không tải
                thừa sản phẩm rồi mới lọc bỏ ở view — xem gợi ý cuối câu trả lời.
            --}}
            @php
                $saleProducts = $saleProducts
                    ->filter(fn($p) => $p->variants->contains(fn($v) => $v->is_sale_active))
                    ->values();
            @endphp

            @forelse($saleProducts as $p)
                @php
                    // Giá thấp nhất/cao nhất dựa trên current_price (đã tự áp sale nếu variant đang active)
                    $minPrice = $p->variants->min(fn($v) => $v->current_price ?? $v->price) ?? 0;
                    $maxPrice = $p->variants->max(fn($v) => $v->current_price ?? $v->price) ?? 0;

                    // Biến thể rẻ nhất — dùng để suy ra giá gốc gạch ngang cho đúng
                    $cheapestVariant = $p->variants->sortBy(fn($v) => $v->current_price ?? $v->price)->first();
                    $oldPrice = $cheapestVariant?->is_sale_active
                        ? $cheapestVariant->price
                        : $p->variants->max('compare_price');

                    $thumbnail = $p->thumbnail ? asset('storage/' . $p->thumbnail) : null;

                    // Biến thể mặc định để add-to-cart nhanh (không dùng để tính giá/giảm giá)
                    $defVar = $p->variants->firstWhere('is_default', true) ?? $p->variants->first();
                @endphp
                <div class="product-card reveal">
                    <div class="product-card__thumb">
                        <div class="product-card__badges">
                            <span class="badge badge-flash">
                                <svg width="10" height="10" viewBox="0 0 24 24" fill="currentColor">
                                    <polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2" />
                                </svg>
                                Flash Sale
                            </span>
                        </div>
                        <button class="product-card__wish" data-wish-id="{{ $p->id }}"
                            data-wish-name="{{ addslashes($p->name) }}" data-wish-price="{{ $minPrice }}"
                            data-wish-slug="{{ $p->slug }}" data-wish-img="{{ $p->thumbnail ?? '' }}"
                            aria-label="Yêu thích">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="1.8" stroke-linecap="round">
                                <path
                                    d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06 a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z" />
                            </svg>
                        </button>
                        <div class="product-card__img">
                            @if ($thumbnail)
                                <img src="{{ $thumbnail }}" alt="{{ $p->name }}"
                                    style="max-width:100%;max-height:100%;object-fit:contain"
                                    onerror="this.style.display='none'">
                            @else
                                <svg width="68" height="68" viewBox="0 0 24 24" fill="none"
                                    stroke="currentColor" stroke-width=".7" stroke-linecap="round"
                                    style="color:#C5C3BC">
                                    <path
                                        d="{{ $p->category?->icon_path ?? 'M20 7H4a2 2 0 0 0-2 2v10a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2z' }}" />
                                </svg>
                            @endif
                        </div>
                        <div class="product-card__actions">
                            <button class="btn btn-ghost"
                                onclick="Cart.add({id:{{ $p->id }},variant_id:{{ $defVar?->id ?? 0 }},name:'{{ addslashes($p->name) }}',price:{{ $defVar?->current_price ?? ($defVar?->price ?? $minPrice) }},img:'{{ $p->thumbnail ?? '' }}',slug:'{{ $p->slug }}'})">
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

                        @php
                            $avgRating = round($p->reviews_avg_rating ?? 0, 1);
                            $reviewCount = $p->reviews_count ?? 0;
                        @endphp
                        <div class="product-card__rating">
                            <div class="product-card__stars">
                                @for ($s = 1; $s <= 5; $s++)
                                    @php $filled = $s <= round($avgRating); @endphp
                                    <svg width="12" height="12" viewBox="0 0 24 24"
                                        fill="{{ $filled ? '#F59E0B' : '#E5E3DE' }}"
                                        stroke="{{ $filled ? '#F59E0B' : '#E5E3DE' }}" stroke-width="1">
                                        <polygon
                                            points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2" />
                                    </svg>
                                @endfor
                            </div>
                            <span class="product-card__count">
                                {{ $reviewCount > 0 ? number_format($avgRating, 1) . ' (' . $reviewCount . ')' : 'Chưa có đánh giá' }}
                            </span>
                        </div>

                        <div class="product-card__price">
                            <span class="product-card__price-current">
                                {{ number_format($minPrice, 0, ',', '.') }}₫
                                @if ($minPrice !== $maxPrice)
                                    — {{ number_format($maxPrice, 0, ',', '.') }}₫
                                @endif
                            </span>
                            @if ($oldPrice && $oldPrice > $minPrice)
                                <span class="product-card__price-old">{{ number_format($oldPrice, 0, ',', '.') }}₫</span>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div style="grid-column:1/-1;text-align:center;padding:60px;color:var(--ink-muted)">
                    <p style="font-size:15px;font-weight:600;margin-bottom:8px">Hiện chưa có sản phẩm giảm giá</p>
                    <a href="{{ route('products.index') }}" class="btn btn-primary">Xem tất cả sản phẩm</a>
                </div>
            @endforelse
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
