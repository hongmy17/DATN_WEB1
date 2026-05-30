@extends('layouts.app')

@section('title', 'Liên hệ - Nexus Store')

@section('content')
<div class="container section">
    <div class="breadcrumb mb-32">
        <a href="{{ url('/') }}">Trang chủ</a>
        <span class="breadcrumb__sep">›</span>
        <span class="breadcrumb__current">Liên Hệ</span>
    </div>

    <div class="section-header section-header--center mb-48">
        <div class="section-header__eyebrow">Hỗ Trợ</div>
        <h1 class="display-2">Liên hệ <span class="text-accent">với chúng tôi</span></h1>
        <p class="section-header__sub">Đội ngũ tư vấn luôn sẵn sàng hỗ trợ bạn 24/7</p>
    </div>

    <div style="display:grid; grid-template-columns:1fr 1.5fr; gap:40px">
        <div>
            <div style="background:var(--bg-alt); border-radius:var(--r-xl); padding:28px">
                <div style="display:flex; gap:14px; padding:14px 0; border-bottom:1px solid var(--border-soft)">
                    <div style="background:var(--accent-light); width:44px; height:44px; border-radius:var(--r-lg); display:flex; align-items:center; justify-content:center; font-size:20px">📞</div>
                    <div>
                        <div style="font-weight:700">Hotline</div>
                        <div style="font-size:14px; color:var(--ink-3)">1900 1234 (miễn phí)<br>Thứ 2 – CN: 8:00 – 22:00</div>
                    </div>
                </div>
                <div style="display:flex; gap:14px; padding:14px 0; border-bottom:1px solid var(--border-soft)">
                    <div style="background:var(--green-light); width:44px; height:44px; border-radius:var(--r-lg); display:flex; align-items:center; justify-content:center; font-size:20px">✉️</div>
                    <div>
                        <div style="font-weight:700">Email</div>
                        <div style="font-size:14px; color:var(--ink-3)">support@nexus.vn<br>Phản hồi trong 2 giờ</div>
                    </div>
                </div>
                <div style="display:flex; gap:14px; padding:14px 0">
                    <div style="background:var(--surface-2); width:44px; height:44px; border-radius:var(--r-lg); display:flex; align-items:center; justify-content:center; font-size:20px">📍</div>
                    <div>
                        <div style="font-weight:700">Cửa hàng</div>
                        <div style="font-size:14px; color:var(--ink-3)">123 Lê Lợi, Q.1, TP.HCM</div>
                    </div>
                </div>
            </div>
        </div>
        <div style="background:var(--bg-alt); border-radius:var(--r-xl); padding:32px">
            <h3 class="heading-2 mb-24">Gửi tin nhắn cho chúng tôi</h3>
            <form>
                <div class="form-group"><label class="form-label">Họ tên</label><input type="text" class="form-control"></div>
                <div class="form-group"><label class="form-label">Email</label><input type="email" class="form-control"></div>
                <div class="form-group"><label class="form-label">Số điện thoại</label><input type="tel" class="form-control"></div>
                <div class="form-group"><label class="form-label">Nội dung</label><textarea class="form-control" rows="5"></textarea></div>
                <button type="button" class="btn btn-primary btn-lg btn-full" onclick="Toast.show('Tin nhắn đã được gửi!','success')">📤 Gửi tin nhắn</button>
            </form>
        </div>
    </div>
</div>
@endsection