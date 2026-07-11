<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\{Product, ProductImage, ProductVariant, AttributeValue, VariantAttributeValue};
use Illuminate\Support\Facades\DB;

/*
|──────────────────────────────────────────────────────────────────────────────
| NEXUS STORE — ProductSeeder
| Web phụ kiện công nghệ, thiên về thương hiệu nổi tiếng dễ tìm ảnh
|
| DANH SÁCH SẢN PHẨM:
|
| 🎧 ÂM THANH — TAI NGHE CHỐNG ỒN (3 SP)
|   1. Sony WH-1000XM5          → 2 màu (Đen/Kem) = 2 biến thể
|   2. Bose QuietComfort 45     → 2 màu (Đen/Trắng) = 2 biến thể
|   3. Jabra Elite 10           → 3 màu (Đen/Kem/Hồng) = 3 biến thể
|
| 🎵 ÂM THANH — TRUE WIRELESS (3 SP)
|   4. Apple AirPods Pro 2      → 1 màu × 3 hộp sạc = 3 biến thể
|   5. Sony WF-1000XM5         → 2 màu (Đen/Bạc) = 2 biến thể
|   6. Samsung Galaxy Buds3 Pro → 2 màu (Đen/Trắng) = 2 biến thể
|
| 🔊 ÂM THANH — LOA DI ĐỘNG (2 SP)
|   7. JBL Flip 6               → 5 màu = 5 biến thể
|   8. Marshall Emberton III    → 2 màu (Đen/Kem) = 2 biến thể
|
| 🖱️ NGOẠI VI — CHUỘT (3 SP)
|   9. Logitech MX Master 3S    → 2 màu × 2 kết nối = 4 biến thể
|  10. Razer DeathAdder V3      → 2 màu × 2 kết nối = 4 biến thể
|  11. Apple Magic Mouse        → 2 màu = 2 biến thể
|
| ⌨️ NGOẠI VI — BÀN PHÍM (2 SP)
|  12. Logitech MX Keys         → 2 màu = 2 biến thể
|  13. Apple Magic Keyboard     → 2 màu × 2 loại = 4 biến thể
|
| 📷 NGOẠI VI — WEBCAM (1 SP)
|  14. Logitech C920 HD Pro     → 1 màu = 1 biến thể
|
| ⚡ SẠC & CÁP — SẠC NHANH (2 SP)
|  15. Anker 65W GaN III        → 2 màu × 2 cổng = 4 biến thể
|  16. Apple 20W USB-C          → 1 màu = 1 biến thể
|
| 🔋 SẠC & CÁP — PIN DỰ PHÒNG (2 SP)
|  17. Anker PowerCore 20000    → 2 màu × 2 dung lượng = 4 biến thể
|  18. Xiaomi Power Bank 3      → 2 màu = 2 biến thể
|
| ⌚ THIẾT BỊ ĐEO — SMARTWATCH (2 SP)
|  19. Apple Watch Series 9     → 2 size × 3 màu = 6 biến thể
|  20. Samsung Galaxy Watch 6   → 2 size × 2 màu = 4 biến thể
|
| Tổng: 20 sản phẩm, 58 biến thể
|──────────────────────────────────────────────────────────────────────────────
*/

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        // ── Lấy ID danh mục ───────────────────────────────────────────────
        $catChongOn  = DB::table('categories')->where('slug', 'tai-nghe-chong-on')->value('id');
        $catTrueWL   = DB::table('categories')->where('slug', 'tai-nghe-true-wireless')->value('id');
        $catLoa      = DB::table('categories')->where('slug', 'loa-di-dong')->value('id');
        $catChuot    = DB::table('categories')->where('slug', 'chuot')->value('id');
        $catBanPhim  = DB::table('categories')->where('slug', 'ban-phim')->value('id');
        $catWebcam   = DB::table('categories')->where('slug', 'webcam-micro')->value('id');
        $catSac      = DB::table('categories')->where('slug', 'sac-nhanh')->value('id');
        $catPin      = DB::table('categories')->where('slug', 'pin-du-phong')->value('id');
        $catWatch    = DB::table('categories')->where('slug', 'smartwatch')->value('id');

        // ── Lấy Attribute Values ──────────────────────────────────────────
        // Màu sắc
        $mDen    = AttributeValue::where('value', 'Đen')->first()->id;
        $mTrg    = AttributeValue::where('value', 'Trắng')->first()->id;
        $mHong   = AttributeValue::where('value', 'Hồng')->first()->id;
        $mNavy   = AttributeValue::where('value', 'Xanh Navy')->first()->id;
        $mXanh   = AttributeValue::where('value', 'Xanh Lá')->first()->id;
        $mKem    = AttributeValue::where('value', 'Kem')->first()->id;
        $mBac    = AttributeValue::where('value', 'Bạc')->first()->id;
        $mDo     = AttributeValue::where('value', 'Đỏ')->first()->id;

        // Kết nối
        $kCoDây  = AttributeValue::where('value', 'Có dây')->first()->id;
        $kWL24   = AttributeValue::where('value', 'Không dây 2.4GHz')->first()->id;
        $kBT50   = AttributeValue::where('value', 'Bluetooth 5.0')->first()->id;
        $kBT53   = AttributeValue::where('value', 'Bluetooth 5.3')->first()->id;

        // Hộp sạc AirPods
        $hLightning = AttributeValue::where('value', 'Lightning')->first()->id;
        $hUSBC      = AttributeValue::where('value', 'USB-C')->first()->id;
        $hMagSafe   = AttributeValue::where('value', 'MagSafe')->first()->id;

        // Dung lượng pin
        $d10k  = AttributeValue::where('value', '10.000mAh')->first()->id;
        $d20k  = AttributeValue::where('value', '20.000mAh')->first()->id;

        // Số cổng sạc
        $p1    = AttributeValue::where('value', '1 cổng')->first()->id;
        $p2    = AttributeValue::where('value', '2 cổng')->first()->id;

        // Kích cỡ smartwatch
        $s41   = AttributeValue::where('value', '41mm')->first()->id;
        $s45   = AttributeValue::where('value', '45mm')->first()->id;

        // ══════════════════════════════════════════════════════════════════
        //  🎧  NHÓM 1: TAI NGHE CHỐNG ỒN
        // ══════════════════════════════════════════════════════════════════

        // 1. Sony WH-1000XM5
        $this->make(
            'PRD-ANC-001', $catChongOn,
            'Sony WH-1000XM5',
            'sony-wh-1000xm5',
            'Tai nghe chống ồn số 1 thế giới, pin 30h, LDAC Hi-Res Audio, kết nối đa điểm 2 thiết bị',
            '<p>Sony WH-1000XM5 với 8 micro và 2 bộ xử lý âm thanh V1 + QN1 mang đến khả năng chống ồn vượt trội nhất từ trước đến nay. LDAC stream âm thanh Hi-Res ở 990kbps — gấp 3 lần Bluetooth thông thường. Pin 30 giờ, sạc nhanh 3 phút dùng được 3 giờ. Hỗ trợ Google Assistant và Alexa tích hợp.</p>',
            'tai-nghe/wh1000xm5-black.jpg',
            [
                ['sony/wh1000xm5-black-front.jpg', 'sony/wh1000xm5-black-side.jpg', 'sony/wh1000xm5-case.jpg']
            ],
            [
                ['WH1K-BLK', 8490000, 9990000, 12, [$mDen]],
                ['WH1K-KEM', 8490000, 9990000, 10, [$mKem]],
            ]
        );

        // 2. Bose QuietComfort 45
        $this->make(
            'PRD-ANC-002', $catChongOn,
            'Bose QuietComfort 45',
            'bose-quietcomfort-45',
            'Tai nghe chống ồn Bose huyền thoại, âm thanh cân bằng hoàn hảo, trọng lượng chỉ 238g',
            '<p>Bose QuietComfort 45 được xếp hạng tai nghe chống ồn thoải mái nhất thế giới nhờ đệm tai siêu mềm và trọng lượng 238g. Chế độ Aware Mode nghe âm thanh môi trường mà không cần tháo tai nghe. Pin 24 giờ với ANC bật.</p>',
            'tai-nghe/bose-qc45-black.jpg',
            [
                ['bose/qc45-black-front.jpg', 'bose/qc45-black-folded.jpg'],
                ['bose/qc45-white-front.jpg', 'bose/qc45-white-side.jpg'],
            ],
            [
                ['QC45-BLK', 7490000, 8990000, 15, [$mDen]],
                ['QC45-WHT', 7490000, 8990000, 12, [$mTrg]],
            ]
        );

        // 3. Jabra Elite 10
        $this->make(
            'PRD-ANC-003', $catChongOn,
            'Jabra Elite 10',
            'jabra-elite-10',
            'Tai nghe over-ear hàng đầu Jabra, chống ồn MultiSensor Voice, âm thanh Dolby Atmos 360°',
            '<p>Jabra Elite 10 với công nghệ chống ồn MultiSensor Voice và màng loa 10mm tùy chỉnh cho âm trầm sâu. Tích hợp Dolby Atmos Spatial Sound và Conversation Detection tự chuyển sang Hear Through khi có người nói chuyện.</p>',
            'tai-nghe/jabra-elite10-black.jpg',
            [
                ['jabra/elite10-black-front.jpg', 'jabra/elite10-black-side.jpg'],
            ],
            [
                ['JABE10-BLK',  5990000, 7490000, 18, [$mDen]],
                ['JABE10-KEM',  5990000, 7490000, 14, [$mKem]],
                ['JABE10-PNK',  5990000, 7490000, 10, [$mHong]],
            ]
        );

        // ══════════════════════════════════════════════════════════════════
        //  🎵  NHÓM 2: TAI NGHE TRUE WIRELESS
        // ══════════════════════════════════════════════════════════════════

        // 4. Apple AirPods Pro 2 (USB-C)
        $this->make(
            'PRD-TWS-001', $catTrueWL,
            'Apple AirPods Pro 2',
            'apple-airpods-pro-2',
            'Tai nghe true wireless cao cấp Apple, chống ồn H2, Adaptive Audio, pin 6h + 24h hộp sạc',
            '<p>AirPods Pro 2 chip H2 mang đến chống ồn mạnh hơn 2x so với thế hệ trước. Adaptive Audio tự thích ứng môi trường xung quanh. Tính năng Conversation Awareness tự giảm nhạc khi bạn bắt đầu nói chuyện. Sạc không dây MagSafe, Apple Watch, Qi2.</p>',
            'tai-nghe/airpods-pro-2-usbc.jpg',
            [['apple/airpods-pro2-usbc-front.jpg', 'apple/airpods-pro2-case-open.jpg', 'apple/airpods-pro2-in-ear.jpg']],
            [
                ['APP2-LIGHT', 6990000, 7990000, 20, [$hLightning]],
                ['APP2-USBC',  6990000, 7990000, 25, [$hUSBC]],
                ['APP2-MAGS',  7490000, 8490000, 15, [$hMagSafe]],
            ]
        );

        // 5. Sony WF-1000XM5
        $this->make(
            'PRD-TWS-002', $catTrueWL,
            'Sony WF-1000XM5',
            'sony-wf-1000xm5',
            'True wireless chống ồn tốt nhất thị trường, chip V2 + QN2e, pin 8h + 24h, LDAC Hi-Res',
            '<p>Sony WF-1000XM5 nhỏ hơn 25% và nhẹ hơn 20% so với XM4 nhưng chống ồn mạnh hơn nhờ chip QN2e. Bộ xử lý V2 tích hợp AI loại bỏ tiếng ồn chính xác hơn. Hỗ trợ LDAC lên đến 990kbps.</p>',
            'tai-nghe/wf1000xm5-black.jpg',
            [['sony/wf1000xm5-black-pods.jpg', 'sony/wf1000xm5-black-case.jpg']],
            [
                ['WF1K-BLK', 5490000, 6490000, 18, [$mDen]],
                ['WF1K-BAC', 5490000, 6490000, 14, [$mBac]],
            ]
        );

        // 6. Samsung Galaxy Buds3 Pro
        $this->make(
            'PRD-TWS-003', $catTrueWL,
            'Samsung Galaxy Buds3 Pro',
            'samsung-galaxy-buds3-pro',
            'True wireless Samsung cao cấp, chống ồn ANC 2.0, âm thanh Hi-Fi 24bit, tích hợp AI',
            '<p>Galaxy Buds3 Pro với thiết kế blade lần đầu xuất hiện trên Buds. ANC 2.0 cải tiến vượt bậc với 3 micro mỗi bên. SSC HiFi 24-bit cho âm thanh studio không dây. Tích hợp Galaxy AI cho phép dịch thời gian thực qua tai nghe.</p>',
            'tai-nghe/buds3pro-black.jpg',
            [['samsung/buds3pro-black-open.jpg', 'samsung/buds3pro-white-case.jpg']],
            [
                ['SBUD3P-BLK', 4990000, 5990000, 20, [$mDen]],
                ['SBUD3P-WHT', 4990000, 5990000, 18, [$mTrg]],
            ]
        );

        // ══════════════════════════════════════════════════════════════════
        //  🔊  NHÓM 3: LOA DI ĐỘNG
        // ══════════════════════════════════════════════════════════════════

        // 7. JBL Flip 6
        $this->make(
            'PRD-SPK-001', $catLoa,
            'JBL Flip 6',
            'jbl-flip-6',
            'Loa Bluetooth di động JBL, chống nước IP67, pin 12h, công suất 20W, kết nối PartyBoost',
            '<p>JBL Flip 6 với 2 driver âm và 1 tweeter cho âm thanh rộng, bass sâu vượt kỳ vọng cho kích thước nhỏ. Chuẩn IP67 chống nước và bụi hoàn toàn — có thể ngâm trong nước 1m trong 30 phút. PartyBoost kết nối hơn 100 loa JBL cùng lúc.</p>',
            'loa/jbl-flip6-black.jpg',
            [
                ['jbl/flip6-black-front.jpg'], ['jbl/flip6-blue-front.jpg'],
                ['jbl/flip6-red-front.jpg'],   ['jbl/flip6-squad-front.jpg'],
                ['jbl/flip6-pink-front.jpg'],
            ],
            [
                ['JBF6-BLK',  1990000, 2490000, 30, [$mDen]],
                ['JBF6-NAVY', 1990000, 2490000, 25, [$mNavy]],
                ['JBF6-DO',   1990000, 2490000, 20, [$mDo]],
                ['JBF6-XANH', 1990000, 2490000, 18, [$mXanh]],
                ['JBF6-HONG', 1990000, 2490000, 15, [$mHong]],
            ]
        );

        // 8. Marshall Emberton III
        $this->make(
            'PRD-SPK-002', $catLoa,
            'Marshall Emberton III',
            'marshall-emberton-iii',
            'Loa Bluetooth Marshall phong cách rock vintage, chống nước IP67, pin 32h, âm thanh 360°',
            '<p>Marshall Emberton III với thiết kế vintage đặc trưng của Marshall và âm thanh 360° signature. Pin khổng lồ 32 giờ — dài nhất trong phân khúc. Chuẩn IP67 chống nước hoàn toàn. Stack Mode ghép 2 loa Emberton III cho âm thanh stereo thật sự.</p>',
            'loa/marshall-emberton3-black.jpg',
            [['marshall/emberton3-black-front.jpg', 'marshall/emberton3-cream-front.jpg']],
            [
                ['MARS3-BLK', 2990000, 3490000, 20, [$mDen]],
                ['MARS3-KEM', 2990000, 3490000, 18, [$mKem]],
            ]
        );

        // ══════════════════════════════════════════════════════════════════
        //  🖱️  NHÓM 4: CHUỘT
        // ══════════════════════════════════════════════════════════════════

        // 9. Logitech MX Master 3S
        $this->make(
            'PRD-MSE-001', $catChuot,
            'Logitech MX Master 3S',
            'logitech-mx-master-3s',
            'Chuột không dây cao cấp, cảm biến 8000 DPI hoạt động trên mọi bề mặt, cuộn MagSpeed siêu êm',
            '<p>Logitech MX Master 3S nâng cấp với cảm biến 8000 DPI hoạt động trên kính, bánh cuộn MagSpeed điện từ không ồn có thể cuộn 1.000 dòng/giây, click im lặng giảm 90% tiếng ồn. Logi Options+ cho phép tùy biến hoàn toàn. Pin 70 ngày.</p>',
            'chuot/mxmaster3s-black.jpg',
            [
                ['logitech/mxmaster3s-black-top.jpg', 'logitech/mxmaster3s-black-side.jpg'],
                ['logitech/mxmaster3s-white-top.jpg', 'logitech/mxmaster3s-white-side.jpg'],
            ],
            [
                ['MX3S-BLK-USB', 1590000, 1890000, 20, [$mDen, $kCoDây]],
                ['MX3S-BLK-WL',  1890000, 2190000, 25, [$mDen, $kWL24]],
                ['MX3S-WHT-USB', 1590000, 1890000, 15, [$mTrg, $kCoDây]],
                ['MX3S-WHT-WL',  1890000, 2190000, 18, [$mTrg, $kWL24]],
            ]
        );

        // 10. Razer DeathAdder V3
        $this->make(
            'PRD-MSE-002', $catChuot,
            'Razer DeathAdder V3',
            'razer-deathadder-v3',
            'Chuột gaming ergonomic nhẹ 59g, cảm biến Focus Pro 30K DPI, switch Razer 90 triệu lần click',
            '<p>Razer DeathAdder V3 với form factor cải tiến ergonomic và trọng lượng chỉ 59g nhờ thiết kế vỏ hốc. Cảm biến Focus Pro 30K là cảm biến gaming chính xác nhất Razer từng sản xuất. Switch Razer Gen-3 90M click bền gấp đôi thế hệ trước.</p>',
            'chuot/deathadder-v3-black.jpg',
            [['razer/dav3-black-top.jpg', 'razer/dav3-black-side.jpg']],
            [
                ['DAV3-BLK-USB', 1690000, 1990000, 25, [$mDen, $kCoDây]],
                ['DAV3-BLK-WL',  2290000, 2690000, 18, [$mDen, $kWL24]],
                ['DAV3-WHT-USB', 1690000, 1990000, 20, [$mTrg, $kCoDây]],
                ['DAV3-WHT-WL',  2290000, 2690000, 14, [$mTrg, $kWL24]],
            ]
        );

        // 11. Apple Magic Mouse
        $this->make(
            'PRD-MSE-003', $catChuot,
            'Apple Magic Mouse',
            'apple-magic-mouse',
            'Chuột Apple thiết kế tối giản, Multi-Touch surface, kết nối Bluetooth 5.0, sạc USB-C',
            '<p>Apple Magic Mouse với bề mặt Multi-Touch cho phép cuộn, swipe và điều hướng bằng cử chỉ tự nhiên. Thiết kế liền khối aluminum recycled 100%. Kết nối Bluetooth 5.0 tự động với mọi thiết bị Apple. Phiên bản 2024 nâng lên cổng USB-C.</p>',
            'chuot/magic-mouse-black.jpg',
            [
                ['apple/magic-mouse-black-top.jpg', 'apple/magic-mouse-black-side.jpg'],
                ['apple/magic-mouse-white-top.jpg'],
            ],
            [
                ['AMGM-BLK', 2190000, 2490000, 20, [$mDen]],
                ['AMGM-WHT', 2190000, 2490000, 22, [$mTrg]],
            ]
        );

        // ══════════════════════════════════════════════════════════════════
        //  ⌨️  NHÓM 5: BÀN PHÍM
        // ══════════════════════════════════════════════════════════════════

        // 12. Logitech MX Keys
        $this->make(
            'PRD-KBD-001', $catBanPhim,
            'Logitech MX Keys',
            'logitech-mx-keys',
            'Bàn phím wireless văn phòng cao cấp, phím cầu Spherically Dished, backlight thích nghi, pin 10 ngày',
            '<p>Logitech MX Keys với phím cầu spherically dished dạng lõm theo ngón tay, giảm mỏi gõ nhiều giờ. Easy-Switch kết nối 3 thiết bị chỉ 1 nút. Backlight tự điều chỉnh theo ánh sáng xung quanh và tự tắt khi ngừng gõ. Pin 10 ngày có đèn, 5 tháng không đèn.</p>',
            'ban-phim/mxkeys-black.jpg',
            [
                ['logitech/mxkeys-black-top.jpg', 'logitech/mxkeys-black-angle.jpg'],
                ['logitech/mxkeys-pale-top.jpg'],
            ],
            [
                ['MXKEYS-BLK', 2290000, 2690000, 22, [$mDen]],
                ['MXKEYS-KEM', 2290000, 2690000, 18, [$mKem]],
            ]
        );

        // 13. Apple Magic Keyboard
        $this->make(
            'PRD-KBD-002', $catBanPhim,
            'Apple Magic Keyboard',
            'apple-magic-keyboard',
            'Bàn phím Apple thiết kế tối giản, Touch ID hoặc Numeric Keypad, kết nối Bluetooth 5.0 + USB-C',
            '<p>Apple Magic Keyboard với hành trình phím thấp êm ái, layout macOS tối ưu và kết nối Bluetooth 5.0 tự động ghép nối với Apple devices. Phiên bản Touch ID tích hợp cảm biến vân tay để đăng nhập và mua hàng. Sạc USB-C, pin dùng 1 tháng.</p>',
            'ban-phim/magic-keyboard-black.jpg',
            [
                ['apple/magic-keyboard-black-touchid.jpg'],
                ['apple/magic-keyboard-white-touchid.jpg'],
                ['apple/magic-keyboard-black-numeric.jpg'],
                ['apple/magic-keyboard-white-numeric.jpg'],
            ],
            [
                ['AMGKB-BLK-TID', 2990000, 3290000, 15, [$mDen]],
                ['AMGKB-WHT-TID', 2990000, 3290000, 18, [$mTrg]],
                ['AMGKB-BLK-NUM', 3490000, 3790000, 12, [$mDen]],
                ['AMGKB-WHT-NUM', 3490000, 3790000, 14, [$mTrg]],
            ]
        );

        // ══════════════════════════════════════════════════════════════════
        //  📷  NHÓM 6: WEBCAM
        // ══════════════════════════════════════════════════════════════════

        // 14. Logitech C920 HD Pro
        $this->make(
            'PRD-CAM-001', $catWebcam,
            'Logitech C920 HD Pro',
            'logitech-c920-hd-pro',
            'Webcam 1080p/30fps tiêu chuẩn vàng cho họp online, lọc âm nền tích hợp, tương thích mọi nền tảng',
            '<p>Logitech C920 HD Pro là webcam được tin dùng nhất thế giới cho work-from-home và streaming. Lens thủy tinh Carl Zeiss cho hình ảnh sắc nét, 2 micro stereo lọc tiếng ồn. Tương thích Zoom, Teams, Meet, OBS. Clip toàn năng gắn được màn hình, laptop, tripod.</p>',
            'webcam/c920-main.jpg',
            [['logitech/c920-front.jpg', 'logitech/c920-clip.jpg', 'logitech/c920-angle.jpg']],
            [
                ['C920-BLK', 1490000, 1890000, 30, [$mDen]],
            ]
        );

        // ══════════════════════════════════════════════════════════════════
        //  ⚡  NHÓM 7: SẠC NHANH
        // ══════════════════════════════════════════════════════════════════

        // 15. Anker 65W GaN III
        $this->make(
            'PRD-CHG-001', $catSac,
            'Anker 65W GaN III',
            'anker-65w-gan-iii',
            'Củ sạc GaN III 65W nhỏ gọn, sạc laptop MacBook + iPhone cùng lúc, PowerIQ 4.0',
            '<p>Anker GaN III 65W nhỏ hơn 50% so với củ sạc Apple 65W. PowerIQ 4.0 thông minh chia công suất cho từng thiết bị. 1 cổng: 65W đủ sạc MacBook Air 14 trong 1,5h. 2 cổng USB-C + USB-A: sạc laptop + điện thoại cùng lúc không chậm.</p>',
            'sac/anker-65w-black.jpg',
            [
                ['anker/65w-black-1port.jpg'], ['anker/65w-black-2port.jpg'],
                ['anker/65w-white-1port.jpg'], ['anker/65w-white-2port.jpg'],
            ],
            [
                ['ANK65-BLK-1P', 590000,  790000, 40, [$mDen, $p1]],
                ['ANK65-BLK-2P', 790000,  990000, 35, [$mDen, $p2]],
                ['ANK65-WHT-1P', 590000,  790000, 38, [$mTrg, $p1]],
                ['ANK65-WHT-2P', 790000,  990000, 30, [$mTrg, $p2]],
            ]
        );

        // 16. Apple 20W USB-C
        $this->make(
            'PRD-CHG-002', $catSac,
            'Apple 20W USB-C Power Adapter',
            'apple-20w-usb-c-power-adapter',
            'Củ sạc nhanh chính hãng Apple 20W, sạc iPhone 15 lên 50% trong 30 phút, cổng USB-C',
            '<p>Củ sạc USB-C 20W chính hãng Apple tương thích iPhone 8 trở lên với sạc nhanh Fast Charge. Đầu ra 9V/2.2A cho iPhone 15 Pro Max lên 50% pin trong 30 phút. Thiết kế gập gọn, chứng nhận an toàn MFi.</p>',
            'sac/apple-20w-white.jpg',
            [['apple/20w-usbc-white-front.jpg', 'apple/20w-usbc-angle.jpg']],
            [
                ['APL20W-WHT', 490000, 590000, 50, [$mTrg]],
            ]
        );

        // ══════════════════════════════════════════════════════════════════
        //  🔋  NHÓM 8: PIN DỰ PHÒNG
        // ══════════════════════════════════════════════════════════════════

        // 17. Anker PowerCore 20000
        $this->make(
            'PRD-PB-001', $catPin,
            'Anker PowerCore 20000',
            'anker-powercore-20000',
            'Pin dự phòng Anker dung lượng lớn, sạc 3 thiết bị cùng lúc, MultiProtect an toàn tuyệt đối',
            '<p>Anker PowerCore 20000 với dung lượng 20.000mAh sạc được iPhone 15 Pro Max hơn 4 lần hoặc MacBook Air M2 1 lần. Đầu vào USB-C và Micro-USB để sạc pin. 2 cổng USB-A + 1 USB-C output sạc 3 thiết bị song song. Công nghệ MultiProtect 12 lớp bảo vệ.</p>',
            'pin/anker-powercore-black.jpg',
            [
                ['anker/powercore20k-black-front.jpg', 'anker/powercore20k-black-ports.jpg'],
                ['anker/powercore20k-white-front.jpg'],
            ],
            [
                ['APC20K-BLK', 690000,  890000, 35, [$mDen, $d20k]],
                ['APC20K-WHT', 690000,  890000, 28, [$mTrg, $d20k]],
                ['APC10K-BLK', 490000,  690000, 40, [$mDen, $d10k]],
                ['APC10K-WHT', 490000,  690000, 32, [$mTrg, $d10k]],
            ]
        );

        // 18. Xiaomi Power Bank 3
        $this->make(
            'PRD-PB-002', $catPin,
            'Xiaomi Power Bank 3 10000mAh',
            'xiaomi-power-bank-3-10000mah',
            'Pin dự phòng Xiaomi siêu mỏng 9.9mm, sạc nhanh 22.5W PD, 2 đầu ra + 1 đầu vào',
            '<p>Xiaomi Power Bank 3 với độ dày chỉ 9.9mm mỏng nhất phân khúc 10.000mAh. Sạc nhanh 22.5W cho Xiaomi và 18W PD cho iPhone/Android khác. Màn hình LED hiển thị % pin chính xác. Vỏ nhôm cao cấp sang trọng không kém thiết bị Apple.</p>',
            'pin/xiaomi-pb3-white.jpg',
            [
                ['xiaomi/pb3-white-front.jpg', 'xiaomi/pb3-black-front.jpg'],
            ],
            [
                ['XPB3-WHT', 390000, 490000, 45, [$mTrg]],
                ['XPB3-BLK', 390000, 490000, 40, [$mDen]],
            ]
        );

        // ══════════════════════════════════════════════════════════════════
        //  ⌚  NHÓM 9: SMARTWATCH
        // ══════════════════════════════════════════════════════════════════

        // 19. Apple Watch Series 9
        $this->make(
            'PRD-WCH-001', $catWatch,
            'Apple Watch Series 9',
            'apple-watch-series-9',
            'Apple Watch thế hệ 9, chip S9 SiP mạnh hơn 60%, Double Tap gesture mới, Always-On Retina 2000 nit',
            '<p>Apple Watch Series 9 với chip S9 SiP mạnh hơn 60% S8, Siri xử lý trên thiết bị không cần internet. Double Tap gesture mới cho phép điều khiển không cần chạm màn hình. Màn hình Always-On Retina sáng gấp 2 lần trong nhà. Carbon neutral, dây sport band recycled.</p>',
            'dong-ho/applewatch9-midnight41.jpg',
            [
                ['apple/watch9-midnight-41.jpg', 'apple/watch9-starlight-41.jpg', 'apple/watch9-pink-41.jpg'],
                ['apple/watch9-midnight-45.jpg', 'apple/watch9-starlight-45.jpg', 'apple/watch9-pink-45.jpg'],
            ],
            [
                ['AW9-41-DEN', 10990000, 12490000, 12, [$s41, $mDen]],
                ['AW9-41-TRG', 10990000, 12490000, 10, [$s41, $mTrg]],
                ['AW9-41-HNG', 10990000, 12490000, 8,  [$s41, $mHong]],
                ['AW9-45-DEN', 11990000, 13490000, 10, [$s45, $mDen]],
                ['AW9-45-TRG', 11990000, 13490000, 9,  [$s45, $mTrg]],
                ['AW9-45-HNG', 11990000, 13490000, 7,  [$s45, $mHong]],
            ]
        );

        // 20. Samsung Galaxy Watch 6
        $this->make(
            'PRD-WCH-002', $catWatch,
            'Samsung Galaxy Watch 6',
            'samsung-galaxy-watch-6',
            'Smartwatch Samsung cao cấp, theo dõi sức khỏe toàn diện, màn hình Sapphire Crystal, Wear OS 4',
            '<p>Samsung Galaxy Watch 6 với màn hình Super AMOLED Sapphire Crystal cứng gấp 2 lần kính thường. Theo dõi huyết áp, ECG, nhịp tim, SpO2 và phân tích giấc ngủ chuyên sâu. Wear OS 4 tích hợp sâu với Android, Samsung DeX. Pin 40h với AOD tắt.</p>',
            'dong-ho/galaxywatch6-graphite40.jpg',
            [
                ['samsung/gw6-graphite-40.jpg', 'samsung/gw6-gold-40.jpg'],
                ['samsung/gw6-graphite-44.jpg', 'samsung/gw6-silver-44.jpg'],
            ],
            [
                ['GW6-40-DEN', 6990000, 8490000, 15, [$s41, $mDen]],
                ['GW6-40-KEM', 6990000, 8490000, 12, [$s41, $mKem]],
                ['GW6-44-DEN', 7490000, 8990000, 13, [$s45, $mDen]],
                ['GW6-44-BAC', 7490000, 8990000, 10, [$s45, $mBac]],
            ]
        );
    }

    // ══════════════════════════════════════════════════════════════════════════
    //  HELPER: Tạo 1 sản phẩm + ảnh + biến thể + liên kết attribute values
    //
    //  @param string   $code         Mã sản phẩm (PRD-xxx)
    //  @param int      $categoryId   ID danh mục con
    //  @param string   $name         Tên sản phẩm
    //  @param string   $slug         Slug URL
    //  @param string   $shortDesc    Mô tả ngắn (hiện trên card sản phẩm)
    //  @param string   $desc         Mô tả dài HTML (hiện trên trang chi tiết)
    //  @param string   $thumbnail    Đường dẫn ảnh thumbnail chính
    //  @param array    $imageGroups  Mảng các nhóm ảnh [['img1.jpg','img2.jpg',...],...]
    //                                Mỗi phần tử là 1 nhóm ảnh cho 1 màu/biến thể
    //  @param array    $variants     Mảng biến thể: [['SKU', giá, giá_gốc, tồn_kho, [attr_value_ids]]]
    // ══════════════════════════════════════════════════════════════════════════
    private function make(
        string $code,
        int    $categoryId,
        string $name,
        string $slug,
        string $shortDesc,
        string $desc,
        string $thumbnail,
        array  $imageGroups,
        array  $variants,
    ): void {
        // Bước 1: Tạo sản phẩm
        $product = Product::create([
            'code'              => $code,
            'category_id'      => $categoryId,
            'name'             => $name,
            'slug'             => $slug,
            'short_description' => $shortDesc,
            'description'      => $desc,
            'thumbnail'        => $thumbnail,
            'status'           => 1,
            'created_by'       => 1,
        ]);

        // Bước 2: Tạo thư viện ảnh (flatten tất cả các nhóm ảnh)
        $sortOrder = 1;
        $isPrimary = true;
        foreach ($imageGroups as $group) {
            foreach ((array) $group as $imgPath) {
                ProductImage::create([
                    'product_id'       => $product->id,
                    'image_url'        => $imgPath,
                    'is_primary'       => $isPrimary ? 1 : 0,
                    'sort_order'       => $sortOrder++,
                ]);
                $isPrimary = false; // chỉ ảnh đầu tiên là primary
            }
        }

        // Bước 3: Tạo từng biến thể + liên kết attribute values
        $isDefault = true;
        foreach ($variants as [$sku, $price, $comparePrice, $stock, $attrValueIds]) {
            $variant = ProductVariant::create([
                'product_id'     => $product->id,
                'sku'            => $sku,
                'price'          => $price,
                'compare_price'  => $comparePrice,
                'stock_quantity' => $stock,
                'status'         => 1,
                'is_default'     => $isDefault ? 1 : 0,
            ]);
            $isDefault = false; // chỉ biến thể đầu là default

            foreach ($attrValueIds as $avId) {
                VariantAttributeValue::create([
                    'variant_id'         => $variant->id,
                    'attribute_value_id' => $avId,
                ]);
            }
        }
    }
}
