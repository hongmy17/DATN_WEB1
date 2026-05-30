<?php

use Illuminate\Support\Facades\Route;

// Trang chủ
Route::get('/', function () {
    return view('pages.home');
})->name('home');

// Sản phẩm
Route::get('/san-pham', function () {
    return view('pages.product.index');
})->name('products.index');

Route::get('/chi-tiet', function () {
    return view('pages.product.show');
})->name('products.show');

// Giỏ hàng & thanh toán
Route::get('/gio-hang', function () {
    return view('pages.cart.index');
})->name('cart.index');

Route::get('/thanh-toan', function () {
    return view('pages.checkout.index');
})->name('checkout.index');

// Đơn hàng
Route::get('/don-hang', function () {
    return view('pages.order.index');
})->name('orders.index');

// Tài khoản
Route::get('/tai-khoan', function () {
    return view('pages.auth.login');
})->name('login');

Route::get('/dang-ky', function () {
    return view('pages.auth.register');
})->name('register');

Route::get('/quen-mat-khau', function () {
    return view('pages.auth.forgot');
})->name('password.request');

// Khuyến mãi
Route::get('/khuyen-mai', function () {
    return view('pages.other.promotions');
})->name('promotions');


// Yêu thích 
Route::get('/yeu-thich', function () {
    return view('pages.other.wishlist');
})->name('wishlist');

Route::get('/lien-he', function () {
    return view('pages.other.contact');
})->name('contact');