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



        {{-- ── PROMO CARDS ─────────────────────── --}}
        {{--
        Mỗi card có:
        - Màu nền đồng bộ với theme đỏ (#E30019) và các màu phụ
        - SVG icon thay hoàn toàn emoji (yêu cầu của thầy)
        - Hover effect scale nhẹ
    --}}


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
                                <svg width="68" height="68" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width=".7" stroke-linecap="round" style="color:#C5C3BC">
                                    <path
                                        d="{{ $p->category?->icon_path ?? 'M20 7H4a2 2 0 0 0-2 2v10a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2z' }}" />
                                </svg>
                            @endif
                        </div>
                        <div class="product-card__actions">
                            <button class="btn btn-ghost"
                                onclick="Cart.add({id:{{ $p->id }},variant_id:{{ $defVar?->id ?? 0 }},name:'{{ addslashes($p->name) }}',price:{{ $defVar?->current_price ?? ($defVar?->price ?? $minPrice) }},img:'{{ $p->thumbnail ?? '' }}',slug:'{{ $p->slug }}'})">
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2" stroke-linecap="round">
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
