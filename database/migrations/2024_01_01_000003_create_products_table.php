<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->char('code', 15)->unique()->comment('Mã định danh sản phẩm');
            $table->unsignedBigInteger('category_id')->comment('Mã danh mục');
            $table->string('name')->comment('Tên sản phẩm');
            $table->string('slug', 200)->unique()->comment('Đường dẫn URL thân thiện');
            $table->text('short_description')->nullable()->comment('Mô tả ngắn');
            $table->longText('description')->nullable()->comment('Mô tả chi tiết');
            $table->string('thumbnail')->nullable()->comment('Ảnh đại diện sản phẩm');
            $table->tinyInteger('status')->default(1)->comment('1=hiện, 0=ẩn');
            $table->unsignedBigInteger('created_by')->nullable()->comment('ID người tạo');
            $table->timestamps();
            $table->timestamp('delete_at')->nullable()->comment('Thời gian xóa mềm');

            $table->foreign('category_id')
                ->references('id')->on('categories')
                ->onDelete('restrict')
                ->onUpdate('cascade');

            $table->foreign('created_by')
                ->references('id')->on('users')
                ->onDelete('set null')
                ->onUpdate('cascade');

            $table->index('category_id', 'idx_products_category');
            $table->index('slug', 'idx_products_slug');
            $table->index(['status', 'delete_at'], 'idx_products_status_deleted');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropForeign(['category_id']);
            $table->dropForeign(['created_by']);
        });
        Schema::dropIfExists('products');
    }
};
