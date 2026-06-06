<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use SoftDeletes;

    // Khớp với tên cột trong DB (delete_at thay vì deleted_at mặc định)
    protected const DELETED_AT = 'delete_at';

    protected $fillable = [
        'code',
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

    // ─── Accessors ────────────────────────────────────────────

    /** Giá thấp nhất trong các biến thể */
    public function getMinPriceAttribute(): float|null
    {
        return $this->variants->min('price');
    }

    /** Giá cao nhất trong các biến thể */
    public function getMaxPriceAttribute(): float|null
    {
        return $this->variants->max('price');
    }
}