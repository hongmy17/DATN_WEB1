<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Review;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    // ─── Trang danh sách sản phẩm ────────────────────────────
    public function index(Request $request)
    {
        $maxPriceInDb = (int) ProductVariant::where('status', true)->max('price');
        $sliderMax    = $maxPriceInDb > 0
            ? (int) (ceil($maxPriceInDb / 1000000) * 1000000)
            : 100000000;

        $priceMin = $request->filled('price_min') ? max(0, (int) $request->price_min) : 0;
        $priceMax = $request->filled('price_max') ? (int) $request->price_max : $sliderMax;

        if ($priceMin > $priceMax) {
            [$priceMin, $priceMax] = [$priceMax, $priceMin];
        }

        $selectedCategories = $request->filled('categories')
            ? collect((array) $request->categories)->map(fn($id) => (int) $id)->filter()->values()->all()
            : ($request->filled('cat') ? [(int) $request->cat] : []);

        $query = Product::visible()
            ->with(['category'])
            ->with(['variants' => fn($q) => $q->active()->orderBy('price')])
            ->withMin(['variants as variants_min_price' => fn($q) => $q->active()], 'price')
            ->withMax(['variants as variants_max_price' => fn($q) => $q->active()], 'price')
            ->withAvg(['reviews as reviews_avg_rating' => fn($q) => $q->visible()], 'rating')
            ->withCount(['reviews as reviews_count' => fn($q) => $q->visible()])
            ->whereHas('variants', fn($q) => $q->active());

        if (! empty($selectedCategories)) {
            // FIX: trước đây chỉ gom con TRỰC TIẾP (1 cấp), và nếu danh mục có con thì
            // BỎ SÓT LUÔN sản phẩm gắn trực tiếp vào chính danh mục cha đó. Giờ dùng
            // selfAndDescendantIds() để gom đệ quy toàn bộ mọi cấp con/cháu + chính nó,
            // khớp với việc danh mục giờ hỗ trợ đa cấp không giới hạn.
            $expandedIds = collect($selectedCategories)
                ->flatMap(function ($id) {
                    $category = \App\Models\Category::find($id);
                    return $category ? $category->selfAndDescendantIds() : [$id];
                })
                ->unique()
                ->toArray();

            $query->whereIn('category_id', $expandedIds);
        } elseif ($request->filled('cat')) {
            $query->where('category_id', $request->cat);
            $selectedCategories = [$request->cat];
        }

        if ($request->filled('q')) {
            $query->where('name', 'like', '%' . $request->q . '%');
        }

        $query->whereHas('variants', function ($q) use ($priceMin, $priceMax) {
            $q->active()->whereBetween('price', [$priceMin, $priceMax]);
        });

        match ($request->sort) {
            'price_asc'  => $query->orderBy('variants_min_price', 'asc'),
            'price_desc' => $query->orderBy('variants_min_price', 'desc'),
            'newest'     => $query->orderBy('created_at', 'desc'),
            default      => $query->orderBy('created_at', 'desc'),
        };

        $products = $query->paginate(9)->withQueryString();

        // Danh mục CON → dùng cho chips filter nhanh ở trên
        $categories = Category::whereNotNull('parent_id')
            ->withCount(['products' => fn($q) => $q->visible()])
            ->orderBy('sort_order')
            ->get();

        // Danh mục CHA kèm danh mục con → dùng cho sidebar nhóm
        $parentCategories = Category::whereNull('parent_id')
            ->with(['children' => function ($q) {
                $q->withCount(['products' => fn($q2) => $q2->visible()])
                    ->orderBy('sort_order');
            }])
            ->orderBy('sort_order')
            ->get()
            ->map(function ($parent) {
                // Tổng số SP của nhóm = tổng SP các danh mục con
                $parent->total_products = $parent->children->sum('products_count');
                return $parent;
            });

        // Giá min/max cho slider lọc giá
        $sliderMax  = (int) (Product::visible()->withMin('variants', 'price')->withMax('variants', 'price')->get()->max('variants_max_price') ?? 50000000);
        $priceMin   = (int) $request->input('price_min', 0);
        $priceMax   = (int) $request->input('price_max', $sliderMax);

        return view('pages.product.index', compact(
            'products',
            'categories',
            'parentCategories',
            'selectedCategories',
            'priceMin',
            'priceMax',
            'sliderMax'
        ));
    }

    // ─── API gợi ý tìm kiếm ──────────────────────────────────
    public function suggest(Request $request)
    {
        $q = trim((string) $request->input('q', ''));
        if (mb_strlen($q) < 2) {
            return response()->json([]);
        }

        // FIX: trước đây gọi addcslashes($keyword, '%_') nhưng $keyword
        // chưa từng được khai báo ở đâu cả → PHP ném "Undefined variable"
        // → Laravel biến warning này thành ErrorException → route trả về
        // trang lỗi 500 (HTML) thay vì JSON → fetch() ở navbar parse lỗi
        // → dropdown kẹt mãi ở "Đang tìm..." không bao giờ hiện kết quả.
        $safeKeyword = addcslashes($q, '%_');

        $products = Product::visible()
            ->where('name', 'like', '%' . $safeKeyword . '%')
            // Chỉ gợi ý sản phẩm còn ít nhất 1 biến thể đang bán,
            // tránh gợi ý sản phẩm hết hàng/ẩn mà vẫn hiện giá 0đ
            ->whereHas('variants', fn($q) => $q->active())
            ->withMin(['variants as variants_min_price' => fn($q) => $q->active()], 'price')
            ->orderByDesc('created_at')
            ->limit(6)
            ->get()
            ->map(fn($p) => [
                // FIX: đổi 'img' → 'thumbnail' và bỏ 'slug' lấy 'url' đầy đủ,
                // khớp đúng field mà JS render() bên app.blade.php đang đọc
                // (trước đó JS luôn nhận undefined nên ảnh trống, link lỗi).
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

        // Sản phẩm liên quan
        $relatedProducts = Product::visible()
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->with(['variants'])
            ->withMin('variants', 'price')
            ->withMax('variants', 'price')
            ->limit(4)
            ->get();

        // ── Review data ──────────────────────────────────────

        // Trang đầu review (5 review mới nhất, đã duyệt)
        $reviews = $product->reviews()
            ->visible()
            ->with(['user', 'replies.user', 'orderItem'])
            ->latest()
            ->paginate(5);

        // Thống kê rating
        $ratingStats = $product->reviews()->visible()
            ->selectRaw('rating, COUNT(*) as count')
            ->groupBy('rating')
            ->pluck('count', 'rating')
            ->toArray();

        $totalReviews = array_sum($ratingStats);
        $avgRating    = $totalReviews > 0
            ? round(collect($ratingStats)->reduce(fn($carry, $count, $rating) => $carry + $count * $rating, 0) / $totalReviews, 1)
            : 0;

        $reviewStats = [
            'avg'   => $avgRating,
            'total' => $totalReviews,
            'dist'  => array_replace([5 => 0, 4 => 0, 3 => 0, 2 => 0, 1 => 0], $ratingStats),
        ];

        // Order items đủ điều kiện review (đơn hoàn tất, chưa review)
        $eligibleOrderItems = collect();
        if (auth()->check()) {
            $reviewedItemIds = $product->reviews()
                ->where('user_id', auth()->id())
                ->whereNotNull('order_item_id')
                ->pluck('order_item_id');

            $eligibleOrderItems = OrderItem::whereHas('order', function ($q) {
                $q->where('user_id', auth()->id())
                    ->where('order_status', 3); // hoàn tất
            })
                ->whereHas('variant', fn($q) => $q->where('product_id', $product->id))
                ->whereNotIn('id', $reviewedItemIds)
                ->with('order')
                ->get();
        }

        return view('pages.product.show', compact(
            'product',
            'relatedProducts',
            'reviews',
            'reviewStats',
            'eligibleOrderItems',
        ));
    }
}
