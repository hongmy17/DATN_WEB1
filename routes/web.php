<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\SocialAuthController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CouponController;
use App\Http\Controllers\UserAddressController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\Admin\InvoiceController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ReviewController;   // ← THÊM

require __DIR__ . '/auth.php';

// ─── TRANG CHÍNH ────────────────────────────────────────────────────────────
Route::get('/', fn() => view('pages.home'))->name('home');

Route::get('/san-pham', [ProductController::class, 'index'])->name('products.index');
Route::get('/san-pham/suggest', [ProductController::class, 'suggest'])->name('products.suggest');
Route::get('/san-pham/{slug}', [ProductController::class, 'show'])->name('products.show');

Route::get('/chinh-sach/{slug}', function ($slug) {
    $policies = [
        'doi-tra'     => ['title' => 'Chính sách đổi trả',    'icon' => 'M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8 M3 3v5h5'],
        'bao-hanh'    => ['title' => 'Chính sách bảo hành',   'icon' => 'M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z'],
        'van-chuyen'  => ['title' => 'Chính sách vận chuyển', 'icon' => 'M1 3h15v13H1zM16 8h4l3 3v4h-7V8z M5.5 21a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5zM18.5 21a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5z'],
        'bao-mat'     => ['title' => 'Chính sách bảo mật',    'icon' => 'M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z M9 12l2 2 4-4'],
        'mua-hang'    => ['title' => 'Hướng dẫn mua hàng',    'icon' => 'M9 11l3 3L22 4 M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11'],
    ];

    if (!isset($policies[$slug])) abort(404);

    return view('pages.other.policy', [
        'slug'   => $slug,
        'title'  => $policies[$slug]['title'],
        'icon'   => $policies[$slug]['icon'],
    ]);
})->name('policy');
// ── Review: load AJAX (không cần auth, ai cũng đọc được) ────────────────────
Route::get('/san-pham/{slug}/danh-gia', [ReviewController::class, 'load'])
    ->name('products.reviews.load');

Route::get('/gio-hang', fn() => view('pages.cart.index'))->name('cart.index');
Route::get('/thanh-toan', [CheckoutController::class, 'index'])->name('checkout.index');

Route::get('/khuyen-mai', fn() => view('pages.other.promotions'))->name('promotions');
Route::get('/yeu-thich', fn() => view('pages.other.wishlist'))->name('wishlist');
Route::get('/lien-he', fn() => view('pages.other.contact'))->name('contact');
Route::get('/chinh-sach/{slug}', function ($slug) {
    $policies = [
        'doi-tra'     => ['title' => 'Chính sách đổi trả',    'icon' => 'M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8 M3 3v5h5'],
        'bao-hanh'    => ['title' => 'Chính sách bảo hành',   'icon' => 'M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z'],
        'van-chuyen'  => ['title' => 'Chính sách vận chuyển', 'icon' => 'M1 3h15v13H1zM16 8h4l3 3v4h-7V8z M5.5 21a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5zM18.5 21a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5z'],
        'bao-mat'     => ['title' => 'Chính sách bảo mật',    'icon' => 'M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z M9 12l2 2 4-4'],
        'mua-hang'    => ['title' => 'Hướng dẫn mua hàng',    'icon' => 'M9 11l3 3L22 4 M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11'],
    ];

    if (!isset($policies[$slug])) abort(404);

    return view('pages.other.policy', [
        'slug'   => $slug,
        'title'  => $policies[$slug]['title'],
        'icon'   => $policies[$slug]['icon'],
    ]);
})->name('policy');

Route::redirect('/tai-khoan', '/dang-nhap');

// ─── AUTH (chỉ cho guest) ───────────────────────────────────────────────────
Route::middleware('guest')->group(function () {
    Route::get('/dang-nhap', [AuthenticatedSessionController::class, 'create'])
        ->name('login');
    Route::post('/dang-nhap', [AuthenticatedSessionController::class, 'store'])
        ->name('login.store');

    Route::get('/dang-ky', [RegisteredUserController::class, 'create'])
        ->name('register');
    Route::post('/dang-ky', [RegisteredUserController::class, 'store'])
        ->name('register.store');

    Route::get('/quen-mat-khau', fn() => view('pages.auth.forgot'))
        ->name('password.request');

    Route::get('/auth/redirect/{provider}', [SocialAuthController::class, 'redirect'])
        ->name('social.redirect');
    Route::get('/auth/callback/{provider}', [SocialAuthController::class, 'callback'])
        ->name('social.callback');
});

