<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Bảng chuẩn của Laravel dùng cho chức năng quên mật khẩu (lưu token + thời
     * điểm gửi, do Password::sendResetLink()/Password::reset() tự động đọc/ghi).
     * Project này dùng migration users tự viết tay thay vì bản mặc định của
     * Laravel, nên bảng này bị thiếu — chưa từng được tạo ra.
     */
    public function up(): void
    {
        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('password_reset_tokens');
    }
};