@extends('layouts.app')

@section('title', $product->name . ' — Nexus Store')

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/pages/products.css') }}">
@endpush

@section('content')
    @php
        // Variant mặc định: is_default=1 hoặc variant đầu tiên
        $defaultVariant = $product->variants->firstWhere('is_default', 1) ?? $product->variants->first();
        $currentPrice = $defaultVariant?->price ?? 0;
        $comparePrice = $defaultVariant?->compare_price ?? 0;
        $stockQty = $defaultVariant?->stock_quantity ?? 0;
        $discount =
            $comparePrice && $comparePrice > $currentPrice ? round((1 - $currentPrice / $comparePrice) * 100) : 0;

        // Nhóm attribute → values từ các variants
        $attrGroups = [];
        foreach ($product->variants as $variant) {
            foreach ($variant->attributeValues as $av) {
                $attrGroups[$av->attribute->name][$av->id] = $av;
            }
        }

        // Map variant_id → combo key (để JS tìm giá)
        $variantMap = [];
        foreach ($product->variants as $variant) {
            $key = $variant->attributeValues->pluck('id')->sort()->join('-');
            $variantMap[$key] = [
                'id' => $variant->id,
                'price' => $variant->price,
                'compare' => $variant->compare_price,
                'stock' => $variant->stock_quantity,
                'sku' => $variant->sku,
                'is_default' => $variant->is_default,
                'image' => $variant->image ? asset('storage/' . $variant->image) : null,
            ];
        }

        // Ảnh gallery
        $images = $product->images ?? collect();

        // Ảnh chính CTSP: ưu tiên ảnh của variant mặc định, sau đó ảnh is_primary trong
        // Thư viện ảnh, cuối cùng mới là ảnh đầu tiên trong Thư viện ảnh.
        // KHÔNG dùng $product->thumbnail ở đây — thumbnail chỉ là ảnh bìa cho trang danh sách.
        $primaryImage = $images->firstWhere('is_primary', 1) ?? $images->first();
        $mainImageUrl = $defaultVariant?->image
            ? asset('storage/' . $defaultVariant->image)
            : ($primaryImage ? asset('storage/' . $primaryImage->image_url) : null);
    @endphp

    <div class="pdp-wrap">
        <div class="pdp-grid">

            {{-- GALLERY --}}
            <div class="gallery">
                <div class="gallery__main">
                    @if ($discount)
                        <div class="gallery__badges">
                            <span class="badge-discount">-{{ $discount }}%</span>
                        </div>
                    @endif


                    <button class="gallery__wish" id="wishBtn" data-wish-id="{{ $product->id }}"
                        data-wish-name="{{ addslashes($product->name) }}"
                        data-wish-price="{{ $defaultVariant?->price ?? 0 }}" data-wish-slug="{{ $product->slug }}"
                        data-wish-img="{{ $defaultVariant?->image ?? ($primaryImage->image_url ?? '') }}" aria-label="Yêu thích">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                            <path
                                d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z" />
                        </svg>
                    </button>

                    <div id="mainImgWrap">
                        @if ($mainImageUrl)
                            <img id="mainImg" src="{{ $mainImageUrl }}"
                                alt="{{ $product->name }}" style="max-width:90%;max-height:90%;object-fit:contain">
                        @else
                            <div class="gallery__main-placeholder" id="mainImg">
                                <svg width="96" height="96" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="1" stroke-linecap="round">
                                    <rect x="2" y="3" width="20" height="14" rx="2" />
                                    <line x1="8" y1="21" x2="16" y2="21" />
                                    <line x1="12" y1="17" x2="12" y2="21" />
                                </svg>
                                <span>Ảnh sản phẩm</span>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="gallery__thumbs">
                    @foreach ($images as $img)
                        <div class="gallery__thumb {{ $primaryImage && $img->id === $primaryImage->id ? 'active' : '' }}"
                            onclick="selectThumb(this, '{{ asset('storage/' . $img->image_url) }}')"
                            title="Ảnh {{ $loop->iteration }}">
                            <img src="{{ asset('storage/' . $img->image_url) }}" alt="">
                        </div>
                    @endforeach
                    @if ($images->isEmpty())
                        @foreach (range(1, 4) as $i)
                            <div class="gallery__thumb {{ $i === 1 ? 'active' : '' }}" onclick="selectThumb(this, null)">
                                <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="1.5" stroke-linecap="round">
                                    <rect x="2" y="3" width="20" height="14" rx="2" />
                                </svg>
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>

            {{-- INFO --}}
            <div class="pdp-info">
                <div class="pdp-breadcrumb">
                    <a href="{{ url('/') }}">Trang chủ</a>
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2">
                        <polyline points="9 18 15 12 9 6" />
                    </svg>
                    <a href="{{ route('products.index') }}">Sản phẩm</a>
                    @if ($product->category)
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2">
                            <polyline points="9 18 15 12 9 6" />
                        </svg>
                        <a
                            href="{{ route('products.index', ['cat' => $product->category_id]) }}">{{ $product->category->name }}</a>
                    @endif
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2">
                        <polyline points="9 18 15 12 9 6" />
                    </svg>
                    <span>{{ Str::limit($product->name, 30) }}</span>
                </div>

                <div class="pdp-brand">{{ $product->category->name ?? '' }}</div>
                <h1 class="pdp-name">{{ $product->name }}</h1>
                <div class="sku-label" id="skuLabel">SKU: {{ $defaultVariant?->sku ?? $product->code }}</div>

                <div class="pdp-rating">
                    <div class="stars">
                        @for ($i = 1; $i <= 5; $i++)
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="#F59E0B" stroke="#F59E0B"
                                stroke-width="1.5">
                                <polygon
                                    points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2" />
                            </svg>
                        @endfor
                    </div>
                    <span class="pdp-rating__score">5.0</span>
                    <span class="pdp-rating__count">0 đánh giá</span>
                    <div class="pdp-rating__sep"></div>
                    <div class="pdp-stock {{ $stockQty > 0 ? 'in-stock' : 'out-stock' }}" id="stockStatus">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2" stroke-linecap="round">
                            <polyline points="20 6 9 17 4 12" />
                        </svg>
                        {{ $stockQty > 0 ? 'Còn hàng' : 'Hết hàng' }}
                    </div>
                </div>

                {{-- PRICE --}}
                <div class="price-block">
                    <div class="price-block__row">
                        <span class="price-block__current"
                            id="currentPrice">{{ number_format($currentPrice, 0, ',', '.') }}₫</span>
                        @if ($comparePrice && $comparePrice > $currentPrice)
                            <span class="price-block__old"
                                id="comparePrice">{{ number_format($comparePrice, 0, ',', '.') }}₫</span>
                            <span class="price-block__save" id="saveBadge">Tiết kiệm
                                {{ number_format($comparePrice - $currentPrice, 0, ',', '.') }}₫</span>
                        @endif
                    </div>
                    <div class="price-block__installment">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2" stroke-linecap="round">
                            <rect x="1" y="4" width="22" height="16" rx="2" />
                            <line x1="1" y1="10" x2="23" y2="10" />
                        </svg>
                        Trả góp 0% lãi suất — liên hệ để biết thêm
                    </div>
                </div>

                {{-- ATTRIBUTE OPTIONS --}}
                @foreach ($attrGroups as $attrName => $values)
                    @php $isColor = collect($values)->first()?->attribute?->display_type === 1; @endphp
                    <div class="option-section">
                        <div class="option-header">
                            {{ $attrName }}:
                            <span class="option-header__value" id="selected-attr-{{ Str::slug($attrName) }}">
                                {{ collect($values)->first()?->value }}
                            </span>
                        </div>

                        @if ($isColor)
                            <div class="color-swatches">
                                @foreach ($values as $av)
                                    <div class="color-swatch {{ $loop->first ? 'active' : '' }}"
                                        style="background:{{ $av->color_code ?? '#ccc' }}; box-shadow: inset 0 0 0 1px rgba(0,0,0,.1)"
                                        onclick="selectAttr(this, 'selected-attr-{{ Str::slug($attrName) }}', '{{ addslashes($av->value) }}', {{ $av->id }})"
                                        data-attr-id="{{ $av->id }}" title="{{ $av->value }}">
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="size-chips">
                                @foreach ($values as $av)
                                    <div class="size-chip {{ $loop->first ? 'active' : '' }}"
                                        onclick="selectAttr(this, 'selected-attr-{{ Str::slug($attrName) }}', '{{ addslashes($av->value) }}', {{ $av->id }})"
                                        data-attr-id="{{ $av->id }}">
                                        {{ $av->value }}
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                @endforeach

                {{-- SỐ LƯỢNG --}}
                <div class="qty-row">
                    <div class="qty-ctrl">
                        <button class="qty-ctrl__btn" onclick="changeQty(-1)" aria-label="Giảm">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2.5" stroke-linecap="round">
                                <line x1="5" y1="12" x2="19" y2="12" />
                            </svg>
                        </button>
                        <input type="number" class="qty-ctrl__input" id="qtyInput" value="1" min="1"
                            max="{{ $stockQty ?: 10 }}">
                        <button class="qty-ctrl__btn" onclick="changeQty(1)" aria-label="Tăng">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2.5" stroke-linecap="round">
                                <line x1="12" y1="5" x2="12" y2="19" />
                                <line x1="5" y1="12" x2="19" y2="12" />
                            </svg>
                        </button>
                    </div>
                    <span class="qty-stock-note" id="stockNote">Còn {{ $stockQty }} sản phẩm</span>
                </div>

                {{-- CTA --}}
                <div class="cta-row">
                    <button class="btn-cart" id="btnAddCart">
                        <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2" stroke-linecap="round">
                            <circle cx="9" cy="21" r="1" />
                            <circle cx="20" cy="21" r="1" />
                            <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6" />
                        </svg>
                        Thêm vào giỏ
                    </button>
                    <button class="btn-buy" onclick="Toast.show('Đang chuyển đến thanh toán...','info')">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2" stroke-linecap="round">
                            <polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2" />
                        </svg>
                        Mua ngay
                    </button>
                </div>

                {{-- TRUST --}}
                <div class="trust-grid">
                    <div class="trust-item">
                        <div class="trust-item__icon"><svg width="20" height="20" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round">
                                <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z" />
                            </svg></div>
                        <div class="trust-item__text">Bảo hành<br>12 tháng</div>
                    </div>
                    <div class="trust-item">
                        <div class="trust-item__icon"><svg width="20" height="20" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round">
                                <polyline points="1 4 1 10 7 10" />
                                <polyline points="23 20 23 14 17 14" />
                                <path d="M20.49 9A9 9 0 0 0 5.64 5.64L1 10m22 4l-4.64 4.36A9 9 0 0 1 3.51 15" />
                            </svg></div>
                        <div class="trust-item__text">Đổi mới<br>30 ngày</div>
                    </div>
                    <div class="trust-item">
                        <div class="trust-item__icon"><svg width="20" height="20" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round">
                                <rect x="1" y="3" width="15" height="13" rx="1" />
                                <path d="M16 8h4l3 3v4h-7V8z" />
                                <circle cx="5.5" cy="18.5" r="2.5" />
                                <circle cx="18.5" cy="18.5" r="2.5" />
                            </svg></div>
                        <div class="trust-item__text">Miễn phí<br>vận chuyển</div>
                    </div>
                </div>

                <div class="delivery-box">
                    <div class="delivery-row">
                        <div class="delivery-row__icon"><svg width="16" height="16" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round">
                                <circle cx="12" cy="12" r="10" />
                                <polyline points="12 6 12 12 16 14" />
                            </svg></div>
                        <div>
                            <div class="delivery-row__label">Giao hàng nhanh</div>
                            <div class="delivery-row__sub">Dự kiến nhận hàng trong 2–3 ngày làm việc</div>
                        </div>
                    </div>
                    <div class="delivery-row">
                        <div class="delivery-row__icon"><svg width="16" height="16" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round">
                                <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z" />
                                <circle cx="12" cy="10" r="3" />
                            </svg></div>
                        <div>
                            <div class="delivery-row__label">Nhận tại cửa hàng</div>
                            <div class="delivery-row__sub">Sẵn sàng để lấy hàng hôm nay tại các chi nhánh</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- TABS --}}
        <div class="pdp-tabs">
            <div class="tab-nav">
                <button class="tab-btn active" onclick="switchTab('specs',this)">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="1.8" stroke-linecap="round">
                        <line x1="8" y1="6" x2="21" y2="6" />
                        <line x1="8" y1="12" x2="21" y2="12" />
                        <line x1="8" y1="18" x2="21" y2="18" />
                        <line x1="3" y1="6" x2="3.01" y2="6" />
                        <line x1="3" y1="12" x2="3.01" y2="12" />
                        <line x1="3" y1="18" x2="3.01" y2="18" />
                    </svg>
                    Thông số kỹ thuật
                </button>
                <button class="tab-btn" onclick="switchTab('desc',this)">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="1.8" stroke-linecap="round">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
                        <polyline points="14 2 14 8 20 8" />
                        <line x1="16" y1="13" x2="8" y2="13" />
                        <line x1="16" y1="17" x2="8" y2="17" />
                    </svg>
                    Mô tả sản phẩm
                </button>
                <button class="tab-btn" onclick="switchTab('reviews',this)">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="1.8" stroke-linecap="round">
                        <polygon
                            points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2" />
                    </svg>
                    Đánh giá
                </button>
            </div>

            {{-- SPECS: từ customAttributes --}}
            <div class="tab-panel active" id="tab-specs">
                @if ($product->customAttributes && $product->customAttributes->count())
                    <table class="specs-table">
                        @foreach ($product->customAttributes->where('is_visible', true) as $attr)
                            <tr>
                                <td>{{ $attr->name }}</td>
                                <td>{{ $attr->values->pluck('value')->join(', ') }}</td>
                            </tr>
                        @endforeach
                    </table>
                @else
                    <p style="color:#9A9790;font-size:14px">Chưa có thông số kỹ thuật.</p>
                @endif
            </div>

            {{-- DESCRIPTION --}}
            <div class="tab-panel" id="tab-desc">
                <div class="desc-content">
                    {!! $product->description ?? '<p>Chưa có mô tả chi tiết.</p>' !!}
                </div>
            </div>

            {{-- REVIEWS --}}
            <div class="tab-panel" id="tab-reviews">
                <div class="review-overview">
                    <div class="review-score" style="text-align:center;flex-shrink:0">
                        <div class="review-score__num">5.0</div>
                        <div class="review-score__stars">
                            @for ($i = 1; $i <= 5; $i++)
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="#F59E0B" stroke="#F59E0B"
                                    stroke-width="1">
                                    <polygon
                                        points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2" />
                                </svg>
                            @endfor
                        </div>
                        <div class="review-score__label">0 đánh giá</div>
                    </div>
                    <div class="review-bars">
                        @foreach ([5, 4, 3, 2, 1] as $star)
                            <div class="review-bar-row">
                                <span class="review-bar-row__label">{{ $star }}</span>
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="#F59E0B"
                                    style="flex-shrink:0">
                                    <polygon
                                        points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2" />
                                </svg>
                                <div class="review-bar-track">
                                    <div class="review-bar-fill" style="width:0%"></div>
                                </div>
                                <span class="review-bar-row__count">0</span>
                            </div>
                        @endforeach
                    </div>
                </div>
                <p style="color:#9A9790;font-size:14px;text-align:center;padding:20px 0">Chưa có đánh giá nào.</p>

                <div class="review-form-wrap">
                    <h4>Viết đánh giá của bạn</h4>
                    <div class="star-picker" id="starPicker">
                        @for ($i = 1; $i <= 5; $i++)
                            <svg width="28" height="28" viewBox="0 0 24 24" fill="#E5E3DE" stroke="#E5E3DE"
                                stroke-width="1" style="cursor:pointer" onclick="selectStar({{ $i }})"
                                data-star="{{ $i }}">
                                <polygon
                                    points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2" />
                            </svg>
                        @endfor
                    </div>
                    <div class="form-group">
                        <label class="form-label">Họ và tên</label>
                        <input type="text" class="form-input" placeholder="Tên của bạn">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Nội dung đánh giá</label>
                        <textarea class="form-input" placeholder="Chia sẻ trải nghiệm sử dụng sản phẩm..."></textarea>
                    </div>
                    <button class="btn-submit-review" onclick="submitReview()">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2" stroke-linecap="round">
                            <line x1="22" y1="2" x2="11" y2="13" />
                            <polygon points="22 2 15 22 11 13 2 9 22 2" />
                        </svg>
                        Gửi đánh giá
                    </button>
                </div>
            </div>
        </div>

        {{-- RELATED --}}
        @if ($relatedProducts && $relatedProducts->count())
            <div class="related-section">
                <div class="section-header">
                    <div class="section-title">Sản phẩm liên quan</div>
                    <div class="section-sub">Có thể bạn cũng thích</div>
                </div>
                <div class="related-grid">
                    @foreach ($relatedProducts as $rel)
                        @php
                            $relPrice = $rel->variants->min('price');
                            $relCompare = $rel->variants->max('compare_price');
                        @endphp
                        <a class="related-card" href="{{ route('products.show', $rel->slug) }}">
                            <div class="related-card__img">
                                @if ($rel->thumbnail)
                                    <img src="{{ asset('storage/' . $rel->thumbnail) }}" alt="{{ $rel->name }}">
                                @else
                                    <svg width="52" height="52" viewBox="0 0 24 24" fill="none"
                                        stroke="currentColor" stroke-width="1" stroke-linecap="round">
                                        <rect x="2" y="3" width="20" height="14" rx="2" />
                                    </svg>
                                @endif
                            </div>
                            <div class="related-card__name">{{ $rel->name }}</div>
                            <div>
                                <span class="related-card__price">{{ number_format($relPrice, 0, ',', '.') }}₫</span>
                                @if ($relCompare && $relCompare > $relPrice)
                                    <span
                                        class="related-card__price-old">{{ number_format($relCompare, 0, ',', '.') }}₫</span>
                                @endif
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        @endif

    </div>

    <script>
        // Variant map từ PHP
        const variantMap = @json($variantMap);
        let selectedAttrs = {};

        // Khởi tạo attrs mặc định từ variant is_default
        @if ($defaultVariant)
            @foreach ($defaultVariant->attributeValues as $av)
                selectedAttrs[{{ $av->attribute_id }}] = {{ $av->id }};
            @endforeach
        @endif

        function selectAttr(el, labelId, value, attrValueId) {
            // Bỏ active các item cùng nhóm
            el.closest('.color-swatches, .size-chips')
                ?.querySelectorAll('.color-swatch, .size-chip')
                .forEach(c => c.classList.remove('active'));
            el.classList.add('active');
            document.getElementById(labelId).textContent = value;

            // Tìm attribute_id từ el

            // Cập nhật selectedAttrs bằng attrValueId
            // Map attrValueId → attribute_id qua PHP
            const attrMeta = @json(collect($attrGroups)->mapWithKeys(function ($values, $name) {
                        return collect($values)->mapWithKeys(fn($av) => [$av->id => $av->attribute_id])->toArray();
                    })->toArray());
            if (attrMeta[attrValueId]) {
                selectedAttrs[attrMeta[attrValueId]] = attrValueId;
            }

            updateVariant();
        }

        function updateVariant() {
            const key = Object.values(selectedAttrs).sort((a, b) => a - b).join('-');
            const v = variantMap[key];
            if (!v) return;

            // Giá
            document.getElementById('currentPrice').textContent =
                v.price.toLocaleString('vi-VN') + '₫';

            const compareEl = document.getElementById('comparePrice');
            const saveEl = document.getElementById('saveBadge');
            if (v.compare && v.compare > v.price) {
                if (compareEl) compareEl.textContent = v.compare.toLocaleString('vi-VN') + '₫';
                if (saveEl) saveEl.textContent = 'Tiết kiệm ' + (v.compare - v.price).toLocaleString('vi-VN') + '₫';
            }

            // Tồn kho
            const stockNote = document.getElementById('stockNote');
            const stockStatus = document.getElementById('stockStatus');
            if (stockNote) stockNote.textContent = 'Còn ' + v.stock + ' sản phẩm';
            if (stockStatus) {
                stockStatus.textContent = v.stock > 0 ? 'Còn hàng' : 'Hết hàng';
                stockStatus.className = 'pdp-stock ' + (v.stock > 0 ? 'in-stock' : 'out-stock');
            }

            // SKU
            const skuLabel = document.getElementById('skuLabel');
            if (skuLabel && v.sku) skuLabel.textContent = 'SKU: ' + v.sku;

            // Đổi ảnh chính theo variant
            if (v.image) {
                const wrap = document.getElementById('mainImgWrap');
                if (wrap) {
                    wrap.innerHTML = `<img src="${v.image}" alt=""
            style="max-width:90%;max-height:90%;object-fit:contain">`;
                }
            }
            // Qty max
            const qtyInput = document.getElementById('qtyInput');
            if (qtyInput) qtyInput.max = v.stock || 10;

            // Cart button
            document.getElementById('btnAddCart').onclick = () => {
                const qty = parseInt(document.getElementById('qtyInput')?.value || 1);
                Cart.add({
                    variant_id: v.id,
                    id:         {{ $product->id }},
                    name:       '{{ addslashes($product->name) }}',
                    variant:    v.label || '',
                    price:      v.price,
                    img:        v.image || '{{ $product->thumbnail ? asset('storage/' . $product->thumbnail) : '' }}',
                    qty,
                });
            };
        }

        function changeQty(delta) {
            let input = document.getElementById('qtyInput');
            let max = parseInt(input.max) || 10;
            input.value = Math.min(max, Math.max(1, (parseInt(input.value) || 1) + delta));
        }

        function selectThumb(el, imgSrc) {
            document.querySelectorAll('.gallery__thumb').forEach(t => t.classList.remove('active'));
            el.classList.add('active');
            if (imgSrc) {
                const wrap = document.getElementById('mainImgWrap');
                wrap.innerHTML = `<img src="${imgSrc}" alt="" style="max-width:90%;max-height:90%;object-fit:contain">`;
            }
        }

        function toggleWish() {
            const btn = document.getElementById('wishBtn');
            Wishlist.toggle(btn.dataset.wishId, btn.dataset.wishName, parseInt(btn.dataset.wishPrice), '');
        }

        function switchTab(id, btn) {
            document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
            document.querySelectorAll('.tab-panel').forEach(p => p.classList.remove('active'));
            btn.classList.add('active');
            document.getElementById('tab-' + id).classList.add('active');
        }

        let selectedStar = 0;

        function selectStar(n) {
            selectedStar = n;
            document.querySelectorAll('#starPicker svg').forEach((s, i) => {
                const on = i < n;
                s.setAttribute('fill', on ? '#F59E0B' : '#E5E3DE');
                s.setAttribute('stroke', on ? '#F59E0B' : '#E5E3DE');
            });
        }

        function submitReview() {
            if (!selectedStar) {
                Toast.show('Vui lòng chọn số sao!', 'error');
                return;
            }
            Toast.show('Cảm ơn bạn đã đánh giá!', 'success');
            selectStar(0);
        }

        // Khởi tạo cart button (variant mặc định)
        document.getElementById('btnAddCart').onclick = () => {
            const qty = parseInt(document.getElementById('qtyInput')?.value || 1);
            Cart.add({
                variant_id: {{ $defaultVariant?->id ?? 0 }},
                id:         {{ $product->id }},
                name:       '{{ addslashes($product->name) }}',
                variant:    '{{ addslashes($defaultVariant?->label ?? '') }}',
                price:      {{ $currentPrice }},
                img:        '{{ $product->thumbnail ? asset('storage/' . $product->thumbnail) : '' }}',
                qty,
            });
        };

        Object.assign(window, {
            selectAttr,
            changeQty,
            selectThumb,
            toggleWish,
            switchTab,
            selectStar,
            submitReview,
            updateVariant
        });
    </script>
@endsection