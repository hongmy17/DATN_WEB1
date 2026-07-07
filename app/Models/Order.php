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
        'payment_method',
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

    // ─── Stock hook: hoàn kho khi đơn chuyển sang "Đã hủy" ───────
    // MỚI: chỉ hoàn kho 1 LẦN tại đúng thời điểm order_status chuyển
    // SANG STATUS_CANCELLED từ 1 trạng thái khác (không lặp lại nếu
    // record được save nhiều lần ở trạng thái hủy, và không hoàn kho
    // khi đơn được TẠO MỚI thẳng với status = hủy, vì khi đó chưa
    // từng bị trừ kho lúc nào cả — chỉ trừ kho lúc tạo OrderItem).
    protected static function booted(): void
    {
        static::updating(function (Order $order) {
            $isBecomingCancelled = $order->isDirty('order_status')
                && (int) $order->order_status === self::STATUS_CANCELLED
                && (int) $order->getOriginal('order_status') !== self::STATUS_CANCELLED;

            if (! $isBecomingCancelled) {
                return;
            }

            $order->items()->with('variant')->get()->each(function (OrderItem $item) {
                ProductVariant::where('id', $item->variant_id)
                    ->increment('stock_quantity', $item->quantity);
            });
        });
    }

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