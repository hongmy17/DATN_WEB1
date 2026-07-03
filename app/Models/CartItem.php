<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CartItem extends Model
{
    protected $fillable = ['user_id', 'variant_id', 'quantity'];

    public function variant()
    {
        return $this->belongsTo(ProductVariant::class, 'variant_id');
    }
}