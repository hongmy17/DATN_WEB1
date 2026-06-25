@extends('layouts.app')
@section('title', 'Liên hệ — Nexus Store')
@push('styles')
<link rel="stylesheet" href="{{ asset('assets/css/pages/contact.css') }}">
@endpush
@section('content')
<div class="container">
  <div class="contact-layout">
    <div class="contact-info-card">
      <h1 class="contact-info-title">Liên hệ với chúng tôi</h1>
      <p class="contact-info-sub">Chúng tôi luôn sẵn sàng hỗ trợ bạn 24/7. Hãy để lại tin nhắn và chúng tôi sẽ phản hồi sớm nhất.</p>

      @php $infos = [
        ['icon'=>'M21 10c0 7-9 13-9 13S3 17 3 10a9 9 0 0 1 18 0z M12 10m-3 0a3 3 0 1 0 6 0 3 3 0 1 0-6 0','label'=>'Địa chỉ','val'=>'123 Nguyễn Huệ, Quận 1, TP. Hồ Chí Minh'],
        ['icon'=>'M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07A19.5 19.5 0 0 1 4.07 12a19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 3 1.18h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L7.09 8.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7a2 2 0 0 1 1.72 2.03z','label'=>'Điện thoại','val'=>'1800 9999 (Miễn phí)'],
        ['icon'=>'M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z M22,6 L12,13 2,6','label'=>'Email','val'=>'support@nexusstore.vn'],
        ['icon'=>'M12 2a10 10 0 1 0 0 20 10 10 0 0 0 0-20z M12 6v6l4 2','label'=>'Giờ làm việc','val'=>'8:00 – 22:00, Thứ 2 – Chủ nhật'],
      ]; @endphp

      @foreach($infos as $info)
      <div class="contact-item">
        <div class="contact-item-icon">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"><path d="{{ $info['icon'] }}"/></svg>
        </div>
        <div>
          <div class="contact-item-label">{{ $info['label'] }}</div>
          <div class="contact-item-value">{{ $info['val'] }}</div>
        </div>
      </div>
      @endforeach
    </div>

    <div class="contact-form-card">
      <div class="contact-form-title">Gửi tin nhắn</div>
      <div class="form-row" style="display:grid;grid-template-columns:1fr 1fr;gap:12px">
        <div class="form-group"><label class="form-label">Họ và tên</label><input type="text" class="form-control" placeholder="Nguyễn Văn A"></div>
        <div class="form-group"><label class="form-label">Email</label><input type="email" class="form-control" placeholder="email@example.com"></div>
      </div>
      <div class="form-group"><label class="form-label">Số điện thoại</label><input type="tel" class="form-control" placeholder="0900 000 000"></div>
      <div class="form-group"><label class="form-label">Chủ đề</label>
        <select class="form-control">
          <option>Tư vấn sản phẩm</option>
          <option>Hỗ trợ đơn hàng</option>
          <option>Bảo hành & sửa chữa</option>
          <option>Khiếu nại</option>
          <option>Khác</option>
        </select>
      </div>
      <div class="form-group"><label class="form-label">Nội dung</label><textarea class="form-control" placeholder="Mô tả vấn đề của bạn..." style="min-height:120px"></textarea></div>
      <button onclick="Toast.show('Đã gửi tin nhắn! Chúng tôi sẽ phản hồi trong vòng 24h.','success')" class="btn btn-accent btn-full btn-lg">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg>
        Gửi tin nhắn
      </button>
    </div>
  </div>
</div>
@endsection
