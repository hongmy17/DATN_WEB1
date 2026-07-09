<?php

/**
 * File: config/vnpay.php
 * Đọc từ .env — không hardcode credentials vào code
 */
return [
    'tmn_code'    => env('VNPAY_TMN_CODE',    'RG546284'),
    'hash_secret' => env('VNPAY_HASH_SECRET', 'M52INLVMA9VV2LT5W78H0EF8X6IB6PJN'),
    'url'         => env('VNPAY_URL',         'https://sandbox.vnpayment.vn/paymentv2/vpcpay.html'),
    'return_url'  => env('VNPAY_RETURN_URL',  env('APP_URL', 'http://127.0.0.1:8000') . '/thanh-toan/vnpay/return'),
    'ipn_url'     => env('VNPAY_IPN_URL',     env('APP_URL', 'http://127.0.0.1:8000') . '/thanh-toan/vnpay/ipn'),
];