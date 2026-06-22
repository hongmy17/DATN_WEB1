@extends('layouts.app')

@section('title', 'Giỏ hàng - Nexus Store')

@push('styles')
<style>
.cart-layout{display:grid;grid-template-columns:1fr 360px;gap:28px;align-items:start}
.cart-table{background:var(--bg-alt);border:1px solid var(--border-soft);border-radius:var(--r-xl);overflow:hidden}
.cart-head{display:grid;grid-template-columns:3fr 1fr 1fr 1fr 36px;gap:16px;padding:14px 24px;background:var(--surface);font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:1px;color:var(--ink-3)}
.cart-row{display:grid;grid-template-columns:3fr 1fr 1fr 1fr 36px;gap:16px;align-items:center;padding:18px 24px;border-top:1px solid var(--border-soft);transition:var(--transition)}
.cart-row:hover{background:var(--surface)}
.cart-product{display:flex;align-items:center;gap:14px}
.cart-img{width:68px;height:68px;border-radius:var(--r-lg);background:var(--surface);display:flex;align-items:center;justify-content:center;font-size:32px;flex-shrink:0}
.cart-name{font-weight:600;font-size:14px;color:var(--ink);line-height:1.4;margin-bottom:3px}
.cart-meta{font-size:12px;color:var(--ink-muted)}
.cart-price{font-weight:600;font-size:14px}
.cart-subtotal{font-weight:800;font-size:15px;color:var(--ink)}
.cart-del{width:32px;height:32px;border-radius:50%;background:transparent;border:1.5px solid var(--border);color:var(--ink-muted);display:flex;align-items:center;justify-content:center;font-size:16px;cursor:pointer;transition:var(--transition)}
.cart-del:hover{background:var(--red-light);border-color:var(--red);color:var(--red)}
.summary-box{background:var(--bg-alt);border:1px solid var(--border-soft);border-radius:var(--r-xl);padding:24px;position:sticky;top:88px}
.summary-title{font-family:var(--font-display);font-size:18px;font-weight:800;margin-bottom:20px}
.summary-row{display:flex;justify-content:space-between;align-items:center;padding:10px 0;border-bottom:1px solid var(--border-soft);font-size:14px}
.summary-row:last-of-type{border-bottom:none}
.summary-total{display:flex;justify-content:space-between;font-size:20px;font-weight:800;padding:16px 0 0;margin-top:8px;border-top:2px solid var(--border)}
.summary-total span:last-child{color:var(--accent)}
.checkout-btn{width:100%;padding:16px;background:var(--ink);color:#fff;border:none;border-radius:var(--r-xl);font-size:16px;font-weight:800;cursor:pointer;transition:var(--transition);margin-top:16px;display:flex;align-items:center;justify-content:center;gap:8px}
.checkout-btn:hover{background:var(--ink-2);transform:translateY(-2px);box-shadow:var(--shadow-md)}
.empty-cart{text-align:center;padding:80px 24px;background:var(--bg-alt);border:1px solid var(--border-soft);border-radius:var(--r-2xl)}
@media(max-width:1024px){.cart-layout{grid-template-columns:1fr}.summary-box{position:static}}
@media(max-width:768px){.cart-head{display:none}.cart-row{grid-template-columns:1fr;gap:12px}}
</style>
@endpush

@section('content')
<div class="container section">
  <div class="breadcrumb mb-24"><a href="{{ url('/') }}">Trang chủ</a><span class="breadcrumb__sep">›</span><span class="breadcrumb__current">Giỏ hàng</span></div>
  <h1 class="heading-1 mb-32">Giỏ hàng <span class="text-accent" id="cartCountTitle"></span></h1>
  <div id="cartMain"></div>
</div>
@endsection

@push('scripts')
<script>
  function renderCart() {
    const cart = Cart.get();
    document.getElementById('cartCountTitle').textContent = cart.length ? `(${cart.length})` : '';
    const main = document.getElementById('cartMain');
    if (!cart.length) {
      main.innerHTML = `<div class="empty-cart"><div style="font-size:72px;margin-bottom:20px">🛒</div><h2 class="heading-2 mb-12">Giỏ hàng trống</h2><p class="text-muted mb-24">Bạn chưa có sản phẩm nào trong giỏ hàng</p><a href="{{ url('san-pham') }}" class="btn btn-primary btn-lg">Tiếp tục mua sắm →</a></div>`;
      return;
    }
    const sub = Cart.total(), ship = sub >= 500000 ? 0 : 30000, total = sub + ship;
    main.innerHTML = `
    <div class="cart-layout">
      <div>
        <div class="cart-table">
          <div class="cart-head"><span>Sản phẩm</span><span>Đơn giá</span><span>Số lượng</span><span>Thành tiền</span><span></span></div>
          ${cart.map(item => `
          <div class="cart-row">
            <div class="cart-product">
              <div class="cart-img">${item.img||'📦'}</div>
              <div><div class="cart-name">${item.name}</div><div class="cart-meta">${item.variant||'Mặc định'}</div></div>
            </div>
            <div class="cart-price">${fmtPrice(item.price)}</div>
            <div><div class="qty-ctrl"><button class="qty-ctrl__btn qty-ctrl__btn--minus" onclick="changeQty(${item.id},'${item.variant||''}',${item.qty-1})">−</button><input type="number" class="qty-ctrl__input" value="${item.qty}" min="1" max="99" onchange="changeQty(${item.id},'${item.variant||''}',parseInt(this.value))"><button class="qty-ctrl__btn qty-ctrl__btn--plus" onclick="changeQty(${item.id},'${item.variant||''}',${item.qty+1})">+</button></div></div>
            <div class="cart-subtotal">${fmtPrice(item.price*item.qty)}</div>
            <button class="cart-del" onclick="Cart.remove(${item.id},'${item.variant||''}');renderCart()">×</button>
          </div>`).join('')}
        </div>
        <div class="d-flex justify-between mt-16" style="flex-wrap:wrap;gap:12px">
          <a href="{{ url('san-pham') }}" class="btn btn-outline">← Tiếp tục mua sắm</a>
          <button class="btn btn-ghost" onclick="if(confirm('Xóa tất cả?')){localStorage.removeItem('nx_cart');Cart.updateUI();renderCart()}">🗑 Xóa tất cả</button>
        </div>
      </div>
      <div>
        <div class="summary-box">
          <div class="summary-title">Đơn hàng</div>
          <div class="summary-row"><span class="text-ink3">Tạm tính</span><span>${fmtPrice(sub)}</span></div>
          <div class="summary-row"><span class="text-ink3">Phí vận chuyển</span><span style="color:${ship===0?'var(--green)':'inherit'}">${ship===0?'Miễn phí':fmtPrice(ship)}</span></div>
          <div class="summary-total"><span>Tổng cộng</span><span>${fmtPrice(total)}</span></div>
          <a href="{{ url('thanh-toan') }}"><button class="checkout-btn">Tiến hành thanh toán →</button></a>
          <div style="margin-top:14px;text-align:center;font-size:12px;color:var(--ink-muted)"><i class="fa-solid fa-lock"></i> Thanh toán bảo mật SSL 256-bit</div>
        </div>
      </div>
    </div>`;
  }

  function changeQty(id, variant, qty) {
    if (qty < 1) { Cart.remove(id, variant); } else { Cart.updateQty(id, variant, qty); }
    renderCart();
  }

  renderCart();
  window.changeQty = changeQty;
</script>
@endpush