@extends('layouts.app')
@section('title', 'Thanh toán — Nexus Store')
@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/pages/checkout.css') }}">
@endpush

@section('content')
    <div class="container checkout-wrap">
        <div class="breadcrumb">
            <a href="{{ url('/') }}">Trang chủ</a>
            <span class="breadcrumb__sep">/</span>
            <a href="{{ url('gio-hang') }}">Giỏ hàng</a>
            <span class="breadcrumb__sep">/</span>
            <span class="breadcrumb__current">Thanh toán</span>
        </div>

        <div class="checkout-layout">
            {{-- LEFT: FORM --}}
            <div>
                {{-- BƯỚC 1: ĐỊA CHỈ --}}
                <div class="ck-card">
                    <div class="ck-card-title">
                        <div class="ck-step-num">1</div>
                        Địa chỉ giao hàng
                    </div>

                    @auth
                        @if (Auth::user()->addresses && Auth::user()->addresses->count() > 0)
                            <div id="savedAddresses">
                                @foreach (Auth::user()->addresses()->latest()->get() as $addr)
                                    <label class="addr-option {{ $addr->is_default ? 'selected' : '' }}">
                                        <input type="radio" name="address_type" value="saved_{{ $addr->id }}"
                                            {{ $addr->is_default ? 'checked' : '' }}
                                            onchange="document.querySelectorAll('.addr-option').forEach(e=>e.classList.remove('selected'));this.closest('.addr-option').classList.add('selected');document.getElementById('newAddrForm').style.display='none'">
                                        <div>
                                            <div class="addr-option-name">{{ $addr->receiver_name }} —
                                                {{ $addr->receiver_phone }}</div>
                                            <div class="addr-option-detail">{{ $addr->address_detail }}, {{ $addr->ward }},
                                                {{ $addr->district }}, {{ $addr->province }}</div>
                                        </div>
                                        @if ($addr->is_default)
                                            <span class="badge badge-success" style="margin-left:auto;flex-shrink:0">Mặc
                                                định</span>
                                        @endif
                                    </label>
                                @endforeach

                                <label class="addr-option" id="newAddrOption">
                                    <input type="radio" name="address_type" value="new"
                                        onchange="document.querySelectorAll('.addr-option').forEach(e=>e.classList.remove('selected'));this.closest('.addr-option').classList.add('selected');document.getElementById('newAddrForm').style.display='block'">
                                    <div>
                                        <div class="addr-option-name"
                                            style="display:flex;align-items:center;gap:6px;color:var(--accent)">
                                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none"
                                                stroke="currentColor" stroke-width="2.5" stroke-linecap="round">
                                                <line x1="12" y1="5" x2="12" y2="19" />
                                                <line x1="5" y1="12" x2="19" y2="12" />
                                            </svg>
                                            Dùng địa chỉ mới
                                        </div>
                                    </div>
                                </label>
                            </div>

                            <div id="newAddrForm"
                                style="display:none;margin-top:16px;padding-top:16px;border-top:1px solid var(--border-soft)">
                                @include('pages.checkout._address_fields', [
                                    'prefix' => 'new_',
                                    'values' => [],
                                ])
                            </div>
                        @else
                            {{-- Chưa có địa chỉ --}}
                            <div id="newAddrForm">
                                @include('pages.checkout._address_fields', [
                                    'prefix' => 'new_',
                                    'values' => [],
                                ])
                            </div>
                        @endif
                    @else
                        {{-- Khách --}}
                        <div id="newAddrForm">
                            @include('pages.checkout._address_fields', [
                                'prefix' => 'new_',
                                'values' => [],
                            ])
                        </div>
                    @endauth
                </div>

                {{-- BƯỚC 2: GHI CHÚ --}}
                <div class="ck-card">
                    <div class="ck-card-title">
                        <div class="ck-step-num">2</div>
                        Ghi chú đơn hàng
                    </div>
                    <textarea id="orderNote" class="form-control" rows="3"
                        placeholder="Ghi chú cho người giao hàng (không bắt buộc)..."></textarea>
                </div>

                {{-- BƯỚC 3: PHƯƠNG THỨC THANH TOÁN --}}
                <div class="ck-card">
                    <div class="ck-card-title">
                        <div class="ck-step-num">3</div>
                        Phương thức thanh toán
                    </div>

                    @php $payMethods = [['value' => 'cod', 'label' => 'Thanh toán khi nhận hàng (COD)', 'sub' => 'Trả tiền mặt khi nhận hàng', 'icon' => '<rect x="1" y="4" width="22" height="16" rx="2"/><line x1="1" y1="10" x2="23" y2="10"/>'], ['value' => 'momo', 'label' => 'Ví MoMo', 'sub' => 'Thanh toán qua ví điện tử MoMo', 'icon' => 'M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 14l-4-4h3V8h2v4h3z'], ['value' => 'bank', 'label' => 'Chuyển khoản ngân hàng', 'sub' => 'Chuyển khoản qua QR hoặc tài khoản', 'icon' => 'M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z M9 22V12h6v10'], ['value' => 'zalopay', 'label' => 'ZaloPay', 'sub' => 'Thanh toán qua ví ZaloPay', 'icon' => 'M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z']]; @endphp

                    @foreach ($payMethods as $i => $m)
                        <label class="pay-option {{ $i === 0 ? 'selected' : '' }}">
                            <input type="radio" name="payment_method" value="{{ $m['value'] }}"
                                {{ $i === 0 ? 'checked' : '' }}
                                onchange="document.querySelectorAll('.pay-option').forEach(e=>e.classList.remove('selected'));this.closest('.pay-option').classList.add('selected')">
                            <div class="pay-option-icon">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="1.7" stroke-linecap="round">
                                    <path d="{{ $m['icon'] }}" />
                                </svg>
                            </div>
                            <div>
                                <div class="pay-option-label">{{ $m['label'] }}</div>
                                <div class="pay-option-sub">{{ $m['sub'] }}</div>
                            </div>
                        </label>
                    @endforeach
                </div>
            </div>

            {{-- RIGHT: SUMMARY --}}
            <div class="summary-card">
                <div class="summary-title">Tóm tắt đơn hàng</div>

                <div id="summaryItems">
                    {{-- render by JS --}}
                </div>

                <div class="summary-divider"></div>

                <div id="couponSection">
                    {{-- Hiển thị mã khả dụng nếu có --}}
                    @if (isset($coupons) && $coupons->count() > 0)
                        <div style="margin-bottom:8px">
                            <p style="font-size:12px;color:var(--ink-3);margin-bottom:6px">Mã khả dụng:</p>
                            <div style="display:flex;flex-wrap:wrap;gap:6px">
                                @foreach ($coupons as $c)
                                    <button type="button"
                                        onclick="document.getElementById('couponCode').value='{{ $c->coupon_code }}'"
                                        style="padding:3px 10px;border:1.5px dashed var(--accent);border-radius:99px;
                   font-size:11px;font-weight:600;color:var(--accent);cursor:pointer;
                   background:var(--accent-light);transition:var(--t)"
                                        title="Giảm {{ $c->type == 0 ? $c->value . '%' : number_format($c->value, 0, ',', '.') . '₫' }}">
                                        {{ $c->coupon_code }}
                                    </button>
                                @endforeach
                            </div>
                        </div>
                    @endif
                    <div class="coupon-row">
                        <input type="text" class="coupon-input" id="couponCode" placeholder="Nhập mã giảm giá">
                        <button class="btn btn-outline btn-sm" onclick="applyCoupon()">Áp dụng</button>
                    </div>
                    <div class="coupon-row" id="couponInputRow">
                        <input type="text" class="coupon-input" id="couponCode" placeholder="Mã giảm giá">
                        <button class="btn btn-outline btn-sm" onclick="applyCoupon()">Áp dụng</button>
                    </div>
                    <div id="couponApplied" style="display:none"></div>
                </div>

                <div class="summary-divider"></div>

                <div class="summary-line">
                    <span style="color:var(--ink-3)">Tạm tính</span>
                    <span id="sumSubtotal" style="font-weight:600">0₫</span>
                </div>
                <div class="summary-line" id="discountLine" style="display:none">
                    <span style="color:var(--green)">Giảm giá</span>
                    <span id="sumDiscount" style="color:var(--green);font-weight:600"></span>
                </div>
                <div class="summary-line">
                    <span style="color:var(--ink-3)">Vận chuyển</span>
                    <span style="color:var(--green);font-weight:600">Miễn phí</span>
                </div>

                <div class="summary-divider"></div>

                <div class="summary-total">
                    <span class="summary-total-label">Tổng cộng</span>
                    <span class="summary-total-price" id="sumTotal">0₫</span>
                </div>

                <button onclick="placeOrder()" class="btn btn-accent btn-full btn-lg mt-8">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2" stroke-linecap="round">
                        <polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2" />
                    </svg>
                    Đặt hàng ngay
                </button>

                <div
                    style="display:flex;align-items:center;justify-content:center;gap:6px;margin-top:14px;font-size:12px;color:var(--ink-muted)">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2" stroke-linecap="round">
                        <rect x="3" y="11" width="18" height="11" rx="2" />
                        <path d="M7 11V7a5 5 0 0 1 10 0v4" />
                    </svg>
                    Thanh toán bảo mật SSL 256-bit
                </div>

                <a href="{{ url('gio-hang') }}"
                    style="display:flex;align-items:center;justify-content:center;gap:5px;font-size:13px;color:var(--ink-muted);margin-top:12px;transition:var(--t)"
                    onmouseover="this.style.color='var(--ink)'" onmouseout="this.style.color='var(--ink-muted)'">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2" stroke-linecap="round">
                        <line x1="19" y1="12" x2="5" y2="12" />
                        <polyline points="12 19 5 12 12 5" />
                    </svg>
                    Quay lại giỏ hàng
                </a>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        const CSRF = '{{ csrf_token() }}';
        let appliedDiscount = 0;

        function fmtPrice(n) {
            return n.toLocaleString('vi-VN') + '₫';
        }

        function renderSummary() {
            const items = Cart.get();
            const container = document.getElementById('summaryItems');
            if (!items.length) {
                container.innerHTML =
                    '<p style="text-align:center;color:var(--ink-muted);font-size:13px;padding:12px 0">Giỏ hàng trống</p>';
                ['sumSubtotal', 'sumTotal'].forEach(id => document.getElementById(id).textContent = '0₫');
                return;
            }
            container.innerHTML = items.map(i => `
    <div class="summary-item-row">
      <div class="summary-item-img">
        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round"><rect x="2" y="3" width="20" height="14" rx="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/></svg>
        <span class="summary-item-qty">${i.qty}</span>
      </div>
      <span class="summary-item-name">${i.name}</span>
      <span class="summary-item-price">${fmtPrice(i.price*i.qty)}</span>
    </div>
  `).join('');

            const sub = Cart.total();
            const total = Math.max(0, sub - appliedDiscount);
            document.getElementById('sumSubtotal').textContent = fmtPrice(sub);
            document.getElementById('sumTotal').textContent = fmtPrice(total);
        }

        function applyCoupon() {
            const code = document.getElementById('couponCode').value.trim().toUpperCase();
            if (!code) {
                Toast.show('Nhập mã giảm giá trước', 'error');
                return;
            }

            fetch('{{ route('coupon.apply') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': CSRF
                    },
                    body: JSON.stringify({
                        coupon_code: code,
                        sub_total: Cart.total()
                    })
                })
                .then(r => r.json())
                .then(data => {
                    if (data.success) {
                        appliedDiscount = data.discount_amount || 0;
                        document.getElementById('couponInputRow').style.display = 'none';
                        document.getElementById('couponApplied').style.display = 'block';
                        document.getElementById('couponApplied').innerHTML = `
        <div class="coupon-applied">
          <span>
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" style="margin-right:4px"><polyline points="20 6 9 17 4 12"/></svg>
            ${code} — Giảm ${fmtPrice(appliedDiscount)}
          </span>
          <button onclick="removeCoupon()" style="color:var(--red);font-size:12px;font-weight:500;cursor:pointer;background:none;border:none">Xoá</button>
        </div>`;
                        document.getElementById('discountLine').style.display = 'flex';
                        document.getElementById('sumDiscount').textContent = '-' + fmtPrice(appliedDiscount);
                        renderSummary();
                        Toast.show(data.message || 'Áp dụng mã thành công!', 'success');
                    } else {
                        Toast.show(data.message || 'Mã không hợp lệ', 'error');
                    }
                })
                .catch(() => Toast.show('Không thể kiểm tra mã, thử lại sau', 'error'));
        }

        function removeCoupon() {
            fetch('{{ route('coupon.remove') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': CSRF
                },
                body: JSON.stringify({})
            }).finally(() => {
                appliedDiscount = 0;
                document.getElementById('couponInputRow').style.display = 'flex';
                document.getElementById('couponApplied').style.display = 'none';
                document.getElementById('discountLine').style.display = 'none';
                document.getElementById('couponCode').value = '';
                renderSummary();
                Toast.show('Đã xoá mã giảm giá', 'info');
            });
        }

        function placeOrder() {
            const btn = document.querySelector('button[onclick="placeOrder()"]');

            // FIX: Khóa nút ngay lập tức, tránh bấm 2 lần
            if (btn.disabled) return; // Nếu đang xử lý rồi → bỏ qua hoàn toàn
            btn.disabled = true;
            btn.innerHTML = `
        <svg class="spin" width="16" height="16" viewBox="0 0 24 24" fill="none"
             stroke="currentColor" stroke-width="2" stroke-linecap="round">
            <path d="M21 12a9 9 0 1 1-6.219-8.56"/>
        </svg>
        Đang xử lý...
    `;
            const cart = Cart.get();
            if (!cart.length) {
                Toast.show('Giỏ hàng trống!', 'error');
                // Mở khóa nút nếu validate thất bại
                resetBtn(btn);
                return;
            }

            const payMethod = document.querySelector('input[name="payment_method"]:checked')?.value || 'cod';
            const addrType = document.querySelector('input[name="address_type"]:checked')?.value || '';
            const note = document.getElementById('orderNote').value;

            let addrPayload = {};
            if (addrType.startsWith('saved_')) {
                addrPayload.address_id = parseInt(addrType.replace('saved_', ''));
            } else {
                addrPayload.receiver_name = document.getElementById('new_receiver_name')?.value;
                addrPayload.receiver_phone = document.getElementById('new_receiver_phone')?.value;
                addrPayload.province = document.getElementById('new_province')?.value;
                addrPayload.district = document.getElementById('new_district')?.value;
                addrPayload.ward = document.getElementById('new_ward')?.value;
                addrPayload.address_detail = document.getElementById('new_address_detail')?.value;
                if (!addrPayload.receiver_name || !addrPayload.receiver_phone || !addrPayload.province) {
                    Toast.show('Vui lòng điền đầy đủ thông tin địa chỉ', 'error');
                    return;
                }
            }

            fetch('{{ route('checkout.store') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': CSRF
                    },
                    body: JSON.stringify({
                        cart,
                        address_id: addrPayload.address_id,
                        payment_method: payMethod, 
                        coupon_code: document.getElementById('couponCode')?.value || '',
                        note: document.getElementById('orderNote')?.value || '',
                    })
                })
                .then(r => {
                    if (!r.ok && r.status === 422) {
                        return r.json().then(err => {
                            const msgs = err.errors ? Object.values(err.errors).flat().join('\n') : (err
                                .message || 'Dữ liệu không hợp lệ');
                            Toast.show(msgs, 'error');
                            throw new Error('validation');
                        });
                    }
                    return r.json();
                })
                .then(data => {
                    if (data.success) {
                        localStorage.removeItem('nx_cart');
                        Cart.updateUI();
                        Toast.show('Đặt hàng thành công!', 'success');
                        setTimeout(() => window.location.href = '{{ url('don-hang') }}', 1200);
                    } else {
                        Toast.show(data.message || 'Có lỗi xảy ra, vui lòng thử lại', 'error');
                        resetBtn(btn); // Mở khóa để người dùng thử lại
                    }
                })
                .catch(err => {
                    if (err.message !== 'validation') Toast.show('Lỗi kết nối, thử lại sau', 'error');
                    resetBtn(btn); // Mở khóa để người dùng thử lại
                });
        }

        // Hàm phụ: khôi phục nút về trạng thái ban đầu
        function resetBtn(btn) {
            btn.disabled = false;
            btn.innerHTML = `
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none"
             stroke="currentColor" stroke-width="2" stroke-linecap="round">
            <polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/>
        </svg>
        Đặt hàng ngay
    `;
        }

        document.addEventListener('DOMContentLoaded', renderSummary);
        Object.assign(window, {
            applyCoupon,
            removeCoupon,
            placeOrder
        });
    </script>
@endpush
