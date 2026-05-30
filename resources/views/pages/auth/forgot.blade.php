@extends('layouts.app')

@section('title', 'Quên mật khẩu - Nexus Store')

@push('styles')
<style>
.forgot-wrap{min-height:calc(100vh - 200px);display:flex;align-items:center;justify-content:center;padding:40px 16px;background:var(--surface)}
.forgot-card{width:100%;max-width:440px;background:var(--bg-alt);border:1px solid var(--border-soft);border-radius:var(--r-2xl);padding:40px;box-shadow:var(--shadow-lg)}
.step-indicator{display:flex;gap:8px;margin-bottom:28px;justify-content:center}
.si{width:8px;height:8px;border-radius:50%;background:var(--border);transition:var(--transition)}
.si.active{background:var(--accent);width:24px;border-radius:4px}
</style>
@endpush

@section('content')
<div class="forgot-wrap">
  <div class="forgot-card">
    <a href="{{ url('/') }}" class="navbar__logo" style="justify-content:center;margin-bottom:24px;font-size:22px;display:flex">
      <em class="navbar__logo-icon">N</em>Nexus<span style="color:var(--accent)">.</span>
    </a>
    <div class="step-indicator"><div class="si active" id="si1"></div><div class="si" id="si2"></div><div class="si" id="si3"></div></div>

    <div id="step1">
      <h2 class="heading-2 mb-8">Quên mật khẩu?</h2>
      <p class="body-md text-ink3 mb-24">Nhập email hoặc số điện thoại đã đăng ký. Chúng tôi sẽ gửi mã xác nhận.</p>
      <div class="form-group"><label class="form-label">Email / Số điện thoại</label><div class="input-icon"><span class="input-icon__icon">📧</span><input type="text" class="form-control" placeholder="email@gmail.com hoặc 0901234567" id="resetId"></div></div>
      <button class="btn btn-primary btn-full btn-lg" onclick="nextStep(2)">Gửi mã xác nhận</button>
      <div class="text-center mt-16"><a href="{{ url('tai-khoan') }}" style="font-size:13px;color:var(--accent)">← Quay lại đăng nhập</a></div>
    </div>

    <div id="step2" style="display:none">
      <h2 class="heading-2 mb-8">Nhập mã xác nhận</h2>
      <p class="body-md text-ink3 mb-24">Mã 6 số đã được gửi đến <strong id="sentTo"></strong></p>
      <div class="form-group"><label class="form-label">Mã OTP</label>
        <div style="display:flex;gap:8px">
          <input type="text" class="form-control" maxlength="1" style="text-align:center;font-size:22px;font-weight:800">
          <input type="text" class="form-control" maxlength="1" style="text-align:center;font-size:22px;font-weight:800">
          <input type="text" class="form-control" maxlength="1" style="text-align:center;font-size:22px;font-weight:800">
          <input type="text" class="form-control" maxlength="1" style="text-align:center;font-size:22px;font-weight:800">
          <input type="text" class="form-control" maxlength="1" style="text-align:center;font-size:22px;font-weight:800">
          <input type="text" class="form-control" maxlength="1" style="text-align:center;font-size:22px;font-weight:800">
        </div>
      </div>
      <button class="btn btn-primary btn-full btn-lg" onclick="nextStep(3)">Xác nhận</button>
      <div class="text-center mt-12" style="font-size:13px;color:var(--ink-muted)">Không nhận được mã? <a style="color:var(--accent);cursor:pointer" onclick="Toast.show('Đã gửi lại mã OTP!','success')">Gửi lại</a></div>
    </div>

    <div id="step3" style="display:none">
      <h2 class="heading-2 mb-8">Tạo mật khẩu mới</h2>
      <p class="body-md text-ink3 mb-24">Mật khẩu mới phải ít nhất 8 ký tự</p>
      <div class="form-group"><label class="form-label">Mật khẩu mới</label><div class="input-icon"><span class="input-icon__icon">🔒</span><input type="password" class="form-control" placeholder="Ít nhất 8 ký tự"></div></div>
      <div class="form-group"><label class="form-label">Xác nhận mật khẩu mới</label><div class="input-icon"><span class="input-icon__icon">🔒</span><input type="password" class="form-control" placeholder="Nhập lại mật khẩu mới"></div></div>
      <button class="btn btn-primary btn-full btn-lg" onclick="Toast.show('Đặt lại mật khẩu thành công! ✓','success');setTimeout(()=>window.location.href='{{ url("tai-khoan") }}',1200)">Đặt Lại Mật Khẩu</button>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
  function nextStep(n) {
    if (n===2 && !document.getElementById('resetId').value) { Toast.show('Vui lòng nhập email hoặc SĐT','error'); return; }
    document.getElementById('sentTo').textContent = document.getElementById('resetId')?.value || '';
    for(let i=1;i<=3;i++) document.getElementById('step'+i).style.display = i===n?'':'none';
    for(let i=1;i<=3;i++) { document.getElementById('si'+i).classList.toggle('active', i<=n); }
    if (n===2) Toast.show('Mã OTP đã gửi thành công!','success');
  }
  window.nextStep = nextStep;
</script>
@endpush