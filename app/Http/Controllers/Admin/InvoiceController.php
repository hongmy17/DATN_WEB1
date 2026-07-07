<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Barryvdh\DomPDF\Facade\Pdf;

/**
 * Controller xuất hóa đơn PDF cho admin.
 *
 * Cách hoạt động:
 * 1. Admin bấm nút "In hóa đơn" trên trang quản lý đơn hàng
 * 2. Request đến route /admin/orders/{order}/invoice
 * 3. Controller tải dữ liệu đơn hàng kèm relations
 * 4. Render Blade template → chuyển sang PDF bằng dompdf
 * 5. Trả về file PDF để download hoặc xem trực tiếp
 */
class InvoiceController extends Controller
{
    public function download(Order $order)
    {
        // Load tất cả quan hệ cần thiết cho hóa đơn
        // items: danh sách sản phẩm
        // user:  thông tin khách hàng
        // coupon: mã giảm giá (nếu có)
        $order->loadMissing(['items', 'user', 'coupon']);

        $orderCode = 'NX-' . str_pad($order->id, 6, '0', STR_PAD_LEFT);

        // Render Blade template thành HTML rồi chuyển sang PDF
        // setPaper('a4') = khổ giấy A4
        // setOption: font tiếng Việt cần isHtml5ParserEnabled
        $pdf = Pdf::loadView('pdf.invoice', [
            'order'     => $order,
            'orderCode' => $orderCode,
        ])
        ->setPaper('a4')
        ->setOption('isHtml5ParserEnabled', true)
        ->setOption('isRemoteEnabled', true)
        ->setOption('defaultFont', 'DejaVu Sans');

        // download() = tải về máy
        // stream()   = xem trực tiếp trên trình duyệt
        return $pdf->stream("HoaDon-{$orderCode}.pdf");
    }
}
