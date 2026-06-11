<?php

namespace App\Models;

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

    // Ngưỡng "sắp hết hàng" — đổi tùy ý
    const LOW_STOCK_THRESHOLD = 5;

    // ─── Relations ────────────────────────────────────────────

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function attributeValues()
    {
        return $this->belongsToMany(
            AttributeValue::class,
            'variant_attribute_values',
            'variant_id',
            'attribute_value_id'
        );
    }

    // ─── Tính năng 1: Stock status accessor ───────────────────

    /**
     * Trả về stock status dạng key: 'in_stock' | 'low_stock' | 'out_of_stock'
     * Dùng cho frontend: if ($variant->stock_status === 'out_of_stock') ...
     */
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

    /**
     * Trả về label tiếng Việt để hiển thị UI
     * Dùng: $variant->stock_status_label → "Còn hàng"
     */
    public function getStockStatusLabelAttribute(): string
    {
        return match ($this->stock_status) {
            'out_of_stock' => 'Hết hàng',
            'low_stock'    => 'Sắp hết hàng',
            default        => 'Còn hàng',
        };
    }

    /**
     * Màu badge cho Filament / frontend
     * Dùng: $variant->stock_status_color → "danger"
     */
    public function getStockStatusColorAttribute(): string
    {
        return match ($this->stock_status) {
            'out_of_stock' => 'danger',
            'low_stock'    => 'warning',
            default        => 'success',
        };
    }
}