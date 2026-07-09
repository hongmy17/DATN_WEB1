<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = [
        'order_id',
        'payment_gateway',
        'transaction_code',
        'amount',
        'status',
        'gateway_response',
        'paid_at',
    ];

    protected $casts = [
        'gateway_response' => 'array',
        'paid_at'          => 'datetime',
        'amount'           => 'float',
    ];

    // Status constants
    const STATUS_PENDING  = 0;
    const STATUS_SUCCESS  = 1;
    const STATUS_FAILED   = 2;
    const STATUS_REFUNDED = 3;

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function statusLabel(): string
    {
        return match ((int) $this->status) {
            1       => 'Thành công',
            2       => 'Thất bại',
            3       => 'Hoàn tiền',
            default => 'Chờ thanh toán',
        };
    }
}