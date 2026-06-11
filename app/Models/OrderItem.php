<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'order_id',
        'variant_id',
        'product_name',
        'variant_description',
        'quantity',
        'unit_price',
    ];

    // Relationships
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function variant()
    {
        return $this->belongsTo(ProductVariant::class, 'variant_id');
    }

    // Tính thành tiền
    public function subTotal(): float
    {
        return $this->quantity * $this->unit_price;
    }
}