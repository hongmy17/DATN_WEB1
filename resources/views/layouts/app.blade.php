<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Nexus Store — Công nghệ đỉnh cao')</title>
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
    <style>
        /* ── Gợi ý tìm kiếm trực tiếp (autocomplete) ─────────────────────── */
        .navbar__search-suggest {
            position: absolute;
            top: calc(100% + 8px);
            left: 0;
            right: 0;
            background: #fff;
            border: 1px solid #e8e6e1;
            border-radius: 12px;
            box-shadow: 0 16px 32px rgba(0, 0, 0, .14);
            max-height: 380px;
            overflow-y: auto;
            z-index: 60;
        }

        .navbar__search-item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 8px 12px;
            text-decoration: none;
            color: inherit;
        }

        .navbar__search-item:hover,
        .navbar__search-item.is-active {
            background: #f6f5f2;
        }

        .navbar__search-item img {
            width: 38px;
            height: 38px;
            object-fit: contain;
            border-radius: 8px;
            background: #fafaf8;
            flex-shrink: 0;
        }

        .navbar__search-item-name {
            font-size: 13px;
            font-weight: 500;
            line-height: 1.3;
        }

        .navbar__search-item-price {
            font-size: 12px;
            color: #d9432e;
            margin-top: 2px;
        }

        .navbar__search-empty,
        .navbar__search-loading {
            padding: 14px 12px;
            font-size: 13px;
            color: #999;
            text-align: center;
        }
    </style>
    @stack('styles')
</head>

