<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Attribute;
use App\Models\AttributeValue;

class AttributeSeeder extends Seeder
{
    public function run(): void
    {
        // Màu sắc
        $color = Attribute::create(['name' => 'Màu sắc', 'display_type' => 1]);
        AttributeValue::insert([
            ['attribute_id' => $color->id, 'value' => 'Space Black', 'color_code' => '#1C1C1C', 'sort_order' => 1],
            ['attribute_id' => $color->id, 'value' => 'Silver',      'color_code' => '#C0C0C0', 'sort_order' => 2],
            ['attribute_id' => $color->id, 'value' => 'Starlight',   'color_code' => '#F5F0E8', 'sort_order' => 3],
        ]);

        // Dung lượng
        $storage = Attribute::create(['name' => 'Dung lượng', 'display_type' => 0]);
        AttributeValue::insert([
            ['attribute_id' => $storage->id, 'value' => '512GB', 'color_code' => null, 'sort_order' => 1],
            ['attribute_id' => $storage->id, 'value' => '1TB',   'color_code' => null, 'sort_order' => 2],
            ['attribute_id' => $storage->id, 'value' => '2TB',   'color_code' => null, 'sort_order' => 3],
        ]);
    }
}