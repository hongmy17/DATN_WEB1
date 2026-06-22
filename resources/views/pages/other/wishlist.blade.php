@extends('layouts.app')

@section('title', 'Sản phẩm yêu thích - Nexus Store')

@push('styles')
    <style>
        .wishlist-empty {
            text-align: center;
            padding: 80px 24px;
            background: var(--bg-alt);
            border-radius: var(--r-2xl);
        }

        .wishlist-empty-icon {
            font-size: 72px;
            margin-bottom: 20px;
        }
    </style>
@endpush

@section('content')
    <div class="container section">
        <div class="breadcrumb mb-24">
            <a href="{{ url('/') }}">Trang chủ</a>
            <span class="breadcrumb__sep">›</span>
            <span class="breadcrumb__current">Yêu thích</span>
        </div>

        <div class="d-flex justify-between align-center mb-32">
            <h1 class="heading-1">Sản phẩm <span class="text-accent">yêu thích</span> <i class="fa-solid fa-heart"></i></h1>
        </div>

        <div id="wishlistContainer">
            <!-- Sản phẩm yêu thích demo -->
            <div class="grid-4">
                <div class="product-card">
                    <div class="product-card__thumb">
                        <button class="product-card__wish active">♥</button>
                        <div class="product-card__img">💻</div>
                        <div class="product-card__actions">
                            <button class="btn btn-primary btn-sm"
                                onclick="Cart.add({id:1,name:'MacBook Pro',price:42990000,img:'💻'})">+ Giỏ hàng</button>
                        </div>
                    </div>
                    <div class="product-card__body">
                        <div class="product-card__brand">Apple</div>
                        <div class="product-card__name">MacBook Pro 14" M3 Pro</div>
                        <div class="product-card__price">42.990.000₫</div>
                    </div>
                </div>
                <div class="product-card">
                    <div class="product-card__thumb">
                        <button class="product-card__wish active">♥</button>
                        <div class="product-card__img">📱</div>
                        <div class="product-card__actions">
                            <button class="btn btn-primary btn-sm"
                                onclick="Cart.add({id:2,name:'iPhone 15',price:32990000,img:'📱'})">+ Giỏ hàng</button>
                        </div>
                    </div>
                    <div class="product-card__body">
                        <div class="product-card__brand">Apple</div>
                        <div class="product-card__name">iPhone 15 Pro Max</div>
                        <div class="product-card__price">32.990.000₫</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        initWishBtns();
    </script>
@endpush
