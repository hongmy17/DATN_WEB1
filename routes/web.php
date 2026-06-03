<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Auth\SocialAuthController;
use Illuminate\Support\Facades\Route;

// ─── TRANG CHÍNH ────────────────────────────────────────────────────────────
Route::get('/', fn() => view('pages.home'))->name('home');

// ─── SẢN PHẨM ───────────────────────────────────────────────────────────────
Route::get('/san-pham', fn() => view('pages.product.index'))->name('products.index');
Route::get('/chi-tiet', fn() => view('pages.product.show'))->name('products.show');

// ─── GIỎ HÀNG & THANH TOÁN ──────────────────────────────────────────────────
Route::get('/gio-hang', fn() => view('pages.cart.index'))->name('cart.index');
Route::get('/thanh-toan', fn() => view('pages.checkout.index'))->name('checkout.index');

// ─── ĐƠN HÀNG ───────────────────────────────────────────────────────────────
Route::get('/don-hang', fn() => view('pages.order.index'))->name('orders.index');

// ─── KHUYẾN MÃI / LIÊN HỆ / YÊU THÍCH ──────────────────────────────────────
Route::get('/khuyen-mai', fn() => view('pages.other.promotions'))->name('promotions');
Route::get('/yeu-thich', fn() => view('pages.other.wishlist'))->name('wishlist');
Route::get('/lien-he', fn() => view('pages.other.contact'))->name('contact');

// ─── AUTH (chỉ cho guest) ───────────────────────────────────────────────────
Route::middleware('guest')->group(function () {
    // Đăng nhập
    Route::get('/tai-khoan', [AuthenticatedSessionController::class, 'create'])
        ->name('login');
    Route::post('/tai-khoan', [AuthenticatedSessionController::class, 'store'])
        ->name('login.store');

    // Đăng ký
    Route::get('/dang-ky', [RegisteredUserController::class, 'create'])
        ->name('register');
    Route::post('/dang-ky', [RegisteredUserController::class, 'store'])
        ->name('register.store');

    // Quên mật khẩu (giữ nguyên, có thể mở rộng sau)
    Route::get('/quen-mat-khau', fn() => view('pages.auth.forgot'))
        ->name('password.request');

    // Social auth
    Route::get('/auth/redirect/{provider}', [SocialAuthController::class, 'redirect'])
        ->name('social.redirect');
    Route::get('/auth/callback/{provider}', [SocialAuthController::class, 'callback'])
        ->name('social.callback');
});

// ─── ĐĂNG XUẤT (chỉ cho auth) ───────────────────────────────────────────────
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
});
