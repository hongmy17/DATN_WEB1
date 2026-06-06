<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = ['code', 'category_id', 'name', 'slug', 'short_description', 'description', 'thumbnail', 'status', 'created_by'];

    // public function category()
    // {
    //     return $this->belongsTo(Category::class);
    // }
    public function images()
    {
        return $this->hasMany(ProductImage::class)->orderBy('sort_order');
    }
    public function variants()
    {
        return $this->hasMany(ProductVariant::class);
    }
    public function primaryImage()
    {
        return $this->hasOne(ProductImage::class)->where('is_primary', 1);
    }
}
