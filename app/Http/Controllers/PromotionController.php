<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class PromotionController extends Controller
{
    // ─── Trang khuyến mãi (/khuyen-mai) ──────────────────────
    // Chỉ lấy sản phẩm có ÍT NHẤT 1 biến thể đang flash sale THẬT SỰ,
    // điều kiện lọc khớp chính xác với logic trong
    // ProductVariant::getIsSaleActiveAttribute():
    //   - sale_price phải có giá trị
    //   - sale_starts_at null HOẶC đã <= hiện tại
    //   - sale_ends_at   null HOẶC vẫn  >= hiện tại
    //   - status = true (biến thể đang bán, dùng chung scopeActive())
    public function index(Request $request)
    {
        $now = now();

        $saleProducts = Product::visible()
            ->whereHas('variants', function ($q) use ($now) {
                $q->active()
                    ->whereNotNull('sale_price')
                    ->where(function ($q2) use ($now) {
                        $q2->whereNull('sale_starts_at')
                            ->orWhere('sale_starts_at', '<=', $now);
                    })
                    ->where(function ($q3) use ($now) {
                        $q3->whereNull('sale_ends_at')
                            ->orWhere('sale_ends_at', '>=', $now);
                    });
            })
            ->with([
                'category',
                'variants' => fn($q) => $q->active()->orderBy('price'),
            ])
            // Rating trung bình + số lượt đánh giá đã duyệt (visible)
            ->withAvg(['reviews as reviews_avg_rating' => fn($q) => $q->visible()], 'rating')
            ->withCount(['reviews as reviews_count' => fn($q) => $q->visible()])
            ->latest()
            ->get();

        return view('pages.other.promotions', compact('saleProducts'));
    }
}
