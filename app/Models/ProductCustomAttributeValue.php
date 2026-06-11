<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductCustomAttributeValue extends Model
{
    protected $fillable = ['product_custom_attribute_id', 'value', 'sort_order'];

    // ─── Auto sort_order ──────────────────────────────────────

    protected static function booted(): void
    {
        static::creating(function ($val) {
            if (empty($val->sort_order)) {
                $val->sort_order = static::where('product_custom_attribute_id', $val->product_custom_attribute_id)
                    ->max('sort_order') + 1;
            }
        });
    }

    // ─── Relations ────────────────────────────────────────────

    public function attribute()
    {
        return $this->belongsTo(ProductCustomAttribute::class, 'product_custom_attribute_id');
    }
}