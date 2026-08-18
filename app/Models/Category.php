<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{
    // Xóa mềm: products.category_id khai báo onDelete('restrict'), nên xóa cứng
    // một danh mục còn sản phẩm sẽ ném lỗi SQL. Ngoài ra, xóa nhầm một danh mục
    // cha là mất cả nhánh cây danh mục — xóa mềm cho phép khôi phục.
    use SoftDeletes;

    protected $fillable = ['name', 'slug', 'parent_id', 'description', 'sort_order'];

    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    /**
     * Lấy toàn bộ ID của các danh mục CON, CHÁU, CHẮT... (đệ quy không giới hạn cấp).
     * Dùng để: (1) chặn không cho chọn 1 danh mục con làm cha của chính tổ tiên nó
     * (tránh vòng lặp vô hạn cha-con), (2) lọc sản phẩm theo danh mục cha phải gồm
     * luôn sản phẩm của TẤT CẢ hậu duệ, không chỉ con trực tiếp.
     */
    public function descendantIds(): array
    {
        $ids = [];
        $stack = $this->children()->pluck('id')->all();

        while (! empty($stack)) {
            $id = array_pop($stack);
            if (in_array($id, $ids, true)) {
                continue; // đã duyệt qua rồi, tránh lặp vô hạn nếu dữ liệu có vòng lặp lạ
            }
            $ids[] = $id;
            $stack = array_merge($stack, static::where('parent_id', $id)->pluck('id')->all());
        }

        return $ids;
    }

    /** ID của chính nó + toàn bộ hậu duệ — tiện dùng khi lọc sản phẩm theo 1 danh mục cha. */
    public function selfAndDescendantIds(): array
    {
        return array_merge([$this->id], $this->descendantIds());
    }

    /** Số cấp từ gốc tới danh mục này (gốc = 0) — dùng để thụt lề hiển thị trong dropdown/cây danh mục. */
    public function depth(): int
    {
        $depth = 0;
        $node = $this;
        while ($node->parent_id) {
            $depth++;
            $node = $node->parent;
            if (! $node || $depth > 20) {
                break; // chống vòng lặp vô hạn nếu dữ liệu lỗi
            }
        }
        return $depth;
    }

    /**
     * Ánh xạ slug danh mục → SVG path icon.
     * Dùng trên trang chủ để hiển thị icon đúng theo từng loại sản phẩm.
     * Thêm slug mới vào match() khi có danh mục mới.
     */
    public function getIconPathAttribute(): string
    {
        return match (true) {
            str_contains($this->slug, 'laptop')    => 'M2 3h20v14H2zM8 21h8M12 17v4',
            str_contains($this->slug, 'phone')
                || str_contains($this->slug, 'dien-thoai')
            => 'M12 18h.01M8 21h8a1 1 0 0 0 1-1V4a1 1 0 0 0-1-1H8a1 1 0 0 0-1 1v16a1 1 0 0 0 1 1z',
            str_contains($this->slug, 'tablet')
                || str_contains($this->slug, 'bang')
            => 'M18 3H6a1 1 0 0 0-1 1v16a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V4a1 1 0 0 0-1-1zM12 17h.01',
            str_contains($this->slug, 'tai-nghe')
                || str_contains($this->slug, 'audio')
                || str_contains($this->slug, 'headphone')
            => 'M3 18v-6a9 9 0 0 1 18 0v6M21 19a2 2 0 0 1-2 2h-1a2 2 0 0 1-2-2v-3a2 2 0 0 1 2-2h3zM3 19a2 2 0 0 0 2 2h1a2 2 0 0 0 2-2v-3a2 2 0 0 0-2-2H3z',
            str_contains($this->slug, 'watch')
                || str_contains($this->slug, 'dong-ho')
            => 'M12 2a10 10 0 1 0 0 20 10 10 0 0 0 0-20zM12 6v6l4 2',
            str_contains($this->slug, 'phu-kien')
                || str_contains($this->slug, 'accessory')
            => 'M20 7H4a2 2 0 0 0-2 2v10a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2z',
            str_contains($this->slug, 'man-hinh')
                || str_contains($this->slug, 'monitor')
            => 'M2 3h20v13H2zM8 20h8M12 17v3',
            str_contains($this->slug, 'ban-phim')
                || str_contains($this->slug, 'keyboard')
            => 'M2 4h20v16H2z M6 9h.01M10 9h.01M14 9h.01M18 9h.01M6 13h.01M10 13h.01M14 13h.01M18 13h.01',
            str_contains($this->slug, 'chuot')
                || str_contains($this->slug, 'mouse')
            => 'M12 2a7 7 0 0 0-7 7v6a7 7 0 0 0 14 0V9a7 7 0 0 0-7-7zM12 2v7',
            str_contains($this->slug, 'sac-va-cap')
                || str_contains($this->slug, 'sac')
                || str_contains($this->slug, 'cap')     => 'M13 2L3 14h9l-1 8 10-12h-9l1-8z',

            str_contains($this->slug, 'thiet-bi-deo')
                || str_contains($this->slug, 'dong-ho')
                || str_contains($this->slug, 'watch')   => 'M12 2a10 10 0 1 0 0 20 10 10 0 0 0 0-20zM12 6v6l4 2',

            default => 'M20 7H4a2 2 0 0 0-2 2v10a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2z',
        };
    }
}
