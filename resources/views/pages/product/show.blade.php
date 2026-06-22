@extends('layouts.app')

@section('title', 'Chi tiết sản phẩm - Nexus Store')

@push('styles')
<style>
    .detail-layout {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 56px;
        align-items: start;
    }

    .gallery {
        position: sticky;
        top: 88px;
    }

    .gallery__main {
        background: var(--surface);
        border: 1px solid var(--border-soft);
        border-radius: var(--r-2xl);
        overflow: hidden;
        aspect-ratio: 1;
        display: flex;
        align-items: center;
        justify-content: center;
        position: relative;
    }

    .gallery__main-img {
        font-size: 140px;
        line-height: 1;
    }

    .gallery__badge-wrap {
        position: absolute;
        top: 14px;
        left: 14px;
        display: flex;
        flex-direction: column;
        gap: 6px;
    }

    .gallery__wish {
        position: absolute;
        top: 12px;
        right: 12px;
        width: 38px;
        height: 38px;
        border-radius: 50%;
        background: var(--bg-alt);
        border: 1px solid var(--border);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 16px;
        cursor: pointer;
    }

    .gallery__thumbs {
        display: flex;
        gap: 8px;
        margin-top: 10px;
    }

    .gallery__thumb {
        flex: 1;
        aspect-ratio: 1;
        background: var(--surface);
        border: 2px solid var(--border-soft);
        border-radius: var(--r-lg);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 26px;
        cursor: pointer;
    }

    .gallery__thumb:hover,
    .gallery__thumb.active {
        border-color: var(--accent);
    }

    .detail__brand {
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        color: var(--accent);
        margin-bottom: 10px;
    }

    .detail__name {
        font-family: var(--font-display);
        font-size: clamp(22px, 3vw, 34px);
        font-weight: 800;
        color: var(--ink);
        margin-bottom: 16px;
    }

    .detail__meta {
        display: flex;
        align-items: center;
        gap: 16px;
        padding: 14px 0;
        border-top: 1px solid var(--border-soft);
        border-bottom: 1px solid var(--border-soft);
        margin-bottom: 20px;
    }

    .price-box {
        background: linear-gradient(135deg, var(--accent-light), #fff8f5);
        border: 1px solid rgba(200, 82, 42, .15);
        border-radius: var(--r-xl);
        padding: 20px;
        margin-bottom: 22px;
    }

    .price-box__current {
        font-family: var(--font-display);
        font-size: 38px;
        font-weight: 800;
        color: var(--ink);
    }

    .price-box__old {
        font-size: 17px;
        color: var(--ink-muted);
        text-decoration: line-through;
    }

    .price-box__save {
        background: var(--red);
        color: #fff;
        border-radius: var(--r-full);
        padding: 3px 10px;
        font-size: 12px;
    }

    .option-block {
        margin-bottom: 20px;
    }

    .option-label {
        font-size: 12px;
        font-weight: 700;
        text-transform: uppercase;
        color: var(--ink-3);
        margin-bottom: 10px;
    }

    .color-swatches {
        display: flex;
        flex-wrap: wrap;
        gap: 12px;
    }

    .color-swatch {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        cursor: pointer;
        border: 2px solid transparent;
    }

    .color-swatch.active {
        border-color: var(--ink);
        transform: scale(1.1);
    }

    .size-chips {
        display: flex;
        flex-wrap: wrap;
        gap: 12px;
    }

    .size-chip {
        min-width: 60px;
        padding: 10px 16px;
        border-radius: var(--r-md);
        border: 1.5px solid var(--border);
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        text-align: center;
    }

    .size-chip.active {
        background: var(--ink);
        border-color: var(--ink);
        color: #fff;
    }

    .add-row {
        display: flex;
        gap: 10px;
        margin-top: 24px;
    }

    .btn-add-main {
        flex: 1;
        padding: 0 24px;
        height: 50px;
        background: var(--ink);
        color: #fff;
        border-radius: var(--r-full);
        font-size: 15px;
        font-weight: 700;
        cursor: pointer;
    }

    .btn-buynow {
        flex: 1;
        padding: 0 24px;
        height: 50px;
        background: var(--accent);
        color: #fff;
        border-radius: var(--r-full);
        font-size: 15px;
        font-weight: 700;
        cursor: pointer;
    }

    .guarantees {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 10px;
        margin-top: 22px;
    }

    .guarantee {
        background: var(--surface);
        border-radius: var(--r-lg);
        padding: 14px;
        text-align: center;
    }

    .detail-tabs {
        margin-top: 64px;
    }

    .tab-nav {
        display: flex;
        border-bottom: 2px solid var(--border-soft);
        margin-bottom: 32px;
    }

    .tab-nav-btn {
        padding: 14px 22px;
        background: transparent;
        border: none;
        border-bottom: 2px solid transparent;
        font-size: 14px;
        font-weight: 600;
        color: var(--ink-muted);
        cursor: pointer;
    }

    .tab-nav-btn.active {
        color: var(--accent);
        border-bottom-color: var(--accent);
    }

    .tab-panel {
        display: none;
    }

    .tab-panel.active {
        display: block;
    }

    .specs-table {
        width: 100%;
        border-collapse: collapse;
    }

    .specs-table tr {
        border-bottom: 1px solid var(--border-soft);
    }

    .specs-table td {
        padding: 12px 16px;
        font-size: 14px;
    }

    .specs-table td:first-child {
        font-weight: 600;
        width: 200px;
    }

    .qty-ctrl {
        display: inline-flex;
        border: 1.5px solid var(--border);
        border-radius: var(--r-md);
        overflow: hidden;
    }

    .qty-ctrl__btn {
        width: 40px;
        height: 42px;
        background: var(--surface);
        font-size: 18px;
        cursor: pointer;
        border: none;
    }

    .qty-ctrl__input {
        width: 50px;
        height: 42px;
        text-align: center;
        border: none;
        border-left: 1.5px solid var(--border);
        border-right: 1.5px solid var(--border);
    }

    /* Bình luận */
    .review-item {
        display: flex;
        gap: 14px;
        padding: 20px 0;
        border-bottom: 1px solid var(--border-soft);
    }

    .review-avatar {
        width: 42px;
        height: 42px;
        border-radius: 50%;
        background: var(--ink);
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 800;
        font-size: 16px;
        flex-shrink: 0;
    }

    .review-stars {
        color: #f59e0b;
        font-size: 14px;
        margin-bottom: 4px;
    }

    .review-form {
        background: var(--surface);
        border-radius: var(--r-xl);
        padding: 24px;
        margin-top: 24px;
    }

    .star-select {
        display: flex;
        gap: 6px;
        margin-bottom: 12px;
    }

    .star-select span {
        font-size: 28px;
        cursor: pointer;
        color: var(--border);
        transition: color .15s;
    }

    .star-select span.active {
        color: #f59e0b;
    }

    /* Sản phẩm liên quan */
    .related-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 20px;
        margin-top: 24px;
    }

    .related-card {
        background: var(--bg-alt);
        border: 1px solid var(--border-soft);
        border-radius: var(--r-xl);
        padding: 16px;
        cursor: pointer;
        transition: var(--transition);
    }

    .related-card:hover {
        box-shadow: var(--shadow-md);
        transform: translateY(-2px);
    }

    .related-card__img {
        font-size: 48px;
        text-align: center;
        margin-bottom: 12px;
    }

    .related-card__name {
        font-weight: 700;
        font-size: 14px;
        margin-bottom: 6px;
        color: var(--ink);
    }

    .related-card__price {
        font-weight: 800;
        color: var(--accent);
        font-size: 15px;
    }

    @media(max-width:1024px) {
        .detail-layout {
            grid-template-columns: 1fr;
        }

        .gallery {
            position: static;
        }

        .related-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    @media(max-width:640px) {
        .related-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }
</style>
@endpush

@section('content')
<div class="container section">
    <div class="breadcrumb mb-32">
        <a href="{{ url('/') }}">Trang chủ</a>
        <span class="breadcrumb__sep">›</span>
        <a href="{{ url('san-pham') }}">Sản phẩm</a>
        <span class="breadcrumb__sep">›</span>
        <span class="breadcrumb__current">MacBook Pro 14" M3 Pro</span>
    </div>

    <div class="detail-layout">
        <!-- GALLERY -->
        <div class="gallery">
            <div class="gallery__main">
                <button class="gallery__wish" data-wish-id="1">♥</button>
                <div class="gallery__main-img" id="mainImg">💻</div>
            </div>
            <div class="gallery__thumbs">
                <div class="gallery__thumb active" onclick="document.getElementById('mainImg').textContent='💻'">💻</div>
                <div class="gallery__thumb" onclick="document.getElementById('mainImg').textContent='📦'">📦</div>
                <div class="gallery__thumb" onclick="document.getElementById('mainImg').textContent='🔌'">🔌</div>
            </div>
        </div>

        <!-- THÔNG TIN -->
        <div>
            <h1 class="detail__name">MacBook Pro 14" M3 Pro</h1>
            <div class="detail__meta">
                <div class="detail__meta-item">⭐ <strong>4.9</strong> (6 đánh giá)</div>
                <div class="detail__meta-sep"></div>
                <div class="detail__meta-item">● <strong>Còn hàng</strong></div>
            </div>

            <div class="price-box">
                <div class="price-box__current" id="currentPrice">42.990.000₫</div>
            </div>

            <!-- MÀU SẮC -->
            <div class="option-block">
                <div class="option-label">Màu sắc <span id="selectedColorLabel">Space Black</span></div>
                <div class="color-swatches">
                    <div class="color-swatch active" style="background:#1C1C1C" onclick="selectColor(this, 'Space Black')"></div>
                    <div class="color-swatch" style="background:#C0C0C0" onclick="selectColor(this, 'Silver')"></div>
                    <div class="color-swatch" style="background:#F5F0E8" onclick="selectColor(this, 'Starlight')"></div>
                </div>
            </div>

            <!-- DUNG LƯỢNG -->
            <div class="option-block">
                <div class="option-label">Dung lượng <span id="selectedSizeLabel">512GB</span></div>
                <div class="size-chips">
                    <div class="size-chip active" onclick="selectSize(this, '512GB')">512GB</div>
                    <div class="size-chip" onclick="selectSize(this, '1TB')">1TB</div>
                    <div class="size-chip" onclick="selectSize(this, '2TB')">2TB</div>
                </div>
            </div>

            <!-- SỐ LƯỢNG -->
            <div class="option-block">
                <div class="option-label">Số lượng</div>
                <div class="qty-ctrl">
                    <button class="qty-ctrl__btn" onclick="changeQty(-1)">−</button>
                    <input type="number" class="qty-ctrl__input" id="qtyInput" value="1" min="1" max="10">
                    <button class="qty-ctrl__btn" onclick="changeQty(1)">+</button>
                </div>
            </div>

            <div class="add-row">
                <button class="btn-add-main" onclick="Toast.show('Đã thêm vào giỏ hàng','success')">🛒 Thêm vào giỏ hàng</button>
                <button class="btn-buynow" onclick="Toast.show('Chuyển đến thanh toán','info')">⚡ Mua ngay</button>
            </div>

            <div class="guarantees">
                <div class="guarantee">
                    <div class="guarantee__icon">🛡️</div>
                    <div>Bảo hành 12 tháng</div>
                </div>
                <div class="guarantee">
                    <div class="guarantee__icon">🔄</div>
                    <div>Đổi mới 1-1 trong 30 ngày</div>
                </div>
                <div class="guarantee">
                    <div class="guarantee__icon">🚚</div>
                    <div>Miễn phí ship toàn quốc</div>
                </div>
            </div>
        </div>
    </div>

    <!-- TABS -->
    <div class="detail-tabs">
        <div class="tab-nav">
            <button class="tab-nav-btn active" onclick="switchTab('specs',this)">📋 Thông số kỹ thuật</button>
            <button class="tab-nav-btn" onclick="switchTab('desc',this)">📖 Mô tả</button>
            <button class="tab-nav-btn" onclick="switchTab('reviews',this)">⭐ Đánh giá (6)</button>
        </div>

        <div class="tab-panel active" id="tab-specs">
            <table class="specs-table">
                <tr>
                    <td>CPU / Chip</td>
                    <td>Apple M3 Pro (12-core CPU, 18-core GPU)</td>
                </tr>
                <tr>
                    <td>RAM</td>
                    <td>18GB Unified Memory</td>
                </tr>
                <tr>
                    <td>Bộ nhớ trong</td>
                    <td>512GB SSD</td>
                </tr>
                <tr>
                    <td>Màn hình</td>
                    <td>14.2" Liquid Retina XDR, ProMotion 120Hz</td>
                </tr>
                <tr>
                    <td>Pin</td>
                    <td>70Wh · Lên đến 18 giờ</td>
                </tr>
                <tr>
                    <td>Trọng lượng</td>
                    <td>1.61 kg</td>
                </tr>
            </table>
        </div>

        <div class="tab-panel" id="tab-desc">
            <p>MacBook Pro 14 inch với chip M3 Pro mang đến hiệu năng chuyên nghiệp đột phá. CPU 12-core và GPU 18-core xử lý nhanh hơn 40% so với thế hệ trước.</p>
        </div>

        <!-- ĐÁNH GIÁ -->
        <div class="tab-panel" id="tab-reviews">

            {{-- Tổng quan đánh giá --}}
            <div style="display:flex;align-items:center;gap:32px;padding:24px;background:var(--surface);border-radius:var(--r-xl);margin-bottom:24px">
                <div style="text-align:center">
                    <div style="font-size:56px;font-weight:800;color:var(--ink);line-height:1">4.9</div>
                    <div style="color:#f59e0b;font-size:20px;margin:4px 0">★★★★★</div>
                    <div style="font-size:13px;color:var(--ink-muted)">6 đánh giá</div>
                </div>
                <div style="flex:1">
                    @foreach([5=>5, 4=>1, 3=>0, 2=>0, 1=>0] as $star => $count)
                    @php
                    $percent = $count > 0 ? ($count / 6 * 100) : 0;
                    @endphp
                    <div style="display:flex;align-items:center;gap:8px;margin-bottom:6px">
                        <span style="font-size:12px;width:16px;text-align:right">{{ $star }}</span>
                        <span style="color:#f59e0b;font-size:12px">★</span>
                        <div style="flex:1;height:8px;background:var(--border-soft);border-radius:99px;overflow:hidden">

                            <div style="height:100%; background:#f59e0b; border-radius:99px; width: calc(var(--w) * 1%); --w: {{ $percent }};"></div>

                        </div>
                        <span style="font-size:12px;color:var(--ink-muted);width:16px">{{ $count }}</span>
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- Danh sách bình luận hardcode --}}
            @php
            $reviews = [
            ['name'=>'Nguyễn Văn An', 'stars'=>5, 'date'=>'20/06/2026', 'content'=>'Máy cực kỳ mạnh mẽ, màn hình đẹp xuất sắc, pin trâu hơn mong đợi. Rất đáng tiền!'],
            ['name'=>'Trần Thị Bích', 'stars'=>5, 'date'=>'18/06/2026', 'content'=>'Seal hộp nguyên vẹn, giao hàng nhanh, sản phẩm chính hãng. Mình rất hài lòng.'],
            ['name'=>'Lê Minh Quân', 'stars'=>5, 'date'=>'15/06/2026', 'content'=>'Hiệu năng vượt trội, làm việc nặng không lag. Thiết kế sang trọng, mỏng nhẹ.'],
            ['name'=>'Phạm Thu Hà', 'stars'=>5, 'date'=>'12/06/2026', 'content'=>'Dùng cho công việc đồ họa rất mượt. Chip M3 Pro thực sự ấn tượng!'],
            ['name'=>'Hoàng Đức Long', 'stars'=>4, 'date'=>'10/06/2026', 'content'=>'Máy rất tốt, chỉ tiếc giá hơi cao. Nhưng chất lượng xứng đáng với số tiền bỏ ra.'],
            ['name'=>'Vũ Thanh Tùng', 'stars'=>5, 'date'=>'08/06/2026', 'content'=>'Mua lần 2 rồi vẫn thấy hài lòng. Shop uy tín, bảo hành tốt.'],
            ];
            @endphp

            @foreach($reviews as $review)
            <div class="review-item">
                <div class="review-avatar">{{ mb_substr($review['name'], 0, 1) }}</div>
                <div style="flex:1">
                    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:4px">
                        <strong style="font-size:14px">{{ $review['name'] }}</strong>
                        <span style="font-size:12px;color:var(--ink-muted)">{{ $review['date'] }}</span>
                    </div>
                    <div class="review-stars">
                        @for($i=1;$i<=5;$i++)
                            {{ $i <= $review['stars'] ? '★' : '☆' }}
                            @endfor
                            </div>
                            <p style="font-size:14px;color:var(--ink-3);margin:0">{{ $review['content'] }}</p>
                    </div>
                </div>
                @endforeach

                {{-- Form viết đánh giá --}}
                <div class="review-form">
                    <h4 style="font-weight:800;margin-bottom:16px"> Viết đánh giá của bạn</h4>
                    <div class="star-select" id="starSelect">
                        <span onclick="selectStar(1)">★</span>
                        <span onclick="selectStar(2)">★</span>
                        <span onclick="selectStar(3)">★</span>
                        <span onclick="selectStar(4)">★</span>
                        <span onclick="selectStar(5)">★</span>
                    </div>
                    <textarea class="form-control" rows="3" placeholder="Chia sẻ trải nghiệm của bạn..." style="margin-bottom:12px"></textarea>
                    <button class="btn btn-primary" onclick="submitReview()">Gửi đánh giá</button>
                </div>
            </div>
        </div>

        {{-- SẢN PHẨM LIÊN QUAN --}}
        <div style="margin-top:64px">
            <h2 class="heading-2 mb-8">Sản phẩm liên quan</h2>
            <p style="color:var(--ink-muted);font-size:14px;margin-bottom:0">Có thể bạn cũng thích</p>
            <div class="related-grid">
                @php
                $related = [
                ['name'=>'MacBook Air 13" M3', 'price'=>'28.990.000₫', 'img'=>'💻', 'badge'=>''],
                ['name'=>'iPad Pro 12.9" M4', 'price'=>'32.990.000₫', 'img'=>'📱', 'badge'=>'Mới'],
                ['name'=>'Apple Magic Keyboard', 'price'=>'3.490.000₫', 'img'=>'⌨️', 'badge'=>''],
                ['name'=>'Apple AirPods Pro 2', 'price'=>'6.990.000₫', 'img'=>'🎧', 'badge'=>'Hot'],
                ];
                @endphp

                @foreach($related as $item)
                <div class="related-card" onclick="window.location.href=`{{ url('san-pham') }}`">
                    @if($item['badge'])
                    <div style="display:inline-block;background:var(--accent);color:#fff;font-size:10px;font-weight:700;padding:2px 8px;border-radius:99px;margin-bottom:8px">{{ $item['badge'] }}</div>
                    @endif
                    <div class="related-card__img">{{ $item['img'] }}</div>
                    <div class="related-card__name">{{ $item['name'] }}</div>
                    <div class="related-card__price">{{ $item['price'] }}</div>
                </div>
                @endforeach
            </div>
        </div>

    </div>

    <script>
        function selectColor(el, color) {
            document.querySelectorAll('.color-swatch').forEach(c => c.classList.remove('active'));
            el.classList.add('active');
            document.getElementById('selectedColorLabel').textContent = color;
        }

        function selectSize(el, size) {
            document.querySelectorAll('.size-chip').forEach(c => c.classList.remove('active'));
            el.classList.add('active');
            document.getElementById('selectedSizeLabel').textContent = size;
        }

        function changeQty(delta) {
            let input = document.getElementById('qtyInput');
            let val = parseInt(input.value) || 1;
            val = Math.min(10, Math.max(1, val + delta));
            input.value = val;
        }

        function switchTab(id, btn) {
            document.querySelectorAll('.tab-nav-btn').forEach(b => b.classList.remove('active'));
            document.querySelectorAll('.tab-panel').forEach(p => p.classList.remove('active'));
            btn.classList.add('active');
            document.getElementById('tab-' + id).classList.add('active');
        }

        let selectedStar = 0;

        function selectStar(n) {
            selectedStar = n;
            document.querySelectorAll('#starSelect span').forEach((s, i) => {
                s.classList.toggle('active', i < n);
            });
        }

        function submitReview() {
            if (!selectedStar) return alert('Vui lòng chọn số sao!');
            Toast.show('Đánh giá của bạn đã được gửi!', 'success');
            selectedStar = 0;
            document.querySelectorAll('#starSelect span').forEach(s => s.classList.remove('active'));
        }

        window.selectColor = selectColor;
        window.selectSize = selectSize;
        window.changeQty = changeQty;
        window.switchTab = switchTab;
        window.selectStar = selectStar;
        window.submitReview = submitReview;
    </script>
    @endsection