@extends('layouts.app')

@section('title', 'Đơn hàng - Nexus Store')

@push('styles')
<style>
.order-detail-layout{display:grid;grid-template-columns:1fr 340px;gap:28px;align-items:start}
.order-card{background:var(--bg-alt);border:1px solid var(--border-soft);border-radius:var(--r-xl);padding:24px;margin-bottom:20px}
.order-card-title{font-family:var(--font-display);font-size:17px;font-weight:800;margin-bottom:20px}
.tracking-steps{padding:20px 0}
.tracking-item{display:flex;gap:16px;padding-bottom:24px;position:relative}
.tracking-item:last-child{padding-bottom:0}
.tracking-item:not(:last-child)::before{content:'';position:absolute;left:17px;top:36px;bottom:0;width:2px;background:var(--border)}
.tracking-item.done:not(:last-child)::before{background:var(--accent)}
.tracking-dot{width:36px;height:36px;border-radius:50%;border:2px solid var(--border);background:var(--bg-alt);display:flex;align-items:center;justify-content:center;font-size:14px;flex-shrink:0;position:relative;z-index:1}
.tracking-item.done .tracking-dot{background:var(--accent);border-color:var(--accent);color:#fff}
.tracking-item.current .tracking-dot{background:var(--ink);border-color:var(--ink);color:#fff;box-shadow:0 0 0 4px rgba(26,23,20,.1)}
.tracking-content{}
.tracking-title{font-weight:700;font-size:14px;color:var(--ink);margin-bottom:2px}
.tracking-item:not(.done):not(.current) .tracking-title{color:var(--ink-muted)}
.tracking-time{font-size:12px;color:var(--ink-muted)}
.tracking-desc{font-size:13px;color:var(--ink-3);margin-top:3px}
.order-product-row{display:flex;align-items:center;gap:14px;padding:14px 0;border-bottom:1px solid var(--border-soft)}
.order-product-row:last-child{border-bottom:none}
@media(max-width:1024px){.order-detail-layout{grid-template-columns:1fr}}
</style>
@endpush

@section('content')
<div class="container section">
  <div class="breadcrumb mb-24"><a href="{{ url('/') }}">Trang chủ</a><span class="breadcrumb__sep">›</span><a href="{{ url('tai-khoan') }}">Tài khoản</a><span class="breadcrumb__sep">›</span><span class="breadcrumb__current">Chi tiết đơn hàng</span></div>

  <div style="background:var(--surface);border-radius:var(--r-xl);padding:24px;margin-bottom:32px">
    <h2 class="heading-2 mb-12">Tra cứu đơn hàng</h2>
    <div style="display:flex;gap:10px;flex-wrap:wrap">
      <input type="text" class="form-control" placeholder="Nhập mã đơn hàng (VD: NX-20241210-4521)" style="flex:1;min-width:200px" id="searchOrderId" value="NX-20241210-4521">
      <button class="btn btn-primary" onclick="Toast.show('Đang tìm kiếm đơn hàng...','info')">Tra cứu</button>
    </div>
  </div>

  <div class="d-flex justify-between align-center mb-24" style="flex-wrap:wrap;gap:12px">
    <div>
      <h1 class="heading-1">Đơn hàng <span class="text-accent">#NX-20241210-4521</span></h1>
      <div style="font-size:14px;color:var(--ink-muted);margin-top:4px">Đặt ngày 10/12/2024 · 1 sản phẩm</div>
    </div>
    <span class="status status-shipping" style="font-size:14px;padding:8px 16px">Đang giao hàng</span>
  </div>

  <div class="steps mb-32">
    <div class="step done"><div class="step__dot">✓</div><div class="step__label">Đã đặt hàng</div></div>
    <div class="step done"><div class="step__dot">✓</div><div class="step__label">Đã xác nhận</div></div>
    <div class="step done"><div class="step__dot">✓</div><div class="step__label">Đang đóng gói</div></div>
    <div class="step active"><div class="step__dot">🚚</div><div class="step__label">Đang giao</div></div>
    <div class="step"><div class="step__dot">5</div><div class="step__label">Đã nhận hàng</div></div>
  </div>

  <div class="order-detail-layout">
    <div>
      <div class="order-card">
        <div class="order-card-title">🚚 Lịch sử vận chuyển</div>
        <div class="tracking-steps">
          <div class="tracking-item current">
            <div class="tracking-dot">🚚</div>
            <div class="tracking-content">
              <div class="tracking-title">Đang trên đường giao hàng</div>
              <div class="tracking-time">14:32 · 12/12/2024</div>
              <div class="tracking-desc">Nhân viên giao hàng đang trên đường đến địa chỉ của bạn. Dự kiến giao trước 18:00 hôm nay.</div>
            </div>
          </div>
          <div class="tracking-item done">
            <div class="tracking-dot">✓</div>
            <div class="tracking-content">
              <div class="tracking-title">Đơn hàng đã rời kho</div>
              <div class="tracking-time">08:15 · 12/12/2024</div>
              <div class="tracking-desc">Đơn hàng đã được bàn giao cho đơn vị vận chuyển Giao Hàng Nhanh</div>
            </div>
          </div>
          <div class="tracking-item done">
            <div class="tracking-dot">✓</div>
            <div class="tracking-content">
              <div class="tracking-title">Đang đóng gói</div>
              <div class="tracking-time">22:30 · 11/12/2024</div>
              <div class="tracking-desc">Đơn hàng đang được kiểm tra và đóng gói cẩn thận</div>
            </div>
          </div>
          <div class="tracking-item done">
            <div class="tracking-dot">✓</div>
            <div class="tracking-content">
              <div class="tracking-title">Đã xác nhận đơn hàng</div>
              <div class="tracking-time">10:05 · 10/12/2024</div>
              <div class="tracking-desc">Nexus Store đã xác nhận và bắt đầu xử lý đơn hàng của bạn</div>
            </div>
          </div>
          <div class="tracking-item done">
            <div class="tracking-dot">✓</div>
            <div class="tracking-content">
              <div class="tracking-title">Đặt hàng thành công</div>
              <div class="tracking-time">09:42 · 10/12/2024</div>
              <div class="tracking-desc">Bạn đã đặt hàng thành công. Mã đơn: #NX-20241210-4521</div>
            </div>
          </div>
        </div>
      </div>

      <div class="order-card">
        <div class="order-card-title">📦 Sản phẩm trong đơn</div>
        <div class="order-product-row">
          <div style="width:60px;height:60px;background:var(--surface);border-radius:var(--r-lg);display:flex;align-items:center;justify-content:center;font-size:28px;flex-shrink:0">💻</div>
          <div style="flex:1">
            <div style="font-weight:600;font-size:14px">MacBook Pro 14" M3 Pro</div>
            <div style="font-size:12px;color:var(--ink-muted)">Space Black · 512GB SSD · x1</div>
          </div>
          <div style="font-weight:800;font-size:16px">42.990.000₫</div>
        </div>
      </div>
    </div>

    <div>
      <div class="order-card">
        <div class="order-card-title">📋 Thông tin đơn hàng</div>
        <div style="display:flex;flex-direction:column;gap:10px;font-size:14px">
          <div class="d-flex justify-between"><span style="color:var(--ink-3)">Mã đơn hàng</span><span style="font-weight:600">#NX-20241210-4521</span></div>
          <div class="d-flex justify-between"><span style="color:var(--ink-3)">Ngày đặt</span><span>10/12/2024</span></div>
          <div class="d-flex justify-between"><span style="color:var(--ink-3)">Thanh toán</span><span class="badge badge-success">COD ✓</span></div>
          <div class="d-flex justify-between"><span style="color:var(--ink-3)">Vận chuyển</span><span style="color:var(--green)">Miễn phí</span></div>
          <div class="divider"></div>
          <div class="d-flex justify-between" style="font-size:18px;font-weight:800"><span>Tổng cộng</span><span style="color:var(--accent)">42.990.000₫</span></div>
        </div>
      </div>

      <div class="order-card">
        <div class="order-card-title">📍 Địa chỉ nhận hàng</div>
        <div style="font-size:14px;line-height:1.8;color:var(--ink-3)">
          <strong style="color:var(--ink)">Nguyễn Văn A</strong><br>
          0901 234 567<br>
          123 Nguyễn Huệ, P. Bến Nghé, Quận 1, TP.HCM
        </div>
      </div>

      <div class="d-flex gap-8" style="flex-wrap:wrap">
        <button class="btn btn-outline btn-sm btn-full" onclick="Toast.show('Đã sao chép mã đơn hàng!','success')">📋 Sao chép mã đơn</button>
        <button class="btn btn-danger btn-sm btn-full" onclick="if(confirm('Hủy đơn hàng này?'))Toast.show('Đơn hàng đã được hủy','info')">Hủy đơn hàng</button>
      </div>
    </div>
  </div>
</div>
@endsection