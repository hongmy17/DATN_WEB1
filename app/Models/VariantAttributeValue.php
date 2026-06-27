<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VariantAttributeValue extends Model
{
    protected $fillable = ['variant_id', 'attribute_value_id'];

    // DB chỉ có created_at (DEFAULT CURRENT_TIMESTAMP), không có updated_at
    const UPDATED_AT = null;

    public function variant()
    {
        return $this->belongsTo(ProductVariant::class, 'variant_id');
    }

    public function attributeValue()
    {
        return $this->belongsTo(AttributeValue::class, 'attribute_value_id');
    }
}