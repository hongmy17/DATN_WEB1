<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    // ─── Trang danh sách sản phẩm ────────────────────────────
    public function index(Request $request)
    {
        $query = Product::visible()
            ->with(['category', 'variants'])
            ->withMin('variants', 'price')
            ->withMax('variants', 'price');

        // Lọc theo nhiều danh mục (checkbox sidebar dùng categories[], chip dùng cat)
        $selectedCategories = array_filter((array) $request->input('categories', []));

        if (!empty($selectedCategories)) {
            $query->whereIn('category_id', $selectedCategories);
        } elseif ($request->filled('cat')) {
            $query->where('category_id', $request->cat);
            $selectedCategories = [$request->cat];
        }

        // Lọc theo từ khóa tìm kiếm
        if ($request->filled('q')) {
            $query->where('name', 'like', '%' . $request->q . '%');
        }

        // Lọc theo khoảng giá
        if ($request->filled('price_min')) {
            $query->where('variants_min_price', '>=', (int) $request->price_min);
        }
        if ($request->filled('price_max')) {
            $query->where('variants_min_price', '<=', (int) $request->price_max);
        }

        // Sắp xếp
        match ($request->sort) {
            'price_asc'  => $query->orderBy('variants_min_price', 'asc'),
            'price_desc' => $query->orderBy('variants_min_price', 'desc'),
            'newest'     => $query->orderBy('created_at', 'desc'),
            default      => $query->orderBy('created_at', 'desc'),
        };

        $products = $query->paginate(9)->withQueryString();

        // Danh mục để hiển thị chips & sidebar
        $categories = Category::whereNotNull('parent_id')
            ->withCount(['products' => fn($q) => $q->visible()])
            ->orderBy('name')
            ->get();

        // Giá min/max cho slider lọc giá
        $sliderMax  = (int) (Product::visible()->withMin('variants', 'price')->withMax('variants', 'price')->get()->max('variants_max_price') ?? 50000000);
        $priceMin   = (int) $request->input('price_min', 0);
        $priceMax   = (int) $request->input('price_max', $sliderMax);

        return view('pages.product.index', compact(
            'products', 'categories', 'selectedCategories',
            'priceMin', 'priceMax', 'sliderMax'
        ));
    }

    // ─── Gợi ý sản phẩm (search suggest) ──────────────────────
    public function suggest(Request $request)
    {
        $q = $request->input('q', '');
        if (strlen($q) < 2) {
            return response()->json([]);
        }

        $products = Product::visible()
            ->where('name', 'like', '%' . $q . '%')
            ->with(['variants'])
            ->withMin('variants', 'price')
            ->limit(6)
            ->get()
            ->map(fn($p) => [
                'id'    => $p->id,
                'name'  => $p->name,
                'slug'  => $p->slug,
                'price' => $p->variants_min_price,
                'img'   => $p->thumbnail ? asset('storage/' . $p->thumbnail) : '',
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