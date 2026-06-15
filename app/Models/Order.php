<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'user_id',
        'address_id',
        'coupon_id',
        'receiver_name',
        'receiver_phone',
        'province',
        'district',
        'ward',
        'address_detail',
        'shipping_address',
        'order_status',
        'coupon_code',
        'subtotal',
        'discount_amount',
        'total_amount',
        'note',
    ];

    // Trạng thái đơn hàng
    const STATUS_PENDING    = 0;
    const STATUS_CONFIRMED  = 1;
    const STATUS_SHIPPING   = 2;
    const STATUS_COMPLETED  = 3;
    const STATUS_CANCELLED  = 4;

    public function statusLabel(): string
    {
        return match ($this->order_status) {
            0 => 'Chờ xác nhận',
            1 => 'Đã xác nhận',
            2 => 'Đang giao',
            3 => 'Hoàn thành',
            4 => 'Đã hủy',
            default => 'Không xác định',
        };
    }

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function address()
    {
        return $this->belongsTo(UserAddress::class, 'address_id');
    }

    public function coupon()
    {
        return $this->belongsTo(Coupon::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }
}
