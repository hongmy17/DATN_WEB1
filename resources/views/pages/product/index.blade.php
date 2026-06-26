@extends('layouts.app')
@section('title', 'Sản phẩm — Nexus Store')
@push('styles')
    <style>
        .shop-wrap {
            padding: 32px 0 80px;
        }

        .shop-layout {
            display: grid;
            grid-template-columns: 240px 1fr;
            gap: 24px;
            align-items: start;
        }

        .toolbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }

        .toolbar-left {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
        }

        .toolbar-count {
            font-size: 14px;
            color: var(--ink-3);
        }

        .toolbar-tag {
            display: flex;
            align-items: center;
            gap: 5px;
            padding: 4px 10px;
            background: var(--surface);
            border-radius: var(--r-full);
            font-size: 13px;
            color: var(--ink-2);
        }

        .toolbar-tag button {
            display: flex;
            color: var(--ink-muted);
            transition: var(--t);
        }

        .toolbar-tag button:hover {
            color: var(--red);
        }

        .sort-select {
            padding: 7px 32px 7px 12px;
            border: 1.5px solid var(--border);
            border-radius: var(--r-md);
            font-size: 13.5px;
            font-family: var(--font-body);
            color: var(--ink);
            background: var(--bg-alt);
            cursor: pointer;
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='11' height='11' viewBox='0 0 24 24' fill='none' stroke='%236B6459' stroke-width='2.5'%3E%3Cpolyline points='6 9 12 15 18 9'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 10px center;
        }

        .sort-select:focus {
            outline: none;
            border-color: var(--accent);
        }

        .products-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 16px;
        }

        .cat-chips {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
            margin-bottom: 24px;
        }

        .cat-chip {
            padding: 7px 14px;
            border-radius: var(--r-full);
            border: 1.5px solid var(--border);
            font-size: 13px;
            font-weight: 500;
            color: var(--ink-2);
            cursor: pointer;
            transition: var(--t);
            background: var(--bg-alt);
            white-space: nowrap;
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
@section('content')
    <div class="container shop-wrap">
        <div class="breadcrumb">
            <a href="{{ url('/') }}">Trang chủ</a>
            <span class="breadcrumb__sep">/</span>
            <span class="breadcrumb__current">Sản phẩm</span>
        </div>

        <div class="cat-chips">
            @php $cats = ['Tất cả','Laptop','Điện thoại','Máy tính bảng','Tai nghe','Smartwatch','Phụ kiện']; @endphp
            @foreach ($cats as $c)
                <div class="cat-chip {{ $loop->first ? 'active' : '' }}" onclick="filterCat(this,'{{ $c }}')">
                    {{ $c }}</div>
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
                            <span id="priceMin">0₫</span>
                            <span id="priceMax">100.000.000₫</span>
                        </div>
                        <div class="range-track">
                            <div class="range-fill" id="rangeFill"></div>
                            <input type="range" class="range-input" id="rangeMin" min="0" max="100000000"
                                value="0" step="1000000" oninput="updateRange()">
                            <input type="range" class="range-input" id="rangeMax" min="0" max="100000000"
                                value="100000000" step="1000000" oninput="updateRange()">
                        </div>
                    </div>
                </div>

                <div class="filter-section">
                    <div class="filter-title">Thương hiệu</div>
                    @php $brands = ['Apple'=>24,'Samsung'=>18,'Dell'=>12,'Sony'=>8,'Asus'=>10,'LG'=>6]; @endphp
                    @foreach ($brands as $b => $count)
                        <div class="filter-check">
                            <label><input type="checkbox" onchange="applyFilter()"> {{ $b }}</label>
                            <span class="filter-count">{{ $count }}</span>
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
                        <span class="toolbar-count" id="productCount">Hiển thị <strong>8</strong> sản phẩm</span>
                    </div>
                    <select class="sort-select" onchange="applySort(this.value)">
                        <option value="">Mặc định</option>
                        <option value="price_asc">Giá: thấp đến cao</option>
                        <option value="price_desc">Giá: cao đến thấp</option>
                        <option value="newest">Mới nhất</option>
                        <option value="rating">Đánh giá cao nhất</option>
                    </select>
                </div>

                <div class="products-grid" id="productsGrid">
                    @php
                        $products = [
                            [
                                'id' => 1,
                                'name' => 'MacBook Pro 14" M3 Pro',
                                'brand' => 'Apple',
                                'price' => 42990000,
                                'old' => 48490000,
                                'rating' => 4.9,
                                'reviews' => 6,
                                'badge' => '',
                            ]
                        ];
                    @endphp

                    @foreach ($products as $p)
                        <div class="product-card reveal">
                            <div class="product-card__thumb">
                                @if ($p['badge'])
                                    <div class="product-card__badges">
                                        <span
                                            class="badge {{ str_starts_with($p['badge'], '-') ? 'badge-sale' : ($p['badge'] === 'Mới' ? 'badge-new' : ($p['badge'] === 'Hot' ? 'badge-hot' : 'badge-best')) }}">{{ $p['badge'] }}</span>
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

                                <div class="product-card__img"
                                    style="display:flex;align-items:center;justify-content:center">
                                    <svg width="68" height="68" viewBox="0 0 24 24" fill="none"
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
                                        Thêm giỏ
                                    </button>
                                    <a href="{{ route('products.show') }}" class="btn btn-primary">Xem ngay</a>
                                </div>
                            </div>

                            <div class="product-card__body">
                                <div class="product-card__brand">{{ $p['brand'] }}</div>
                                <div class="product-card__name">
                                    <a href="{{ route('products.show') }}">{{ $p['name'] }}</a>
                                </div>
                                <div class="product-card__rating">
                                    <div class="product-card__stars">
                                        @for ($s = 1; $s <= 5; $s++)
                                            <svg width="12" height="12" viewBox="0 0 24 24"
                                                fill="{{ $s <= $p['rating'] ? '#F59E0B' : '#E5E3DE' }}"
                                                stroke="{{ $s <= $p['rating'] ? '#F59E0B' : '#E5E3DE' }}" stroke-width="1">
                                                <polygon
                                                    points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2" />
                                            </svg>
                                        @endfor
                                    </div>
                                    <span class="product-card__count">{{ $p['rating'] }} ({{ $p['reviews'] }})</span>
                                </div>
                                <div class="product-card__price">
                                    <span
                                        class="product-card__price-current">{{ number_format($p['price'], 0, ',', '.') }}₫</span>
                                    @if ($p['old'])
                                        <span
                                            class="product-card__price-old">{{ number_format($p['old'], 0, ',', '.') }}₫</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="pagination">
                    <button class="page-btn" disabled>
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2" stroke-linecap="round">
                            <polyline points="15 18 9 12 15 6" />
                        </svg>
                    </button>
                    <button class="page-btn active">1</button>
                    <button class="page-btn" onclick="Toast.show('Trang 2 đang phát triển','info')">2</button>
                    <button class="page-btn" onclick="Toast.show('Trang 3 đang phát triển','info')">3</button>
                    <button class="page-btn" onclick="Toast.show('Trang tiếp theo đang phát triển','info')">
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
        function filterCat(el, cat) {
            document.querySelectorAll('.cat-chip').forEach(c => c.classList.remove('active'));
            el.classList.add('active');
            Toast.show(`Đang lọc: ${cat}`, 'info');
        }

        function updateRange() {
            const min = +document.getElementById('rangeMin').value;
            const max = +document.getElementById('rangeMax').value;
            document.getElementById('priceMin').textContent = min.toLocaleString('vi-VN') + '₫';
            document.getElementById('priceMax').textContent = max.toLocaleString('vi-VN') + '₫';
            const pct1 = min / 100000000 * 100;
            const pct2 = max / 100000000 * 100;
            document.getElementById('rangeFill').style.cssText = `left:${pct1}%;right:${100-pct2}%`;
        }

        function resetPrice() {
            document.getElementById('rangeMin').value = 0;
            document.getElementById('rangeMax').value = 100000000;
            updateRange();
        }

        function applyFilter() {
            Toast.show('Đã áp dụng bộ lọc', 'success');
        }

        function applySort(v) {
            if (v) Toast.show('Đang sắp xếp...', 'info');
        }
        Object.assign(window, {
            filterCat,
            updateRange,
            resetPrice,
            applyFilter,
            applySort
        });
        updateRange();
    </script>
@endsection
