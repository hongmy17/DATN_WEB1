<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Attribute;
use App\Models\AttributeValue;

class AttributeSeeder extends Seeder
{
    public function run(): void
    {
        // ── Màu sắc (dùng chung cho cả 3 loại SP) ────────────────────────
        // display_type = 1 → hiển thị dạng ô màu (color picker) trên trang SP
        $color = Attribute::create(['name' => 'Màu sắc', 'display_type' => 1]);
        AttributeValue::insert([
            ['attribute_id' => $color->id, 'value' => 'Đen',    'color_code' => '#1A1A1A', 'sort_order' => 1],
            ['attribute_id' => $color->id, 'value' => 'Trắng',  'color_code' => '#F5F5F5', 'sort_order' => 2],
            ['attribute_id' => $color->id, 'value' => 'Hồng',   'color_code' => '#F4A7B9', 'sort_order' => 3],
            ['attribute_id' => $color->id, 'value' => 'Xanh dương', 'color_code' => '#4A90D9', 'sort_order' => 4],
        ]);

        // ── Kết nối (dùng cho chuột & bàn phím) ──────────────────────────
        // display_type = 0 → hiển thị dạng nút chữ (button)
        $connect = Attribute::create(['name' => 'Kết nối', 'display_type' => 0]);
        AttributeValue::insert([
            ['attribute_id' => $connect->id, 'value' => 'Có dây (USB-A)',     'color_code' => null, 'sort_order' => 1],
            ['attribute_id' => $connect->id, 'value' => 'Không dây (2.4GHz)', 'color_code' => null, 'sort_order' => 2],
            ['attribute_id' => $connect->id, 'value' => 'Bluetooth 5.0',      'color_code' => null, 'sort_order' => 3],
        ]);

        // ── Layout bàn phím ───────────────────────────────────────────────
        $layout = Attribute::create(['name' => 'Layout', 'display_type' => 0]);
        AttributeValue::insert([
            ['attribute_id' => $layout->id, 'value' => 'Full-size (100%)', 'color_code' => null, 'sort_order' => 1],
            ['attribute_id' => $layout->id, 'value' => 'TKL (80%)',        'color_code' => null, 'sort_order' => 2],
            ['attribute_id' => $layout->id, 'value' => 'Compact (65%)',    'color_code' => null, 'sort_order' => 3],
        ]);

        // ── Kiểu kết nối tai nghe ─────────────────────────────────────────
        $headset = Attribute::create(['name' => 'Cổng kết nối', 'display_type' => 0]);
        AttributeValue::insert([
            ['attribute_id' => $headset->id, 'value' => '3.5mm Jack',     'color_code' => null, 'sort_order' => 1],
            ['attribute_id' => $headset->id, 'value' => 'USB-A',          'color_code' => null, 'sort_order' => 2],
            ['attribute_id' => $headset->id, 'value' => 'USB-C',          'color_code' => null, 'sort_order' => 3],
            ['attribute_id' => $headset->id, 'value' => 'Wireless 2.4GHz','color_code' => null, 'sort_order' => 4],
        ]);
    }
}