<body>

    <nav class="navbar" id="navbar">
        <div class="navbar__inner">
            <a href="{{ url('/') }}" class="navbar__logo">
                Nexus<span class="navbar__logo-accent">.</span>
            </a>

            <nav class="navbar__nav">
                <a href="{{ url('/') }}" class="navbar__nav-link">Trang chủ</a>
                <div class="navbar__dropdown">
                    <a href="{{ url('san-pham') }}" class="navbar__nav-link">
                        Sản phẩm
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2.5">
                            <polyline points="6 9 12 15 18 9" />
                        </svg>
                    </a>
                    <div class="navbar__dropdown-menu">
                        {{-- FIX: Dropdown từ DB (danh mục cha thật) --}}
                        @php
                            $navCategories = \App\Models\Category::whereNull('parent_id')->orderBy('name')->get();
                        @endphp
                        @foreach ($navCategories as $navCat)
                            <a href="{{ route('products.index', ['categories' => [$navCat->id]]) }}"
                                class="navbar__dropdown-link">
                                <svg class="navbar__dropdown-icon" viewBox="0 0 24 24" fill="none"
                                    stroke="currentColor" stroke-width="1.7" stroke-linecap="round">
                                    <path d="{{ $navCat->icon_path }}" />
                                </svg>
                                {{ $navCat->name }}
                            </a>
                        @endforeach
                    </div>
                </div>
                <a href="{{ url('khuyen-mai') }}" class="navbar__nav-link">Khuyến mãi</a>
                <a href="{{ url('lien-he') }}" class="navbar__nav-link">Liên hệ</a>
                <div class="navbar__dropdown">
                    <a href="#" class="navbar__nav-link">
                        Hỗ trợ
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2.5">
                            <polyline points="6 9 12 15 18 9" />
                        </svg>
                    </a>
                    <div class="navbar__dropdown-menu">
                        @foreach ([['slug' => 'mua-hang', 'label' => 'Hướng dẫn mua hàng', 'path' => 'M9 11l3 3L22 4 M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11'], ['slug' => 'doi-tra', 'label' => 'Chính sách đổi trả', 'path' => 'M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8 M3 3v5h5'], ['slug' => 'bao-hanh', 'label' => 'Chính sách bảo hành', 'path' => 'M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z'], ['slug' => 'van-chuyen', 'label' => 'Chính sách vận chuyển', 'path' => 'M1 3h15v13H1zM16 8h4l3 3v4h-7V8z M5.5 21a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5zM18.5 21a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5z']] as $p)
                            <a href="{{ route('policy', $p['slug']) }}" class="navbar__dropdown-link">
                                <svg class="navbar__dropdown-icon" viewBox="0 0 24 24" fill="none"
                                    stroke="currentColor" stroke-width="1.7" stroke-linecap="round">
                                    <path d="{{ $p['path'] }}" />
                                </svg>
                                {{ $p['label'] }}
                            </a>
                        @endforeach
                    </div>
                </div>
            </nav>

            <form class="navbar__search-wrap" id="navSearchForm" action="{{ route('products.index') }}" method="GET"
                role="search" style="position:relative">
                <button type="submit" aria-label="Tìm kiếm"
                    style="background:none;border:none;padding:0;display:flex;align-items:center;cursor:pointer">
                    <svg class="navbar__search-btn" width="16" height="16" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round">
                        <circle cx="11" cy="11" r="8" />
                        <line x1="21" y1="21" x2="16.65" y2="16.65" />
                    </svg>
                </button>
                {{-- name="q" khớp đúng với $request->q mà ProductController@index đang đọc.
           value=request('q') để ô search "nhớ" từ khóa vừa tìm khi đang ở trang kết quả. --}}
                <input type="text" name="q" id="navSearchInput" class="navbar__search-input"
                    placeholder="Tìm sản phẩm..." value="{{ request('q') }}" autocomplete="off">

                {{-- Dropdown gợi ý — JS tự đổ nội dung vào đây, mặc định ẩn --}}
                <div id="navSearchSuggest" class="navbar__search-suggest" style="display:none"></div>
            </form>

            <div class="navbar__actions">
                <a href="{{ url('yeu-thich') }}" class="navbar__action-btn" title="Yêu thích">
                    <svg width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                        <path
                            d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z" />
                    </svg>
                    <span class="navbar__badge js-wish-count" style="display:none">0</span>
                </a>
                <a href="{{ url('gio-hang') }}" class="navbar__action-btn" title="Giỏ hàng">
                    <svg width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="9" cy="21" r="1" />
                        <circle cx="20" cy="21" r="1" />
                        <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6" />
                    </svg>
                    <span class="navbar__badge js-cart-count" style="display:none">0</span>
                </a>

                @guest
                    <a href="{{ route('login') }}" class="btn btn-ghost btn-sm">Đăng nhập</a>
                    <a href="{{ route('register') }}" class="btn btn-primary btn-sm">Đăng ký</a>
                @else
                    <div style="position:relative;display:flex;align-items:center;gap:8px">
                        <a href="{{ route('profile.edit') }}" class="navbar__user-btn">
                            <div class="navbar__user-avatar">{{ strtoupper(substr(Auth::user()->name, 0, 1)) }}</div>
                            {{ Auth::user()->name }}
                        </a>
                        <form method="POST" action="{{ route('logout') }}" id="logout-form">
                            @csrf
                            <button type="button" class="btn btn-ghost btn-sm" onclick="handleLogout()">Đăng
                                xuất</button>
                        </form>
                    </div>
                @endguest
            </div>

            <button class="navbar__hamburger" onclick="document.body.classList.toggle('nav-open')" aria-label="Menu">
                <span></span><span></span><span></span>
            </button>
        </div>
    </nav>

    <main class="pt-nav">
        @yield('content')
    </main>

    @include('layouts.footer')

    <div class="toast-wrap" id="toastWrap"></div>
    <script>
        window.__authUser = @json(auth()->check() ? ['id' => auth()->id()] : null);
        // FIX: sau khi thanh toán VNPay thành công, server đã xóa giỏ hàng trong DB
        // (CartItem). Phải xóa luôn bản sao trong localStorage ở đây — TRƯỚC khi
        // main.js chạy Cart.syncToServer() — nếu không, vì localStorage vẫn còn hàng,
        // syncToServer() sẽ đẩy ngược các sản phẩm đó lên server và "hồi sinh" giỏ hàng.
        @if (session('clear_cart'))
            localStorage.removeItem('nx_cart');
        @endif
    </script>
    <script src="{{ asset('assets/js/main.js') }}"></script>
    @if (session('success') || session('error'))
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                @if (session('success'))
                    Toast.show(@json(session('success')), 'success', 4500);
                @endif
                @if (session('error'))
                    Toast.show(@json(session('error')), 'error', 4500);
                @endif
            });
        </script>
    @endif
    <script>
        (function() {
            const form = document.getElementById('navSearchForm');
            const input = document.getElementById('navSearchInput');
            const box = document.getElementById('navSearchSuggest');
            if (!form || !input || !box) return;

            const SUGGEST_URL = "{{ route('products.suggest') }}";
            const DEBOUNCE_MS = 250;

            let debounceTimer = null;
            let controller = null; // để hủy request cũ khi gõ nhanh
            let items = [];
            let activeIndex = -1;

            function esc(str) {
                return (str ?? '').replace(/[&<>"']/g, m => ({
                    '&': '&amp;',
                    '<': '&lt;',
                    '>': '&gt;',
                    '"': '&quot;',
                    "'": '&#39;'
                } [m]));
            }

            function money(v) {
                return Math.round(v || 0).toLocaleString('vi-VN') + '₫';
            }

            function highlight() {
                box.querySelectorAll('.navbar__search-item').forEach((el, i) => {
                    el.classList.toggle('is-active', i === activeIndex);
                });
            }

            function render(products) {
                items = products || [];
                activeIndex = -1;

                if (!items.length) {
                    box.innerHTML = '<div class="navbar__search-empty">Không tìm thấy sản phẩm phù hợp</div>';
                    box.style.display = 'block';
                    return;
                }

                box.innerHTML = items.map((p, i) => `
      <a href="${p.url}" class="navbar__search-item" data-index="${i}">
        <img src="${esc(p.thumbnail || '')}" onerror="this.style.visibility='hidden'" alt="">
        <div>
          <div class="navbar__search-item-name">${esc(p.name)}</div>
          <div class="navbar__search-item-price">${money(p.price)}</div>
        </div>
      </a>
    `).join('');

                box.style.display = 'block';
            }

            function close() {
                box.style.display = 'none';
                box.innerHTML = '';
                activeIndex = -1;
            }

            function fetchSuggestions(keyword) {
                if (controller) controller.abort(); // hủy request trước đó nếu còn đang chạy
                controller = new AbortController();

                box.innerHTML = '<div class="navbar__search-loading">Đang tìm…</div>';
                box.style.display = 'block';

                fetch(SUGGEST_URL + '?q=' + encodeURIComponent(keyword), {
                        signal: controller.signal
                    })
                    .then(res => res.json())
                    .then(render)
                    .catch(err => {
                        if (err.name !== 'AbortError') console.error(err);
                    });
            }

            // Gõ tới đâu, gợi ý tới đó — không cần bấm Enter
            input.addEventListener('input', function() {
                const keyword = this.value.trim();
                clearTimeout(debounceTimer);

                if (!keyword) {
                    close();
                    return;
                }

                debounceTimer = setTimeout(() => fetchSuggestions(keyword), DEBOUNCE_MS);
            });

            // Điều hướng bằng bàn phím: ↑ ↓ chọn dòng, Enter vào sản phẩm đang chọn, Esc đóng
            input.addEventListener('keydown', function(e) {
                if (box.style.display !== 'block' || !items.length) return;

                if (e.key === 'ArrowDown') {
                    e.preventDefault();
                    activeIndex = (activeIndex + 1) % items.length;
                    highlight();
                } else if (e.key === 'ArrowUp') {
                    e.preventDefault();
                    activeIndex = (activeIndex - 1 + items.length) % items.length;
                    highlight();
                } else if (e.key === 'Enter' && activeIndex > -1) {
                    e.preventDefault();
                    window.location = items[activeIndex].url;
                } else if (e.key === 'Escape') {
                    close();
                }
            });

            // Focus lại ô search mà vẫn còn kết quả cũ → mở lại dropdown luôn, khỏi gõ lại
            input.addEventListener('focus', function() {
                if (this.value.trim() && items.length) box.style.display = 'block';
            });

            // Bấm ra ngoài form thì đóng dropdown
            document.addEventListener('click', function(e) {
                if (!form.contains(e.target)) close();
            });
        })();
    </script>
    @stack('scripts')
    @auth
        <script>
            function handleLogout() {
                Cart.clearLocal();
                localStorage.removeItem('nx_wish');
                document.getElementById('logout-form').submit();
            }
        </script>
    @endauth
</body>

</html>
