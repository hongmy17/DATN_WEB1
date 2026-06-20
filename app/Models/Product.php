<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use SoftDeletes;

    protected const DELETED_AT = 'delete_at';

    protected $fillable = [
        'code', 'base_sku', 'category_id', 'name', 'slug',
        'short_description', 'description',
        'thumbnail', 'status', 'created_by',
    ];

    protected $casts = [
        'status' => 'boolean',
    ];

    // ─── Relations ────────────────────────────────────────────

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function images()
    {
        return $this->hasMany(ProductImage::class)->orderBy('sort_order');
    }

    public function primaryImage()
    {
        return $this->hasOne(ProductImage::class)->where('is_primary', 1);
    }

    public function variants()
    {
        return $this->hasMany(ProductVariant::class);
    }

    /** Attributes toàn cục (dùng chung toàn hệ thống) */
    public function attributes()
    {
        return $this->belongsToMany(Attribute::class, 'product_attributes');
    }

    /** Attributes riêng cho sản phẩm này */
    public function customAttributes()
    {
        return $this->hasMany(ProductCustomAttribute::class)->orderBy('sort_order');
    }

    // ─── Scopes ───────────────────────────────────────────────

    /**
     * Chỉ lấy sản phẩm đang publish — dùng cho client.
     * Admin KHÔNG dùng scope này (admin cần xem cả sản phẩm đang ẩn).
     */
    public function scopeVisible(Builder $query): Builder
    {
        return $query->where('status', true);
    }

    /**
     * Sản phẩm "sẵn sàng publish": có status=true VÀ có ít nhất 1 variant
     * VÀ có ít nhất 1 ảnh. Dùng để admin tự kiểm tra trước khi public.
     */
    public function scopeReadyToPublish(Builder $query): Builder
    {
        return $query->where('status', true)
            ->whereHas('variants')
            ->whereHas('images');
    }

    // ─── Accessors ────────────────────────────────────────────

    public function getMinPriceAttribute(): ?float
    {
        return $this->variants_min_price ?? $this->variants()->min('price');
    }

    public function getMaxPriceAttribute(): ?float
    {
        return $this->variants_max_price ?? $this->variants()->max('price');
    }

    public function getThumbnailUrlAttribute(): ?string
    {
        return $this->thumbnail ? asset('storage/' . $this->thumbnail) : null;
    }

    /**
     * Sản phẩm có đủ điều kiện publish hay không.
     * Dùng để cảnh báo admin trong form trước khi lưu status=true.
     */
    public function getIsReadyToPublishAttribute(): bool
    {
        return $this->variants()->exists() && $this->images()->exists();
    }
}