<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>@yield('title', 'Nexus Store — Công nghệ đỉnh cao')</title>
  <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
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
          <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 12 15 18 9"/></svg>
        </a>
        <div class="navbar__dropdown-menu">
          @php
          $cats = [
            ['slug'=>'laptop',  'label'=>'Laptop',        'path'=>'M2 3h20v14H2zM8 21h8M12 17v4'],
            ['slug'=>'phone',   'label'=>'Điện thoại',    'path'=>'M12 18h.01M8 21h8a1 1 0 0 0 1-1V4a1 1 0 0 0-1-1H8a1 1 0 0 0-1 1v16a1 1 0 0 0 1 1z'],
            ['slug'=>'tablet',  'label'=>'Máy tính bảng', 'path'=>'M18 3H6a1 1 0 0 0-1 1v16a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V4a1 1 0 0 0-1-1zM12 17h.01'],
            ['slug'=>'audio',   'label'=>'Tai nghe',      'path'=>'M3 18v-6a9 9 0 0 1 18 0v6M21 19a2 2 0 0 1-2 2h-1a2 2 0 0 1-2-2v-3a2 2 0 0 1 2-2h3zM3 19a2 2 0 0 0 2 2h1a2 2 0 0 0 2-2v-3a2 2 0 0 0-2-2H3z'],
            ['slug'=>'watch',   'label'=>'Smartwatch',    'path'=>'M12 12m-3 0a3 3 0 1 0 6 0 3 3 0 1 0-6 0M12 7V5M12 19v-2M7 12H5M19 12h-2'],
            ['slug'=>'accessory','label'=>'Phụ kiện',     'path'=>'M12 22V8M5 12H2a10 10 0 0 0 20 0h-3'],
          ];
          @endphp
          @foreach($cats as $cat)
          <a href="{{ url('san-pham?cat='.$cat['slug']) }}" class="navbar__dropdown-link">
            <svg class="navbar__dropdown-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round">
              <path d="{{ $cat['path'] }}"/>
            </svg>
            {{ $cat['label'] }}
          </a>
          @endforeach
        </div>
      </div>
      <a href="{{ url('khuyen-mai') }}" class="navbar__nav-link">Khuyến mãi</a>
      <a href="{{ url('lien-he') }}" class="navbar__nav-link">Liên hệ</a>
    </nav>

    <div class="navbar__search-wrap">
      <svg class="navbar__search-btn" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
        <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
      </svg>
      <input type="text" class="navbar__search-input" placeholder="Tìm sản phẩm...">
    </div>

    <div class="navbar__actions">
      <a href="{{ url('yeu-thich') }}" class="navbar__action-btn" title="Yêu thích">
        <svg width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
          <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/>
        </svg>
        <span class="navbar__badge js-wish-count" style="display:none">0</span>
      </a>
      <a href="{{ url('gio-hang') }}" class="navbar__action-btn" title="Giỏ hàng">
        <svg width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
          <circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/>
          <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/>
        </svg>
        <span class="navbar__badge js-cart-count" style="display:none">0</span>
      </a>

      @guest
        <a href="{{ route('login') }}" class="btn btn-ghost btn-sm">Đăng nhập</a>
        <a href="{{ route('register') }}" class="btn btn-primary btn-sm">Đăng ký</a>
      @else
        <div style="position:relative;display:flex;align-items:center;gap:8px">
          <a href="{{ route('profile.edit') }}" class="navbar__user-btn">
            <div class="navbar__user-avatar">{{ strtoupper(substr(Auth::user()->name,0,1)) }}</div>
            {{ Auth::user()->name }}
          </a>
          <form method="POST" action="{{ route('logout') }}" id="logout-form">
            @csrf
            <button type="button" class="btn btn-ghost btn-sm" onclick="handleLogout()">Đăng xuất</button>
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
</script>
<script src="{{ asset('assets/js/main.js') }}"></script>
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