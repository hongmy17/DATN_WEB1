<footer>
    {{-- ── Main Footer ───────────────────── --}}
    <div style="background:#111827; padding:52px 0 32px">
        <div class="container">
            <div style="display:grid;grid-template-columns:2fr 1fr 1fr 1.5fr;gap:40px;margin-bottom:40px">

                {{-- Cột 1: Brand --}}
                <div>
                    <div style="display:flex;align-items:center;gap:10px;margin-bottom:12px">
                        <svg width="30" height="30" viewBox="0 0 34 34" fill="none">
                            <rect width="34" height="34" rx="8" fill="#2563EB" />
                            <circle cx="17" cy="10" r="2.6" fill="#fff" />
                            <circle cx="10" cy="22" r="2.6" fill="#fff" />
                            <circle cx="24" cy="22" r="2.6" fill="#fff" />
                            <line x1="17" y1="10" x2="10" y2="22" stroke="#fff"
                                stroke-width="1.6" stroke-linecap="round" />
                            <line x1="17" y1="10" x2="24" y2="22" stroke="#fff"
                                stroke-width="1.6" stroke-linecap="round" />
                            <line x1="10" y1="22" x2="24" y2="22" stroke="#fff"
                                stroke-width="1.6" stroke-linecap="round" />
                        </svg>
                        <div style="font-family:var(--font-display);font-size:22px;font-weight:700;color:#fff">
                            Nexus<span style="color:#F59E0B">.</span>
                        </div>
                    </div>
                    <p
                        style="font-size:13.5px;color:rgba(255,255,255,.5);line-height:1.8;margin-bottom:20px;max-width:280px">
                        Chuyên cung cấp thiết bị công nghệ chính hãng, chất lượng cao với giá cả cạnh tranh nhất thị
                        trường.
                    </p>
                    {{-- Social icons SVG --}}
                    <div style="display:flex;gap:8px">
                        @php
                            $socials = [
                                [
                                    'label' => 'Facebook',
                                    'path' => 'M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z',
                                ],
                                [
                                    'label' => 'Instagram',
                                    'path' =>
                                        'M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z M17.5 6.5h.01 M7.5 2h9a5.5 5.5 0 0 1 5.5 5.5v9A5.5 5.5 0 0 1 16.5 22h-9A5.5 5.5 0 0 1 2 16.5v-9A5.5 5.5 0 0 1 7.5 2z',
                                ],
                                [
                                    'label' => 'YouTube',
                                    'path' =>
                                        'M22.54 6.42a2.78 2.78 0 0 0-1.95-1.96C18.88 4 12 4 12 4s-6.88 0-8.59.46a2.78 2.78 0 0 0-1.95 1.96A29 29 0 0 0 1 12a29 29 0 0 0 .46 5.58A2.78 2.78 0 0 0 3.41 19.6C5.12 20 12 20 12 20s6.88 0 8.59-.46a2.78 2.78 0 0 0 1.95-1.95A29 29 0 0 0 23 12a29 29 0 0 0-.46-5.58z M9.75 15.02 15.5 12 9.75 8.98z',
                                ],
                                [
                                    'label' => 'TikTok',
                                    'path' =>
                                        'M19.59 6.69a4.83 4.83 0 0 1-3.77-4.25V2h-3.45v13.67a2.89 2.89 0 0 1-2.88 2.5 2.89 2.89 0 0 1-2.89-2.89 2.89 2.89 0 0 1 2.89-2.89c.28 0 .54.04.79.1V9.01a6.27 6.27 0 0 0-.79-.05A6.34 6.34 0 0 0 3.15 15.3a6.34 6.34 0 0 0 6.34 6.34 6.34 6.34 0 0 0 6.34-6.34V8.69a8.27 8.27 0 0 0 4.84 1.55V6.8a4.85 4.85 0 0 1-1.08-.11z',
                                ],
                            ];
                        @endphp
                        @foreach ($socials as $s)
                            <a href="#" aria-label="{{ $s['label'] }}"
                                style="width:34px;height:34px;border-radius:8px;background:rgba(255,255,255,.07);
                      border:1px solid rgba(255,255,255,.1);display:flex;align-items:center;
                      justify-content:center;color:rgba(255,255,255,.5);
                      transition:.2s;text-decoration:none"
                                onmouseover="this.style.background='#2563EB';this.style.color='#fff';this.style.borderColor='#2563EB'"
                                onmouseout="this.style.background='rgba(255,255,255,.07)';this.style.color='rgba(255,255,255,.5)';this.style.borderColor='rgba(255,255,255,.1)'">
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none"
                                    stroke="currentColor" stroke-width="1.8" stroke-linecap="round">
                                    <path d="{{ $s['path'] }}" />
                                </svg>
                            </a>
                        @endforeach
                    </div>
                </div>

                {{-- Cột 2: Sản phẩm --}}
                <div>
                    <div
                        style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.08em;
                      color:#fff;margin-bottom:16px">
                        Sản phẩm</div>
                    @foreach (['Laptop', 'Điện thoại', 'Máy tính bảng', 'Tai nghe', 'Smartwatch', 'Phụ kiện'] as $cat)
                        <a href="{{ url('san-pham') }}"
                            style="display:block;font-size:13.5px;color:rgba(255,255,255,.5);
                    margin-bottom:10px;text-decoration:none;transition:.2s"
                            onmouseover="this.style.color='#fff'" onmouseout="this.style.color='rgba(255,255,255,.5)'">
                            {{ $cat }}
                        </a>
                    @endforeach
                </div>

                {{-- Cột 3: Hỗ trợ --}}
                <div>
                    <div
                        style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.08em;
                      color:#fff;margin-bottom:16px">
                        Hỗ trợ</div>
                    <a href="{{ route('contact') }}"
                        style="display:block;font-size:13.5px;color:rgba(255,255,255,.5);margin-bottom:10px;text-decoration:none;transition:.2s"
                        onmouseover="this.style.color='#fff'" onmouseout="this.style.color='rgba(255,255,255,.5)'">
                        Liên hệ
                    </a>

                    <a href="{{ route('policy', 'doi-tra') }}"
                        style="display:block;font-size:13.5px;color:rgba(255,255,255,.5);margin-bottom:10px;text-decoration:none;transition:.2s"
                        onmouseover="this.style.color='#fff'" onmouseout="this.style.color='rgba(255,255,255,.5)'">
                        Chính sách đổi trả
                    </a>

                    <a href="{{ route('policy', 'bao-hanh') }}"
                        style="display:block;font-size:13.5px;color:rgba(255,255,255,.5);margin-bottom:10px;text-decoration:none;transition:.2s"
                        onmouseover="this.style.color='#fff'" onmouseout="this.style.color='rgba(255,255,255,.5)'">
                        Chính sách bảo hành
                    </a>

                    <a href="{{ route('policy', 'mua-hang') }}"
                        style="display:block;font-size:13.5px;color:rgba(255,255,255,.5);margin-bottom:10px;text-decoration:none;transition:.2s"
                        onmouseover="this.style.color='#fff'" onmouseout="this.style.color='rgba(255,255,255,.5)'">
                        Hướng dẫn mua hàng
                    </a>

                    <a href="{{ route('policy', 'van-chuyen') }}"
                        style="display:block;font-size:13.5px;color:rgba(255,255,255,.5);margin-bottom:10px;text-decoration:none;transition:.2s"
                        onmouseover="this.style.color='#fff'" onmouseout="this.style.color='rgba(255,255,255,.5)'">
                        Chính sách vận chuyển
                    </a>

                    <a href="{{ route('policy', 'bao-mat') }}"
                        style="display:block;font-size:13.5px;color:rgba(255,255,255,.5);margin-bottom:10px;text-decoration:none;transition:.2s"
                        onmouseover="this.style.color='#fff'" onmouseout="this.style.color='rgba(255,255,255,.5)'">
                        Chính sách bảo mật
                    </a>
                </div>

                {{-- Cột 4: Newsletter + Hotline --}}
                <div>
                    <div
                        style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.08em;
                      color:#fff;margin-bottom:12px">
                        Nhận ưu đãi</div>
                    <p style="font-size:13px;color:rgba(255,255,255,.5);margin-bottom:12px;line-height:1.6">
                        Đăng ký nhận thông tin khuyến mãi và sản phẩm mới nhất.
                    </p>
                    <div style="display:flex;gap:6px;margin-bottom:20px">
                        <input type="email" placeholder="Email của bạn"
                            style="flex:1;padding:10px 14px;background:rgba(255,255,255,.07);
                          border:1px solid rgba(255,255,255,.12);border-radius:8px;
                          color:#fff;font-size:13px;font-family:var(--font-body);
                          outline:none;transition:.2s">
                        <button onclick="Toast.show('Đăng ký thành công!','success')"
                            style="padding:10px 14px;background:#2563EB;color:#fff;border:none;
                           border-radius:8px;cursor:pointer;transition:.2s;display:flex;align-items:center">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2" stroke-linecap="round">
                                <line x1="22" y1="2" x2="11" y2="13" />
                                <polygon points="22 2 15 22 11 13 2 9 22 2" />
                            </svg>
                        </button>
                    </div>

                    {{-- Hotline --}}
                    <div
                        style="padding:14px;background:rgba(37,99,235,.12);
                      border:1px solid rgba(37,99,235,.2);border-radius:8px">
                        <div style="display:flex;align-items:center;gap:6px;margin-bottom:4px">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#2563EB"
                                stroke-width="2" stroke-linecap="round">
                                <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07
                         A19.5 19.5 0 0 1 4.07 12a19.79 19.79 0 0 1-3.07-8.67
                         A2 2 0 0 1 3 1.18h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81
                         a2 2 0 0 1-.45 2.11L7.09 8.91a16 16 0 0 0 6 6l1.27-1.27
                         a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7
                         a2 2 0 0 1 1.72 2.03z" />
                            </svg>
                            <span style="font-size:11px;color:rgba(255,255,255,.5)">Hotline hỗ trợ</span>
                        </div>
                        <div style="font-size:20px;font-weight:700;color:#2563EB;letter-spacing:.5px">
                            1800 9999
                        </div>
                        <div style="font-size:11px;color:rgba(255,255,255,.35);margin-top:2px">
                            8:00 – 22:00, Thứ 2 – Chủ nhật
                        </div>
                    </div>
                </div>
            </div>

            {{-- Divider --}}
            <div style="height:1px;background:rgba(255,255,255,.07);margin-bottom:22px"></div>

            {{-- Bottom: copyright + payments --}}
            <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px">
                <p style="font-size:12.5px;color:rgba(255,255,255,.3)">
                    © 2026 Nexus Store. Bản quyền thuộc về nhóm DATN — FPT Polytechnic.
                </p>
                <div style="display:flex;gap:5px;align-items:center">
                    @foreach (['VISA', 'MC', 'MOMO', 'ZLP', 'COD'] as $pay)
                        <span
                            style="padding:3px 9px;background:rgba(255,255,255,.07);
                       border:1px solid rgba(255,255,255,.1);border-radius:4px;
                       font-size:10px;font-weight:700;color:rgba(255,255,255,.55)">
                            {{ $pay }}
                        </span>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</footer>
