<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100)->comment('Tên danh mục');
            $table->string('slug', 150)->unique()->comment('Đường dẫn URL');
            $table->unsignedBigInteger('parent_id')->nullable()->comment('Danh mục cha (tự tham chiếu)');
            $table->text('description')->nullable()->comment('Mô tả danh mục');
            $table->timestamps();
            $table->foreign('parent_id')
                ->references('id')->on('categories')
                ->onDelete('set null')
                ->onUpdate('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->dropForeign(['parent_id']);
        });
        Schema::dropIfExists('categories');
    }
};
