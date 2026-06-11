<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttributeTemplate extends Model
{
    protected $fillable = ['category_id', 'name'];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function items()
    {
        return $this->hasMany(AttributeTemplateItem::class)->orderBy('sort_order');
    }
}