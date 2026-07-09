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

require __DIR__ . '/auth.php';

// ─── TRANG CHÍNH ────────────────────────────────────────────────────────────
Route::get('/', fn() => view('pages.home'))->name('home');

Route::get('/san-pham', [ProductController::class, 'index'])->name('products.index');
Route::get('/san-pham/{slug}', [ProductController::class, 'show'])->name('products.show');
Route::get('/san-pham/suggest', [ProductController::class, 'suggest'])->name('products.suggest');

Route::get('/gio-hang', fn() => view('pages.cart.index'))->name('cart.index');
Route::get('/thanh-toan', [CheckoutController::class, 'index'])->name('checkout.index');

Route::get('/khuyen-mai', fn() => view('pages.other.promotions'))->name('promotions');
Route::get('/yeu-thich', fn() => view('pages.other.wishlist'))->name('wishlist');
Route::get('/lien-he', fn() => view('pages.other.contact'))->name('contact');

// redirect /tai-khoan → /dang-nhap
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

    // ── Giỏ hàng API ──────────────────────────────────────────────────────
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
Route::post('/thanh-toan/vnpay/ipn',    [\App\Http\Controllers\PaymentController::class, 'ipnVNPay'])
    ->name('vnpay.ipn')
    ->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class]);

Route::get('/thanh-toan/vnpay/return',  [\App\Http\Controllers\PaymentController::class, 'returnVNPay'])
    ->name('vnpay.return');