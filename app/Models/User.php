<?php

namespace App\Models;

use App\Models\UserAddress;
use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasName;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements FilamentUser, HasName
{
    // Xóa mềm bắt buộc với bảng này: orders.user_id khai báo
    // onDelete('restrict'), nên xóa cứng một khách đã từng đặt hàng sẽ ném
    // lỗi SQL. Ngoài ra reviews.user_id là cascadeOnDelete — xóa cứng một
    // khách là mất luôn toàn bộ đánh giá của họ trên mọi sản phẩm.
    //
    // Tác dụng phụ có lợi: tài khoản đã xóa mềm không đăng nhập được nữa,
    // vì Laravel tra cứu người dùng qua Model (đã có global scope lọc
    // deleted_at IS NULL).
    use HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'code',
        'name',
        'email',
        'phone',
        'password',
        'avatar',
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

    public function canAccessPanel(Panel $panel): bool
    {
        return true;
    }

    public function getFilamentName(): string
    {
        return trim((string) ($this->name ?: $this->email ?: $this->code ?: 'User'));
    }

    // ── Relationships ────────────────────────────────────────

    public function addresses()
    {
        return $this->hasMany(UserAddress::class);
    }

    public function defaultAddress()
    {
        return $this->hasOne(UserAddress::class)->where('is_default', true);
    }

    public function orders()
    {
        return $this->hasMany(\App\Models\Order::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    // FIX: mặc định Laravel gửi email đặt lại mật khẩu bằng tiếng Anh, không có
    // thương hiệu gì ("Reset Password Notification"...). Đổi sang notification
    // tiếng Việt riêng cho Nexus Store.
    public function sendPasswordResetNotification($token): void
    {
        $this->notify(new \App\Notifications\ResetPasswordNotification($token));
    }
}
