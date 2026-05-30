@extends('layouts.app')

@section('title', 'Đăng ký - Nexus Store')

@push('styles')
<style>
.auth-wrap{min-height:calc(100vh - 200px);display:flex;align-items:center;justify-content:center;padding:40px 16px}
.auth-card{width:100%;max-width:460px;background:var(--bg-alt);border:1px solid var(--border-soft);border-radius:var(--r-2xl);padding:40px;box-shadow:var(--shadow-lg)}
.auth-logo{text-align:center;margin-bottom:28px;font-family:var(--font-display);font-size:26px;font-weight:800}
.auth-tabs{display:flex;background:var(--surface);border-radius:var(--r-lg);padding:4px;margin-bottom:28px;gap:4px}
.auth-tab{flex:1;padding:10px;text-align:center;border-radius:var(--r-md);font-size:14px;font-weight:600;cursor:pointer;transition:var(--transition);color:var(--ink-3);background:transparent;border:none}
.auth-tab.active{background:var(--bg-alt);color:var(--ink);box-shadow:var(--shadow-xs)}
.auth-panel{display:none}
.auth-panel.active{display:block}
.social-btn{width:100%;padding:12px;border-radius:var(--r-lg);background:var(--surface);border:1.5px solid var(--border);color:var(--ink);font-size:14px;font-weight:600;cursor:pointer;transition:var(--transition);display:flex;align-items:center;justify-content:center;gap:10px;margin-bottom:10px}
.social-btn:hover{border-color:var(--ink);background:var(--surface-2)}
</style>
@endpush

@section('content')
<div class="auth-wrap">
  <div class="auth-card">
    <div class="auth-logo">
      <em style="font-style:normal;background:var(--ink);color:var(--bg);width:38px;height:38px;border-radius:10px;display:inline-flex;align-items:center;justify-content:center;font-size:18px;margin-right:8px">N</em>
      Nexus Store
    </div>
    <div class="auth-tabs">
      <button class="auth-tab" onclick="window.location.href='{{ url('tai-khoan') }}'">Đăng Nhập</button>
      <button class="auth-tab active">Đăng Ký</button>
    </div>

    <div class="auth-panel active">
      <div class="form-row" style="display:grid;grid-template-columns:1fr 1fr;gap:14px">
        <div class="form-group"><label class="form-label">Họ</label><input type="text" class="form-control" placeholder="Nguyễn"></div>
        <div class="form-group"><label class="form-label">Tên</label><input type="text" class="form-control" placeholder="Văn A"></div>
      </div>
      <div class="form-group"><label class="form-label">Email</label><div class="input-icon"><span class="input-icon__icon">📧</span><input type="email" class="form-control" placeholder="email@gmail.com"></div></div>
      <div class="form-group"><label class="form-label">Số điện thoại</label><div class="input-icon"><span class="input-icon__icon">📱</span><input type="tel" class="form-control" placeholder="0901 234 567"></div></div>
      <div class="form-group"><label class="form-label">Mật khẩu</label><div class="input-icon"><span class="input-icon__icon">🔒</span><input type="password" class="form-control" placeholder="Ít nhất 8 ký tự"></div></div>
      <div class="form-group"><label class="form-label">Xác nhận mật khẩu</label><div class="input-icon"><span class="input-icon__icon">🔒</span><input type="password" class="form-control" placeholder="Nhập lại mật khẩu"></div></div>
      <label class="checkbox-wrap mb-16"><input type="checkbox"><span style="font-size:13px">Tôi đồng ý với <a href="#" style="color:var(--accent)">điều khoản dịch vụ</a></span></label>
      <button class="btn btn-primary btn-full btn-lg" onclick="doRegister()">Tạo Tài Khoản</button>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
  function doRegister() {
    Toast.show('Đăng ký thành công! Vui lòng đăng nhập 📧', 'success');
    setTimeout(() => {
      window.location.href = '{{ url("tai-khoan") }}';
    }, 1200);
  }
  window.doRegister = doRegister;
</script>
@endpush