@extends('layouts.app')
@section('title', $title . ' — Nexus Store')

@section('content')
<div class="container" style="padding:48px 0 80px; max-width:860px">

    {{-- Breadcrumb --}}
    <div class="breadcrumb mb-32">
        <a href="{{ url('/') }}">Trang chủ</a>
        <span class="breadcrumb__sep">›</span>
        <span class="breadcrumb__current">{{ $title }}</span>
    </div>

    {{-- Header --}}
    <div style="display:flex;align-items:center;gap:14px;margin-bottom:36px">
        <div style="width:48px;height:48px;border-radius:12px;background:var(--accent-light);
                    display:flex;align-items:center;justify-content:center;color:var(--accent);flex-shrink:0">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                 stroke="currentColor" stroke-width="1.8" stroke-linecap="round">
                <path d="{{ $icon }}"/>
            </svg>
        </div>
        <h1 class="h1">{{ $title }}</h1>
    </div>

    {{-- Nội dung theo từng slug --}}
    <div style="font-size:15px;line-height:1.9;color:var(--ink-2)">

        @if($slug === 'doi-tra')
        <div class="policy-section">
            <h2 class="policy-h2">1. Điều kiện đổi trả</h2>
            <p>Nexus Store chấp nhận đổi trả sản phẩm trong vòng <strong>30 ngày</strong> kể từ ngày nhận hàng với các điều kiện sau:</p>
            <ul class="policy-list">
                <li>Sản phẩm còn nguyên seal, chưa qua sử dụng (nếu yêu cầu đổi do lỗi người dùng)</li>
                <li>Sản phẩm bị lỗi kỹ thuật do nhà sản xuất: đổi mới 100% trong 7 ngày</li>
                <li>Có hóa đơn mua hàng và đầy đủ phụ kiện đi kèm</li>
                <li>Không áp dụng với sản phẩm đã qua sửa chữa bởi đơn vị ngoài</li>
            </ul>
        </div>
        <div class="policy-section">
            <h2 class="policy-h2">2. Quy trình đổi trả</h2>
            <ol class="policy-list">
                <li>Liên hệ hotline <strong>1800 9999</strong> hoặc email support@nexusstore.vn</li>
                <li>Cung cấp mã đơn hàng và mô tả lý do đổi trả</li>
                <li>Đội ngũ xác nhận và gửi hướng dẫn hoàn trả trong 24 giờ</li>
                <li>Nhận sản phẩm mới hoặc hoàn tiền trong 3-5 ngày làm việc</li>
            </ol>
        </div>

        @elseif($slug === 'bao-hanh')
        <div class="policy-section">
            <h2 class="policy-h2">1. Thời gian bảo hành</h2>
            <ul class="policy-list">
                <li><strong>Laptop, Điện thoại, Tablet:</strong> 12 tháng bảo hành chính hãng</li>
                <li><strong>Tai nghe, Smartwatch:</strong> 12 tháng</li>
                <li><strong>Phụ kiện:</strong> 3-6 tháng tùy loại</li>
            </ul>
        </div>
        <div class="policy-section">
            <h2 class="policy-h2">2. Điều kiện bảo hành</h2>
            <ul class="policy-list">
                <li>Sản phẩm còn trong thời hạn bảo hành</li>
                <li>Lỗi do nhà sản xuất, không phải do tác động vật lý hay chất lỏng</li>
                <li>Không áp dụng nếu sản phẩm bị bóc seal bảo hành hoặc sửa chữa ngoài</li>
            </ul>
        </div>

        @elseif($slug === 'van-chuyen')
        <div class="policy-section">
            <h2 class="policy-h2">1. Phí vận chuyển</h2>
            <ul class="policy-list">
                <li><strong>Đơn từ 500.000₫:</strong> Miễn phí giao hàng toàn quốc</li>
                <li><strong>Đơn dưới 500.000₫:</strong> Phí 30.000₫ nội thành, 50.000₫ ngoại thành</li>
                <li><strong>Giao nhanh 2H nội thành TP.HCM & Hà Nội:</strong> Phí 50.000₫</li>
            </ul>
        </div>
        <div class="policy-section">
            <h2 class="policy-h2">2. Thời gian giao hàng</h2>
            <ul class="policy-list">
                <li>Nội thành TP.HCM & Hà Nội: 1-2 ngày làm việc</li>
                <li>Các tỉnh thành khác: 2-5 ngày làm việc</li>
                <li>Vùng sâu vùng xa: 5-7 ngày làm việc</li>
            </ul>
        </div>

        @elseif($slug === 'bao-mat')
        <div class="policy-section">
            <h2 class="policy-h2">1. Thu thập thông tin</h2>
            <p>Nexus Store thu thập các thông tin cần thiết để xử lý đơn hàng bao gồm: họ tên, số điện thoại, địa chỉ giao hàng và email. Thông tin thẻ thanh toán được xử lý trực tiếp bởi cổng thanh toán, Nexus Store không lưu trữ.</p>
        </div>
        <div class="policy-section">
            <h2 class="policy-h2">2. Cam kết bảo mật</h2>
            <ul class="policy-list">
                <li>Toàn bộ giao dịch được mã hóa SSL 256-bit</li>
                <li>Không bán hoặc chia sẻ thông tin khách hàng cho bên thứ ba</li>
                <li>Khách hàng có quyền yêu cầu xóa dữ liệu cá nhân bất kỳ lúc nào</li>
            </ul>
        </div>

        @elseif($slug === 'mua-hang')
        <div class="policy-section">
            <h2 class="policy-h2">Các bước mua hàng</h2>
            <ol class="policy-list">
                <li>Chọn sản phẩm và biến thể (màu sắc, dung lượng...)</li>
                <li>Bấm <strong>"Thêm vào giỏ"</strong> hoặc <strong>"Mua ngay"</strong></li>
                <li>Vào giỏ hàng, kiểm tra lại đơn</li>
                <li>Bấm <strong>"Tiến hành đặt hàng"</strong></li>
                <li>Điền địa chỉ giao hàng và chọn phương thức thanh toán</li>
                <li>Bấm <strong>"Đặt hàng ngay"</strong></li>
                <li>Nhận email xác nhận đơn hàng</li>
            </ol>
        </div>
        @endif

    </div>

    {{-- Liên kết các chính sách khác --}}
    <div style="margin-top:48px;padding-top:32px;border-top:1px solid var(--border-soft)">
        <div style="font-size:13px;font-weight:600;color:var(--ink-muted);text-transform:uppercase;letter-spacing:.06em;margin-bottom:14px">
            Xem thêm
        </div>
        <div style="display:flex;gap:10px;flex-wrap:wrap">
            @foreach(['doi-tra'=>'Đổi trả','bao-hanh'=>'Bảo hành','van-chuyen'=>'Vận chuyển','bao-mat'=>'Bảo mật','mua-hang'=>'Hướng dẫn mua hàng'] as $s => $label)
            @if($s !== $slug)
            <a href="{{ route('policy', $s) }}"
               style="padding:7px 16px;border:1.5px solid var(--border);border-radius:99px;
                      font-size:13px;color:var(--ink-2);text-decoration:none;transition:var(--t)"
               onmouseover="this.style.borderColor='var(--accent)';this.style.color='var(--accent)'"
               onmouseout="this.style.borderColor='var(--border)';this.style.color='var(--ink-2)'">
                {{ $label }}
            </a>
            @endif
            @endforeach
        </div>
    </div>
</div>

@push('styles')
<style>
.policy-section   { margin-bottom: 32px; }
.policy-h2        { font-size: 17px; font-weight: 700; color: var(--ink); margin-bottom: 12px; padding-bottom: 8px; border-bottom: 2px solid var(--accent-light); }
.policy-list      { padding-left: 20px; margin-top: 10px; }
.policy-list li   { margin-bottom: 8px; }
.policy-list li strong { color: var(--ink); }
</style>
@endpush
@endsection