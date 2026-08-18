<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'order_id',
        'variant_id',
        'product_name',
        'variant_description',
        'variant_sku',
        'product_thumbnail',
        'quantity',
        'unit_price',
        'compare_price',
        'total_price',
    ];

    protected $casts = [
        'quantity'      => 'integer',
        'unit_price'    => 'float',
        'compare_price' => 'float',
        'total_price'   => 'float',
    ];

    /*
    |--------------------------------------------------------------------------
    | GHI CHÚ: hook trừ/hoàn tồn kho đã được GỠ BỎ khỏi model này
    |--------------------------------------------------------------------------
    | Trước đây ở đây có hai hook:
    |   - creating() → trừ kho ngay khi dòng chi tiết đơn được tạo
    |   - deleting() → hoàn kho khi dòng chi tiết đơn bị xóa
    |
    | Cách làm đó gắn tồn kho vào việc "dòng dữ liệu được tạo hay xóa", trong
    | khi về mặt nghiệp vụ, tồn kho phải gắn vào TRẠNG THÁI ĐƠN HÀNG: hàng chỉ
    | thực sự rời kho khi shop xác nhận sẽ bán, và chỉ quay lại kho khi đơn bị
    | hủy hoặc khách trả hàng.
    |
    | Toàn bộ logic tồn kho nay nằm tập trung ở MỘT nơi duy nhất:
    |     app/Models/Order.php  →  static::updating() trong booted()
    |
    | Việc kiểm tra "còn đủ hàng không" lúc khách đặt vẫn được giữ nguyên ở
    | app/Http/Controllers/CheckoutController.php (Bước 1) — chỉ kiểm tra, không
    | trừ kho.
    */

    // ─── Relationships ──────────────────────────────────────────────────────

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function variant()
    {
        // withTrashed(): biến thể có thể đã bị xóa mềm sau khi đơn được đặt.
        // Không có nó, chi tiết đơn hàng cũ sẽ hiện thiếu thông tin biến thể.
        // (Các trường product_name / variant_description / unit_price đã được
        // lưu snapshot ngay trong bảng order_items nên vẫn hiển thị đúng giá
        // và tên tại thời điểm mua — relation này chỉ dùng khi cần dữ liệu
        // sống của biến thể.)
        return $this->belongsTo(ProductVariant::class, 'variant_id')->withTrashed();
    }

    public function review()
    {
        return $this->hasOne(Review::class);
    }

    /** Thành tiền của dòng chi tiết đơn. */
    public function subTotal(): float
    {
        return $this->quantity * $this->unit_price;
    }
}
