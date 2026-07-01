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

        // Lọc theo danh mục
        if ($request->filled('cat')) {
            $query->where('category_id', $request->cat);
        }

        // Lọc theo từ khóa tìm kiếm
        if ($request->filled('q')) {
            $query->where('name', 'like', '%' . $request->q . '%');
        }

        // Sắp xếp
        match ($request->sort) {
            'price_asc'  => $query->orderBy('variants_min_price', 'asc'),
            'price_desc' => $query->orderBy('variants_min_price', 'desc'),
            'newest'     => $query->orderBy('created_at', 'desc'),
            default      => $query->orderBy('created_at', 'desc'),
        };

        $products = $query->paginate(9)->withQueryString();

        // Danh mục con để hiển thị chips
        $categories = Category::whereNotNull('parent_id')
            ->withCount(['products' => fn($q) => $q->visible()])
            ->orderBy('name')
            ->get();

        return view('pages.product.index', compact('products', 'categories'));
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
