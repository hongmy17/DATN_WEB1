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
                                <circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/>
                                <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/>
                            </svg>
                            Giỏ hàng của bạn
                        </span>
                        <button onclick="showClearModal()"
                            style="font-size:13px;color:var(--ink-muted);display:flex;align-items:center;gap:5px;transition:var(--t)"
                            onmouseover="this.style.color='var(--red)'" onmouseout="this.style.color='var(--ink-muted)'">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2" stroke-linecap="round">
                                <polyline points="3 6 5 6 21 6"/>
                                <path d="M19 6l-1 14H6L5 6"/>
                                <path d="M10 11v6M14 11v6M9 6V4h6v2"/>
                            </svg>
                            Xoá tất cả
                        </button>
                    </div>
                    {{-- Nội dung giỏ hàng render bằng JS --}}
                    <div id="cartList"></div>
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
                        <path d="M20 12v6a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h6"/>
                        <path d="M16 2l6 6-8 8H8v-6l8-8z"/>
                    </svg>
                    Có mã giảm giá? Nhập ở bước thanh toán
                </a>
                <a href="{{ route('checkout.index') }}" id="checkoutBtn" class="btn btn-accent btn-full btn-lg"
                    style="gap:8px;margin-top:4px">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2" stroke-linecap="round">
                        <polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/>
                    </svg>
                    Tiến hành đặt hàng
                </a>
                <a href="{{ url('san-pham') }}" class="btn btn-ghost btn-full mt-8" style="font-size:13px">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2" stroke-linecap="round">
                        <line x1="19" y1="12" x2="5" y2="12"/>
                        <polyline points="12 19 5 12 12 5"/>
                    </svg>
                    Tiếp tục mua sắm
                </a>

                <div class="trust-row">
                    <div class="trust-tag">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="var(--green)" stroke-width="1.8" stroke-linecap="round">
                            <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                        </svg>Bảo mật
                    </div>
                    <div class="trust-tag">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="var(--green)" stroke-width="1.8" stroke-linecap="round">
                            <rect x="1" y="3" width="15" height="13" rx="1"/>
                            <path d="M16 8h4l3 3v4h-7V8z"/>
                            <circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/>
                        </svg>Giao nhanh
                    </div>
                    <div class="trust-tag">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="var(--green)" stroke-width="1.8" stroke-linecap="round">
                            <polyline points="1 4 1 10 7 10"/><polyline points="23 20 23 14 17 14"/>
                            <path d="M20.49 9A9 9 0 0 0 5.64 5.64L1 10m22 4l-4.64 4.36A9 9 0 0 1 3.51 15"/>
                        </svg>Đổi trả 30 ngày
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL XÁC NHẬN XOÁ TẤT CẢ --}}
    <div id="clearModal" style="display:none;position:fixed;inset:0;z-index:9999;
        background:rgba(0,0,0,.45);backdrop-filter:blur(4px);
        align-items:center;justify-content:center;">
        <div style="background:var(--surface,#fff);border-radius:16px;padding:32px 28px;
            max-width:380px;width:90%;box-shadow:0 20px 60px rgba(0,0,0,.18);animation:modalIn .2s ease;">
            <div style="width:56px;height:56px;border-radius:50%;background:#fff1f1;
                display:flex;align-items:center;justify-content:center;margin:0 auto 18px;">
                <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="#ef4444" stroke-width="2" stroke-linecap="round">
                    <polyline points="3 6 5 6 21 6"/>
                    <path d="M19 6l-1 14H6L5 6M10 11v6M14 11v6M9 6V4h6v2"/>
                </svg>
            </div>
            <p style="font-size:17px;font-weight:700;text-align:center;margin-bottom:8px">Xoá tất cả sản phẩm?</p>
            <p style="font-size:13.5px;text-align:center;color:var(--ink-3,#888);margin-bottom:24px;line-height:1.5">
                Toàn bộ sản phẩm sẽ bị xoá khỏi giỏ hàng.
            </p>
            <div style="display:flex;gap:10px;">
                <button onclick="document.getElementById('clearModal').style.display='none'"
                    style="flex:1;padding:10px;border-radius:8px;font-size:14px;font-weight:500;
                    border:1.5px solid var(--border,#e5e7eb);background:transparent;cursor:pointer;">
                    Huỷ
                </button>
                <button onclick="confirmClear()"
                    style="flex:1;padding:10px;border-radius:8px;font-size:14px;font-weight:600;
                    border:none;background:#ef4444;color:#fff;cursor:pointer;">
                    Xoá tất cả
                </button>
            </div>
        </div>
    </div>
    <style>
        @keyframes modalIn {
            from { opacity:0; transform:scale(.94) translateY(8px); }
            to   { opacity:1; transform:scale(1) translateY(0); }
        }
    </style>

    <script>
        /* ════════════════════════════════════════
           RENDER GIỎ HÀNG
           - Dùng variant_id làm key trên data-vid
           - Event delegation: 1 listener cho tất cả nút
        ════════════════════════════════════════ */
        function renderCart() {
            const items = Cart.get();
            const list  = document.getElementById('cartList');

            if (!items.length) {
                list.innerHTML = `
                    <div class="cart-empty">
                        <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="1" stroke-linecap="round">
                            <circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/>
                            <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/>
                        </svg>
                        <p style="font-size:15px;font-weight:600;margin-bottom:6px">Giỏ hàng trống</p>
                        <p style="font-size:13.5px;margin-bottom:20px">Thêm sản phẩm vào giỏ để tiếp tục mua sắm.</p>
                        <a href="{{ url('san-pham') }}" class="btn btn-primary">Khám phá sản phẩm</a>
                    </div>`;
                document.getElementById('sumSubtotal').textContent = '0₫';
                document.getElementById('sumTotal').textContent    = '0₫';
                return;
            }

            list.innerHTML = items.map(i => `
                <div class="cart-item" data-vid="${i.variant_id}">
                    <div class="cart-item__img">
                        ${i.img
                            ? `<img src="${i.img}" alt="${i.name}" style="width:72px;height:72px;object-fit:contain;border-radius:6px">`
                            : `<svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round"><rect x="2" y="3" width="20" height="14" rx="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/></svg>`
                        }
                    </div>
                    <div style="flex:1;min-width:0">
                        <div class="cart-item__name">${i.name}</div>
                        <div class="cart-item__variant">${i.variant || 'Mặc định'}</div>
                        <div class="qty-ctrl">
                            <button class="qty-ctrl__btn" data-act="minus" data-vid="${i.variant_id}"
                                ${i.qty <= 1 ? 'disabled style="opacity:.4"' : ''}>
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                    <line x1="5" y1="12" x2="19" y2="12"/>
                                </svg>
                            </button>
                            <input type="number" class="qty-ctrl__input" value="${i.qty}"
                                min="1" max="99" data-vid="${i.variant_id}">
                            <button class="qty-ctrl__btn" data-act="plus" data-vid="${i.variant_id}"
                                ${i.qty >= 99 ? 'disabled style="opacity:.4"' : ''}>
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                    <line x1="12" y1="5" x2="12" y2="19"/>
                                    <line x1="5" y1="12" x2="19" y2="12"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                    <div class="cart-item__right">
                        <button class="cart-item__remove" data-act="remove" data-vid="${i.variant_id}" title="Xoá">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round">
                                <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
                            </svg>
                        </button>
                        <div class="cart-item__price">${fmtPrice(i.price * i.qty)}</div>
                    </div>
                </div>`).join('');

            const sub = Cart.total();
            document.getElementById('sumSubtotal').textContent = fmtPrice(sub);
            document.getElementById('sumTotal').textContent    = fmtPrice(sub);
        }

        /* ── Event delegation — tất cả thao tác qua 1 listener ── */
        document.getElementById('cartList').addEventListener('click', async function(e) {
            const btn = e.target.closest('[data-act]');
            if (!btn) return;
            const vid  = btn.dataset.vid;
            const act  = btn.dataset.act;
            const item = Cart.get().find(i => String(i.variant_id) === String(vid));
            if (!item && act !== 'remove') return;

            if      (act === 'minus')  await Cart.updateQty(vid, item.qty - 1);
            else if (act === 'plus')   await Cart.updateQty(vid, item.qty + 1);
            else if (act === 'remove') await Cart.removeByVariantId(vid);

            renderCart();
        });

        /* ── Thay đổi input số lượng ── */
        document.getElementById('cartList').addEventListener('change', async function(e) {
            const input = e.target.closest('.qty-ctrl__input');
            if (!input) return;
            const qty = parseInt(input.value);
            if (qty > 0) { await Cart.updateQty(input.dataset.vid, qty); renderCart(); }
        });

        /* ── Modal xoá tất cả ── */
        function showClearModal() {
            if (!Cart.get().length) return;
            document.getElementById('clearModal').style.display = 'flex';
        }
        async function confirmClear() {
            document.getElementById('clearModal').style.display = 'none';
            await Cart.clearAll();
            renderCart();
            Toast.show('Đã xoá toàn bộ giỏ hàng', 'info');
        }
        document.getElementById('clearModal').addEventListener('click', function(e) {
            if (e.target === this) this.style.display = 'none';
        });

        document.addEventListener('DOMContentLoaded', renderCart);
        Object.assign(window, { showClearModal, confirmClear });
    </script>
@endsection