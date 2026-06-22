<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\{Product, ProductImage, ProductVariant, AttributeValue, VariantAttributeValue};
use Illuminate\Support\Facades\DB;

/*
|──────────────────────────────────────────────────────────────────────────────
|  GIẢI THÍCH CẤU TRÚC BIẾN THỂ (VARIANT)
|──────────────────────────────────────────────────────────────────────────────
|
|  Mỗi sản phẩm có nhiều BIẾN THỂ.
|  Biến thể = tổ hợp các thuộc tính tạo ra 1 phiên bản riêng của sản phẩm.
|
|  Ví dụ: Chuột Logitech MX Master 3
|    Biến thể 1: Màu Đen  + Có dây     → SKU: MX3-BLK-USB  → giá: 1.290.000đ
|    Biến thể 2: Màu Đen  + Không dây  → SKU: MX3-BLK-WL   → giá: 1.590.000đ
|    Biến thể 3: Màu Trắng + Có dây    → SKU: MX3-WHT-USB  → giá: 1.290.000đ
|    Biến thể 4: Màu Trắng + Không dây → SKU: MX3-WHT-WL   → giá: 1.590.000đ
|
|  Trong DB:
|    product_variants      → lưu giá, tồn kho, SKU của từng biến thể
|    variant_attribute_values → bảng nối biến thể ↔ giá trị thuộc tính
|
|──────────────────────────────────────────────────────────────────────────────
|
|  DANH SÁCH SẢN PHẨM TRONG SEEDER NÀY:
|
|  🖱️  CHUỘT (category: chuot)
|    1. Logitech MX Master 3S    → 2 màu (Đen/Trắng) × 2 kết nối = 4 biến thể
|    2. Razer DeathAdder V3      → 2 màu (Đen/Trắng) × 2 kết nối = 4 biến thể
|    3. Logitech G305            → 3 màu (Đen/Trắng/Xanh) × 2 kết nối = 6 biến thể
|
|  🎧  TAI NGHE (category: tai-nghe)
|    4. Sony WH-1000XM5          → 2 màu (Đen/Trắng) × 2 cổng = 4 biến thể
|    5. Logitech G733            → 3 màu (Đen/Trắng/Hồng) × 1 cổng = 3 biến thể
|    6. HyperX Cloud Alpha       → 2 màu (Đen/Đỏ) × 2 cổng = 4 biến thể
|
|  ⌨️  BÀN PHÍM (category: ban-phim)
|    7. Keychron K2 Pro          → 2 màu × 3 layout = 6 biến thể
|    8. Logitech MX Keys         → 2 màu × 1 kết nối = 2 biến thể
|    9. Akko 3087                → 3 màu × 2 layout = 6 biến thể
|
|  Tổng: 9 sản phẩm, 39 biến thể
|──────────────────────────────────────────────────────────────────────────────
*/

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        // ── Lấy ID danh mục ───────────────────────────────────────────────
        $catChuot  = DB::table('categories')->where('slug', 'chuot')->value('id');
        $catTaiNghe = DB::table('categories')->where('slug', 'tai-nghe')->value('id');
        $catBanPhim = DB::table('categories')->where('slug', 'ban-phim')->value('id');

        // ── Lấy giá trị thuộc tính đã tạo trong AttributeSeeder ──────────
        // Màu sắc
        $mDen  = AttributeValue::where('value', 'Đen')->first()->id;
        $mTrg  = AttributeValue::where('value', 'Trắng')->first()->id;
        $mHong = AttributeValue::where('value', 'Hồng')->first()->id;
        $mXanh = AttributeValue::where('value', 'Xanh dương')->first()->id;

        // Kết nối (chuột & bàn phím)
        $kCoDây    = AttributeValue::where('value', 'Có dây (USB-A)')->first()->id;
        $kKhongDay = AttributeValue::where('value', 'Không dây (2.4GHz)')->first()->id;
        $kBT       = AttributeValue::where('value', 'Bluetooth 5.0')->first()->id;

        // Layout bàn phím
        $lFull    = AttributeValue::where('value', 'Full-size (100%)')->first()->id;
        $lTKL     = AttributeValue::where('value', 'TKL (80%)')->first()->id;
        $lCompact = AttributeValue::where('value', 'Compact (65%)')->first()->id;

        // Cổng kết nối tai nghe
        $p35mm = AttributeValue::where('value', '3.5mm Jack')->first()->id;
        $pUSBA = AttributeValue::where('value', 'USB-A')->first()->id;
        $pUSBC = AttributeValue::where('value', 'USB-C')->first()->id;
        $pWL   = AttributeValue::where('value', 'Wireless 2.4GHz')->first()->id;


        // ══════════════════════════════════════════════════════════════════
        //  🖱️  NHÓM 1: CHUỘT
        // ══════════════════════════════════════════════════════════════════

        // ── Sản phẩm 1: Logitech MX Master 3S ────────────────────────────
        // Biến thể: 2 màu (Đen/Trắng) × 2 kết nối (Có dây/Không dây) = 4
        $this->createProduct(
            code: 'PRD-CHUOT-001',
            categoryId: $catChuot,
            name: 'Chuột Logitech MX Master 3S',
            slug: 'chuot-logitech-mx-master-3s',
            shortDesc: 'Chuột không dây cao cấp, cảm biến 8000 DPI, cuộn MagSpeed siêu êm, pin 70 ngày',
            desc: '<p>Logitech MX Master 3S là chuột không dây hàng đầu dành cho dân văn phòng và lập trình viên. Cảm biến quang học 8000 DPI cho phép sử dụng trên mọi bề mặt kể cả kính. Bánh cuộn MagSpeed điện từ có thể cuộn 1000 dòng/giây hoàn toàn không ồn.</p>',
            images: ['chuot/mx-master-3s-main.jpg', 'chuot/mx-master-3s-side.jpg', 'chuot/mx-master-3s-bottom.jpg'],
            variants: [
                // [SKU, giá, giá_gốc, tồn_kho, [màu_id, kết_nối_id, ...]]
                ['MXM3S-BLK-USB', 1290000, 1490000, 15, [$mDen,  $kCoDây]],
                ['MXM3S-BLK-WL',  1590000, 1790000, 20, [$mDen,  $kKhongDay]],
                ['MXM3S-WHT-USB', 1290000, 1490000, 12, [$mTrg,  $kCoDây]],
                ['MXM3S-WHT-WL',  1590000, 1790000, 18, [$mTrg,  $kKhongDay]],
            ]
        );

        // ── Sản phẩm 2: Razer DeathAdder V3 ──────────────────────────────
        // Biến thể: 2 màu × 2 kết nối = 4
        $this->createProduct(
            code: 'PRD-CHUOT-002',
            categoryId: $catChuot,
            name: 'Chuột Gaming Razer DeathAdder V3',
            slug: 'chuot-gaming-razer-deathadder-v3',
            shortDesc: 'Chuột gaming 30.000 DPI, nhẹ chỉ 59g, switch Razer 90M clicks, kiểu dáng ergonomic',
            desc: '<p>Razer DeathAdder V3 là chuột gaming ergonomic với trọng lượng chỉ 59g. Cảm biến Focus Pro 30K với độ chính xác 99.8% không bỏ sót bất kỳ thao tác nào. Switch Razer Gen-3 bền 90 triệu lần click.</p>',
            images: ['chuot/deathadder-v3-main.jpg', 'chuot/deathadder-v3-side.jpg'],
            variants: [
                ['DAV3-BLK-USB', 1690000, 1990000, 25, [$mDen, $kCoDây]],
                ['DAV3-BLK-WL',  2190000, 2490000, 15, [$mDen, $kKhongDay]],
                ['DAV3-WHT-USB', 1690000, 1990000, 20, [$mTrg, $kCoDây]],
                ['DAV3-WHT-WL',  2190000, 2490000, 10, [$mTrg, $kKhongDay]],
            ]
        );

        // ── Sản phẩm 3: Logitech G305 ────────────────────────────────────
        // Biến thể: 3 màu × 2 kết nối = 6
        $this->createProduct(
            code: 'PRD-CHUOT-003',
            categoryId: $catChuot,
            name: 'Chuột Gaming Logitech G305',
            slug: 'chuot-gaming-logitech-g305',
            shortDesc: 'Chuột gaming không dây giá tốt, cảm biến HERO 12K, pin AA 250 giờ',
            desc: '<p>Logitech G305 sở hữu cảm biến HERO 12K DPI với hiệu quả sử dụng pin lên đến 250 giờ từ 1 viên AA. Đây là lựa chọn hoàn hảo cho game thủ muốn trải nghiệm gaming không dây mà không tốn nhiều chi phí.</p>',
            images: ['chuot/g305-main.jpg', 'chuot/g305-top.jpg', 'chuot/g305-side.jpg'],
            variants: [
                ['G305-BLK-USB', 790000,  990000, 30, [$mDen,  $kCoDây]],
                ['G305-BLK-WL',  990000, 1190000, 25, [$mDen,  $kKhongDay]],
                ['G305-WHT-USB', 790000,  990000, 28, [$mTrg,  $kCoDây]],
                ['G305-WHT-WL',  990000, 1190000, 20, [$mTrg,  $kKhongDay]],
                ['G305-BLU-USB', 790000,  990000, 15, [$mXanh, $kCoDây]],
                ['G305-BLU-WL',  990000, 1190000, 12, [$mXanh, $kKhongDay]],
            ]
        );


        // ══════════════════════════════════════════════════════════════════
        //  🎧  NHÓM 2: TAI NGHE
        // ══════════════════════════════════════════════════════════════════

        // ── Sản phẩm 4: Sony WH-1000XM5 ─────────────────────────────────
        // Biến thể: 2 màu × 2 cổng (USB-C/3.5mm) = 4
        $this->createProduct(
            code: 'PRD-TAINGHE-001',
            categoryId: $catTaiNghe,
            name: 'Tai Nghe Sony WH-1000XM5',
            slug: 'tai-nghe-sony-wh-1000xm5',
            shortDesc: 'Tai nghe chống ồn hàng đầu thế giới, pin 30h, kết nối đa điểm, LDAC Hi-Res Audio',
            desc: '<p>Sony WH-1000XM5 với 8 micro và 2 bộ xử lý âm thanh mang đến khả năng chống ồn vượt trội nhất từ trước đến nay. Pin 30 giờ, sạc nhanh 3 phút dùng được 3 giờ. LDAC cho phép stream âm thanh chất lượng 3x Bluetooth thông thường.</p>',
            images: ['tai-nghe/wh1000xm5-main.jpg', 'tai-nghe/wh1000xm5-fold.jpg', 'tai-nghe/wh1000xm5-case.jpg'],
            variants: [
                ['WH1K-BLK-USBC', 8490000, 9990000, 10, [$mDen, $pUSBC]],
                ['WH1K-BLK-35MM', 8490000, 9990000, 8,  [$mDen, $p35mm]],
                ['WH1K-WHT-USBC', 8490000, 9990000, 9,  [$mTrg, $pUSBC]],
                ['WH1K-WHT-35MM', 8490000, 9990000, 7,  [$mTrg, $p35mm]],
            ]
        );

        // ── Sản phẩm 5: Logitech G733 ────────────────────────────────────
        // Biến thể: 3 màu × 1 kết nối (Wireless) = 3
        $this->createProduct(
            code: 'PRD-TAINGHE-002',
            categoryId: $catTaiNghe,
            name: 'Tai Nghe Gaming Logitech G733',
            slug: 'tai-nghe-gaming-logitech-g733',
            shortDesc: 'Tai nghe gaming không dây lightspeed, âm thanh DTS 7.1 ảo, mic lọc tiếng ồn Blue VO!CE',
            desc: '<p>Logitech G733 với công nghệ LIGHTSPEED không dây cho độ trễ cực thấp, không thể phân biệt với có dây. Micro Blue VO!CE lọc tạp âm hoàn hảo trong môi trường ồn ào. Pin 29 giờ liên tục.</p>',
            images: ['tai-nghe/g733-main.jpg', 'tai-nghe/g733-mic.jpg'],
            variants: [
                ['G733-BLK-WL',  2490000, 2990000, 20, [$mDen,  $pWL]],
                ['G733-WHT-WL',  2490000, 2990000, 18, [$mTrg,  $pWL]],
                ['G733-PNK-WL',  2490000, 2990000, 15, [$mHong, $pWL]],
            ]
        );

        // ── Sản phẩm 6: HyperX Cloud Alpha ───────────────────────────────
        // Biến thể: 2 màu × 2 cổng (USB-A/3.5mm) = 4
        $this->createProduct(
            code: 'PRD-TAINGHE-003',
            categoryId: $catTaiNghe,
            name: 'Tai Nghe Gaming HyperX Cloud Alpha',
            slug: 'tai-nghe-gaming-hyperx-cloud-alpha',
            shortDesc: 'Tai nghe gaming 2 buồng loa độc lập, âm thanh Hi-Fi 13-27.000Hz, mic detachable',
            desc: '<p>HyperX Cloud Alpha với thiết kế 2 buồng loa độc lập trong cùng một driver cho phép tách biệt âm bass và treble, mang lại âm thanh Hi-Fi sắc nét hơn hẳn tai nghe thông thường. Micro tháo rời tiện lợi.</p>',
            images: ['tai-nghe/cloud-alpha-main.jpg', 'tai-nghe/cloud-alpha-mic.jpg', 'tai-nghe/cloud-alpha-bag.jpg'],
            variants: [
                ['HXCA-BLK-USBA', 1590000, 1890000, 22, [$mDen, $pUSBA]],
                ['HXCA-BLK-35MM', 1490000, 1790000, 25, [$mDen, $p35mm]],
                ['HXCA-WHT-USBA', 1590000, 1890000, 18, [$mTrg, $pUSBA]],
                ['HXCA-WHT-35MM', 1490000, 1790000, 20, [$mTrg, $p35mm]],
            ]
        );


        // ══════════════════════════════════════════════════════════════════
        //  ⌨️  NHÓM 3: BÀN PHÍM
        // ══════════════════════════════════════════════════════════════════

        // ── Sản phẩm 7: Keychron K2 Pro ──────────────────────────────────
        // Biến thể: 2 màu × 3 layout = 6
        $this->createProduct(
            code: 'PRD-BANPHIM-001',
            categoryId: $catBanPhim,
            name: 'Bàn Phím Cơ Keychron K2 Pro',
            slug: 'ban-phim-co-keychron-k2-pro',
            shortDesc: 'Bàn phím cơ QMK/VIA, Bluetooth 5.1 đa điểm 3 thiết bị, switch Gateron Pro hot-swap',
            desc: '<p>Keychron K2 Pro là bàn phím cơ full aluminum với firmware QMK/VIA cho phép lập trình phím hoàn toàn. Hot-swap switch không cần hàn, hỗ trợ đồng thời Bluetooth 5.1 (3 thiết bị) và USB-C có dây.</p>',
            images: ['ban-phim/k2pro-main.jpg', 'ban-phim/k2pro-side.jpg', 'ban-phim/k2pro-switch.jpg'],
            variants: [
                ['K2P-BLK-FULL', 1990000, 2390000, 15, [$mDen, $lFull]],
                ['K2P-BLK-TKL',  1890000, 2290000, 18, [$mDen, $lTKL]],
                ['K2P-BLK-65',   1790000, 2190000, 20, [$mDen, $lCompact]],
                ['K2P-WHT-FULL', 1990000, 2390000, 12, [$mTrg, $lFull]],
                ['K2P-WHT-TKL',  1890000, 2290000, 15, [$mTrg, $lTKL]],
                ['K2P-WHT-65',   1790000, 2190000, 17, [$mTrg, $lCompact]],
            ]
        );

        // ── Sản phẩm 8: Logitech MX Keys ─────────────────────────────────
        // Biến thể: 2 màu × 1 kết nối (Bluetooth) = 2
        $this->createProduct(
            code: 'PRD-BANPHIM-002',
            categoryId: $catBanPhim,
            name: 'Bàn Phím Logitech MX Keys',
            slug: 'ban-phim-logitech-mx-keys',
            shortDesc: 'Bàn phím wireless dành cho dân văn phòng, phím cầu, backlight thích nghi, pin 10 ngày',
            desc: '<p>Logitech MX Keys với thiết kế phím cầu spherically dished giúp ngón tay định vị chính xác, giảm mỏi khi gõ nhiều giờ. Easy-Switch kết nối tới 3 thiết bị chỉ với 1 nút bấm. Backlight tự điều chỉnh theo ánh sáng môi trường.</p>',
            images: ['ban-phim/mxkeys-main.jpg', 'ban-phim/mxkeys-angle.jpg'],
            variants: [
                ['MXKEYS-BLK-BT', 2290000, 2690000, 20, [$mDen, $kBT]],
                ['MXKEYS-WHT-BT', 2290000, 2690000, 15, [$mTrg, $kBT]],
            ]
        );

        // ── Sản phẩm 9: Akko 3087 ────────────────────────────────────────
        // Biến thể: 3 màu × 2 layout = 6
        $this->createProduct(
            code: 'PRD-BANPHIM-003',
            categoryId: $catBanPhim,
            name: 'Bàn Phím Cơ Akko 3087',
            slug: 'ban-phim-co-akko-3087',
            shortDesc: 'Bàn phím cơ giá tốt, thiết kế retro, switch Akko CS, kháng nước IP54',
            desc: '<p>Akko 3087 với thiết kế retro độc đáo và switch Akko CS Jelly Pink/Blue chuyên dụng cho người thích phím nhẹ. Kháng nước IP54, keycap PBT double-shot bền màu theo thời gian.</p>',
            images: ['ban-phim/akko3087-main.jpg', 'ban-phim/akko3087-top.jpg', 'ban-phim/akko3087-keycap.jpg'],
            variants: [
                ['AK3087-BLK-FULL', 990000, 1290000, 30, [$mDen,  $lFull]],
                ['AK3087-BLK-TKL',  890000, 1190000, 35, [$mDen,  $lTKL]],
                ['AK3087-WHT-FULL', 990000, 1290000, 25, [$mTrg,  $lFull]],
                ['AK3087-WHT-TKL',  890000, 1190000, 30, [$mTrg,  $lTKL]],
                ['AK3087-PNK-FULL', 990000, 1290000, 20, [$mHong, $lFull]],
                ['AK3087-PNK-TKL',  890000, 1190000, 22, [$mHong, $lTKL]],
            ]
        );
    }

    // ══════════════════════════════════════════════════════════════════════
    //  HELPER: tạo 1 sản phẩm + ảnh + biến thể + liên kết thuộc tính
    //
    //  Tại sao tách ra helper?
    //  → Lặp đi lặp lại code tạo Product → ProductImage → ProductVariant
    //    → VariantAttributeValue cho 9 sản phẩm là rất dài và dễ lỗi
    //  → Gom vào hàm này, mỗi sản phẩm chỉ cần gọi 1 lần
    // ══════════════════════════════════════════════════════════════════════
    private function createProduct(
        string $code,
        int $categoryId,
        string $name,
        string $slug,
        string $shortDesc,
        string $desc,
        array $images,     // ['path/anh1.jpg', 'path/anh2.jpg']
        array $variants,   // [['SKU', gia, gia_goc, ton_kho, [attr_ids]], ...]
    ): void {
        // Bước 1: Tạo sản phẩm chính
        $product = Product::create([
            'code'              => $code,
            'category_id'      => $categoryId,
            'name'             => $name,
            'slug'             => $slug,
            'short_description' => $shortDesc,
            'description'      => $desc,
            'thumbnail'        => $images[0] ?? null, // ảnh đầu tiên làm thumbnail
            'status'           => 1,
            'created_by'       => 1, // admin
        ]);

        // Bước 2: Tạo thư viện ảnh
        foreach ($images as $index => $imgPath) {
            ProductImage::create([
                'product_id' => $product->id,
                'image_url'  => $imgPath,
                'is_primary' => $index === 0 ? 1 : 0, // ảnh đầu tiên là primary
                'sort_order' => $index + 1,
            ]);
        }

        // Bước 3: Tạo từng biến thể
        foreach ($variants as [$sku, $price, $comparePrice, $stock, $attrValueIds]) {
            $variant = ProductVariant::create([
                'product_id'     => $product->id,
                'sku'            => $sku,
                'price'          => $price,
                'compare_price'  => $comparePrice,
                'stock_quantity' => $stock,
                'status'         => 1,
            ]);

            // Bước 4: Liên kết biến thể với các giá trị thuộc tính
            // Ví dụ: variant MXM3S-BLK-USB liên kết với:
            //   - AttributeValue "Đen" (màu sắc)
            //   - AttributeValue "Có dây (USB-A)" (kết nối)
            foreach ($attrValueIds as $attrValueId) {
                VariantAttributeValue::create([
                    'variant_id'         => $variant->id,
                    'attribute_value_id' => $attrValueId,
                ]);
            }
        }
    }
}
