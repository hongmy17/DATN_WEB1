<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\SocialAuthController;
use App\Http\Controllers\CartItemController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\CouponController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\UserAddressController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

require __DIR__ . '/auth.php';

// ── TRANG CÔNG KHAI ──────────────────────────────────────────────────────────

// FIX: Home truyền categories thật + sản phẩm nổi bật thật
Route::get('/', function () {
    // Danh mục cha: đếm sản phẩm qua danh mục CON (products.category_id → child categories)
    $categories = \App\Models\Category::whereNull('parent_id')
        ->withCount([
            // Đếm sản phẩm visible trong các danh mục CON
            'products as products_count' => fn($q) => $q->visible(),
        ])
        ->orderBy('name')
        ->get()
        ->map(function ($cat) {
            // Nếu danh mục cha không có SP trực tiếp, đếm SP qua danh mục con
            if ($cat->products_count === 0) {
                $childIds = \App\Models\Category::where('parent_id', $cat->id)->pluck('id');
                $cat->products_count = \App\Models\Product::visible()
                    ->whereIn('category_id', $childIds)
                    ->count();
            }
            return $cat;
        });

    // 8 sản phẩm nổi bật mới nhất có biến thể active
    $featuredProducts = collect();

    $childCats = \App\Models\Category::whereNotNull('parent_id')->pluck('id');

    foreach ($childCats as $catId) {
        $sps = \App\Models\Product::visible()
            ->where('category_id', $catId)
            ->with(['variants' => fn($q) => $q->active()->orderBy('price'), 'category'])
            ->whereHas('variants', fn($q) => $q->active())
            ->latest()
            ->limit(2)
            ->get();
        $featuredProducts = $featuredProducts->merge($sps);
    }

    // Shuffle để không hiện theo thứ tự danh mục
    $featuredProducts = $featuredProducts->shuffle()->take(8);

    // Sản phẩm nổi bật nhất = sản phẩm có giá cao nhất
    $topProduct = $featuredProducts
        ->sortByDesc(fn($p) => optional($p->variants->first())->price ?? 0)
        ->first();

    // Reviews thật từ DB (rating >= 4), fallback về mẫu phù hợp web phụ kiện
    $reviews = \App\Models\Review::with('user')
        ->where('rating', '>=', 4)
        ->latest()
        ->limit(3)
        ->get();
    $tabCategories = \App\Models\Category::whereNull('parent_id')
        ->with('children') // load danh mục con để lấy childIds cho tab filter
        ->orderBy('name')
        ->limit(4)
        ->get();

    // Đếm SP qua danh mục con (vì SP gắn với con, không phải cha)
    $tabCategories->each(function ($cat) {
        $childIds = $cat->children->pluck('id');
        $cat->products_count = \App\Models\Product::visible()
            ->whereIn('category_id', $childIds)
            ->count();
    });
    return view('pages.home', compact('categories', 'featuredProducts', 'topProduct', 'reviews', 'tabCategories'));
})->name('home');

Route::get('/san-pham/suggest', [ProductController::class, 'suggest'])->name('products.suggest');
Route::get('/san-pham',         [ProductController::class, 'index'])->name('products.index');
Route::get('/san-pham/{slug}',  [ProductController::class, 'show'])->name('products.show');

Route::get('/san-pham/{slug}/danh-gia', [ReviewController::class, 'load'])
    ->name('products.reviews.load');

Route::get('/gio-hang', fn() => view('pages.cart.index'))->name('cart.index');
Route::get('/thanh-toan', [CheckoutController::class, 'index'])->name('checkout.index');

// Khuyến mãi: truyền sản phẩm có giảm giá thật
Route::get('/khuyen-mai', function () {
    $saleProducts = \App\Models\Product::visible()
        ->with(['variants' => fn($q) => $q->active()->orderBy('price'), 'category'])
        ->whereHas('variants', fn($q) => $q->active()->where('compare_price', '>', 0))
        ->latest()
        ->limit(8)
        ->get();
    return view('pages.other.promotions', compact('saleProducts'));
})->name('promotions');

Route::get('/yeu-thich', fn() => view('pages.other.wishlist'))->name('wishlist');
Route::get('/lien-he',   fn() => view('pages.other.contact'))->name('contact');

