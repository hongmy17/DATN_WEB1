<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Attribute;
use App\Models\AttributeValue;

class AttributeSeeder extends Seeder
{
    public function run(): void
    {
        // ── Màu sắc (dùng chung toàn bộ sản phẩm) ───────────────────────
        // display_type = 1 → hiển thị dạng ô màu trên trang chi tiết SP
        $color = Attribute::create(['name' => 'Màu sắc', 'display_type' => 1]);
        $colors = [
            ['value' => 'Đen',       'color_code' => '#1A1A1A', 'sort_order' => 1],
            ['value' => 'Trắng',     'color_code' => '#F0F0F0', 'sort_order' => 2],
            ['value' => 'Hồng',      'color_code' => '#F4A7B9', 'sort_order' => 3],
            ['value' => 'Xanh Navy', 'color_code' => '#1B2A4A', 'sort_order' => 4],
            ['value' => 'Xanh Lá',  'color_code' => '#4CAF50', 'sort_order' => 5],
            ['value' => 'Kem',       'color_code' => '#F5E6CA', 'sort_order' => 6],
            ['value' => 'Bạc',       'color_code' => '#C0C0C0', 'sort_order' => 7],
            ['value' => 'Đỏ',        'color_code' => '#E30019', 'sort_order' => 8],
        ];
        foreach ($colors as $c) {
            AttributeValue::create(array_merge(['attribute_id' => $color->id], $c));
        }

        // ── Kết nối (chuột, bàn phím) ────────────────────────────────────
        // display_type = 0 → hiển thị dạng nút chữ
        $connect = Attribute::create(['name' => 'Kết nối', 'display_type' => 0]);
        $connects = [
            ['value' => 'Có dây',           'sort_order' => 1],
            ['value' => 'Không dây 2.4GHz', 'sort_order' => 2],
            ['value' => 'Bluetooth 5.0',    'sort_order' => 3],
            ['value' => 'Bluetooth 5.3',    'sort_order' => 4],
        ];
        foreach ($connects as $c) {
            AttributeValue::create(array_merge(['attribute_id' => $connect->id, 'color_code' => null], $c));
        }

        // ── Hộp sạc (AirPods) ────────────────────────────────────────────
        $case = Attribute::create(['name' => 'Hộp sạc', 'display_type' => 0]);
        $cases = [
            ['value' => 'Lightning',    'sort_order' => 1],
            ['value' => 'USB-C',        'sort_order' => 2],
            ['value' => 'MagSafe',      'sort_order' => 3],
        ];
        foreach ($cases as $c) {
            AttributeValue::create(array_merge(['attribute_id' => $case->id, 'color_code' => null], $c));
        }

        // ── Dung lượng pin dự phòng ───────────────────────────────────────
        $capacity = Attribute::create(['name' => 'Dung lượng', 'display_type' => 0]);
        $caps = [
            ['value' => '10.000mAh', 'sort_order' => 1],
            ['value' => '20.000mAh', 'sort_order' => 2],
            ['value' => '26.800mAh', 'sort_order' => 3],
        ];
        foreach ($caps as $c) {
            AttributeValue::create(array_merge(['attribute_id' => $capacity->id, 'color_code' => null], $c));
        }

        // ── Số cổng sạc ───────────────────────────────────────────────────
        $port = Attribute::create(['name' => 'Số cổng', 'display_type' => 0]);
        $ports = [
            ['value' => '1 cổng', 'sort_order' => 1],
            ['value' => '2 cổng', 'sort_order' => 2],
            ['value' => '3 cổng', 'sort_order' => 3],
        ];
        foreach ($ports as $p) {
            AttributeValue::create(array_merge(['attribute_id' => $port->id, 'color_code' => null], $p));
        }

        // ── Kích cỡ (Smartwatch) ──────────────────────────────────────────
        $size = Attribute::create(['name' => 'Kích cỡ', 'display_type' => 0]);
        $sizes = [
            ['value' => '41mm', 'sort_order' => 1],
            ['value' => '45mm', 'sort_order' => 2],
            ['value' => '49mm', 'sort_order' => 3],
        ];
        foreach ($sizes as $s) {
            AttributeValue::create(array_merge(['attribute_id' => $size->id, 'color_code' => null], $s));
        }
    }
}
