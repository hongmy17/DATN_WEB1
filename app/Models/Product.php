<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use SoftDeletes;

    protected const DELETED_AT = 'delete_at';

    protected $fillable = [
        'code', 'category_id', 'name', 'slug',
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

    /** Tính năng 2: Attributes riêng cho sản phẩm này */
    public function customAttributes()
    {
        return $this->hasMany(ProductCustomAttribute::class)->orderBy('sort_order');
    }

    // ─── Accessors ────────────────────────────────────────────

    public function getMinPriceAttribute(): float|null
    {
        return $this->variants->min('price');
    }

    public function getMaxPriceAttribute(): float|null
    {
        return $this->variants->max('price');
    }
}