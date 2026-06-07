@extends('layouts.app')

@section('title', 'Thanh toán - Nexus Store')

@push('styles')
<style>
.checkout-layout{display:grid;grid-template-columns:1fr 380px;gap:28px;align-items:start}
.checkout-card{background:var(--bg-alt);border:1px solid var(--border-soft);border-radius:var(--r-xl);padding:28px;margin-bottom:20px}
.checkout-card-title{display:flex;align-items:center;gap:12px;font-family:var(--font-display);font-size:17px;font-weight:800;margin-bottom:22px}
.step-dot{width:28px;height:28px;border-radius:50%;background:var(--ink);color:#fff;display:flex;align-items:center;justify-content:center;font-size:13px;font-weight:800;flex-shrink:0}
.form-row{display:grid;grid-template-columns:1fr 1fr;gap:16px}
.payment-opts{display:flex;flex-direction:column;gap:10px}
.payment-opt{display:flex;align-items:center;gap:14px;padding:16px;background:var(--surface);border:2px solid var(--border-soft);border-radius:var(--r-lg);cursor:pointer;transition:var(--transition)}
.payment-opt:hover{border-color:var(--border)}
.payment-opt.selected{border-color:var(--ink);background:var(--bg-alt);box-shadow:var(--shadow-sm)}
.payment-opt input{accent-color:var(--ink);width:16px;height:16px}
.popt-icon{font-size:24px}
.popt-name{font-weight:700;font-size:14px;color:var(--ink)}
.popt-desc{font-size:12px;color:var(--ink-muted)}
.popt-price{margin-left:auto;font-weight:700;font-size:14px}
.order-summary-sticky{background:var(--bg-alt);border:1px solid var(--border-soft);border-radius:var(--r-xl);padding:24px;position:sticky;top:88px}
.order-item{display:flex;align-items:center;gap:12px;padding:10px 0;border-bottom:1px solid var(--border-soft)}
.order-item:last-of-type{border-bottom:none}
.order-item-img{width:50px;height:50px;border-radius:var(--r-md);background:var(--surface);display:flex;align-items:center;justify-content:center;font-size:24px;flex-shrink:0;position:relative}
.order-item-qty{position:absolute;top:-6px;right:-6px;width:18px;height:18px;border-radius:50%;background:var(--ink);color:#fff;font-size:10px;font-weight:800;display:flex;align-items:center;justify-content:center}
.order-total-row{display:flex;justify-content:space-between;font-size:14px;padding:6px 0;color:var(--ink-3)}
.order-total-final{display:flex;justify-content:space-between;font-size:20px;font-weight:800;padding:14px 0 0;margin-top:8px;border-top:2px solid var(--border)}
.order-total-final span:last-child{color:var(--accent)}
.place-btn{width:100%;padding:17px;background:var(--ink);color:#fff;border:none;border-radius:var(--r-xl);font-size:16px;font-weight:800;cursor:pointer;transition:var(--transition);margin-top:18px;display:flex;align-items:center;justify-content:center;gap:8px}
.place-btn:hover{background:var(--ink-2);transform:translateY(-2px);box-shadow:var(--shadow-md)}
.modal-success{background:var(--bg-alt);border-radius:var(--r-2xl);width:100%;max-width:460px;padding:48px;text-align:center;box-shadow:var(--shadow-xl);transform:translateY(20px);transition:transform .35s var(--ease-out)}
.modal-overlay.open .modal-success{transform:none}
.success-icon{font-size:72px;margin-bottom:20px;animation:bounce .6s ease .2s both}
@keyframes bounce{0%,100%{transform:translateY(0)}50%{transform:translateY(-12px)}}
.order-code{background:var(--surface);border:1.5px dashed var(--border);border-radius:var(--r-lg);padding:12px 20px;font-family:monospace;font-size:18px;font-weight:800;color:var(--ink);margin:18px 0;letter-spacing:2px}
@media(max-width:1024px){.checkout-layout{grid-template-columns:1fr}.order-summary-sticky{position:static}}
@media(max-width:640px){.form-row{grid-template-columns:1fr}}
.addr-modal-overlay{display:none;position:fixed;inset:0;background:rgba(0,0,0,.45);z-index:999;align-items:center;justify-content:center}
.addr-modal-overlay.open{display:flex}
.addr-modal{background:var(--bg-alt);border-radius:var(--r-xl);width:100%;max-width:540px;padding:32px;box-shadow:var(--shadow-xl)}
.addr-modal-title{font-family:var(--font-display);font-size:18px;font-weight:800;margin-bottom:20px}
</style>
@endpush

@section('content')
<nav class="navbar" style="position:relative;background:var(--bg-alt);margin-bottom:0">
  <div class="navbar__inner">
    <a href="{{ url('/') }}" class="navbar__logo"><em class="navbar__logo-icon">N</em>Nexus<span style="color:var(--accent)">.</span></a>
    <div style="display:flex;align-items:center;gap:8px;font-size:13px;color:var(--ink-muted)">
      <span style="color:var(--ink);font-weight:600">1. Giỏ hàng</span>
      <span>›</span><span style="color:var(--accent);font-weight:600">2. Thanh toán</span>
      <span>›</span><span>3. Xác nhận</span>
    </div>
    <a href="{{ url('gio-hang') }}" class="btn btn-outline btn-sm">← Quay lại giỏ hàng</a>
  </div>
</nav>

<div class="container section">
  <h1 class="heading-1 mb-32">Thanh toán</h1>
  <div class="checkout-layout">
    <div>
      <div class="checkout-card">
        <div class="checkout-card-title"><div class="step-dot">1</div>Thông tin giao hàng</div>

        @auth
          <div class="form-group" style="margin-bottom:20px">
            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:8px">
              <label class="form-label" style="margin:0">Địa chỉ đã lưu</label>
              <button type="button" onclick="openAddrModal('create')"
  style="font-size:13px;color:var(--accent);font-weight:600;background:none;border:none;cursor:pointer;padding:0">
  + Thêm địa chỉ mới
</button>
            </div>
            @if($addresses->count() > 0)
            <div style="display:flex;gap:8px;align-items:center">
              <select class="form-control" id="savedAddress" onchange="fillAddress(this)" style="flex:1">
                <option value="">-- Chọn địa chỉ --</option>
                @foreach($addresses as $addr)
                <option value="{{ $addr->id }}"
                  data-name="{{ $addr->receiver_name }}"
                  data-phone="{{ $addr->receiver_phone }}"
                  data-province="{{ $addr->province }}"
                  data-district="{{ $addr->district }}"
                  data-ward="{{ $addr->ward }}"
                  data-detail="{{ $addr->address_detail }}"
                  {{ $addr->is_default ? 'selected' : '' }}>
                  {{ $addr->receiver_name }} — {{ $addr->address_detail }}, {{ $addr->ward }}, {{ $addr->district }}, {{ $addr->province }}
                  {{ $addr->is_default ? '(Mặc định)' : '' }}
                </option>
                @endforeach
              </select>
              <button type="button" onclick="openAddrModal('edit')"
                style="padding:10px 14px;border:1px solid var(--border);border-radius:var(--r-lg);background:var(--surface);cursor:pointer;font-size:13px;font-weight:600;white-space:nowrap">
                ✏️ Sửa
              </button>
              <button type="button" onclick="deleteSelected()"
                style="padding:10px 14px;border:1px solid var(--border);border-radius:var(--r-lg);background:var(--surface);cursor:pointer;font-size:13px;font-weight:600;color:var(--red);white-space:nowrap">
                🗑 Xóa
              </button>
            </div>
            <form id="deleteForm" method="POST" style="display:none">
              @csrf @method('DELETE')
              
            </form>
            @else
            <p style="font-size:13px;color:var(--ink-muted)">Bạn chưa có địa chỉ nào.</p>
            @endif
          </div>
        @endauth

        <div class="form-row">
          <div class="form-group">
            <label class="form-label">Họ và tên người nhận</label>
            <input type="text" id="receiver_name" class="form-control"
                   placeholder="Nguyễn Văn A"
                   value="{{ $defaultAddress->receiver_name ?? '' }}">
          </div>
          <div class="form-group">
            <label class="form-label">Số điện thoại</label>
            <input type="tel" id="receiver_phone" class="form-control"
                   placeholder="0901 234 567"
                   value="{{ $defaultAddress->receiver_phone ?? '' }}">
          </div>
        </div>
        <div class="form-row">
          <div class="form-group">
            <label class="form-label">Tỉnh / Thành phố</label>
            <input type="text" id="province" class="form-control"
                   placeholder="TP. Hồ Chí Minh"
                   value="{{ $defaultAddress->province ?? '' }}">
          </div>
          <div class="form-group">
            <label class="form-label">Quận / Huyện</label>
            <input type="text" id="district" class="form-control"
                   placeholder="Quận 1"
                   value="{{ $defaultAddress->district ?? '' }}">
          </div>
        </div>
        <div class="form-row">
          <div class="form-group">
            <label class="form-label">Phường / Xã</label>
            <input type="text" id="ward" class="form-control"
                   placeholder="Phường Bến Nghé"
                   value="{{ $defaultAddress->ward ?? '' }}">
          </div>
          <div class="form-group">
            <label class="form-label">Địa chỉ chi tiết</label>
            <input type="text" id="address_detail" class="form-control"
                   placeholder="Số nhà, tên đường..."
                   value="{{ $defaultAddress->address_detail ?? '' }}">
          </div>
        </div>
        <div class="form-group">
          <label class="form-label">Ghi chú (tùy chọn)</label>
          <textarea class="form-control" rows="2" placeholder="Giao sau 18h, gọi trước khi giao..."></textarea>
        </div>
      </div>

      <div class="checkout-card">
        <div class="checkout-card-title"><div class="step-dot">2</div>Phương thức vận chuyển</div>
        <div class="payment-opts">
          <label class="payment-opt selected" onclick="selectOpt(this)">
            <input type="radio" name="ship" checked>
            <span class="popt-icon">🚀</span>
            <div><div class="popt-name">Giao nhanh — Nhận trong 24h</div><div class="popt-desc">Miễn phí cho đơn từ 500.000₫</div></div>
            <span class="popt-price" style="color:var(--green)">Miễn phí</span>
          </label>
          <label class="payment-opt" onclick="selectOpt(this)">
            <input type="radio" name="ship">
            <span class="popt-icon">📦</span>
            <div><div class="popt-name">Giao tiêu chuẩn — 2-3 ngày</div><div class="popt-desc">Theo dõi đơn hàng realtime</div></div>
            <span class="popt-price">30.000₫</span>
          </label>
          <label class="payment-opt" onclick="selectOpt(this)">
            <input type="radio" name="ship">
            <span class="popt-icon">🏪</span>
            <div><div class="popt-name">Nhận tại cửa hàng</div><div class="popt-desc">123 Lê Lợi, Q.1, TP.HCM</div></div>
            <span class="popt-price" style="color:var(--green)">Miễn phí</span>
          </label>
        </div>
      </div>

      <div class="checkout-card">
        <div class="checkout-card-title"><div class="step-dot">3</div>Phương thức thanh toán</div>
        <div class="payment-opts">
          <label class="payment-opt selected" onclick="selectOpt(this)">
            <input type="radio" name="pay" checked>
            <span class="popt-icon">💵</span>
            <div><div class="popt-name">Thanh toán khi nhận hàng (COD)</div><div class="popt-desc">Kiểm tra hàng trước khi trả tiền</div></div>
          </label>
          <label class="payment-opt" onclick="selectOpt(this)">
            <input type="radio" name="pay">
            <span class="popt-icon">💳</span>
            <div><div class="popt-name">Thẻ Visa / Mastercard</div><div class="popt-desc">Trả góp 0% lãi suất 3-24 tháng</div></div>
          </label>
          <label class="payment-opt" onclick="selectOpt(this)">
            <input type="radio" name="pay">
            <span class="popt-icon">📱</span>
            <div><div class="popt-name">Ví MoMo</div><div class="popt-desc">Quét QR thanh toán nhanh</div></div>
          </label>
          <label class="payment-opt" onclick="selectOpt(this)">
            <input type="radio" name="pay">
            <span class="popt-icon">🏦</span>
            <div><div class="popt-name">VNPAY / Chuyển khoản ngân hàng</div><div class="popt-desc">Tất cả ngân hàng nội địa</div></div>
          </label>
        </div>
      </div>
    </div>

    <div>
      <div class="order-summary-sticky">
        <div class="summary-title" style="font-family:var(--font-display);font-size:17px;font-weight:800;margin-bottom:16px">Đơn hàng của bạn</div>
        <div id="orderItems"></div>
        <div style="margin-top:8px">
          <div class="order-total-row"><span>Tạm tính</span><span id="subTotal">—</span></div>
          <div class="order-total-row"><span>Phí vận chuyển</span><span style="color:var(--green)">Miễn phí</span></div>
          <div class="order-total-row"><span>Giảm giá</span><span style="color:var(--red)">-0₫</span></div>
          <div class="order-total-final"><span>Tổng cộng</span><span id="grandTotal">—</span></div>
        </div>
        <div style="background:var(--green-light);border-radius:var(--r-lg);padding:10px 14px;margin-top:14px;font-size:12px;color:var(--green);text-align:center;font-weight:600">🔒 Thanh toán bảo mật SSL 256-bit</div>
        <button class="place-btn" onclick="placeOrder()">✅ Đặt hàng ngay</button>
        <p style="text-align:center;font-size:12px;color:var(--ink-muted);margin-top:10px">Đặt hàng đồng nghĩa bạn đồng ý với <a href="#" style="color:var(--accent)">điều khoản dịch vụ</a></p>
      </div>
    </div>
  </div>
</div>

{{-- Modal đặt hàng thành công --}}
<div class="modal-overlay" id="successModal">
  <div class="modal-success">
    <div class="success-icon">🎉</div>
    <h2 class="heading-1 mb-12">Đặt hàng thành công!</h2>
    <p class="body-md text-ink3 mb-16">Cảm ơn bạn đã mua hàng tại Nexus Store! Đơn hàng đang được xử lý. Chúng tôi sẽ xác nhận trong vòng 30 phút.</p>
    <div class="order-code" id="orderCodeEl">NX-20240101-0000</div>
    <div style="display:flex;gap:12px;justify-content:center;flex-wrap:wrap">
      <a href="{{ url('don-hang') }}" class="btn btn-primary">📦 Theo dõi đơn hàng</a>
      <a href="{{ url('/') }}" class="btn btn-outline">🏠 Về trang chủ</a>
    </div>
  </div>
</div>

{{-- Modal thêm/sửa địa chỉ --}}
<div class="addr-modal-overlay" id="addrModal">
  <div class="addr-modal">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px">
      <div class="addr-modal-title" id="addrModalTitle">Thêm địa chỉ mới</div>
      <button onclick="closeAddrModal()" style="background:none;border:none;font-size:20px;cursor:pointer;color:var(--ink-muted)">✕</button>
    </div>
    <form id="addrForm" method="POST">
      @csrf
      @csrf
<input type="hidden" name="redirect_to" value="{{ url('/thanh-toan') }}">
      <span id="addrMethodField"></span>
      <div class="form-row">
        <div class="form-group">
          <label class="form-label">Họ và tên người nhận</label>
          <input type="text" name="receiver_name" id="modal_receiver_name" class="form-control" placeholder="Nguyễn Văn A" required>
        </div>
        <div class="form-group">
          <label class="form-label">Số điện thoại</label>
          <input type="text" name="receiver_phone" id="modal_receiver_phone" class="form-control" placeholder="0901 234 567" required>
        </div>
      </div>
      <div class="form-row">
        <div class="form-group">
          <label class="form-label">Tỉnh / Thành phố</label>
          <input type="text" name="province" id="modal_province" class="form-control" placeholder="TP. Hồ Chí Minh" required>
        </div>
        <div class="form-group">
          <label class="form-label">Quận / Huyện</label>
          <input type="text" name="district" id="modal_district" class="form-control" placeholder="Quận 1" required>
        </div>
      </div>
      <div class="form-row">
        <div class="form-group">
          <label class="form-label">Phường / Xã</label>
          <input type="text" name="ward" id="modal_ward" class="form-control" placeholder="Phường Bến Nghé" required>
        </div>
        <div class="form-group">
          <label class="form-label">Địa chỉ chi tiết</label>
          <input type="text" name="address_detail" id="modal_address_detail" class="form-control" placeholder="Số nhà, tên đường..." required>
        </div>
      </div>
      <div class="form-group" style="margin-top:8px">
        <label style="display:flex;align-items:center;gap:8px;cursor:pointer">
          <input type="checkbox" name="is_default" value="1" id="modal_is_default">
          <span style="font-size:14px">Đặt làm địa chỉ mặc định</span>
        </label>
      </div>
      <div style="display:flex;gap:12px;margin-top:20px">
        <button type="submit" class="place-btn" style="margin-top:0;flex:1">Lưu địa chỉ</button>
        <button type="button" onclick="closeAddrModal()"
          style="padding:14px 20px;border:1px solid var(--border);border-radius:var(--r-xl);background:var(--surface);cursor:pointer;font-weight:600">
          Hủy
        </button>
      </div>
    </form>
  </div>
</div>
@endsection
@push('scripts')
<script>
  function selectOpt(el) {
    const name = el.querySelector('input').name;
    document.querySelectorAll(`.payment-opt input[name="${name}"]`).forEach(r => r.closest('.payment-opt').classList.remove('selected'));
    el.classList.add('selected');
    el.querySelector('input').checked = true;
  }

  function renderOrderItems() {
    const cart = Cart.get();
    if (!cart.length) { document.getElementById('orderItems').innerHTML = '<p class="text-muted text-center" style="padding:16px">Giỏ hàng trống</p>'; return; }
    document.getElementById('orderItems').innerHTML = cart.map(i => `
      <div class="order-item">
        <div class="order-item-img">${i.img||'📦'}<span class="order-item-qty">${i.qty}</span></div>
        <div style="flex:1;font-size:13px;font-weight:500;color:var(--ink);line-height:1.4">${i.name}</div>
        <div style="font-weight:700;font-size:14px;white-space:nowrap">${fmtPrice(i.price*i.qty)}</div>
      </div>`).join('');
    const sub = Cart.total();
    document.getElementById('subTotal').textContent = fmtPrice(sub);
    document.getElementById('grandTotal').textContent = fmtPrice(sub);
  }

  function placeOrder() {
    const code = 'NX-' + Date.now().toString().slice(-8);
    document.getElementById('orderCodeEl').textContent = code;
    document.getElementById('successModal').classList.add('open');
    localStorage.removeItem('nx_cart');
    Cart.updateUI();
  }

  function fillAddress(select) {
    const opt = select.options[select.selectedIndex];
    if (!opt.value) return;
    document.getElementById('receiver_name').value  = opt.dataset.name;
    document.getElementById('receiver_phone').value = opt.dataset.phone;
    document.getElementById('province').value       = opt.dataset.province;
    document.getElementById('district').value       = opt.dataset.district;
    document.getElementById('ward').value           = opt.dataset.ward;
    document.getElementById('address_detail').value = opt.dataset.detail;
  }

  function editSelected() {
    const select = document.getElementById('savedAddress');
    const opt = select.options[select.selectedIndex];
    if (!opt.value) return alert('Vui lòng chọn địa chỉ!');
    window.location.href = opt.dataset.edit;
  }

  function deleteSelected() {
    const select = document.getElementById('savedAddress');
    const opt = select.options[select.selectedIndex];
    if (!opt.value) return alert('Vui lòng chọn địa chỉ!');
    if (!confirm('Xóa địa chỉ này?')) return;
    const form = document.getElementById('deleteForm');
    form.action = `/dia-chi/${opt.value}`;
    form.submit();
  }

  renderOrderItems();
  window.addEventListener('click', e => {
    if (e.target.id === 'successModal') e.target.classList.remove('open');
  });

function openAddrModal(mode, id) {
    const modal = document.getElementById('addrModal');
    const form  = document.getElementById('addrForm');
    const title = document.getElementById('addrModalTitle');
    const methodField = document.getElementById('addrMethodField');

    // Reset form
    form.reset();

    if (mode === 'create') {
      title.textContent = 'Thêm địa chỉ mới';
      form.action = '/dia-chi';
      methodField.innerHTML = '';
    } else {
      // Edit mode
      const select = document.getElementById('savedAddress');
      const opt = select.options[select.selectedIndex];
      if (!opt.value) return alert('Vui lòng chọn địa chỉ!');

      title.textContent = 'Sửa địa chỉ';
      form.action = `/dia-chi/${opt.value}`;
      methodField.innerHTML = '<input type="hidden" name="_method" value="PUT">';

      document.getElementById('modal_receiver_name').value   = opt.dataset.name;
      document.getElementById('modal_receiver_phone').value  = opt.dataset.phone;
      document.getElementById('modal_province').value        = opt.dataset.province;
      document.getElementById('modal_district').value        = opt.dataset.district;
      document.getElementById('modal_ward').value            = opt.dataset.ward;
      document.getElementById('modal_address_detail').value  = opt.dataset.detail;
    }

    modal.classList.add('open');
  }

  function closeAddrModal() {
    document.getElementById('addrModal').classList.remove('open');
  }

  window.openAddrModal = openAddrModal;
  window.closeAddrModal = closeAddrModal;

  window.selectOpt = selectOpt;
  window.placeOrder = placeOrder;
  window.fillAddress = fillAddress;
  window.editSelected = editSelected;
  window.deleteSelected = deleteSelected;
</script>
@endpush