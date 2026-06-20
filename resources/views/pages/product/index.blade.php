@extends('layouts.app')

@section('title', 'Sản phẩm - Nexus Store')

@push('styles')
<style>
.shop-layout { display: grid; grid-template-columns: 260px 1fr; gap: 28px; align-items: start; }
.filter-card { background: var(--bg-alt); border: 1px solid var(--border-soft); border-radius: var(--r-xl); padding: 24px; position: sticky; top: 88px; }
.filter-section { margin-bottom: 24px; }
.filter-title { font-size: 12px; font-weight: 700; text-transform: uppercase; color: var(--ink-3); margin-bottom: 14px; }
.filter-check { display: flex; align-items: center; justify-content: space-between; margin-bottom: 8px; cursor: pointer; }
.filter-check label { display: flex; align-items: center; gap: 8px; font-size: 14px; }
.filter-check-count { font-size: 12px; color: var(--ink-muted); background: var(--surface); padding: 1px 7px; border-radius: var(--r-full); }
.toolbar {
  display: flex; align-items: center; justify-content: space-between;
  padding: 14px 20px; background: var(--bg-alt); border: 1px solid var(--border-soft);
  border-radius: var(--r-xl); margin-bottom: 20px;
}
.sort-select {
  padding: 8px 34px 8px 12px; border-radius: var(--r-md);
  border: 1.5px solid var(--border); background: var(--bg-alt);
  font-size: 14px; cursor: pointer;
}
.grid-3 { display: grid; grid-template-columns: repeat(3, 1fr); gap: 24px; }
@media(max-width: 1024px) { .shop-layout { grid-template-columns: 1fr; } .filter-card { display: none; } }
@media(max-width: 768px) { .grid-3 { grid-template-columns: repeat(2, 1fr); } }
@media(max-width: 480px) { .grid-3 { grid-template-columns: 1fr; } }
</style>
@endpush

