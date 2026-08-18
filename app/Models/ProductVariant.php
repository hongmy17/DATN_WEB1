<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductVariant extends Model
{
    // Xóa mềm: order_items.variant_id trỏ tới bảng này. Xóa cứng một biến thể
    // đã từng được đặt mua sẽ phá vỡ liên kết của đơn hàng cũ — và làm hỏng
    // chức năng hoàn kho khi hủy đơn (xem Order::booted()).
    use SoftDeletes;

    protected $fillable = [
        'product_id',
        'sku',
        'price',
        'compare_price',
        'sale_price',          // MỚI: giá khuyến mãi có thời hạn
        'sale_starts_at',      // MỚI
        'sale_ends_at',        // MỚI
        'stock_quantity',
        'manage_stock',        // MỚI: false = không trừ/check kho (hàng đặt trước, dịch vụ)
        'image',
        'gallery',             // MỚI: nhiều ảnh phụ riêng cho biến thể
        'description',         // MỚI: mô tả ngắn riêng biến thể
        'is_default',          // MỚI: biến thể được chọn sẵn
        'status',
    ];

    protected $casts = [
        'price'          => 'float',
        'compare_price'  => 'float',
        'sale_price'     => 'float',
        'sale_starts_at' => 'datetime',
        'sale_ends_at'   => 'datetime',
        'stock_quantity' => 'integer',
        'manage_stock'   => 'boolean',
        'gallery'        => 'array',
        'is_default'     => 'boolean',
        'status'         => 'boolean',
    ];

    const LOW_STOCK_THRESHOLD = 5;

    // ─── MỚI: đảm bảo mỗi sản phẩm chỉ có ĐÚNG 1 is_default = true ────
    // Khi 1 variant được set is_default, tự bỏ cờ này ở tất cả variant
    // khác cùng sản phẩm — admin không cần tự tay làm việc đó.
    protected static function booted(): void
    {
        static::saved(function (ProductVariant $variant) {
            if ($variant->is_default) {
                static::where('product_id', $variant->product_id)
                    ->where('id', '!=', $variant->id)
                    ->update(['is_default' => false]);
            }
        });

        static::deleted(function (ProductVariant $variant) {
            // Nếu xóa đúng variant đang là default, tự gán default cho 1 variant còn lại
            if ($variant->is_default) {
                // Từ khi model dùng SoftDeletes, sự kiện `deleted` cũng chạy khi
                // xóa MỀM — bản ghi vẫn còn trong bảng và vẫn giữ is_default = true.
                // Nếu không hạ cờ này xuống, lúc Khôi phục sẽ có HAI biến thể cùng
                // là mặc định. whereKey()->update() không kích hoạt lại event nên
                // không gây vòng lặp.
                static::withTrashed()->whereKey($variant->id)->update(['is_default' => false]);

                // where() bên dưới đã tự loại biến thể trong thùng rác (global
                // scope), nên chỉ chọn được biến thể đang hoạt động.
                $next = static::where('product_id', $variant->product_id)->first();
                $next?->update(['is_default' => true]);
            }
        });
    }

    // ─── Relations ────────────────────────────────────────────

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * FIX 3: Thêm ->withPivot() không cần thiết ở đây, nhưng quan trọng là
     * eager-load 'attribute' ngay trong relation để getAttributeLabelAttribute
     * không bắn thêm query khi duyệt collection.
     *
     * Cách dùng đúng khi query variants:
     *   $product->variants()->with('attributeValues.attribute')->get()
     * Hoặc dùng $with bên dưới để tự động load.
     */
    public function attributeValues()
    {
        return $this->belongsToMany(
            AttributeValue::class,
            'variant_attribute_values',
            'variant_id',
            'attribute_value_id'
        );
    }

    // ─── Scopes ───────────────────────────────────────────────

    /** Chỉ lấy variant đang bán (status=true) — dùng cho client */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', true);
    }

    /** Chỉ lấy variant còn hàng (bỏ qua nếu variant không quản lý kho) */
    public function scopeInStock(Builder $query): Builder
    {
        return $query->where(
            fn($q) => $q
                ->where('manage_stock', false)
                ->orWhere('stock_quantity', '>', 0)
        );
    }

    // ─── Stock status accessors ────────────────────────────────

    public function getStockStatusAttribute(): string
    {
        // MỚI: sản phẩm không quản lý kho (đặt trước/dịch vụ) luôn coi như còn hàng
        if (! $this->manage_stock) {
            return 'in_stock';
        }

        if ($this->stock_quantity <= 0) {
            return 'out_of_stock';
        }
        if ($this->stock_quantity <= self::LOW_STOCK_THRESHOLD) {
            return 'low_stock';
        }
        return 'in_stock';
    }

    public function getStockStatusLabelAttribute(): string
    {
        if (! $this->manage_stock) {
            return 'Đặt trước / Dịch vụ';
        }

        return match ($this->stock_status) {
            'out_of_stock' => 'Hết hàng',
            'low_stock'    => 'Sắp hết hàng',
            default        => 'Còn hàng',
        };
    }

    public function getStockStatusColorAttribute(): string
    {
        if (! $this->manage_stock) {
            return 'gray';
        }

        return match ($this->stock_status) {
            'out_of_stock' => 'danger',
            'low_stock'    => 'warning',
            default        => 'success',
        };
    }

    // ─── Price / image accessors ───────────────────────────────

    /**
     * MỚI: trạng thái flash sale — nguồn sự thật DUY NHẤT cho cả admin lẫn client,
     * tránh mỗi nơi tự so sánh sale_starts_at/sale_ends_at một kiểu khác nhau.
     * - 'none'     : chưa đặt giá khuyến mãi
     * - 'upcoming' : đã lên lịch nhưng chưa tới giờ bắt đầu
     * - 'active'   : đang trong khoảng bắt đầu → kết thúc (đây là lúc hiển thị sale cho khách)
     * - 'expired'  : đã qua giờ kết thúc nhưng dữ liệu vẫn còn lưu trong DB
     */
    public function getSaleStatusAttribute(): string
    {
        if (! $this->sale_price) {
            return 'none';
        }

        $now = now();

        if ($this->sale_starts_at && $now->lt($this->sale_starts_at)) {
            return 'upcoming';
        }

        if ($this->sale_ends_at && $now->gt($this->sale_ends_at)) {
            return 'expired';
        }

        return 'active';
    }

    public function getSaleStatusLabelAttribute(): string
    {
        return match ($this->sale_status) {
            'active'   => 'Đang diễn ra',
            'upcoming' => 'Sắp diễn ra',
            'expired'  => 'Đã kết thúc',
            default    => '—',
        };
    }

    public function getSaleStatusColorAttribute(): string
    {
        return match ($this->sale_status) {
            'active'   => 'success',
            'upcoming' => 'warning',
            'expired'  => 'gray',
            default    => 'gray',
        };
    }

    /**
     * MỚI: giá khuyến mãi đang còn hiệu lực hay không.
     * Chỉ còn là 1 lớp alias mỏng của sale_status để code cũ dùng $variant->is_sale_active
     * không phải sửa lại — nhưng logic thật sự nằm duy nhất ở getSaleStatusAttribute().
     */
    public function getIsSaleActiveAttribute(): bool
    {
        return $this->sale_status === 'active';
    }

    /**
     * MỚI: giá bán thực tế hiển thị cho khách — ưu tiên sale_price nếu đang
     * trong thời hạn khuyến mãi, ngược lại dùng `price` như bình thường.
     * Dùng accessor này ở client thay vì đọc trực tiếp `price`.
     */
    public function getCurrentPriceAttribute(): float
    {
        return $this->is_sale_active ? (float) $this->sale_price : (float) $this->price;
    }

    /** % giảm giá, null nếu không có compare_price hoặc không giảm */
    public function getDiscountPercentAttribute(): ?int
    {
        $current = $this->current_price;

        if (! $this->compare_price || $this->compare_price <= $current) {
            return null;
        }

        return (int) round((($this->compare_price - $current) / $this->compare_price) * 100);
    }

    /** Ảnh hiển thị: ưu tiên ảnh riêng variant, fallback về ảnh đại diện sản phẩm */
    public function getDisplayImageAttribute(): ?string
    {
        if ($this->image) {
            return asset('storage/' . $this->image);
        }

        return $this->product?->thumbnail_url;
    }

    /** MỚI: toàn bộ ảnh của biến thể (ảnh chính + gallery), dùng cho slider/lightbox */
    public function getAllImagesAttribute(): array
    {
        $images = [];

        if ($this->image) {
            $images[] = asset('storage/' . $this->image);
        }

        foreach ($this->gallery ?? [] as $path) {
            $images[] = asset('storage/' . $path);
        }

        return $images;
    }

    /**
     * FIX 3: Nhãn tổ hợp thuộc tính — sort nhất quán theo attribute_id rồi sort_order.
     * VD: "Màu sắc: Đen / Dung lượng: 512GB"
     *
     * QUAN TRỌNG: Luôn eager-load 'attributeValues.attribute' trước khi gọi accessor này.
     * Nếu không, mỗi lần dùng $variant->attribute_label sẽ tạo thêm N query.
     *
     *   ✅ Đúng: $variants->load('attributeValues.attribute')
     *   ✅ Đúng: Variant::with('attributeValues.attribute')->get()
     *   ❌ Sai:  Variant::all()->map(fn($v) => $v->attribute_label)  ← N+1
     */
    public function getAttributeLabelAttribute(): string
    {
        // FIX 3: sort theo [attribute_id, sort_order] để thứ tự nhất quán
        // (Màu sắc luôn đứng trước Dung lượng nếu attribute_id Màu < Dung lượng)
        return $this->attributeValues
            ->sortBy(fn($v) => [$v->attribute_id, $v->sort_order])
            ->map(fn($v) => $v->attribute->name . ': ' . $v->value)
            ->join(' / ');
    }
}
