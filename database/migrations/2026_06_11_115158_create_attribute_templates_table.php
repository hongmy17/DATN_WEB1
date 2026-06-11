<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Template theo danh mục
        Schema::create('attribute_templates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('categories')->cascadeOnDelete();
            $table->string('name', 100)->comment('VD: Điện Thoại, Laptop');
            $table->timestamps();
        });

        // Các thuộc tính trong template
        Schema::create('attribute_template_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('attribute_template_id')->constrained('attribute_templates')->cascadeOnDelete();
            $table->string('name', 100)->comment('VD: RAM, Màn hình, Pin');
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attribute_template_items');
        Schema::dropIfExists('attribute_templates');
    }
};