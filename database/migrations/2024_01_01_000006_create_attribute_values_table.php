<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attribute_values', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('attribute_id')->comment('Mã thuộc tính');
            $table->string('value', 100)->comment('Giá trị thuộc tính');
            $table->string('color_code', 20)->nullable()->comment('Mã màu hex');
            $table->integer('sort_order')->default(0)->comment('Thứ tự hiển thị');
            $table->timestamps();

            $table->foreign('attribute_id')
                ->references('id')->on('attributes')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->index('attribute_id', 'idx_attribute_values_attribute');
        });
    }

    public function down(): void
    {
        Schema::table('attribute_values', function (Blueprint $table) {
            $table->dropForeign(['attribute_id']);
        });
        Schema::dropIfExists('attribute_values');
    }
};
