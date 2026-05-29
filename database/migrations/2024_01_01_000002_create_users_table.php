<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->char('code', 10)->unique()->comment('Mã định danh người dùng');
            $table->string('full_name')->comment('Họ và tên');
            $table->string('email', 100)->unique()->comment('Email đăng nhập');
            $table->string('password')->comment('Mật khẩu đã mã hóa');
            $table->string('phone', 15)->nullable()->comment('Số điện thoại');
            $table->tinyInteger('role')->default(0)->comment('0=khách hàng, 1=admin');
            $table->tinyInteger('status')->default(1)->comment('1=hoạt động, 0=khóa');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
