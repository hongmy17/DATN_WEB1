<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use SoftDeletes;

    // ── FIX 1: đặt tên column xóa mềm đúng với schema DB ────────────
    // Column trong DB là "delete_at", không phải "deleted_at"
    const DELETED_AT = 'delete_at';

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
     *
     * FIX 1: Bỏ whereNull('delete_at') vì SoftDeletes đã tự thêm điều kiện
     * này vào mọi query (global scope). scopeVisible chỉ cần check status.
     * Nếu bạn KHÔNG dùng SoftDeletes global scope (withTrashed), thì Laravel
     * tự loại sản phẩm có delete_at != NULL rồi — không cần check tay.
     */
    public function scopeVisible(Builder $query): Builder
    {
        return $query->where('status', true);
        // SoftDeletes global scope đã tự thêm: AND delete_at IS NULL
        // Không cần ->whereNull('delete_at') thêm nữa.
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

    /**
     * FIX 5: Accessor min_price dùng aggregate đã được eager-load qua
     * withMin('variants', 'price'). Nếu chưa eager-load thì fallback về
     * query — nhưng khuyến khích luôn dùng withMin() khi query danh sách.
     *
     * Cách dùng đúng trong Controller/Query:
     *   Product::visible()->withMin('variants', 'price')->withMax('variants', 'price')->get()
     * Khi đó $product->variants_min_price và $product->variants_max_price sẽ có giá trị,
     * accessor sẽ trả về ngay mà không cần thêm query.
     */
    public function getMinPriceAttribute(): ?float
    {
        // variants_min_price được Eloquent tự gắn khi dùng withMin()
        if (array_key_exists('variants_min_price', $this->attributes)) {
            return $this->attributes['variants_min_price'] !== null
                ? (float) $this->attributes['variants_min_price']
                : null;
        }

        // Fallback: query trực tiếp (chấp nhận thêm 1 query nếu không eager-load)
        return $this->variants()->active()->min('price');
    }

    public function getMaxPriceAttribute(): ?float
    {
        if (array_key_exists('variants_max_price', $this->attributes)) {
            return $this->attributes['variants_max_price'] !== null
                ? (float) $this->attributes['variants_max_price']
                : null;
        }

        return $this->variants()->active()->max('price');
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

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function getAvgRatingAttribute(): float
    {
        return round($this->reviews()->visible()->avg('rating') ?? 0, 1);
    }

    public function getReviewCountAttribute(): int
    {
        return $this->reviews()->visible()->count();
    }
}
