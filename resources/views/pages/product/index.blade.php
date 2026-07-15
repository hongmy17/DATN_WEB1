@extends('layouts.app')
@section('title', 'Sản phẩm — Nexus Store')

@push('styles')
    <style>
        /* ── TRANG SẢN PHẨM ────────────────────────────────────────── */
        .shop-wrap {
            padding: 32px 0 80px;
        }

        /* ── CHIPS DANH MỤC CON ─────────────────────────────────────── */
        .cat-chips {
            display: flex;
            gap: 8px;
            flex-wrap: nowrap;
            overflow-x: auto;
            margin-bottom: 24px;
            padding-bottom: 4px;
            scrollbar-width: none;
        }

        .cat-chips::-webkit-scrollbar {
            display: none;
        }

        .cat-chip {
            padding: 7px 16px;
            border-radius: var(--r-full);
            border: 1.5px solid var(--border);
            font-size: 13px;
            font-weight: 500;
            color: var(--ink-2);
            white-space: nowrap;
            cursor: pointer;
            transition: var(--t);
            background: var(--bg-alt);
            text-decoration: none;
            flex-shrink: 0;
        }

        .cat-chip:hover {
            border-color: var(--ink);
            color: var(--ink);
        }

        .cat-chip.active {
            background: var(--ink);
            border-color: var(--ink);
            color: #fff;
        }

        /* ── LAYOUT CHÍNH ────────────────────────────────────────────── */
        .shop-layout {
            display: grid;
            grid-template-columns: 230px 1fr;
            gap: 24px;
            align-items: start;
        }

        /* ── SIDEBAR ─────────────────────────────────────────────────── */
        .filter-card {
            background: var(--bg-alt);
            border: 1px solid var(--border-soft);
            border-radius: var(--r-xl);
            padding: 20px;
            position: sticky;
            top: calc(var(--nav-h) + 16px);
        }

        .filter-section {
            margin-bottom: 20px;
        }

        .filter-section:last-of-type {
            margin-bottom: 0;
        }

        .filter-title {
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .06em;
            color: var(--ink-muted);
            margin-bottom: 14px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .filter-title button {
            font-size: 11px;
            color: var(--accent);
            font-weight: 500;
            background: none;
            border: none;
            cursor: pointer;
            text-transform: none;
            letter-spacing: 0;
        }

        /* Nhóm danh mục cha */
        .cat-group {
            margin-bottom: 8px;
        }

        .cat-group__head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-size: 13px;
            font-weight: 600;
            color: var(--ink);
            cursor: pointer;
            padding: 6px 0;
            user-select: none;
        }

        .cat-group__count {
            font-size: 11px;
            color: var(--ink-muted);
            font-weight: 400;
            background: var(--surface);
            padding: 1px 7px;
            border-radius: 99px;
            flex-shrink: 0;
        }

        .cat-group__chevron {
            transition: transform .2s;
            color: var(--ink-muted);
            flex-shrink: 0;
        }

        .cat-group__chevron.open {
            transform: rotate(90deg);
        }

        .cat-group__children {
            display: none;
            padding-left: 4px;
            margin-top: 4px;
        }

        .cat-group__children.open {
            display: block;
        }

        /* Checkbox con */
        .filter-check {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 5px 0;
        }

        .filter-check label {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 13px;
            color: var(--ink-2);
            cursor: pointer;
            flex: 1;
        }

        .filter-check input[type="checkbox"] {
            width: 15px;
            height: 15px;
            accent-color: var(--accent);
            cursor: pointer;
            flex-shrink: 0;
        }

        .filter-count {
            font-size: 11px;
            color: var(--ink-muted);
            background: var(--surface);
            padding: 1px 6px;
            border-radius: 99px;
            min-width: 20px;
            text-align: center;
        }

        /* Thanh khoảng giá */
        .price-range-wrap {
            margin-top: 8px;
        }

        .price-display {
            display: flex;
            justify-content: space-between;
            font-size: 13px;
            font-weight: 600;
            margin-bottom: 12px;
        }

        .range-track {
            position: relative;
            height: 4px;
            background: var(--border);
            border-radius: 2px;
        }

        .range-fill {
            position: absolute;
            top: 0;
            height: 100%;
            background: var(--accent);
            border-radius: 2px;
        }

        .range-input {
            position: absolute;
            width: 100%;
            height: 4px;
            background: transparent;
            appearance: none;
            pointer-events: none;
            top: 0;
        }

        .range-input::-webkit-slider-thumb {
            appearance: none;
            width: 18px;
            height: 18px;
            border-radius: 50%;
            background: var(--accent);
            border: 2px solid #fff;
            box-shadow: 0 1px 4px rgba(0, 0, 0, .2);
            pointer-events: all;
            cursor: pointer;
        }

        /* ── TOOLBAR ─────────────────────────────────────────────────── */
        .toolbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            margin-bottom: 16px;
            flex-wrap: wrap;
        }

        .toolbar-count {
            font-size: 13.5px;
            color: var(--ink-3);
        }

        .toolbar-count strong {
            color: var(--ink);
        }

        .sort-select {
            padding: 7px 32px 7px 12px;
            border: 1.5px solid var(--border);
            border-radius: var(--r-md);
            font-size: 13.5px;
            font-family: var(--font-body);
            color: var(--ink);
            background: var(--bg-alt) url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='11' height='11' viewBox='0 0 24 24' fill='none' stroke='%236B6459' stroke-width='2.5'%3E%3Cpolyline points='6 9 12 15 18 9'/%3E%3C/svg%3E") no-repeat right 10px center;
            appearance: none;
            cursor: pointer;
        }

        .sort-select:focus {
            outline: none;
            border-color: var(--accent);
        }

        /* ── PRODUCT GRID ────────────────────────────────────────────── */
        .products-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 16px;
        }

        /* ── PAGINATION ──────────────────────────────────────────────── */
        .pagination {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            margin-top: 36px;
        }

        .page-btn {
            width: 36px;
            height: 36px;
            border-radius: var(--r-md);
            border: 1.5px solid var(--border);
            background: var(--bg-alt);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 13.5px;
            font-weight: 500;
            color: var(--ink-2);
            cursor: pointer;
            transition: var(--t);
        }

        .page-btn:hover:not(:disabled) {
            border-color: var(--accent);
            color: var(--accent);
        }

        .page-btn.active {
            background: var(--accent);
            border-color: var(--accent);
            color: #fff;
        }

        .page-btn:disabled {
            opacity: .35;
            cursor: not-allowed;
        }

        /* ── RESPONSIVE ──────────────────────────────────────────────── */
        @media(max-width:1024px) {
            .shop-layout {
                grid-template-columns: 1fr;
            }

            .filter-card {
                display: none;
            }
        }

        @media(max-width:640px) {
            .products-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }
    </style>
