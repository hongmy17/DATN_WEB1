<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * XÓA MỀM (Soft Delete) — thêm cột deleted_at cho các bảng cần giữ lịch sử.
 *
 * XÓA MỀM LÀ GÌ?
 * Thay vì chạy `DELETE FROM products WHERE id = 5` (mất vĩnh viễn), Laravel
 * chạy `UPDATE products SET deleted_at = NOW() WHERE id = 5`. Bản ghi vẫn nằm
 * nguyên trong database, chỉ bị "đánh dấu là đã xóa". Mọi câu truy vấn sau đó
 * tự động thêm điều kiện `WHERE deleted_at IS NULL` nên bản ghi biến mất khỏi
 * giao diện — nhưng có thể khôi phục bất cứ lúc nào.
 *
 * VÌ SAO CẦN CHO DỰ ÁN NÀY?
 *   1. An toàn dữ liệu — admin lỡ tay xóa sản phẩm có thể khôi phục.
 *   2. Giữ toàn vẹn khóa ngoại — orders.user_id khai báo onDelete('restrict'),
 *      nên xóa cứng một khách đã từng mua hàng sẽ báo lỗi SQL. Xóa mềm né được.
 *   3. Giữ lịch sử — reviews có cascadeOnDelete từ users: xóa cứng một khách
 *      là mất luôn toàn bộ đánh giá của họ trên các sản phẩm.
 *
 * BẢNG NÀO KHÔNG XÓA MỀM (cố ý bỏ qua — mỗi bảng một lý do khác nhau):
 *
 *   - orders, order_items, payments
 *     Chứng từ tài chính, không bao giờ xóa kể cả xóa mềm. Đơn sai thì đổi
 *     trạng thái sang "Đã hủy" để còn dấu vết đối soát doanh thu.
 *
 *   - coupons
 *     Bảng này đã có cột `status` (Kích hoạt / Vô hiệu) làm đúng việc mà xóa
 *     mềm định làm. Thêm nữa: orders.coupon_id khai báo onDelete('set null')
 *     nên xóa cứng KHÔNG gây lỗi SQL, và orders.coupon_code đã lưu snapshot
 *     chuỗi mã ngay trên đơn hàng nên lịch sử khuyến mãi vẫn còn nguyên.
 *     Ngược lại, xóa mềm còn gây hại: coupons.coupon_code là UNIQUE, nên một
 *     mã nằm trong thùng rác sẽ chặn admin tạo lại mã cùng tên.
 */
return new class extends Migration
{
  public function up(): void
  {
    /*
        | products — CHUẨN HÓA TÊN CỘT
        |
        | Bảng này đã có cột xóa mềm từ đầu nhưng đặt tên là `delete_at`
        | (thiếu chữ "d"), nên Model Product phải khai báo thêm
        | `const DELETED_AT = 'delete_at'` để Laravel hiểu.
        |
        | Cách đó chạy được, nhưng gây hai vấn đề:
        |   - Mọi lập trình viên đọc code sau này đều phải nhớ ngoại lệ này.
        |   - Một số gói mở rộng (kể cả một vài tính năng của Filament) mặc
        |     định tìm cột `deleted_at` và sẽ không hoạt động đúng.
        | Đổi về tên chuẩn là cách sửa dứt điểm.
        */
    if (Schema::hasColumn('products', 'delete_at') && ! Schema::hasColumn('products', 'deleted_at')) {
      Schema::table('products', function (Blueprint $table) {
        $table->renameColumn('delete_at', 'deleted_at');
      });
    } elseif (! Schema::hasColumn('products', 'deleted_at')) {
      Schema::table('products', function (Blueprint $table) {
        $table->softDeletes();
      });
    }

    // categories — xóa mềm để có thể khôi phục cây danh mục
    if (! Schema::hasColumn('categories', 'deleted_at')) {
      Schema::table('categories', function (Blueprint $table) {
        $table->softDeletes();
      });
    }

    // users — orders.user_id có onDelete('restrict'), xóa cứng khách đã
    // từng mua hàng sẽ ném lỗi SQL
    if (! Schema::hasColumn('users', 'deleted_at')) {
      Schema::table('users', function (Blueprint $table) {
        $table->softDeletes();
      });
    }

    // reviews — admin gỡ đánh giá vi phạm nhưng vẫn giữ được bằng chứng
    if (! Schema::hasColumn('reviews', 'deleted_at')) {
      Schema::table('reviews', function (Blueprint $table) {
        $table->softDeletes();
      });
    }

    // product_variants — order_items.variant_id trỏ tới đây. Xóa cứng một
    // biến thể đã từng được đặt mua sẽ phá vỡ liên kết của đơn hàng cũ
    // (và làm hỏng chức năng hoàn kho khi hủy đơn).
    if (! Schema::hasColumn('product_variants', 'deleted_at')) {
      Schema::table('product_variants', function (Blueprint $table) {
        $table->softDeletes();
      });
    }
  }

  public function down(): void
  {
    foreach (['categories', 'users', 'reviews', 'product_variants'] as $tableName) {
      if (Schema::hasColumn($tableName, 'deleted_at')) {
        Schema::table($tableName, function (Blueprint $table) {
          $table->dropSoftDeletes();
        });
      }
    }

    if (Schema::hasColumn('products', 'deleted_at')) {
      Schema::table('products', function (Blueprint $table) {
        $table->renameColumn('deleted_at', 'delete_at');
      });
    }
  }
};
