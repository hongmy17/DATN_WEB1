<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductCustomAttribute extends Model
{
    protected $fillable = ['product_id', 'name', 'is_visible', 'sort_order'];

    protected $casts = [
        'is_visible' => 'boolean',
    ];

    // ─── Auto sort_order ──────────────────────────────────────

    protected static function booted(): void
    {
        static::creating(function ($attr) {
            if (empty($attr->sort_order)) {
                $attr->sort_order = static::where('product_id', $attr->product_id)
                    ->max('sort_order') + 1;
            }
        });
    }

    // ─── Relations ────────────────────────────────────────────

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function values()
    {
        return $this->hasMany(ProductCustomAttributeValue::class)->orderBy('sort_order');
    }
}