<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class ProductVariant extends Model
{
    protected $fillable = [
        'product_id',
        'sku',
        'price',
        'compare_price',
        'stock_quantity',
        'image',
        'status',
    ];

    protected $casts = [
        'price'          => 'float',
        'compare_price'  => 'float',
        'stock_quantity' => 'integer',
        'status'         => 'boolean',
    ];

    const LOW_STOCK_THRESHOLD = 5;

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

    /** Chỉ lấy variant còn hàng */
    public function scopeInStock(Builder $query): Builder
    {
        return $query->where('stock_quantity', '>', 0);
    }

    // ─── Stock status accessors ────────────────────────────────

    public function getStockStatusAttribute(): string
    {
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
        return match ($this->stock_status) {
            'out_of_stock' => 'Hết hàng',
            'low_stock'    => 'Sắp hết hàng',
            default        => 'Còn hàng',
        };
    }

    public function getStockStatusColorAttribute(): string
    {
        return match ($this->stock_status) {
            'out_of_stock' => 'danger',
            'low_stock'    => 'warning',
            default        => 'success',
        };
    }

    // ─── Price / image accessors ───────────────────────────────

    /** % giảm giá, null nếu không có compare_price hoặc không giảm */
    public function getDiscountPercentAttribute(): ?int
    {
        if (! $this->compare_price || $this->compare_price <= $this->price) {
            return null;
        }

        return (int) round((($this->compare_price - $this->price) / $this->compare_price) * 100);
    }

    /** Ảnh hiển thị: ưu tiên ảnh riêng variant, fallback về ảnh đại diện sản phẩm */
    public function getDisplayImageAttribute(): ?string
    {
        if ($this->image) {
            return asset('storage/' . $this->image);
        }

        return $this->product?->thumbnail_url;
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
            ->sortBy(fn ($v) => [$v->attribute_id, $v->sort_order])
            ->map(fn ($v) => $v->attribute->name . ': ' . $v->value)
            ->join(' / ');
    }
}
