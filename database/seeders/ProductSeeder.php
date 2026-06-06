<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\{Product, ProductImage, ProductVariant, AttributeValue, VariantAttributeValue};

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $product = Product::create([
            'code'              => 'PRD0000001',
            'category_id'       => 13, // Laptop
            'name'              => 'MacBook Pro 14" M3 Pro',
            'slug'              => 'macbook-pro-14-m3-pro',
            'short_description' => 'CPU 12-core, GPU 18-core, 18GB RAM, màn hình Liquid Retina XDR 120Hz',
            'description'       => '<p>MacBook Pro 14 inch với chip M3 Pro mang đến hiệu năng chuyên nghiệp đột phá. CPU 12-core và GPU 18-core xử lý nhanh hơn 40% so với thế hệ trước.</p>',
            'thumbnail'         => null,
            'status'            => 1,
            'created_by'        => 1,
        ]);

        // Ảnh thư viện
        ProductImage::insert([
            ['product_id' => $product->id, 'image_url' => 'products/mbp14-main.jpg',  'is_primary' => 1, 'sort_order' => 1],
            ['product_id' => $product->id, 'image_url' => 'products/mbp14-box.jpg',   'is_primary' => 0, 'sort_order' => 2],
            ['product_id' => $product->id, 'image_url' => 'products/mbp14-ports.jpg', 'is_primary' => 0, 'sort_order' => 3],
        ]);

        // Lấy attribute values
        $blk  = AttributeValue::where('value', 'Space Black')->first();
        $slv  = AttributeValue::where('value', 'Silver')->first();
        $s512 = AttributeValue::where('value', '512GB')->first();
        $s1tb = AttributeValue::where('value', '1TB')->first();
        $s2tb = AttributeValue::where('value', '2TB')->first();

        // Tạo variants + liên kết attribute_values
        $variants = [
            ['sku' => 'MBP14-BLK-512', 'price' => 42990000, 'compare_price' => 49990000, 'stock' => 10, 'attrs' => [$blk->id, $s512->id]],
            ['sku' => 'MBP14-BLK-1TB', 'price' => 52990000, 'compare_price' => 59990000, 'stock' => 8,  'attrs' => [$blk->id, $s1tb->id]],
            ['sku' => 'MBP14-BLK-2TB', 'price' => 62990000, 'compare_price' => 69990000, 'stock' => 5,  'attrs' => [$blk->id, $s2tb->id]],
            ['sku' => 'MBP14-SLV-512', 'price' => 42990000, 'compare_price' => 49990000, 'stock' => 10, 'attrs' => [$slv->id, $s512->id]],
            ['sku' => 'MBP14-SLV-1TB', 'price' => 52990000, 'compare_price' => 59990000, 'stock' => 6,  'attrs' => [$slv->id, $s1tb->id]],
            ['sku' => 'MBP14-SLV-2TB', 'price' => 62990000, 'compare_price' => 69990000, 'stock' => 3,  'attrs' => [$slv->id, $s2tb->id]],
        ];

        foreach ($variants as $v) {
            $variant = ProductVariant::create([
                'product_id'     => $product->id,
                'sku'            => $v['sku'],
                'price'          => $v['price'],
                'compare_price'  => $v['compare_price'],
                'stock_quantity' => $v['stock'],
                'status'         => 1,
            ]);
            foreach ($v['attrs'] as $attrValueId) {
                VariantAttributeValue::create([
                    'variant_id'         => $variant->id,
                    'attribute_value_id' => $attrValueId,
                ]);
            }
        }
    }
}