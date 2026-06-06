<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\SocialAuthController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

require __DIR__ . '/auth.php';

Route::get('/', fn () => view('pages.home'))->name('home');

Route::get('/san-pham', fn () => view('pages.product.index'))->name('products.index');
Route::get('/chi-tiet', fn() => view('pages.product.show'))->name('products.show');

Route::get('/gio-hang', fn() => view('pages.cart.index'))->name('cart.index');
Route::get('/thanh-toan', fn() => view('pages.checkout.index'))->name('checkout.index');

Route::get('/don-hang', fn() => view('pages.order.index'))->name('orders.index');

Route::get('/khuyen-mai', fn() => view('pages.other.promotions'))->name('promotions');
Route::get('/yeu-thich', fn() => view('pages.other.wishlist'))->name('wishlist');
Route::get('/lien-he', fn() => view('pages.other.contact'))->name('contact');

Route::redirect('/tai-khoan', '/login');

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

    Route::get('/profile', [ProfileController::class, 'edit'])
        ->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])
        ->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])
        ->name('profile.destroy');
});
