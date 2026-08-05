@extends('layouts.app')
@section('title', 'Yêu thích — Nexus Store')

@push('styles')
  
@endpush
 <link rel="stylesheet" href="{{ asset('assets/css/pages/wishlist.css') }}">
@section('content')
    <div class="container wish-wrap">
        <div class="wish-header">
            <div>
                <h1 class="h1">Sản phẩm yêu thích</h1>
                <div class="wish-count" id="wishCount"></div>
            </div>
            <button onclick="clearWishlist()" class="btn btn-ghost btn-sm" id="clearBtn" style="display:none">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                    stroke-linecap="round">
                    <polyline points="3 6 5 6 21 6" />
                    <path d="M19 6l-1 14H6L5 6" />
                    <path d="M10 11v6M14 11v6" />
                    <path d="M9 6V4h6v2" />
                </svg>
                Xoá tất cả
            </button>
        </div>

        {{-- Grid sản phẩm --}}
        <div class="wish-grid" id="wishGrid"></div>

        {{-- Trạng thái rỗng --}}
        <div id="wishEmpty" style="display:none; text-align:center; padding:80px 24px">
            <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1"
                stroke-linecap="round" style="color:var(--border); margin:0 auto 16px; display:block">
                <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06
                                                             a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78
                                                             1.06-1.06a5.5 5.5 0 0 0 0-7.78z" />
            </svg>
            <p style="font-size:16px; font-weight:600; margin-bottom:8px">Chưa có sản phẩm yêu thích</p>
            <p style="font-size:14px; color:var(--ink-3); margin-bottom:24px">
                Bấm vào biểu tượng trái tim trên sản phẩm để lưu vào đây.
            </p>
            <a href="{{ url('san-pham') }}" class="btn btn-primary">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                    stroke-linecap="round">
                    <circle cx="11" cy="11" r="8" />
                    <line x1="21" y1="21" x2="16.65" y2="16.65" />
                </svg>
                Khám phá sản phẩm
            </a>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const items = Wishlist.get(); // [{id, name, price, img, slug}]
            const grid = document.getElementById('wishGrid');
            const empty = document.getElementById('wishEmpty');
            const count = document.getElementById('wishCount');
            const clearBtn = document.getElementById('clearBtn');

            if (!items.length) {
                grid.style.display = 'none';
                empty.style.display = 'block';
                return;
            }

            count.textContent = items.length + ' sản phẩm';
            clearBtn.style.display = 'flex';

            grid.innerHTML = items.map(p => {
                const price = p.price ?
                    Number(p.price).toLocaleString('vi-VN') + '₫' :
                    '—';
                const name = p.name || ('Sản phẩm #' + p.id);

                // URL sản phẩm: dùng slug nếu có, không thì về trang danh sách
                const url = p.slug ?
                    '/san-pham/' + p.slug :
                    '/san-pham';

                // Ảnh: dùng img từ localStorage nếu có, không thì placeholder SVG
                const imgHtml = p.img ?
                    `<img src="/storage/${p.img}" alt="${name}"
            style="object-fit:contain;padding:16px;width:100%;height:100%;display:block"
            onerror="this.style.display='none';document.getElementById('ph_${p.id}').style.display='flex'">` :
                    '';

                const svgPlaceholder = `
    <div id="ph_${p.id}"
         style="${p.img ? 'display:none' : 'display:flex'};
                width:100%;height:100%;
                align-items:center;justify-content:center;color:#ccc">
        <svg width="56" height="56" viewBox="0 0 24 24" fill="none"
             stroke="currentColor" stroke-width=".8" stroke-linecap="round">
            <rect x="2" y="3" width="20" height="14" rx="2"/>
            <line x1="8" y1="21" x2="16" y2="21"/>
            <line x1="12" y1="17" x2="12" y2="21"/>
        </svg>
    </div>`;

                return `
        <div class="wcard">
          <div class="wcard__thumb">
            ${imgHtml}
            ${svgPlaceholder}

            {{-- Nút xoá --}}
       
            <button class="wcard__del"
    onclick="removeFromWish('${p.id}', this)"
    title="Xoá khỏi yêu thích">
    <svg width="13" height="13" viewBox="0 0 24 24" fill="none"
         stroke="currentColor" stroke-width="2.5" stroke-linecap="round">
        <line x1="18" y1="6" x2="6" y2="18"/>
        <line x1="6" y1="6" x2="18" y2="18"/>
    </svg>
</button>

            {{-- Nút hover --}}
            <div class="wcard__hover">
             <button class="btn btn-ghost"
                onclick="${p.variant_id
                    ? `Cart.add({id:${p.id},variant_id:${p.variant_id},name:'${name.replace(/'/g, "\\'")}',price:${p.price||0},img:'${p.img||''}'});Wishlist.updateUI()`
                    : `Toast.show('Vui lòng chọn phân loại trên trang sản phẩm', 'info');window.location.href='${url}'`
                }">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none"
                     stroke="currentColor" stroke-width="2" stroke-linecap="round">
                  <circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/>
                  <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/>
                </svg>
                Thêm giỏ
              </button>
              <a href="${url}" class="btn btn-primary">Xem</a>
            </div>
          </div>

          <div class="wcard__body">
            <a href="${url}" style="text-decoration:none">
              <div class="wcard__name">${name}</div>
            </a>
            <div class="wcard__price">${price}</div>
          </div>
        </div>`;
            }).join('');
        });

        function removeFromWish(id, btn) {
            // Lấy danh sách hiện tại
            const w = Wishlist.get();
            // Lọc bỏ item có id trùng
            const newList = w.filter(i => String(i.id) !== String(id));
            // Lưu lại
            localStorage.setItem('nx_wish', JSON.stringify(newList));
            // Cập nhật badge số lượng trên navbar
            Wishlist.updateUI();
            // Xóa card khỏi giao diện (không reload trang)
            const card = btn.closest('.wcard');
            card.style.transition = 'opacity .2s, transform .2s';
            card.style.opacity = '0';
            card.style.transform = 'scale(.95)';
            setTimeout(() => {
                card.remove();
                // Cập nhật đếm số lượng
                const remaining = Wishlist.get().length;
                const countEl = document.getElementById('wishCount');
                if (countEl) countEl.textContent = remaining + ' sản phẩm';
                // Nếu hết sản phẩm → hiện trạng thái rỗng
                if (remaining === 0) {
                    document.getElementById('wishGrid').style.display = 'none';
                    document.getElementById('wishEmpty').style.display = 'block';
                    document.getElementById('clearBtn').style.display = 'none';
                }
            }, 200);
            Toast.show('Đã xoá khỏi yêu thích', 'info');
        }
        window.removeFromWish = removeFromWish;

        function clearWishlist() {
            if (!confirm('Xoá tất cả sản phẩm yêu thích?')) return;
            localStorage.removeItem('nx_wish');
            Wishlist.updateUI();
            location.reload();
        }
        window.clearWishlist = clearWishlist;
    </script>
@endsection
