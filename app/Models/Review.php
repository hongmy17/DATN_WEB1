<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    protected $fillable = [
        'product_id',
        'user_id',
        'order_item_id',
        'rating',
        'comment',
        'images',
        'status',
        'edit_count',
        'edited_at',
    ];

    protected $casts = [
        'images'     => 'array',
        'status'     => 'boolean',
        'edit_count' => 'integer',
        'edited_at'  => 'datetime',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function orderItem()
    {
        return $this->belongsTo(OrderItem::class);
    }

    // Chỉ lấy reply gốc (không phải reply của khách) — admin reply
    public function replies()
    {
        return $this->hasMany(ReviewReply::class)
            ->whereNull('parent_id')
            ->with(['user', 'childReplies.user'])
            ->orderBy('created_at');
    }

    public function scopeVisible($query)
    {
        return $query->where('status', true);
    }

    public static function hasReviewed(int $orderItemId): bool
    {
        return static::where('order_item_id', $orderItemId)->exists();
    }

    public function canEdit(): bool
    {
        return $this->edit_count < 1;
    }
}
