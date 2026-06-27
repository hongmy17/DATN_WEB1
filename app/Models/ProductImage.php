<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductImage extends Model
{
    public $timestamps = false;

    const CREATED_AT = 'created_at';
    const UPDATED_AT = null;

    protected $fillable = [
        'product_id',
        'image_url',
        'is_primary',
        'sort_order',
        'attribute_value_id',  // ← MỚI: gắn ảnh với 1 giá trị thuộc tính (thường là màu)
    ];

    protected $casts = [
        'is_primary'          => 'boolean',
        'attribute_value_id'  => 'integer',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * MỚI: Ảnh gắn với AttributeValue nào (vd: Đen, Đỏ...).
     * Client dùng để swap ảnh khi user chọn màu, y hệt Flasome.
     */
    public function attributeValue()
    {
        return $this->belongsTo(AttributeValue::class);
    }

    /**
     * Đường dẫn đầy đủ để hiển thị.
     */
    public function getUrlAttribute(): string
    {
        return asset('storage/' . $this->image_url);
    }
}