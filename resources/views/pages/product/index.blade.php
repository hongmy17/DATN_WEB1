@extends('layouts.app')
@section('title', 'Sản phẩm — Nexus Store')
@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/pages/products.css') }}">
@endpush
@section('content')
    <div class="container shop-wrap">
        <div class="breadcrumb">
            <a href="{{ url('/') }}">Trang chủ</a>
            <span class="breadcrumb__sep">/</span>
            <span class="breadcrumb__current">Sản phẩm</span>
        </div>

        <div class="cat-chips">
            @php
                $allCats = ['Tất cả' => null] + $categories->pluck('name', 'id')->toArray();

                // Chip là kiểu chọn nhanh 1 danh mục — nhưng vẫn giữ nguyên sort/giá đang lọc.
                // Bấm chip khác sẽ THAY TOÀN BỘ lựa chọn danh mục hiện tại (kể cả từ checkbox sidebar)
                // bằng đúng 1 danh mục đó, giống hành vi thực tế của các trang thương mại điện tử.
                $chipUrl = fn ($catId) => route('products.index', array_filter([
                    'categories' => $catId ? [$catId] : null,
                    'sort'       => request('sort'),
                    'price_min'  => request('price_min'),
                    'price_max'  => request('price_max'),
                ], fn ($v) => $v !== null && $v !== ''));

                $isChipActive = fn ($catId) => $catId
                    ? (count($selectedCategories) === 1 && (int) $selectedCategories[0] === (int) $catId)
                    : empty($selectedCategories);
            @endphp
            @foreach ($allCats as $catId => $catName)
                <a class="cat-chip {{ $isChipActive($catId) ? 'active' : '' }}" href="{{ $chipUrl($catId) }}">
                    {{ $catName }}
                </a>
            @endforeach
        </div>

        <div class="shop-layout">
            {{-- FILTER SIDEBAR --}}
            <aside class="filter-card reveal">
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
                            {{-- min/max thật lấy từ giá cao nhất trong DB (xem ProductController), không hard-code --}}
                            <input type="range" class="range-input" id="rangeMin" min="0" max="{{ $sliderMax }}"
                                value="{{ $priceMin }}" step="100000" oninput="updateRange(event)">
                            <input type="range" class="range-input" id="rangeMax" min="0" max="{{ $sliderMax }}"
                                value="{{ $priceMax }}" step="100000" oninput="updateRange(event)">
                        </div>
                    </div>
                </div>

                <div class="filter-section">
                    <div class="filter-title">Danh mục</div>
                    @foreach ($categories as $cat)
                        <div class="filter-check">
                            <label>
                                <input type="checkbox" name="categories[]" value="{{ $cat->id }}"
                                    onchange="applyFilter()"
                                    {{ in_array((int) $cat->id, $selectedCategories, true) ? 'checked' : '' }}>
                                {{ $cat->name }}
                            </label>
                            <span class="filter-count">{{ $cat->products_count }}</span>
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

            {{-- MAIN --}}
            <div>
                <div class="toolbar">
                    <div class="toolbar-left">
                        <span class="toolbar-count">Hiển thị <strong>{{ $products->total() }}</strong> sản phẩm</span>
                    </div>
                    <select class="sort-select" onchange="applySort(this.value)">
                        <option value="">Mặc định</option>
                        <option value="price_asc" {{ request('sort') === 'price_asc' ? 'selected' : '' }}>Giá: thấp đến cao
                        </option>
                        <option value="price_desc" {{ request('sort') === 'price_desc' ? 'selected' : '' }}>Giá: cao đến
                            thấp</option>
                        <option value="newest" {{ request('sort') === 'newest' ? 'selected' : '' }}>Mới nhất</option>
                    </select>
                </div>

                <div class="products-grid" id="productsGrid">
                    @forelse ($products as $p)
                        @php
                            $minPrice = $p->variants->min('price');
                            $maxPrice = $p->variants->max('price');
                            $comparePrice = $p->variants->max('compare_price');
                            $discount =
                                $comparePrice && $comparePrice > $minPrice
                                    ? round((1 - $minPrice / $comparePrice) * 100)
                                    : 0;
                        @endphp
                        <div class="product-card reveal" data-cat="{{ $p->category_id }}"
                            data-price="{{ $minPrice }}">
                            <div class="product-card__thumb">
                                @if ($discount >= 5)
                                    <div class="product-card__badges">
                                        <span class="badge badge-sale">-{{ $discount }}%</span>
                                    </div>
                                @endif

                                <button class="product-card__wish" data-wish-id="{{ $p->id }}"
                                    data-wish-name="{{ $p->name }}"
                                    data-wish-price="{{ $p->defaultVariant?->price ?? 0 }}"
                                    data-wish-slug="{{ $p->slug }}" data-wish-img="{{ $p->thumbnail ?? '' }}"
                                    aria-label="Yêu thích">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none"
                                        stroke="currentColor" stroke-width="1.8" stroke-linecap="round">
                                        <path
                                            d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z" />
                                    </svg>
                                </button>

                                <div class="product-card__img"
                                    style="display:flex;align-items:center;justify-content:center">
                                    @if ($p->thumbnail)
                                        <img src="{{ asset('storage/' . $p->thumbnail) }}" alt="{{ $p->name }}"
                                            style="max-width:100%;max-height:100%;object-fit:contain">
                                    @else
                                        <svg width="68" height="68" viewBox="0 0 24 24" fill="none"
                                            stroke="currentColor" stroke-width=".7" stroke-linecap="round">
                                            <rect x="2" y="3" width="20" height="14" rx="2" />
                                            <line x1="8" y1="21" x2="16" y2="21" />
                                            <line x1="12" y1="17" x2="12" y2="21" />
                                        </svg>
                                    @endif
                                </div>

                                <div class="product-card__actions">
                                    <button class="btn btn-ghost"
                                        onclick="Cart.add({variant_id:{{ $p->variants->first()?->id ?? 0 }},id:{{ $p->id }},name:'{{ addslashes($p->name) }}',variant:'',price:{{ $minPrice }},img:'{{ $p->thumbnail ? asset('storage/' . $p->thumbnail) : '' }}'})">
                                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none"
                                            stroke="currentColor" stroke-width="2" stroke-linecap="round">
                                            <circle cx="9" cy="21" r="1" />
                                            <circle cx="20" cy="21" r="1" />
                                            <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6" />
                                        </svg>
                                        Thêm giỏ
                                    </button>
                                    <a href="{{ route('products.show', $p->slug) }}" class="btn btn-primary">Xem ngay</a>
                                </div>
                            </div>

                            <div class="product-card__body">
                                <div class="product-card__brand">{{ $p->category->name ?? '' }}</div>
                                <div class="product-card__name">
                                    <a href="{{ route('products.show', $p->slug) }}">{{ $p->name }}</a>
                                </div>
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
                                    <span class="product-card__count">5.0</span>
                                </div>
                                <div class="product-card__price">
                                    <span class="product-card__price-current">
                                        {{ number_format($minPrice, 0, ',', '.') }}₫
                                        @if ($minPrice != $maxPrice)
                                            — {{ number_format($maxPrice, 0, ',', '.') }}₫
                                        @endif
                                    </span>
                                    @if ($comparePrice && $comparePrice > $minPrice)
                                        <span
                                            class="product-card__price-old">{{ number_format($comparePrice, 0, ',', '.') }}₫</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @empty
                        <div style="grid-column:1/-1;text-align:center;padding:60px 0;color:var(--ink-3)">
                            Không có sản phẩm nào.
                        </div>
                    @endforelse
                </div>

                <div class="pagination">
                    <button class="page-btn" {{ $products->onFirstPage() ? 'disabled' : '' }}
                        onclick="window.location='{{ $products->previousPageUrl() }}'">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2" stroke-linecap="round">
                            <polyline points="15 18 9 12 15 6" />
                        </svg>
                    </button>
                    @for ($page = 1; $page <= $products->lastPage(); $page++)
                        <button class="page-btn {{ $products->currentPage() === $page ? 'active' : '' }}"
                            onclick="window.location='{{ $products->url($page) }}'">{{ $page }}</button>
                    @endfor
                    <button class="page-btn" {{ !$products->hasMorePages() ? 'disabled' : '' }}
                        onclick="window.location='{{ $products->nextPageUrl() }}'">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2" stroke-linecap="round">
                            <polyline points="9 18 15 12 9 6" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        // ── Thanh trượt khoảng giá ───────────────────────────────────────────
        function updateRange(e) {
            const minInput = document.getElementById('rangeMin');
            const maxInput = document.getElementById('rangeMax');
            const sliderMax = +minInput.max; // 2 input luôn cùng max, lấy 1 cái là đủ

            let min = +minInput.value;
            let max = +maxInput.value;

            // Không cho 2 thanh trượt vượt qua nhau — kẹp lại theo thanh vừa được kéo
            if (min > max) {
                if (e && e.target === maxInput) {
                    min = max;
                    minInput.value = min;
                } else {
                    max = min;
                    maxInput.value = max;
                }
            }

            document.getElementById('priceMin').textContent = min.toLocaleString('vi-VN') + '₫';
            document.getElementById('priceMax').textContent = max.toLocaleString('vi-VN') + '₫';

            const pct1 = sliderMax ? (min / sliderMax) * 100 : 0;
            const pct2 = sliderMax ? (max / sliderMax) * 100 : 100;
            document.getElementById('rangeFill').style.cssText = `left:${pct1}%;right:${100 - pct2}%`;
        }

        function resetPrice() {
            const sliderMax = +document.getElementById('rangeMin').max;
            document.getElementById('rangeMin').value = 0;
            document.getElementById('rangeMax').value = sliderMax;
            updateRange();
        }

        // ── Áp dụng bộ lọc: gộp danh mục (checkbox) + khoảng giá vào URL,
        //    giữ nguyên sort đang chọn, rồi điều hướng để server lọc thật ──
        function applyFilter() {
            const url = new URL(window.location.origin + window.location.pathname);

            const currentSort = new URLSearchParams(window.location.search).get('sort');
            if (currentSort) {
                url.searchParams.set('sort', currentSort);
            }

            document
                .querySelectorAll('.filter-check input[type="checkbox"]:checked')
                .forEach(cb => url.searchParams.append('categories[]', cb.value));

            url.searchParams.set('price_min', document.getElementById('rangeMin').value);
            url.searchParams.set('price_max', document.getElementById('rangeMax').value);

            window.location = url.toString();
        }

        // ── Sắp xếp: chỉ đổi tham số 'sort', giữ nguyên danh mục + khoảng giá đang lọc ──
        function applySort(v) {
            if (v) {
                const url = new URL(window.location.href);
                url.searchParams.set('sort', v);
                window.location = url.toString();
            }
        }

        Object.assign(window, {
            updateRange,
            resetPrice,
            applyFilter,
            applySort
        });
        updateRange();
    </script>
@endsection