<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductImage extends Model
{
    protected $fillable = ['product_id', 'image_url', 'is_primary', 'sort_order'];

    // DB chỉ có created_at (DEFAULT CURRENT_TIMESTAMP), không có updated_at
    const UPDATED_AT = null;

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
