@extends('layouts.app')
@section('title', 'Yêu cầu hoàn tiền — Nexus Store')

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/pages/order.css') }}">
@endpush

@section('content')
    <div class="container" style="padding-bottom:80px">
        <div style="max-width:640px;margin:0 auto">

            <a href="{{ route('orders.show', $order) }}"
                style="display:inline-flex;align-items:center;gap:6px;font-size:13px;color:var(--ink-muted);margin-bottom:20px;text-decoration:none">
                ← Quay lại đơn hàng
            </a>

            <div class="od-card">
                <div class="od-card-title" style="margin-bottom:16px">
                    Yêu cầu hoàn tiền — Đơn NX-{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}
                </div>

                @if ($errors->any())
                    <div style="padding:12px 16px;background:var(--red-light);color:var(--red);border-radius:var(--r-md);font-size:13px;margin-bottom:16px">
                        <ul style="margin:0;padding-left:18px">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('refunds.store', $order) }}" enctype="multipart/form-data">
                    @csrf

                    <div style="margin-bottom:16px">
                        <label style="display:block;font-size:13px;font-weight:600;margin-bottom:6px">
                            Lý do hoàn tiền <span style="color:var(--red)">*</span>
                        </label>
                        <select name="reason" required
                            style="width:100%;padding:10px 12px;border:1px solid var(--border-soft);border-radius:var(--r-md)">
                            <option value="">-- Chọn lý do --</option>
                            @foreach (\App\Models\RefundRequest::REASONS as $key => $label)
                                <option value="{{ $key }}" {{ old('reason') === $key ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div style="margin-bottom:16px">
                        <label style="display:block;font-size:13px;font-weight:600;margin-bottom:6px">
                            Mô tả thêm
                        </label>
                        <textarea name="reason_detail" rows="4" maxlength="2000"
                            placeholder="Mô tả chi tiết vấn đề bạn gặp phải..."
                            style="width:100%;padding:10px 12px;border:1px solid var(--border-soft);border-radius:var(--r-md)">{{ old('reason_detail') }}</textarea>
                    </div>

                    <div style="margin-bottom:16px">
                        <label style="display:block;font-size:13px;font-weight:600;margin-bottom:6px">
                            Ảnh/Video bằng chứng (tối đa 5 file, mỗi file &lt; 10MB)
                        </label>
                        <input type="file" name="evidence_images[]" multiple accept="image/*,video/*">
                    </div>

                    @if ($order->payment_method === 'cod')
                        <input type="hidden" name="need_bank_info" value="1">
                        <div style="padding:14px 16px;background:var(--bg-alt);border-radius:var(--r-md);margin-bottom:16px">
                            <p style="font-size:13px;font-weight:600;margin:0 0 10px">
                                Đơn hàng thanh toán COD — vui lòng cung cấp thông tin nhận tiền:
                            </p>
                            <input type="text" name="bank_name" placeholder="Tên ngân hàng" value="{{ old('bank_name') }}"
                                style="width:100%;padding:10px 12px;border:1px solid var(--border-soft);border-radius:var(--r-md);margin-bottom:8px">
                            <input type="text" name="bank_account_number" placeholder="Số tài khoản" value="{{ old('bank_account_number') }}"
                                style="width:100%;padding:10px 12px;border:1px solid var(--border-soft);border-radius:var(--r-md);margin-bottom:8px">
                            <input type="text" name="bank_account_holder" placeholder="Tên chủ tài khoản" value="{{ old('bank_account_holder') }}"
                                style="width:100%;padding:10px 12px;border:1px solid var(--border-soft);border-radius:var(--r-md)">
                        </div>
                    @else
                        <div style="padding:12px 16px;background:var(--bg-alt);border-radius:var(--r-md);margin-bottom:16px;font-size:13px;color:var(--ink-muted)">
                            Đơn hàng thanh toán qua {{ strtoupper($order->payment_method) }} — hệ thống sẽ tự
                            đối chiếu theo mã giao dịch cũ, bạn không cần nhập tài khoản ngân hàng.
                        </div>
                    @endif

                    <button type="submit" class="btn btn-primary" style="width:100%">
                        Gửi yêu cầu hoàn tiền
                    </button>
                </form>
            </div>
        </div>
    </div>
@endsection