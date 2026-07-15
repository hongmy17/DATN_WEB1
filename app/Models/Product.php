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
        'code',
        'base_sku',
        'category_id',
        'name',
        'slug',
        'short_description',
        'description',
        'thumbnail',
        'status',
        'created_by',
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

    /**
     * Bản đồ [product_id => tổng số lượng đã bán trong X ngày gần nhất].
     * Dùng ĐÚNG quy tắc đếm với TopProductsWidget (COD tính khi Hoàn thành,
     * online tính từ Đã xác nhận) để không lệch số liệu giữa các nơi.
     * Cache lại 6 tiếng — không tính lại mỗi lần load trang.
     */
    public static function soldCountsMap(int $days = 30): array
    {
        return \Illuminate\Support\Facades\Cache::remember(
            "product_sold_counts_{$days}d",
            now()->addHours(6),
            function () use ($days) {
                return \Illuminate\Support\Facades\DB::table('order_items')
                    ->join('orders', 'orders.id', '=', 'order_items.order_id')
                    ->join('product_variants', 'product_variants.id', '=', 'order_items.variant_id')
                    ->where('orders.created_at', '>=', now()->subDays($days))
                    ->where(function ($q) {
                        $q->where(function ($cod) {
                            $cod->where('orders.payment_method', 'cod')
                                ->where('orders.order_status', \App\Models\Order::STATUS_COMPLETED);
                        })->orWhere(function ($online) {
                            $online->where('orders.payment_method', '!=', 'cod')
                                ->whereIn('orders.order_status', [
                                    \App\Models\Order::STATUS_CONFIRMED,
                                    \App\Models\Order::STATUS_SHIPPING,
                                    \App\Models\Order::STATUS_COMPLETED,
                                ]);
                        });
                    })
                    ->select('product_variants.product_id', \Illuminate\Support\Facades\DB::raw('SUM(order_items.quantity) as total_qty'))
                    ->groupBy('product_variants.product_id')
                    ->pluck('total_qty', 'product_id')
                    ->toArray();
            }
        );
    }

    /** Gắn thuộc tính sold_count vào 1 collection sản phẩm, đọc từ map đã cache — KHÔNG query thêm cho từng dòng. */
    public static function attachSoldCounts($products, int $days = 30)
    {
        $map = self::soldCountsMap($days);
        foreach ($products as $product) {
            $product->sold_count = (int) ($map[$product->id] ?? 0);
        }
        return $products;
    }
}
