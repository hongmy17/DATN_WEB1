<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_addresses', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->comment('Mã người dùng');
            $table->string('receiver_name', 100)->comment('Tên người nhận');
            $table->string('receiver_phone', 15)->comment('Số điện thoại người nhận');
            $table->string('province', 100)->comment('Tỉnh / Thành phố');
            $table->string('district', 100)->comment('Quận / Huyện');
            $table->string('ward', 100)->comment('Phường / Xã');
            $table->text('address_detail')->comment('Địa chỉ chi tiết');
            $table->boolean('is_default')->default(false)->comment('Địa chỉ mặc định');
            $table->timestamps();

            $table->foreign('user_id')
                ->references('id')->on('users')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->index('user_id', 'idx_user_addresses_user');
        });
    }

    public function down(): void
    {
        Schema::table('user_addresses', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
        });
        Schema::dropIfExists('user_addresses');
    }
};