Route::get('/chinh-sach/{slug}', function ($slug) {
    $policies = [
        'doi-tra'    => ['title' => 'Chính sách đổi trả',    'icon' => 'M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8 M3 3v5h5'],
        'bao-hanh'   => ['title' => 'Chính sách bảo hành',   'icon' => 'M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z'],
        'van-chuyen' => ['title' => 'Chính sách vận chuyển', 'icon' => 'M1 3h15v13H1zM16 8h4l3 3v4h-7V8zM5.5 21a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5zM18.5 21a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5z'],
        'bao-mat'    => ['title' => 'Chính sách bảo mật',    'icon' => 'M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z M9 12l2 2 4-4'],
        'mua-hang'   => ['title' => 'Hướng dẫn mua hàng',    'icon' => 'M9 11l3 3L22 4 M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11'],
    ];
    if (!isset($policies[$slug])) abort(404);
    return view('pages.other.policy', array_merge(['slug' => $slug], $policies[$slug]));
})->name('policy');

// ── GUEST ONLY ────────────────────────────────────────────────────────────────
Route::middleware('guest')->group(function () {
    Route::get('/dang-nhap',  [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/dang-nhap', [AuthenticatedSessionController::class, 'store']);
    Route::get('/dang-ky',    [RegisteredUserController::class, 'create'])->name('register');
    Route::post('/dang-ky',   [RegisteredUserController::class, 'store']);
    Route::get('/quen-mat-khau', fn() => view('pages.auth.forgot'))->name('password.request');
    Route::get('/auth/redirect/{provider}', [SocialAuthController::class, 'redirect'])->name('social.redirect');
    Route::get('/auth/callback/{provider}', [SocialAuthController::class, 'callback'])->name('social.callback');
});

// ── AUTH REQUIRED ─────────────────────────────────────────────────────────────
Route::middleware('auth')->group(function () {
    Route::post('/dang-xuat', [AuthenticatedSessionController::class, 'destroy'])->name('logout');

    Route::get('/profile',    [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile',  [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/don-hang', function () {
        $orders = Auth::user()->orders()->with('items')->latest()->get();
        return view('pages.order.index', compact('orders'));
    })->name('orders.index');
    Route::get('/don-hang/{order}',      [OrderController::class, 'show'])->name('orders.show');
    Route::post('/don-hang/{order}/huy', [OrderController::class, 'cancel'])->name('orders.cancel');

    Route::post('/coupon/apply',  [CouponController::class, 'apply'])->name('coupon.apply');
    Route::post('/coupon/remove', [CouponController::class, 'remove'])->name('coupon.remove');

    Route::post('/thanh-toan/dat-hang', [CheckoutController::class, 'store'])->name('checkout.store');

    Route::prefix('dia-chi')->name('addresses.')->group(function () {
        Route::get('/',                     [UserAddressController::class, 'index'])->name('index');
        Route::get('/them',                 [UserAddressController::class, 'create'])->name('create');
        Route::post('/',                    [UserAddressController::class, 'store'])->name('store');
        Route::get('/{address}/sua',        [UserAddressController::class, 'edit'])->name('edit');
        Route::put('/{address}',            [UserAddressController::class, 'update'])->name('update');
        Route::delete('/{address}',         [UserAddressController::class, 'destroy'])->name('destroy');
        Route::patch('/{address}/mac-dinh', [UserAddressController::class, 'setDefault'])->name('setDefault');
    });

    Route::post('/san-pham/{slug}/danh-gia',                    [ReviewController::class, 'store'])->name('products.reviews.store');
    Route::put('/san-pham/{slug}/danh-gia/{review}',            [ReviewController::class, 'update'])->name('products.reviews.update');
    Route::post('/san-pham/{slug}/danh-gia/{review}/tra-loi',   [ReviewController::class, 'reply'])->name('products.reviews.reply');

    Route::prefix('api/cart')->name('cart.api.')->group(function () {
        Route::get('/',             [CartItemController::class, 'index'])->name('index');
        Route::post('/sync',        [CartItemController::class, 'sync'])->name('sync');
        Route::post('/',            [CartItemController::class, 'store'])->name('store');
        Route::patch('/{cartItem}', [CartItemController::class, 'update'])->name('update');
        Route::delete('/{cartItem}', [CartItemController::class, 'destroy'])->name('destroy');
        Route::delete('/',          [CartItemController::class, 'clear'])->name('clear');
    });

    Route::post('/thanh-toan/vnpay/create', [PaymentController::class, 'createVNPay'])->name('vnpay.create');
    Route::get('/thanh-toan/vnpay/return',  [PaymentController::class, 'returnVNPay'])->name('vnpay.return');
});

// FIX: IPN NGOÀI auth — VNPay server không có session đăng nhập
Route::post('/thanh-toan/vnpay/ipn', [PaymentController::class, 'ipnVNPay'])
    ->name('vnpay.ipn')
    ->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class]);

Route::get('/admin-invoice/{order}', [\App\Http\Controllers\Admin\InvoiceController::class, 'download'])
    ->name('admin.invoice.download')
    ->middleware('auth');
