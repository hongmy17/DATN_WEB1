<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserAddress extends Model
{
    protected $fillable = [
        'user_id',
        'receiver_name',
        'receiver_phone',
        'province',
        'district',
        'ward',
        'address_detail',
        'is_default',
    ];

    protected $casts = [
        'is_default' => 'boolean',
    ];

    // Quan hệ với User
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Khi set mặc định mới → tự bỏ mặc định cũ
    public function setAsDefault()
    {
        self::where('user_id', $this->user_id)
            ->update(['is_default' => false]);

        $this->update(['is_default' => true]);
    }
}