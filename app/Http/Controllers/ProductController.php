<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    // ─── Trang danh sách sản phẩm ────────────────────────────
    public function index(Request $request)
    {
        // Chặn trên thanh trượt giá lấy từ giá cao nhất thật đang có trong DB
        // (chỉ tính biến thể đang bán — status=1), không hard-code.
        $maxPriceInDb = (int) ProductVariant::where('status', true)->max('price');
        $sliderMax    = $maxPriceInDb > 0
            ? (int) (ceil($maxPriceInDb / 1000000) * 1000000)
            : 100000000;

        $priceMin = $request->filled('price_min') ? max(0, (int) $request->price_min) : 0;
        $priceMax = $request->filled('price_max') ? (int) $request->price_max : $sliderMax;

        // Chống trường hợp URL bị chỉnh tay khiến min > max
        if ($priceMin > $priceMax) {
            [$priceMin, $priceMax] = [$priceMax, $priceMin];
        }

        // Hỗ trợ cả 'cat' (chip, chọn 1 danh mục) và 'categories[]' (checkbox sidebar, chọn nhiều)
        $selectedCategories = $request->filled('categories')
            ? collect((array) $request->categories)->map(fn ($id) => (int) $id)->filter()->values()->all()
            : ($request->filled('cat') ? [(int) $request->cat] : []);

        $query = Product::visible()
            ->with(['category'])
            // FIX: chỉ eager-load + tính min/max theo biến thể ĐANG BÁN (status=1).
            // Trước đây lấy cả variant đã ẩn → giá min/max hiển thị có thể sai.
            ->with(['variants' => fn ($q) => $q->active()->orderBy('price')])
            ->withMin(['variants as variants_min_price' => fn ($q) => $q->active()], 'price')
            ->withMax(['variants as variants_max_price' => fn ($q) => $q->active()], 'price')
            // Chỉ hiện sản phẩm có ít nhất 1 biến thể đang bán — tránh thẻ sản phẩm "0đ"
            ->whereHas('variants', fn ($q) => $q->active());

        // Lọc theo danh mục
        if (! empty($selectedCategories)) {
            $query->whereIn('category_id', $selectedCategories);
        }

        // Lọc theo từ khóa tìm kiếm
        // Escape % và _ (ký tự đặc biệt của LIKE) — nếu không, khách gõ đúng dấu %
        // sẽ vô tình khớp gần như mọi sản phẩm thay vì tìm chữ "%" theo nghĩa đen.
        if ($request->filled('q')) {
            $keyword = addcslashes(trim($request->q), '%_');
            $query->where('name', 'like', '%' . $keyword . '%');
        }

        // Lọc theo khoảng giá: sản phẩm có ít nhất 1 biến thể đang bán rơi vào khoảng giá đã chọn
        $query->whereHas('variants', function ($q) use ($priceMin, $priceMax) {
            $q->active()->whereBetween('price', [$priceMin, $priceMax]);
        });

        // Sắp xếp
        match ($request->sort) {
            'price_asc'  => $query->orderBy('variants_min_price', 'asc'),
            'price_desc' => $query->orderByDesc('variants_max_price'),
            'newest'     => $query->orderByDesc('created_at'),
            default      => $query->orderByDesc('created_at'),
        };

        // withQueryString() để link phân trang tự giữ nguyên cat/categories/price_min/price_max/sort/q
        $products = $query->paginate(9)->withQueryString();

        // Danh mục con để hiển thị chips + checkbox sidebar
        $categories = Category::whereNotNull('parent_id')
            ->withCount(['products' => fn ($q) => $q->visible()])
            ->orderBy('name')
            ->get();

        return view('pages.product.index', compact(
            'products',
            'categories',
            'priceMin',
            'priceMax',
            'sliderMax',
            'selectedCategories',
        ));
    }

    // ─── API gợi ý tìm kiếm trực tiếp (autocomplete) ──────────
    // Trả JSON nhẹ (tối đa 6 sản phẩm) để hiện dropdown ngay dưới ô search
    // trong lúc gõ, không cần bấm Enter. Gọi từ navbar (app.blade.php).
    public function suggest(Request $request)
    {
        $keyword = trim((string) $request->get('q', ''));

        if ($keyword === '') {
            return response()->json([]);
        }

        // Escape ký tự đặc biệt của LIKE giống hệt logic trong index()
        $safeKeyword = addcslashes($keyword, '%_');

        $products = Product::visible()
            ->where('name', 'like', '%' . $safeKeyword . '%')
            ->whereHas('variants', fn ($q) => $q->active())
            ->withMin(['variants as variants_min_price' => fn ($q) => $q->active()], 'price')
            ->orderByDesc('created_at')
            ->limit(6)
            ->get()
            ->map(fn ($p) => [
                'name'      => $p->name,
                'price'     => (float) $p->variants_min_price,
                'thumbnail' => $p->thumbnail ? asset('storage/' . $p->thumbnail) : null,
                'url'       => route('products.show', $p->slug),
            ]);

        return response()->json($products);
    }

    // ─── Trang chi tiết sản phẩm ─────────────────────────────
    public function show(string $slug)
    {
        $product = Product::visible()
            ->where('slug', $slug)
            ->with([
                'category',
                'images',
                'customAttributes.values',
                'variants' => fn($q) => $q->where('status', 1)
                                          ->with('attributeValues.attribute'),
            ])
            ->firstOrFail();

        // Sản phẩm liên quan cùng danh mục
        $relatedProducts = Product::visible()
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->with(['variants'])
            ->withMin('variants', 'price')
            ->withMax('variants', 'price')
            ->limit(4)
            ->get();

        return view('pages.product.show', compact('product', 'relatedProducts'));
    }
}
