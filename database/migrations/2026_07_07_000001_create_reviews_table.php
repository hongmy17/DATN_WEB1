<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            // order_item_id để xác minh "đã mua hàng" — NULL = chưa xác minh
            $table->foreignId('order_item_id')->nullable()->constrained('order_items')->nullOnDelete();
            $table->tinyInteger('rating');                  // 1–5 sao
            $table->text('comment')->nullable();
            $table->json('images')->nullable();             // mảng path ảnh review
            $table->boolean('status')->default(true);       // admin có thể ẩn
            $table->timestamps();

            // 1 order_item chỉ được review 1 lần
            $table->unique('order_item_id');
        });

        Schema::create('review_replies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('review_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete(); // admin/shop
            $table->text('comment');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('review_replies');
        Schema::dropIfExists('reviews');
    }
};
