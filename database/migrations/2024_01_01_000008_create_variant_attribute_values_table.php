<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('variant_attribute_values', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('variant_id')->comment('Mã biến thể');
            $table->unsignedBigInteger('attribute_value_id')->comment('Mã giá trị thuộc tính');
            $table->timestamp('created_at')->useCurrent();

            $table->unique(['variant_id', 'attribute_value_id'], 'uq_variant_attr_value');

            $table->foreign('variant_id')
                ->references('id')->on('product_variants')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->foreign('attribute_value_id')
                ->references('id')->on('attribute_values')
                ->onDelete('cascade')
                ->onUpdate('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('variant_attribute_values', function (Blueprint $table) {
            $table->dropForeign(['variant_id']);
            $table->dropForeign(['attribute_value_id']);
        });
        Schema::dropIfExists('variant_attribute_values');
    }
};
