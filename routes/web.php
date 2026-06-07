<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\SocialAuthController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

require __DIR__ . '/auth.php';
use App\Http\Controllers\UserAddressController;
use App\Http\Controllers\CheckoutController;

require __DIR__ . '/auth.php';

// ─── TRANG CHÍNH ────────────────────────────────────────────────────────────
Route::get('/', fn() => view('pages.home'))->name('home');

Route::get('/', fn () => view('pages.home'))->name('home');

Route::get('/san-pham', fn () => view('pages.product.index'))->name('products.index');
Route::get('/chi-tiet', fn() => view('pages.product.show'))->name('products.show');

Route::get('/gio-hang', fn() => view('pages.cart.index'))->name('cart.index');
Route::get('/thanh-toan', [CheckoutController::class, 'index'])->name('checkout.index');

Route::get('/don-hang', fn() => view('pages.order.index'))->name('orders.index');

Route::get('/khuyen-mai', fn() => view('pages.other.promotions'))->name('promotions');
Route::get('/yeu-thich', fn() => view('pages.other.wishlist'))->name('wishlist');
Route::get('/lien-he', fn() => view('pages.other.contact'))->name('contact');

Route::redirect('/tai-khoan', '/login');
// ─── AUTH (chỉ cho guest) ───────────────────────────────────────────────────
Route::middleware('guest')->group(function () {
    // Đăng nhập        
    Route::get('/tai-khoan', [AuthenticatedSessionController::class, 'create'])
        ->name('login');
    Route::post('/tai-khoan', [AuthenticatedSessionController::class, 'store'])
        ->name('login.store');
});
Route::middleware('guest')->group(function () {
    Route::get('/dang-ky', [RegisteredUserController::class, 'create'])
        ->name('register');
    Route::post('/dang-ky', [RegisteredUserController::class, 'store'])
        ->name('register.store');

    Route::get('/quen-mat-khau', fn () => view('pages.auth.forgot'))
        ->name('password.request');

    Route::get('/auth/redirect/{provider}', [SocialAuthController::class, 'redirect'])
        ->name('social.redirect');
    Route::get('/auth/callback/{provider}', [SocialAuthController::class, 'callback'])
        ->name('social.callback');
});

Route::middleware('auth')->group(function () {
    Route::post('/dang-xuat', [AuthenticatedSessionController::class, 'destroy'])
        ->name('logout');

    // Profile management
    Route::get('/profile', [ProfileController::class, 'edit'])
        ->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])
        ->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])
        ->name('profile.destroy');

    // ─── ĐỊA CHỈ ────────────────────────────────────────────────────────────
    Route::prefix('dia-chi')->name('addresses.')->group(function () {
        Route::get('/', [UserAddressController::class, 'index'])->name('index');
        Route::get('/them', [UserAddressController::class, 'create'])->name('create');
        Route::post('/', [UserAddressController::class, 'store'])->name('store');
        Route::get('/{address}/sua', [UserAddressController::class, 'edit'])->name('edit');
        Route::put('/{address}', [UserAddressController::class, 'update'])->name('update');
        Route::delete('/{address}', [UserAddressController::class, 'destroy'])->name('destroy');
        Route::patch('/{address}/mac-dinh', [UserAddressController::class, 'setDefault'])->name('setDefault');
    });
});
