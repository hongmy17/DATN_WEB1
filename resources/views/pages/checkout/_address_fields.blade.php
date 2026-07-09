<div class="form-row-2">
  <div class="form-group">
    <label class="form-label">Họ tên người nhận</label>
    <input type="text" id="{{ $prefix }}receiver_name" name="{{ $prefix }}receiver_name"
      value="{{ $values['receiver_name'] ?? old($prefix.'receiver_name') }}"
      class="form-control" placeholder="Nguyễn Văn A">
  </div>
  <div class="form-group">
    <label class="form-label">Số điện thoại</label>
    <input type="text" id="{{ $prefix }}receiver_phone" name="{{ $prefix }}receiver_phone"
      value="{{ $values['receiver_phone'] ?? old($prefix.'receiver_phone') }}"
      class="form-control" placeholder="0900 000 000">
  </div>
</div>
<div class="form-row-2">
  <div class="form-group">
    <label class="form-label">Tỉnh / Thành phố</label>
    <input type="text" id="{{ $prefix }}province" name="{{ $prefix }}province"
      value="{{ $values['province'] ?? old($prefix.'province') }}"
      class="form-control" placeholder="TP. Hồ Chí Minh">
  </div>
  <div class="form-group">
    <label class="form-label">Quận / Huyện</label>
    <input type="text" id="{{ $prefix }}district" name="{{ $prefix }}district"
      value="{{ $values['district'] ?? old($prefix.'district') }}"
      class="form-control" placeholder="Quận 1">
  </div>
</div>
<div class="form-row-2">
  <div class="form-group">
    <label class="form-label">Phường / Xã</label>
    <input type="text" id="{{ $prefix }}ward" name="{{ $prefix }}ward"
      value="{{ $values['ward'] ?? old($prefix.'ward') }}"
      class="form-control" placeholder="Phường Bến Nghé">
  </div>
  <div class="form-group">
    <label class="form-label">Địa chỉ chi tiết</label>
    <input type="text" id="{{ $prefix }}address_detail" name="{{ $prefix }}address_detail"
      value="{{ $values['address_detail'] ?? old($prefix.'address_detail') }}"
      class="form-control" placeholder="Số nhà, tên đường...">
  </div>
</div>