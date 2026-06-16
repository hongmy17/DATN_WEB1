<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Coupon extends Model
{
    protected $fillable = [
        'coupon_code',
        'type',
        'value',
        'max_discount',
        'min_order_value',
        'max_usage',
        'used_count',
        'start_date',
        'end_date',
        'status',
    ];

    protected $casts = [
        'start_date'   => 'datetime',
        'end_date'     => 'datetime',
        'max_discount' => 'float',
    ];

    const TYPE_PERCENT = 0;
    const TYPE_FIXED   = 1;

    /**
     * Validate coupon - trả về chuỗi lỗi cụ thể, hoặc null nếu hợp lệ.
     */
    public function validate(float $orderTotal): ?string
    {
        if ($this->status !== 1) {
            return 'Mã giảm giá đã bị vô hiệu hóa.';
        }

        $now = Carbon::now();
        if ($now->lt($this->start_date)) {
            return 'Mã giảm giá chưa đến ngày hiệu lực (từ ' . $this->start_date->format('d/m/Y') . ').';
        }
        if ($now->gt($this->end_date)) {
            return 'Mã giảm giá đã hết hạn vào ' . $this->end_date->format('d/m/Y H:i') . '.';
        }

        if ($this->max_usage !== null && $this->used_count >= $this->max_usage) {
            return 'Mã giảm giá đã hết lượt sử dụng.';
        }

        if ($orderTotal < $this->min_order_value) {
            return 'Đơn hàng tối thiểu ' . number_format($this->min_order_value, 0, ',', '.') . '₫ để dùng mã này.';
        }

        return null;
    }

    /**
     * Backward-compatible: trả về bool.
     */
    public function isValid(float $orderTotal): bool
    {
        return $this->validate($orderTotal) === null;
    }

    /**
     * Tính số tiền giảm, có hỗ trợ max_discount với loại %.
     */
    public function calcDiscount(float $orderTotal): float
    {
        if ($this->type === self::TYPE_PERCENT) {
            $discount = round($orderTotal * $this->value / 100, 2);
            if ($this->max_discount !== null && $this->max_discount > 0) {
                $discount = min($discount, $this->max_discount);
            }
            return $discount;
        }

        return min($this->value, $orderTotal);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}