<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Nexus Store — Công Nghệ Đỉnh Cao')</title>
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    @stack('styles')
</head>

<body>

    {{-- NAVBAR --}}
    <nav class="navbar">
        <div class="navbar__inner">
            <a href="{{ url('/') }}" class="navbar__logo">
                <span class="navbar__logo-main">Nexus</span>
                <span class="navbar__logo-accent">Store</span>
            </a>
            <nav class="navbar__nav">
                <a href="{{ url('/') }}" class="navbar__nav-link">Trang Chủ</a>
                <div class="navbar__dropdown">
                    <a href="{{ url('san-pham') }}" class="navbar__nav-link">Sản Phẩm ▾</a>
                    <div class="navbar__dropdown-menu">
                        <a href="{{ url('san-pham?cat=laptop') }}" class="navbar__dropdown-link"> <i
                                class="fa-solid fa-laptop"></i> Laptop
                            Laptop</a>
                        <a href="{{ url('san-pham?cat=phone') }}" class="navbar__dropdown-link"> <i
                                class="fa-solid fa-mobile-screen-button"></i> Điện Thoại
                            Thoại</a>
                        <a href="{{ url('san-pham?cat=tablet') }}" class="navbar__dropdown-link"> <i
                                class="fa-solid fa-tablet-screen-button"></i> Máy Tính Bảng
                            Bảng</a>
                        <a href="{{ url('san-pham?cat=audio') }}" class="navbar__dropdown-link"> <i
                                class="fa-solid fa-headphones"></i> Tai Nghe
                            Nghe</a>
                        <a href="{{ url('san-pham?cat=watch') }}" class="navbar__dropdown-link"> <i
                                class="fa-solid fa-clock"></i> Smartwatch
                            Smartwatch</a>
                        <a href="{{ url('san-pham?cat=accessory') }}" class="navbar__dropdown-link"> <i
                                class="fa-solid fa-plug"></i> Phụ Kiện
                            Kiện</a>
                    </div>
                </div>
                <a href="{{ url('khuyen-mai') }}" class="navbar__nav-link">Khuyến Mãi</a>
                <a href="{{ url('lien-he') }}" class="navbar__nav-link">Liên Hệ</a>
            </nav>
            <div class="navbar__search-wrap">
                <button class="navbar__search-btn">
                    <i class="fa-solid fa-magnifying-glass"></i>
                </button>
                <input type="text" class="navbar__search-input" placeholder="Tìm kiếm sản phẩm...">
            </div>
            <div class="navbar__actions">
                <a href="{{ url('yeu-thich') }}" class="navbar__action-btn" title="Yêu thích">
                    <i class="fa-solid fa-heart"></i>
                    <span class="navbar__badge js-wish-count" style="display:none">0</span>
                </a>
                <a href="{{ url('gio-hang') }}" class="navbar__action-btn" title="Giỏ hàng">
                    <i class="fa-solid fa-cart-shopping"></i>
                    <span class="navbar__badge js-cart-count" style="display:none">0</span>
                </a>

                @guest
                    <a href="{{ route('login') }}" class="btn btn-ghost btn-sm">Đăng nhập</a>
                    <a href="{{ route('register') }}" class="btn btn-primary btn-sm">Đăng ký</a>
                @else
                    <div class="navbar__user-wrap" style="position:relative; display:flex; align-items:center; gap:8px;">
                        <a href="{{ route('profile.edit') }}" class="navbar__user-btn">
                            <div class="navbar__user-avatar">{{ strtoupper(substr(Auth::user()->name, 0, 1)) }}</div>
                            {{ Auth::user()->name }}
                        </a>

                        {{-- Nút đăng xuất --}}
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="btn btn-ghost btn-sm">Đăng xuất</button>
                        </form>
                    </div>
                @endguest
            </div>
            <button class="navbar__hamburger" aria-label="Menu">
                <span></span><span></span><span></span>
            </button>
        </div>
    </nav>

    <div class="pt-nav"></div>

    <main>
        @yield('content')
    </main>

    {{-- FOOTER --}}
    <footer class="footer">
        <div class="container">
            <div class="footer__grid">
                <div>
                    <div class="footer__logo"><em class="footer__logo-icon" style="font-style:normal">N</em> Nexus Store
                    </div>
                    <p class="footer__desc">Chuyên cung cấp thiết bị công nghệ chính hãng. Bảo hành 12 tháng, giao hàng
                        toàn quốc, hỗ trợ 24/7.</p>
                    <div class="footer__socials">
                        <a href="#" class="footer__social"><i class="fa-brands fa-facebook"></i></a>
                        <a href="#" class="footer__social"><i class="fa-brands fa-instagram"></i></a>
                        <a href="#" class="footer__social"><i class="fa-brands fa-youtube"></i></a>
                    </div>
                </div>
                <div>
                    <div class="footer__col-title">Sản phẩm</div>
                    <div class="footer__links">
                        <a href="{{ url('san-pham?cat=laptop') }}" class="footer__link">Laptop</a>
                        <a href="{{ url('san-pham?cat=phone') }}" class="footer__link">Điện thoại</a>
                        <a href="{{ url('san-pham?cat=tablet') }}" class="footer__link">Máy tính bảng</a>
                        <a href="{{ url('san-pham?cat=audio') }}" class="footer__link">Tai nghe</a>
                        <a href="{{ url('san-pham?cat=watch') }}" class="footer__link">Smartwatch</a>
                    </div>
                </div>
                <div>
                    <div class="footer__col-title">Hỗ trợ</div>
                    <div class="footer__links">
                        <a href="{{ url('lien-he') }}" class="footer__link">Liên hệ</a>
                        <a href="{{ url('chinh-sach') }}" class="footer__link">Chính sách bảo hành</a>
                        <a href="{{ url('chinh-sach') }}" class="footer__link">Chính sách đổi trả</a>
                        <a href="{{ url('don-hang') }}" class="footer__link">Tra cứu đơn hàng</a>
                    </div>
                </div>
                <div>
                    <div class="footer__col-title">Nhận ưu đãi</div>
                    <p class="footer__newsletter-label">Đăng ký để nhận thông tin khuyến mãi mới nhất</p>
                    <div class="footer__newsletter-form">
                        <input type="email" class="footer__newsletter-input" placeholder="Email của bạn">
                        <button class="btn btn-accent btn-sm"
                            onclick="Toast.show('Đăng ký thành công!','success')">Đăng ký</button>
                    </div>
                    <div style="margin-top:16px">
                        <div class="footer__col-title" style="margin-bottom:10px">Liên hệ</div>
                        <div class="footer__links">
                            <span class="footer__link"><i class="fa-solid fa-phone"></i> 1900 1234</span>
                            <span class="footer__link"><i class="fa-solid fa-envelope"></i> support@nexus.vn</span>
                            <span class="footer__link"><i class="fa-solid fa-location-dot"></i> 123 Lê Lợi, Q.1,
                                TP.HCM</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="footer__bottom">
                <span class="footer__copy">© {{ date('Y') }} Nexus Store. All rights reserved.</span>
                <div class="footer__payments">
                    <span class="footer__payment">VISA</span>
                    <span class="footer__payment">MC</span>
                    <span class="footer__payment">MOMO</span>
                    <span class="footer__payment">VNPAY</span>
                    <span class="footer__payment">COD</span>
                </div>
            </div>
        </div>
    </footer>

    <script src="{{ asset('assets/js/main.js') }}"></script>
    @stack('scripts')

</body>

</html>
