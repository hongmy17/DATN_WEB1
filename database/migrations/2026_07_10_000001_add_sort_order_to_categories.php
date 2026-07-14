<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Tính năng "kéo-thả sắp xếp thứ tự danh mục" chưa từng được code — bảng
     * categories chưa có cột lưu thứ tự hiển thị, Filament table cũng chưa
     * bật ->reorderable(). Migration này thêm cột sort_order để làm việc đó.
     */
    public function up(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->unsignedInteger('sort_order')->default(0)->after('parent_id');
        });

        // Gán sẵn sort_order ban đầu theo đúng thứ tự id hiện có (giữ nguyên
        // thứ tự hiển thị cũ), để sau khi thêm cột không bị xáo trộn bất ngờ.
        DB::table('categories')->orderBy('id')->select('id')->get()
            ->each(function ($row, $index) {
                DB::table('categories')->where('id', $row->id)->update(['sort_order' => $index]);
            });
    }

    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->dropColumn('sort_order');
        });
    }
};