// ─── AUTH (chỉ cho người đã đăng nhập) ─────────────────────────────────────
Route::middleware('auth')->group(function () {
    Route::post('/dang-xuat', [AuthenticatedSessionController::class, 'destroy'])
        ->name('logout');
});
    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // // Đơn hàng
    Route::get('/don-hang', function () {
        $orders = Auth::user()->orders()->with('items')->latest()->get();
        return view('pages.order.index', compact('orders'));
    })->name('orders.index');

    Route::get('/don-hang/{order}', [\App\Http\Controllers\OrderController::class, 'show'])
        ->name('orders.show');

    Route::post('/don-hang/{order}/huy', [\App\Http\Controllers\OrderController::class, 'cancel'])
        ->name('orders.cancel');

    // Coupon
    Route::post('/coupon/apply',  [CouponController::class, 'apply'])->name('coupon.apply');
    Route::post('/coupon/remove', [CouponController::class, 'remove'])->name('coupon.remove');

    // Đặt hàng
    Route::post('/thanh-toan/dat-hang', [CheckoutController::class, 'store'])
        ->name('checkout.store');

    // Địa chỉ
    Route::prefix('dia-chi')->name('addresses.')->group(function () {
        Route::get('/',              [UserAddressController::class, 'index'])->name('index');
        Route::get('/them',          [UserAddressController::class, 'create'])->name('create');
        Route::post('/',             [UserAddressController::class, 'store'])->name('store');
        Route::get('/{address}/sua', [UserAddressController::class, 'edit'])->name('edit');
        Route::put('/{address}',     [UserAddressController::class, 'update'])->name('update');
        Route::delete('/{address}',  [UserAddressController::class, 'destroy'])->name('destroy');
        Route::patch('/{address}/mac-dinh', [UserAddressController::class, 'setDefault'])->name('setDefault');
    });

    // ── Review: gửi đánh giá (cần đăng nhập) ───────────────────────────────
    Route::post('/san-pham/{slug}/danh-gia', [ReviewController::class, 'store'])
        ->name('products.reviews.store');
    Route::put('/san-pham/{slug}/danh-gia/{review}', [ReviewController::class, 'update'])
        ->name('products.reviews.update');
    Route::post('/san-pham/{slug}/phan-hoi/{reply}', [ReviewController::class, 'reply'])
        ->name('products.replies.reply');

    // ── Giỏ hàng API ────────────────────────────────────────────────────────
    Route::prefix('api/cart')->name('cart.api.')->group(function () {
        Route::get('/',              [\App\Http\Controllers\CartItemController::class, 'index'])->name('index');
        Route::post('/sync',         [\App\Http\Controllers\CartItemController::class, 'sync'])->name('sync');
        Route::post('/',             [\App\Http\Controllers\CartItemController::class, 'store'])->name('store');
        Route::patch('/{cartItem}',  [\App\Http\Controllers\CartItemController::class, 'update'])->name('update');
        Route::delete('/{cartItem}', [\App\Http\Controllers\CartItemController::class, 'destroy'])->name('destroy');
        Route::delete('/',           [\App\Http\Controllers\CartItemController::class, 'clear'])->name('clear');
    });

    // Route in hóa đơn PDF
    Route::middleware(['auth'])->group(function () {
        Route::get('/admin-invoice/{order}', [InvoiceController::class, 'download'])
            ->name('admin.invoice.download');
    });

    // VNPay
    Route::post('/thanh-toan/vnpay/create', [\App\Http\Controllers\PaymentController::class, 'createVNPay'])
        ->name('vnpay.create');

    // IPN VNPay — không cần auth (VNPay server gọi trực tiếp)
    Route::post('/thanh-toan/vnpay/ipn', [\App\Http\Controllers\PaymentController::class, 'ipnVNPay'])
        ->name('vnpay.ipn')
        ->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class]);

    Route::get('/thanh-toan/vnpay/return',  [\App\Http\Controllers\PaymentController::class, 'returnVNPay'])
        ->name('vnpay.return');
