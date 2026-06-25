@extends('layouts.app')
@section('title', 'Yêu thích — Nexus Store')
@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/pages/wishlist.css') }}">
@endpush
@section('content')
    <div class="container" style="padding:40px 0 80px">
        <div class="wish-head">
            <h1 class="h1">Sản phẩm yêu thích</h1>
            <button onclick="clearWishlist()" class="btn btn-ghost btn-sm">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                    stroke-linecap="round">
                    <polyline points="3 6 5 6 21 6" />
                    <path d="M19 6l-1 14H6L5 6" />
                </svg>
                Xoá tất cả
            </button>
        </div>

        <div class="wish-grid" id="wishGrid"></div>
        <div class="wish-empty" id="wishEmpty" style="display:none">
            <svg width="56" height="56" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1"
                stroke-linecap="round" style="color:var(--border);margin:0 auto 14px">
                <path
                    d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z" />
            </svg>
            <p style="font-size:15px;font-weight:600;margin-bottom:6px">Chưa có sản phẩm yêu thích</p>
            <p style="font-size:13.5px;margin-bottom:20px">Nhấn vào biểu tượng trái tim để lưu sản phẩm.</p>
            <a href="{{ url('san-pham') }}" class="btn btn-primary">Khám phá ngay</a>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const items = Wishlist.get(); // giờ là [{id,name,price,img}]
            const grid = document.getElementById('wishGrid');
            const empty = document.getElementById('wishEmpty');

            if (!items.length) {
                grid.style.display = 'none';
                empty.style.display = 'block';
                return;
            }

            grid.innerHTML = items.map(p => {
                const price = p.price ? Number(p.price).toLocaleString('vi-VN') + '₫' : '—';
                const name = p.name || ('Sản phẩm #' + p.id);
                return `
    <div class="product-card">
      <div class="product-card__thumb">
        <div class="product-card__img" style="display:flex;align-items:center;justify-content:center">
          <svg width="52" height="52" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round">
            <rect x="2" y="3" width="20" height="14" rx="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/>
          </svg>
        </div>
        <button class="product-card__wish active"
          data-wish-id="${p.id}"
          data-wish-name="${name}"
          data-wish-price="${p.price||0}"
          title="Xoá khỏi yêu thích">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="var(--red)" stroke="var(--red)" stroke-width="1.5" stroke-linecap="round">
            <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/>
          </svg>
        </button>
        <div class="product-card__actions">
          <button class="btn btn-ghost" onclick="Cart.add({id:${p.id},name:'${name.replace(/'/g,"\\'")}',price:${p.price||0},img:''})">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
              <circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/>
              <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/>
            </svg>
            Giỏ hàng
          </button>
          <a href="{{ route('products.show') }}" class="btn btn-primary">Xem</a>
        </div>
      </div>
      <div class="product-card__body">
        <div class="product-card__name">${name}</div>
        <div class="product-card__price">
          <span class="product-card__price-current">${price}</span>
        </div>
      </div>
    </div>`;
            }).join('');
        });

        function clearWishlist() {
            localStorage.removeItem('nx_wish');
            Wishlist.updateUI();
            location.reload();
        }
        window.clearWishlist = clearWishlist;
    </script>
@endsection
