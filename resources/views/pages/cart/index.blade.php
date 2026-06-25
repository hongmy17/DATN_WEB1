@extends('layouts.app')
@section('title', 'Giỏ hàng — Nexus Store')
@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/pages/cart.css') }}">
@endpush
@section('content')
    <div class="container" style="padding-top:40px">
        <div class="breadcrumb">
            <a href="{{ url('/') }}">Trang chủ</a>
            <span class="breadcrumb__sep">/</span>
            <span class="breadcrumb__current">Giỏ hàng</span>
        </div>

        <div class="cart-layout">
            {{-- CART ITEMS --}}
            <div>
                <div class="cart-section">
                    <div class="cart-head">
                        <span class="cart-head-title">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="1.8" stroke-linecap="round">
                                <circle cx="9" cy="21" r="1" />
                                <circle cx="20" cy="21" r="1" />
                                <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6" />
                            </svg>
                            Giỏ hàng của bạn
                        </span>
                        <button onclick="clearCart()"
                            style="font-size:13px;color:var(--ink-muted);display:flex;align-items:center;gap:5px;transition:var(--t)"
                            onmouseover="this.style.color='var(--red)'" onmouseout="this.style.color='var(--ink-muted)'">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2" stroke-linecap="round">
                                <polyline points="3 6 5 6 21 6" />
                                <path d="M19 6l-1 14H6L5 6" />
                                <path d="M10 11v6M14 11v6" />
                                <path d="M9 6V4h6v2" />
                            </svg>
                            Xoá tất cả
                        </button>
                    </div>
                    <div id="cartList">
                        <div class="cart-empty" id="cartEmpty">
                            <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="1" stroke-linecap="round">
                                <circle cx="9" cy="21" r="1" />
                                <circle cx="20" cy="21" r="1" />
                                <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6" />
                            </svg>
                            <p style="font-size:15px;font-weight:600;margin-bottom:6px">Giỏ hàng trống</p>
                            <p style="font-size:13.5px;margin-bottom:20px">Thêm sản phẩm vào giỏ để tiếp tục mua sắm.</p>
                            <a href="{{ url('san-pham') }}" class="btn btn-primary">Khám phá sản phẩm</a>
                        </div>
                    </div>
                </div>
            </div>

            {{-- SUMMARY --}}
            <div class="summary-section">
                <div style="font-size:16px;font-weight:600;margin-bottom:16px">Tóm tắt đơn hàng</div>

                <div class="summary-row">
                    <span style="color:var(--ink-3)">Tạm tính</span>
                    <span id="sumSubtotal" style="font-weight:600">0₫</span>
                </div>
                <div class="summary-row" id="discountRow" style="display:none">
                    <span style="color:var(--green)">Giảm giá</span>
                    <span style="color:var(--green);font-weight:600" id="sumDiscount">-0₫</span>
                </div>
                <div class="summary-row">
                    <span style="color:var(--ink-3)">Phí vận chuyển</span>
                    <span style="color:var(--green);font-weight:600">Miễn phí</span>
                </div>

                <div class="summary-row summary-row--total">
                    <span>Tổng cộng</span>
                    <span id="sumTotal">0₫</span>
                </div>

                <a href="{{ route('checkout.index') }}"
                    style="display:flex;align-items:center;gap:8px;padding:10px 14px;
          background:var(--accent-light);border:1.5px dashed var(--accent);
          border-radius:var(--r-md);font-size:13px;color:var(--accent);
          font-weight:500;text-decoration:none;transition:var(--t);margin-top:4px"
                    onmouseover="this.style.background='var(--accent)';this.style.color='#fff'"
                    onmouseout="this.style.background='var(--accent-light)';this.style.color='var(--accent)'">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2" stroke-linecap="round">
                        <path d="M20 12v6a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h6" />
                        <path d="M16 2l6 6-8 8H8v-6l8-8z" />
                    </svg>
                    Có mã giảm giá? Nhập ở bước thanh toán
                </a>

                <a href="{{ route('checkout.index') }}" id="checkoutBtn" class="btn btn-accent btn-full btn-lg"
                    style="gap:8px;margin-top:4px">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2" stroke-linecap="round">
                        <polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2" />
                    </svg>
                    Tiến hành đặt hàng
                </a>
                <a href="{{ url('san-pham') }}" class="btn btn-ghost btn-full mt-8" style="font-size:13px">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2" stroke-linecap="round">
                        <line x1="19" y1="12" x2="5" y2="12" />
                        <polyline points="12 19 5 12 12 5" />
                    </svg>
                    Tiếp tục mua sắm
                </a>

                <div class="trust-row">
                    <div class="trust-tag">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="var(--green)"
                            stroke-width="1.8" stroke-linecap="round">
                            <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z" />
                        </svg>
                        Bảo mật
                    </div>
                    <div class="trust-tag">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="var(--green)"
                            stroke-width="1.8" stroke-linecap="round">
                            <rect x="1" y="3" width="15" height="13" rx="1" />
                            <path d="M16 8h4l3 3v4h-7V8z" />
                            <circle cx="5.5" cy="18.5" r="2.5" />
                            <circle cx="18.5" cy="18.5" r="2.5" />
                        </svg>
                        Giao nhanh
                    </div>
                    <div class="trust-tag">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="var(--green)"
                            stroke-width="1.8" stroke-linecap="round">
                            <polyline points="1 4 1 10 7 10" />
                            <polyline points="23 20 23 14 17 14" />
                            <path d="M20.49 9A9 9 0 0 0 5.64 5.64L1 10m22 4l-4.64 4.36A9 9 0 0 1 3.51 15" />
                        </svg>
                        Đổi trả 30 ngày
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function fmtPrice(n) {
            return n.toLocaleString('vi-VN') + '₫';
        }

        function renderCart() {
            const items = Cart.get();
            const list = document.getElementById('cartList');
            const empty = document.getElementById('cartEmpty');

            if (!items.length) {
                empty.style.display = 'block';
                document.getElementById('sumSubtotal').textContent = '0₫';
                document.getElementById('sumTotal').textContent = '0₫';
                return;
            }
            empty.style.display = 'none';

            const rows = items.map(i => `
    <div class="cart-item" data-key="${i.id}_${i.variant||''}">
      <div class="cart-item__img">
        <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round"><rect x="2" y="3" width="20" height="14" rx="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/></svg>
      </div>
      <div>
        <div class="cart-item__name">${i.name}</div>
        <div class="cart-item__variant">${i.variant||'Mặc định'}</div>
        <div class="qty-ctrl">
          <button class="qty-ctrl__btn" onclick="changeItemQty('${i.id}','${i.variant||''}',${i.qty-1})">
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="5" y1="12" x2="19" y2="12"/></svg>
          </button>
          <input type="number" class="qty-ctrl__input" value="${i.qty}" min="1" max="99" onchange="changeItemQty('${i.id}','${i.variant||''}',+this.value)">
          <button class="qty-ctrl__btn" onclick="changeItemQty('${i.id}','${i.variant||''}',${i.qty+1})">
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
          </button>
        </div>
      </div>
      <div class="cart-item__right">
        <button class="cart-item__remove" onclick="removeItem('${i.id}','${i.variant||''}')" title="Xoá">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
        </button>
        <div class="cart-item__price">${fmtPrice(i.price*i.qty)}</div>
      </div>
    </div>
  `).join('');
            list.innerHTML = rows + '<div id="cartEmpty" style="display:none"></div>';

            const sub = Cart.total();
            document.getElementById('sumSubtotal').textContent = fmtPrice(sub);
            document.getElementById('sumTotal').textContent = fmtPrice(sub);
        }

        function removeItem(id, variant) {
            Cart.remove(id, variant);
            renderCart();
        }

        function changeItemQty(id, variant, qty) {
            Cart.updateQty(id, variant, qty);
            renderCart();
        }

        function clearCart() {
            if (!confirm('Xoá tất cả sản phẩm trong giỏ hàng?')) return;
            localStorage.removeItem('nx_cart');
            Cart.updateUI();
            renderCart();
        }


        document.addEventListener('DOMContentLoaded', renderCart);
        Object.assign(window, {
            removeItem,
            changeItemQty,
            clearCart,
        });
    </script>
@endsection