@endpush
<link rel="stylesheet" href="{{ asset('assets/css/pages/products.css') }}">
@section('content')
    <div class="container shop-wrap">

        {{-- Breadcrumb --}}
        <div class="breadcrumb" style="margin-bottom:16px">
            <a href="{{ url('/') }}">Trang chủ</a>
            <span class="breadcrumb__sep">/</span>
            <span class="breadcrumb__current">Sản phẩm</span>
        </div>

        {{-- ── CHIPS DANH MỤC CON (lọc nhanh) ───────────────────── --}}
        {{--
        Chips hiển thị danh mục CON để người dùng lọc nhanh 1 nhóm cụ thể.
        Ví dụ: bấm "Tai nghe chống ồn" → chỉ hiện SP trong danh mục đó.
        Khác sidebar: sidebar nhóm theo danh mục CHA (Âm thanh, Ngoại vi...)
    --}}
        @php
            $chipUrl = fn($catId) => route(
                'products.index',
                array_filter(
                    [
                        'categories' => $catId ? [$catId] : null,
                        'sort' => request('sort'),
                        'price_min' => request('price_min') != 0 ? request('price_min') : null,
                        'price_max' => request('price_max') != $sliderMax ? request('price_max') : null,
                    ],
                    fn($v) => $v !== null && $v !== '',
                ),
            );

            $isChipActive = fn($catId) => $catId
                ? count($selectedCategories) === 1 && (int) $selectedCategories[0] === (int) $catId
                : empty($selectedCategories);
        @endphp

        <div class="cat-chips">
            {{-- Chip "Tất cả" --}}
            <a class="cat-chip {{ $isChipActive(null) ? 'active' : '' }}" href="{{ $chipUrl(null) }}">Tất cả</a>

            {{-- Chips danh mục con --}}
            @foreach ($categories as $cat)
                <a class="cat-chip {{ $isChipActive($cat->id) ? 'active' : '' }}" href="{{ $chipUrl($cat->id) }}">
                    {{ $cat->name }}
                    @if ($cat->products_count > 0)
                        <span style="opacity:.6;font-size:11px">({{ $cat->products_count }})</span>
                    @endif
                </a>
            @endforeach
        </div>

        <div class="shop-layout">

            {{-- ── SIDEBAR: Danh mục CHA + nhóm con ─────────────── --}}
            <aside class="filter-card reveal">

                {{-- Khoảng giá --}}
                <div class="filter-section">
                    <div class="filter-title">
                        Khoảng giá
                        <button onclick="resetPrice()">Đặt lại</button>
                    </div>
                    <div class="price-range-wrap">
                        <div class="price-display">
                            <span id="priceMin">{{ number_format($priceMin, 0, ',', '.') }}₫</span>
                            <span id="priceMax">{{ number_format($priceMax, 0, ',', '.') }}₫</span>
                        </div>
                        <div class="range-track">
                            <div class="range-fill" id="rangeFill"></div>
                            <input type="range" class="range-input" id="rangeMin" min="0"
                                max="{{ $sliderMax }}" value="{{ $priceMin }}" step="100000"
                                oninput="updateRange(event)">
                            <input type="range" class="range-input" id="rangeMax" min="0"
                                max="{{ $sliderMax }}" value="{{ $priceMax }}" step="100000"
                                oninput="updateRange(event)">
                        </div>
                    </div>
                </div>

                {{-- Danh mục theo nhóm cha → con ────────────────────
                 Giống FPT Shop: mỗi nhóm cha có thể expand/collapse
                 để hiện danh mục con bên trong
            --}}
                <div class="filter-section">
                    <div class="filter-title">Danh mục</div>

                    @foreach ($parentCategories as $parent)
                        @php
                            // Kiểm tra xem nhóm này có danh mục con nào đang được chọn không
                            $childIds = $parent->children->pluck('id')->toArray();
                            $hasSelected = count(array_intersect($childIds, $selectedCategories)) > 0;
                            $isOpen = $hasSelected; // tự mở nhóm nếu đang lọc
                        @endphp
                        <div class="cat-group">
                            {{-- Tiêu đề nhóm cha (click toggle) --}}
                            <div class="cat-group__head" onclick="toggleGroup(this)"
                                style="{{ $hasSelected ? 'color:var(--accent)' : '' }}">
                                <span>{{ $parent->name }}</span>
                                <div style="display:flex;align-items:center;gap:8px">
                                    @if ($parent->total_products > 0)
                                        <span class="cat-group__count">{{ $parent->total_products }}</span>
                                    @endif
                                    <svg class="cat-group__chevron {{ $isOpen ? 'open' : '' }}" width="13"
                                        height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                        stroke-width="2.5" stroke-linecap="round">
                                        <polyline points="9 18 15 12 9 6" />
                                    </svg>
                                </div>
                            </div>

                            {{-- Danh mục con --}}
                            <div class="cat-group__children {{ $isOpen ? 'open' : '' }}">
                                @foreach ($parent->children as $child)
                                    <div class="filter-check">
                                        <label>
                                            <input type="checkbox" name="categories[]" value="{{ $child->id }}"
                                                onchange="applyFilter()"
                                                {{ in_array((int) $child->id, $selectedCategories, true) ? 'checked' : '' }}>
                                            {{ $child->name }}
                                        </label>
                                        <span class="filter-count">{{ $child->products_count }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>

                <button onclick="applyFilter()" class="btn btn-accent btn-full">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2" stroke-linecap="round">
                        <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3" />
                    </svg>
                    Áp dụng bộ lọc
                </button>
            </aside>

            {{-- ── MAIN: Grid sản phẩm ────────────────────────────── --}}
            <div>
                {{-- Toolbar --}}
                <div class="toolbar">
                    <div>
                        <span class="toolbar-count">
                            Tìm thấy <strong>{{ $products->total() }}</strong> sản phẩm
                            @if (!empty($selectedCategories))
                                <span style="font-size:12px;color:var(--ink-muted)">
                                    (đang lọc)
                                    <a href="{{ route('products.index') }}" style="color:var(--accent);margin-left:4px">
                                        Xóa bộ lọc
                                    </a>
                                </span>
                            @endif
                        </span>
                    </div>
                    <select class="sort-select" onchange="applySort(this.value)">
                        <option value="">Mặc định</option>
                        <option value="price_asc" {{ request('sort') === 'price_asc' ? 'selected' : '' }}>Giá: thấp → cao
                        </option>
                        <option value="price_desc" {{ request('sort') === 'price_desc' ? 'selected' : '' }}>Giá: cao → thấp
                        </option>
                        <option value="newest" {{ request('sort') === 'newest' ? 'selected' : '' }}>Mới nhất</option>
                    </select>
                </div>

                {{-- Grid --}}
                <div class="products-grid" id="productsGrid">
                    @forelse($products as $p)
                        @php
                            // Variant mặc định — chỉ dùng để add-to-cart nhanh & ảnh, KHÔNG dùng để tính flash sale
                            $defaultV = $p->variants->firstWhere('is_default', true) ?? $p->variants->first();
                            $defVariant = $defaultV;
                            $thumbnail = $p->thumbnail ? asset('storage/' . $p->thumbnail) : null;

                            // Flash sale: xét TẤT CẢ biến thể, không riêng biến thể mặc định
                            // → set sale ở bất kỳ biến thể nào cũng phải hiện badge ở trang danh sách
                            $isFlash = $p->variants->contains(fn($v) => $v->is_sale_active);

                            // Giá hiển thị: current_price của từng biến thể đã tự tính sẵn sale (nếu có)
                            $minPrice = $p->variants->min(fn($v) => $v->current_price ?? $v->price) ?? 0;
                            $maxPrice = $p->variants->max(fn($v) => $v->current_price ?? $v->price) ?? 0;

                            // Giá gốc để gạch ngang: lấy theo biến thể đang có giá thấp nhất
                            // (nếu biến thể đó đang sale thì giá gốc = price trước giảm)
                            $cheapestVariant = $p->variants->sortBy(fn($v) => $v->current_price ?? $v->price)->first();
                            $comparePrice = $cheapestVariant?->is_sale_active
                                ? $cheapestVariant->price
                                : $p->variants->max('compare_price');

                            $discount =
                                $comparePrice && $comparePrice > $minPrice
                                    ? round((1 - $minPrice / $comparePrice) * 100)
                                    : 0;
                        @endphp

                        <div class="product-card reveal">
                            <div class="product-card__thumb">
                                {{-- Badge giảm giá / flash sale --}}

                                @if ($isFlash || $discount >= 5)
                                    <div class="product-card__badges">
                                        @if ($isFlash)
                                            <span class="badge badge-flash">
                                                <svg width="10" height="10" viewBox="0 0 24 24"
                                                    fill="currentColor">
                                                    <polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2" />
                                                </svg>
                                                Flash Sale
                                            </span>
                                        @elseif ($discount >= 5)
                                            <span class="badge badge-sale">-{{ $discount }}%</span>
                                        @endif
                                    </div>
                                @endif

                                {{-- Nút yêu thích --}}
                                <button class="product-card__wish" data-wish-id="{{ $p->id }}"
                                    data-wish-name="{{ addslashes($p->name) }}" data-wish-price="{{ $minPrice }}"
                                    data-wish-slug="{{ $p->slug }}" data-wish-img="{{ $p->thumbnail ?? '' }}"
                                    data-wish-variant-id="{{ $p->variants->first()?->id ?? '' }}" aria-label="Yêu thích">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none"
                                        stroke="currentColor" stroke-width="1.8" stroke-linecap="round">
                                        <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06
                                                         a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78
                                                         1.06-1.06a5.5 5.5 0 0 0 0-7.78z" />
                                    </svg>
                                </button>

                                {{-- Ảnh --}}
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

                                {{-- Actions --}}
                                <div class="product-card__actions">
                                    <button class="btn btn-ghost"
                                        onclick="Cart.add({
                                    variant_id: {{ $defVariant?->id ?? 0 }},
                                    id:         {{ $p->id }},
                                    name:       '{{ addslashes($p->name) }}',
                                    variant:    'Mặc định',
                                    price:      {{ $defVariant?->current_price ?? ($defVariant?->price ?? $minPrice) }},
                                    img:        '{{ $p->thumbnail ? asset('storage/' . $p->thumbnail) : '' }}'
                                })">
                                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none"
                                            stroke="currentColor" stroke-width="2" stroke-linecap="round">
                                            <circle cx="9" cy="21" r="1" />
                                            <circle cx="20" cy="21" r="1" />
                                            <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6" />
                                        </svg>
                                        Giỏ hàng
                                    </button>
                                    <a href="{{ route('products.show', $p->slug) }}" class="btn btn-primary">
                                        Xem ngay
                                    </a>
                                </div>
                            </div>

                            <div class="product-card__body">
                                {{-- Tên danh mục con làm "brand" --}}
                                <div class="product-card__brand">{{ $p->category?->name ?? '' }}</div>

                                <div class="product-card__name">
                                    <a href="{{ route('products.show', $p->slug) }}">{{ $p->name }}</a>
                                </div>

                                {{-- Rating (5 sao cố định khi chưa có review thật) --}}
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
                                @if (($p->sold_count ?? 0) > 0)
                                    <div style="font-size:11px;color:var(--ink-muted);margin-bottom:2px">
                                        Đã bán {{ number_format($p->sold_count) }}
                                    </div>
                                @endif
                                <div class="product-card__price">
                                    <span class="product-card__price-current">
                                        {{ number_format($minPrice, 0, ',', '.') }}₫
                                        @if ($minPrice !== $maxPrice)
                                            — {{ number_format($maxPrice, 0, ',', '.') }}₫
                                        @endif
                                    </span>
                                    @if ($comparePrice && $comparePrice > $minPrice)
                                        <span class="product-card__price-old">
                                            {{ number_format($comparePrice, 0, ',', '.') }}₫
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @empty
                        <div style="grid-column:1/-1;text-align:center;padding:80px 0;color:var(--ink-3)">
                            <svg width="56" height="56" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="1" stroke-linecap="round"
                                style="margin:0 auto 16px;display:block;color:var(--border)">
                                <circle cx="11" cy="11" r="8" />
                                <line x1="21" y1="21" x2="16.65" y2="16.65" />
                            </svg>
                            <p style="font-size:15px;font-weight:600;margin-bottom:6px">Không tìm thấy sản phẩm</p>
                            <p style="font-size:13.5px;margin-bottom:20px">Thử thay đổi bộ lọc hoặc từ khoá tìm kiếm.</p>
                            <a href="{{ route('products.index') }}" class="btn btn-outline">Xóa bộ lọc</a>
                        </div>
                    @endforelse
                </div>

                {{-- ── PHÂN TRANG ─────────────────────────────────── --}}
                @if ($products->lastPage() > 1)
                    <div class="pagination">
                        {{-- Nút lùi --}}
                        <button class="page-btn" {{ $products->onFirstPage() ? 'disabled' : '' }}
                            onclick="window.location='{{ $products->previousPageUrl() }}'">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2" stroke-linecap="round">
                                <polyline points="15 18 9 12 15 6" />
                            </svg>
                        </button>

                        {{-- Số trang (tối đa 7 nút, ẩn bớt nếu nhiều trang) --}}
                        @php
                            $cur = $products->currentPage();
                            $last = $products->lastPage();
                            $pages = [];
                            if ($last <= 7) {
                                $pages = range(1, $last);
                            } else {
                                $pages = array_unique(
                                    array_merge([1], range(max(2, $cur - 1), min($last - 1, $cur + 1)), [$last]),
                                );
                                sort($pages);
                            }
                        @endphp

                        @php $prev = null; @endphp
                        @foreach ($pages as $page)
                            @if ($prev !== null && $page - $prev > 1)
                                <span style="color:var(--ink-muted);align-self:center">...</span>
                            @endif
                            <button class="page-btn {{ $cur === $page ? 'active' : '' }}"
                                onclick="window.location='{{ $products->url($page) }}'">{{ $page }}</button>
                            @php $prev = $page; @endphp
                        @endforeach

                        {{-- Nút tiến --}}
                        <button class="page-btn" {{ !$products->hasMorePages() ? 'disabled' : '' }}
                            onclick="window.location='{{ $products->nextPageUrl() }}'">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2" stroke-linecap="round">
                                <polyline points="9 18 15 12 9 6" />
                            </svg>
                        </button>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <script>
        // ── Thanh kéo khoảng giá ─────────────────────────────────────
        function updateRange(e) {
            const min = document.getElementById('rangeMin');
            const max = document.getElementById('rangeMax');
            let lo = +min.value,
                hi = +max.value;
            if (lo > hi) {
                if (e?.target === max) {
                    lo = hi;
                    min.value = lo;
                } else {
                    hi = lo;
                    max.value = hi;
                }
            }
            document.getElementById('priceMin').textContent = lo.toLocaleString('vi-VN') + '₫';
            document.getElementById('priceMax').textContent = hi.toLocaleString('vi-VN') + '₫';
            const pct1 = (+min.max) ? (lo / +min.max) * 100 : 0;
            const pct2 = (+min.max) ? (hi / +min.max) * 100 : 100;
            document.getElementById('rangeFill').style.cssText = `left:${pct1}%;right:${100-pct2}%`;
        }

        function resetPrice() {
            const max = +document.getElementById('rangeMin').max;
            document.getElementById('rangeMin').value = 0;
            document.getElementById('rangeMax').value = max;
            updateRange();
        }

        // ── Toggle nhóm danh mục cha ─────────────────────────────────
        function toggleGroup(head) {
            const chevron = head.querySelector('.cat-group__chevron');
            const children = head.nextElementSibling;
            const open = children.classList.toggle('open');
            chevron.classList.toggle('open', open);
        }

        // ── Áp dụng bộ lọc ──────────────────────────────────────────
        function applyFilter() {
            const url = new URL(window.location.origin + window.location.pathname);
            const sort = new URLSearchParams(window.location.search).get('sort');
            if (sort) url.searchParams.set('sort', sort);
            document.querySelectorAll('.filter-check input:checked')
                .forEach(cb => url.searchParams.append('categories[]', cb.value));
            url.searchParams.set('price_min', document.getElementById('rangeMin').value);
            url.searchParams.set('price_max', document.getElementById('rangeMax').value);
            window.location = url.toString();
        }

        // ── Sắp xếp ─────────────────────────────────────────────────
        function applySort(v) {
            if (!v) return;
            const url = new URL(window.location.href);
            url.searchParams.set('sort', v);
            window.location = url.toString();
        }

        Object.assign(window, {
            updateRange,
            resetPrice,
            toggleGroup,
            applyFilter,
            applySort
        });
        updateRange();
    </script>
@endsection
