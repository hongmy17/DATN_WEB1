<?php

namespace App\Models;

use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements FilamentUser
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'code',
        'name',
        'email',
        'phone',
        'password',
        'avatar',
        'phone',
        'role',
        'status',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function ($user) {
            if (empty($user->code)) {
                $lastUser = self::orderByDesc('id')->first();

                if (! $lastUser) {
                    $user->code = 'USER000001';
                } else {
                    $number = (int) substr($lastUser->code, 4);
                    $user->code = 'USER' . str_pad($number + 1, 6, '0', STR_PAD_LEFT);
                }
            }
        });
    }

    /**
     * Cho phép đăng nhập Filament.
     */
    public function canAccessPanel(Panel $panel): bool
    {
        return true;

        // Nếu chỉ admin được vào:
        // return $this->role === 1;
    }

    /**
     * Tên hiển thị trên Filament.
     */
    public function getFilamentName(): string
    {
        return $this->full_name ?: $this->email;
    }

    public function getNameAttribute(): string
    {
        return $this->full_name;
    }

  
}
