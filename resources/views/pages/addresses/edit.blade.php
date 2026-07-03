@extends('layouts.app')
@section('title', 'Sửa địa chỉ — Nexus Store')
@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/pages/addresses.css') }}">
    <style>
        /* ===== Ô gõ-để-tìm cho Tỉnh/Thành phố & Phường/Xã ===== */
        .addr-combo { position: relative; }
        .addr-combo-panel {
            position: absolute;
            top: calc(100% + 4px);
            left: 0;
            right: 0;
            z-index: 30;
            background: #fff;
            border: 1px solid var(--border, #E3E3E3);
            border-radius: 8px;
            box-shadow: 0 10px 28px rgba(0, 0, 0, 0.12);
            max-height: 240px;
            overflow-y: auto;
            display: none;
        }
        .addr-combo-panel.is-open { display: block; }
        .addr-combo-item {
            padding: 9px 12px;
            font-size: 14px;
            color: var(--ink, #1A1A1A);
            cursor: pointer;
        }
        .addr-combo-item:hover,
        .addr-combo-item.is-active {
            background: var(--accent-soft, #F2EDE4);
        }
        .addr-combo-empty {
            padding: 12px;
            font-size: 13px;
            color: var(--ink-2, #8A8A8A);
        }
        .addr-combo-hint {
            font-size: 12px;
            color: var(--ink-2, #8A8A8A);
            margin-top: 4px;
        }
    </style>
@endpush
@section('content')
    <div class="container">
        <div class="addr-form-wrap">
            <div class="breadcrumb">
                <a href="{{ url('/') }}">Trang chủ</a>
                <span class="breadcrumb__sep">/</span>
                <a href="{{ url('dia-chi') }}">Địa chỉ</a>
                <span class="breadcrumb__sep">/</span>
                <span class="breadcrumb__current">Chỉnh sửa</span>
            </div>

            <h1 class="h1 mb-24">Chỉnh sửa địa chỉ</h1>

            <div class="addr-form-card">
                <form action="{{ route('addresses.update', $address) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="form-row-2">
                        <div class="form-group">
                            <label class="form-label">Họ tên người nhận</label>
                            <input type="text" name="receiver_name"
                                value="{{ old('receiver_name', $address->receiver_name) }}"
                                class="form-control {{ $errors->has('receiver_name') ? 'is-error' : '' }}"
                                placeholder="Nguyễn Văn A">
                            @error('receiver_name')
                                <p class="form-error">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label class="form-label">Số điện thoại</label>
                            <input type="text" name="receiver_phone"
                                value="{{ old('receiver_phone', $address->receiver_phone) }}"
                                class="form-control {{ $errors->has('receiver_phone') ? 'is-error' : '' }}"
                                placeholder="0900 000 000">
                            @error('receiver_phone')
                                <p class="form-error">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    
                        <div class="form-group addr-combo" id="province-combo">
                            <label class="form-label">Tỉnh / Thành phố</label>
                            <input type="text" name="province" id="province-input"
                                value="{{ old('province', $address->province) }}"
                                class="form-control {{ $errors->has('province') ? 'is-error' : '' }}"
                                placeholder="Gõ để tìm, VD: Hà Nội" autocomplete="off">
                            <div class="addr-combo-panel" id="province-panel"></div>
                            @error('province')
                                <p class="form-error">{{ $message }}</p>
                            @enderror
                        </div>
                        
                    

                    <div class="form-group addr-combo" id="ward-combo">
                        <label class="form-label">Phường / Xã</label>
                        <input type="text" name="ward" id="ward-input" value="{{ old('ward', $address->ward) }}"
                            class="form-control {{ $errors->has('ward') ? 'is-error' : '' }}"
                            placeholder="Chọn Tỉnh/Thành phố trước" autocomplete="off">
                        <div class="addr-combo-panel" id="ward-panel"></div>
                        @error('ward')
                            <p class="form-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label">Địa chỉ chi tiết</label>
                        <textarea name="address_detail" rows="2"
                            class="form-control {{ $errors->has('address_detail') ? 'is-error' : '' }}">{{ old('address_detail', $address->address_detail) }}</textarea>
                        @error('address_detail')
                            <p class="form-error">{{ $message }}</p>
                        @enderror
                    </div>

                    @if (!$address->is_default)
                        <div style="display:flex;align-items:center;gap:8px;margin-bottom:24px">
                            <input type="checkbox" name="is_default" value="1" id="isDefault"
                                {{ old('is_default') ? 'checked' : '' }}
                                style="width:16px;height:16px;accent-color:var(--accent);cursor:pointer">
                            <label for="isDefault" style="font-size:14px;color:var(--ink-2);cursor:pointer">Đặt làm địa chỉ
                                mặc định</label>
                        </div>
                    @else
                        <div
                            style="display:flex;align-items:center;gap:6px;margin-bottom:24px;font-size:13px;color:var(--green)">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2.5" stroke-linecap="round">
                                <polyline points="20 6 9 17 4 12" />
                            </svg>
                            Đây là địa chỉ mặc định của bạn
                        </div>
                    @endif

                    <div style="display:flex;gap:10px">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2" stroke-linecap="round">
                                <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z" />
                                <polyline points="17 21 17 13 7 13 7 21" />
                                <polyline points="7 3 7 8 15 8" />
                            </svg>
                            Cập nhật địa chỉ
                        </button>
                        <a href="{{ route('addresses.index') }}" class="btn btn-ghost btn-lg">Huỷ</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Dữ liệu 34 tỉnh/thành + phường/xã sau sáp nhập (Nghị quyết 202/2025/QH15) --}}
    <script src="{{ asset('assets/js/vn-address-data.js') }}"></script>
    <script>
        (function () {
            const DATA = window.VN_ADDRESS_DATA || [];

            if (!DATA.length) {
                console.error(
                    '[addr] Không load được vn-address-data.js — kiểm tra: ' +
                    '1) file có nằm đúng ở public/assets/js/vn-address-data.js không, ' +
                    '2) mở thẳng URL ' + '{{ asset("assets/js/vn-address-data.js") }}' + ' xem có 404 không.'
                );
                const provinceInput = document.getElementById('province-input');
                if (provinceInput) {
                    provinceInput.placeholder = 'Lỗi tải dữ liệu tỉnh/thành — báo kỹ thuật';
                }
                return; // dừng, không gắn sự kiện combo nếu không có data
            }

            function normalize(s) {
                return (s || '')
                    .toLowerCase()
                    .normalize('NFD')
                    .replace(/[\u0300-\u036f]/g, '')
                    .replace(/đ/g, 'd');
            }

            function findProvinceByName(name) {
                const n = normalize(name);
                if (!n) return null;
                return DATA.find(p => normalize(p.n) === n) || null;
            }

            function setupCombo({ inputId, panelId, getItems, onPick, emptyText }) {
                const input = document.getElementById(inputId);
                const panel = document.getElementById(panelId);
                let items = [];
                let activeIndex = -1;

                function renderPanel(query) {
                    items = getItems(query);
                    panel.innerHTML = '';
                    if (!items.length) {
                        panel.innerHTML = `<div class="addr-combo-empty">${emptyText}</div>`;
                        panel.classList.add('is-open');
                        return;
                    }
                    items.slice(0, 200).forEach((text, idx) => {
                        const el = document.createElement('div');
                        el.className = 'addr-combo-item';
                        el.textContent = text;
                        el.dataset.idx = idx;
                        el.addEventListener('mousedown', (e) => {
                            e.preventDefault();
                            input.value = text;
                            panel.classList.remove('is-open');
                            onPick && onPick(text);
                        });
                        panel.appendChild(el);
                    });
                    activeIndex = -1;
                    panel.classList.add('is-open');
                }

                input.addEventListener('focus', () => renderPanel(input.value));
                input.addEventListener('input', () => {
                    renderPanel(input.value);
                    onPick && onPick(null); // đang gõ dở, coi như chưa chọn xong
                });
                input.addEventListener('blur', () => {
                    setTimeout(() => panel.classList.remove('is-open'), 120);
                });
                input.addEventListener('keydown', (e) => {
                    const rows = panel.querySelectorAll('.addr-combo-item');
                    if (!rows.length) return;
                    if (e.key === 'ArrowDown') {
                        e.preventDefault();
                        activeIndex = Math.min(activeIndex + 1, rows.length - 1);
                    } else if (e.key === 'ArrowUp') {
                        e.preventDefault();
                        activeIndex = Math.max(activeIndex - 1, 0);
                    } else if (e.key === 'Enter') {
                        if (activeIndex >= 0 && rows[activeIndex]) {
                            e.preventDefault();
                            rows[activeIndex].dispatchEvent(new Event('mousedown'));
                        }
                        return;
                    } else {
                        return;
                    }
                    rows.forEach(r => r.classList.remove('is-active'));
                    rows[activeIndex].classList.add('is-active');
                });

                return { refresh: () => renderPanel(input.value) };
            }

            const wardInput = document.getElementById('ward-input');

            const wardCombo = setupCombo({
                inputId: 'ward-input',
                panelId: 'ward-panel',
                emptyText: 'Không tìm thấy phường/xã phù hợp',
                getItems: (query) => {
                    const province = findProvinceByName(document.getElementById('province-input').value);
                    if (!province) return [];
                    const q = normalize(query);
                    return q ? province.w.filter(w => normalize(w).includes(q)) : province.w;
                }
            });

            setupCombo({
                inputId: 'province-input',
                panelId: 'province-panel',
                emptyText: 'Không tìm thấy tỉnh/thành phố phù hợp',
                getItems: (query) => {
                    const q = normalize(query);
                    const names = DATA.map(p => p.n);
                    return q ? names.filter(n => normalize(n).includes(q)) : names;
                },
                onPick: (pickedName) => {
                    const province = findProvinceByName(pickedName || document.getElementById('province-input').value);
                    if (province) {
                        wardInput.disabled = false;
                        wardInput.placeholder = 'Gõ để tìm phường/xã';
                        // Nếu phường/xã đang chọn không thuộc tỉnh mới -> xoá để tránh sai dữ liệu
                        if (wardInput.value && !province.w.includes(wardInput.value)) {
                            wardInput.value = '';
                        }
                    } else {
                        wardInput.disabled = true;
                        wardInput.placeholder = 'Chọn Tỉnh/Thành phố trước';
                        wardInput.value = '';
                    }
                }
            });

            // Khởi tạo trạng thái ban đầu: dữ liệu địa chỉ cũ của user (province/ward đã lưu
            // trước đây) có thể không khớp tên mới sau sáp nhập -> không khoá cứng ward,
            // chỉ bật/tắt gợi ý dựa trên việc tỉnh hiện tại có khớp dữ liệu mới hay không.
            const initialProvince = findProvinceByName(document.getElementById('province-input').value);
            if (!initialProvince) {
                wardInput.disabled = false; // vẫn cho sửa tay vì có thể là địa chỉ cũ trước sáp nhập
                wardInput.placeholder = 'Gõ để tìm phường/xã (hoặc chọn lại Tỉnh/Thành phố ở trên)';
            }
        })();
    </script>
@endsection