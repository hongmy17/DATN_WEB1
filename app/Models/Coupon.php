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
        'min_order_value',
        'max_usage',
        'used_count',
        'start_date',
        'end_date',
        'status',
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date'   => 'datetime',
    ];

    // Kiểm tra coupon có hợp lệ không
    public function isValid(float $orderTotal): bool
    {
        // Kiểm tra status
        if ($this->status !== 1) return false;

        // Kiểm tra thời hạn
        $now = Carbon::now();
        if ($now->lt($this->start_date) || $now->gt($this->end_date)) return false;

        // Kiểm tra số lần dùng
        if ($this->max_usage !== null && $this->used_count >= $this->max_usage) return false;

        // Kiểm tra giá trị đơn hàng tối thiểu
        if ($orderTotal < $this->min_order_value) return false;

        return true;
    }

    // Tính số tiền giảm
    public function calcDiscount(float $orderTotal): float
    {
        if ($this->type === 0) {
            // Giảm theo %
            return round($orderTotal * $this->value / 100, 2);
        }
        // Giảm số tiền cố định
        return min($this->value, $orderTotal);
    }
}