@section('content')
<div class="container section">
  <div class="breadcrumb mb-24">
    <a href="{{ url('/') }}">Trang chủ</a>
    <span class="breadcrumb__sep">›</span>
    <span class="breadcrumb__current">Tất cả sản phẩm</span>
  </div>
  <h1 class="heading-1 mb-32">Tất cả <span class="text-accent">sản phẩm</span></h1>

  <div class="shop-layout">
    <aside>
      <div class="filter-card">
        <div class="filter-section">
          <div class="filter-title">Danh mục</div>
          <div class="filter-check"><label><input type="radio" name="cat" checked> Tất cả</label><span class="filter-check-count">12</span></div>
          <div class="filter-check"><label><input type="radio" name="cat"> Laptop</label><span class="filter-check-count">4</span></div>
          <div class="filter-check"><label><input type="radio" name="cat"> Điện thoại</label><span class="filter-check-count">2</span></div>
          <div class="filter-check"><label><input type="radio" name="cat"> Tablet</label><span class="filter-check-count">2</span></div>
        </div>
       
      </div>
    </aside>

    <div>
      <div class="toolbar">
        <div class="toolbar__left"><span class="result-info">Hiển thị <b>8</b> sản phẩm</span></div>
        <div class="toolbar__right">
          <select class="sort-select"><option>Mặc định</option><option>Giá tăng dần</option><option>Giá giảm dần</option></select>
        </div>
      </div>

      <div class="grid-3">
        <!-- MacBook -->
        <div class="product-card">
          <div class="product-card__thumb">
            <div class="product-card__badges"><span class="badge badge-hot">Hot 🔥</span></div>
            <button class="product-card__wish" data-wish-id="1">♥</button>
            <div class="product-card__img">💻</div>
            <div class="product-card__actions">
              <button class="btn btn-primary btn-sm" onclick="Cart.add({id:1,name:'MacBook Pro',price:42990000,img:'💻'})">+ Giỏ hàng</button>
            </div>
          </div>
          <div class="product-card__body">
            <div class="product-card__brand">Apple</div>
            <div class="product-card__name"><a href="{{ url('chi-tiet?id=1') }}">MacBook Pro 14" M3 Pro</a></div>
            <div class="product-card__price"><span class="product-card__price-current">42.990.000₫</span></div>
          </div>
        </div>

        <!-- iPhone -->
        <div class="product-card">
          <div class="product-card__thumb">
            <div class="product-card__badges"><span class="badge badge-sale">-11%</span></div>
            <button class="product-card__wish" data-wish-id="2">♥</button>
            <div class="product-card__img">📱</div>
            <div class="product-card__actions">
              <button class="btn btn-primary btn-sm" onclick="Cart.add({id:2,name:'iPhone 15 Pro Max',price:32990000,img:'📱'})">+ Giỏ hàng</button>
            </div>
          </div>
          <div class="product-card__body">
            <div class="product-card__brand">Apple</div>
            <div class="product-card__name"><a href="{{ url('chi-tiet?id=2') }}">iPhone 15 Pro Max 256GB</a></div>
            <div class="product-card__price"><span class="product-card__price-current">32.990.000₫</span><span class="product-card__price-old">36.990.000₫</span></div>
          </div>
        </div>

        <!-- Samsung -->
        <div class="product-card">
          <div class="product-card__thumb">
            <div class="product-card__badges"><span class="badge badge-new">Mới</span></div>
            <button class="product-card__wish" data-wish-id="3">♥</button>
            <div class="product-card__img">📱</div>
            <div class="product-card__actions">
              <button class="btn btn-primary btn-sm" onclick="Cart.add({id:3,name:'Samsung S24 Ultra',price:29990000,img:'📱'})">+ Giỏ hàng</button>
            </div>
          </div>
          <div class="product-card__body">
            <div class="product-card__brand">Samsung</div>
            <div class="product-card__name"><a href="{{ url('chi-tiet?id=3') }}">Samsung Galaxy S24 Ultra</a></div>
            <div class="product-card__price"><span class="product-card__price-current">29.990.000₫</span><span class="product-card__price-old">33.990.000₫</span></div>
          </div>
        </div>

        <!-- iPad -->
        <div class="product-card">
          <div class="product-card__thumb">
            <div class="product-card__badges"><span class="badge badge-sale">-10%</span></div>
            <button class="product-card__wish" data-wish-id="5">♥</button>
            <div class="product-card__img">📟</div>
            <div class="product-card__actions">
              <button class="btn btn-primary btn-sm" onclick="Cart.add({id:5,name:'iPad Pro',price:28990000,img:'📟'})">+ Giỏ hàng</button>
            </div>
          </div>
          <div class="product-card__body">
            <div class="product-card__brand">Apple</div>
            <div class="product-card__name"><a href="{{ url('chi-tiet?id=5') }}">iPad Pro 12.9" M2</a></div>
            <div class="product-card__price"><span class="product-card__price-current">28.990.000₫</span><span class="product-card__price-old">32.000.000₫</span></div>
          </div>
        </div>

        <!-- Sony Headphone -->
        <div class="product-card">
          <div class="product-card__thumb">
            <div class="product-card__badges"><span class="badge badge-hot">Hot 🔥</span></div>
            <button class="product-card__wish" data-wish-id="6">♥</button>
            <div class="product-card__img">🎧</div>
            <div class="product-card__actions">
              <button class="btn btn-primary btn-sm" onclick="Cart.add({id:6,name:'Sony WH-1000XM5',price:8490000,img:'🎧'})">+ Giỏ hàng</button>
            </div>
          </div>
          <div class="product-card__body">
            <div class="product-card__brand">Sony</div>
            <div class="product-card__name"><a href="{{ url('chi-tiet?id=6') }}">Sony WH-1000XM5</a></div>
            <div class="product-card__price"><span class="product-card__price-current">8.490.000₫</span><span class="product-card__price-old">9.990.000₫</span></div>
          </div>
        </div>

        <!-- Dell XPS -->
        <div class="product-card">
          <div class="product-card__thumb">
            <div class="product-card__badges"><span class="badge badge-best">Best</span></div>
            <button class="product-card__wish" data-wish-id="4">♥</button>
            <div class="product-card__img">💻</div>
            <div class="product-card__actions">
              <button class="btn btn-primary btn-sm" onclick="Cart.add({id:4,name:'Dell XPS 15',price:38990000,img:'💻'})">+ Giỏ hàng</button>
            </div>
          </div>
          <div class="product-card__body">
            <div class="product-card__brand">Dell</div>
            <div class="product-card__name"><a href="{{ url('chi-tiet?id=4') }}">Dell XPS 15 OLED</a></div>
            <div class="product-card__price"><span class="product-card__price-current">38.990.000₫</span></div>
          </div>
        </div>

        <!-- Apple Watch -->
        <div class="product-card">
          <div class="product-card__thumb">
            <div class="product-card__badges"><span class="badge badge-new">Mới</span></div>
            <button class="product-card__wish" data-wish-id="8">♥</button>
            <div class="product-card__img">⌚</div>
            <div class="product-card__actions">
              <button class="btn btn-primary btn-sm" onclick="Cart.add({id:8,name:'Apple Watch S9',price:11990000,img:'⌚'})">+ Giỏ hàng</button>
            </div>
          </div>
          <div class="product-card__body">
            <div class="product-card__brand">Apple</div>
            <div class="product-card__name"><a href="{{ url('chi-tiet?id=8') }}">Apple Watch Series 9</a></div>
            <div class="product-card__price"><span class="product-card__price-current">11.990.000₫</span><span class="product-card__price-old">13.990.000₫</span></div>
          </div>
        </div>

        <!-- Bose QC45 -->
        <div class="product-card">
          <div class="product-card__thumb">
            <div class="product-card__badges"><span class="badge badge-sale">-14%</span></div>
            <button class="product-card__wish" data-wish-id="12">♥</button>
            <div class="product-card__img">🎧</div>
            <div class="product-card__actions">
              <button class="btn btn-primary btn-sm" onclick="Cart.add({id:12,name:'Bose QC45',price:7290000,img:'🎧'})">+ Giỏ hàng</button>
            </div>
          </div>
          <div class="product-card__body">
            <div class="product-card__brand">Bose</div>
            <div class="product-card__name"><a href="{{ url('chi-tiet?id=12') }}">Bose QuietComfort 45</a></div>
            <div class="product-card__price"><span class="product-card__price-current">7.290.000₫</span><span class="product-card__price-old">8.500.000₫</span></div>
          </div>
        </div>
      </div>

      <div class="pagination mt-32" style="display: flex; justify-content: center; gap: 8px;">
        <button class="page-btn active">1</button>
        <button class="page-btn">2</button>
        <button class="page-btn">→</button>
      </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
  initWishBtns();
</script>
@endpush