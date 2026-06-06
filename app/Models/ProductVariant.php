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
}