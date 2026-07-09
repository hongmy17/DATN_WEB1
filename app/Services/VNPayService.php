<?php

namespace App\Services;

use Illuminate\Support\Str;

/**
 * VNPay Service — Sandbox
 * Tạo URL thanh toán & verify IPN callback
 */
class VNPayService
{
    private string $tmnCode;
    private string $hashSecret;
    private string $payUrl;
    private string $returnUrl;
    private string $ipnUrl;

    public function __construct()
    {
        $this->tmnCode    = config('vnpay.tmn_code');
        $this->hashSecret = config('vnpay.hash_secret');
        $this->payUrl     = config('vnpay.url');
        $this->returnUrl  = config('vnpay.return_url');
        $this->ipnUrl     = config('vnpay.ipn_url');
    }

    /**
     * Tạo URL redirect sang VNPay
     *
     * @param int    $orderId     ID đơn hàng
     * @param float  $amount      Số tiền (VNĐ, chưa nhân 100)
     * @param string $orderInfo   Mô tả đơn hàng
     * @param string $clientIp    IP người dùng
     */
    public function createPaymentUrl(
        int    $orderId,
        float  $amount,
        string $orderInfo = '',
        string $clientIp  = '127.0.0.1'
    ): string {
        $txnRef  = $orderId . '_' . time(); // unique mỗi lần tạo URL
        $now     = now()->setTimezone('Asia/Ho_Chi_Minh');

        $params = [
            'vnp_Version'    => '2.1.0',
            'vnp_Command'    => 'pay',
            'vnp_TmnCode'    => $this->tmnCode,
            'vnp_Amount'     => (int) ($amount * 100),   // VNPay tính theo đồng * 100
            'vnp_CurrCode'   => 'VND',
            'vnp_TxnRef'     => $txnRef,
            'vnp_OrderInfo'  => $orderInfo ?: "Thanh toan don hang NX-{$orderId}",
            'vnp_OrderType'  => 'other',
            'vnp_Locale'     => 'vn',
            'vnp_ReturnUrl'  => $this->returnUrl,
            'vnp_IpAddr'     => $clientIp,
            'vnp_CreateDate' => $now->format('YmdHis'),
            'vnp_ExpireDate' => $now->addMinutes(15)->format('YmdHis'),
        ];

        // Sắp xếp theo key (bắt buộc của VNPay)
        ksort($params);

        // Tạo query string & chữ ký
        $query     = http_build_query($params);
        $signature = hash_hmac('sha512', $query, $this->hashSecret);

        return $this->payUrl . '?' . $query . '&vnp_SecureHash=' . $signature;
    }

    /**
     * Verify chữ ký từ VNPay gửi về (Return URL hoặc IPN)
     *
     * @param array $data  Toàn bộ $_GET / request()->all()
     * @return bool
     */
    public function verifySignature(array $data): bool
    {
        $receivedHash = $data['vnp_SecureHash'] ?? '';

        // Loại bỏ các key liên quan chữ ký trước khi tính lại
        $filtered = collect($data)
            ->except(['vnp_SecureHash', 'vnp_SecureHashType'])
            ->toArray();

        ksort($filtered);
        $query    = http_build_query($filtered);
        $computed = hash_hmac('sha512', $query, $this->hashSecret);

        return hash_equals($computed, strtolower($receivedHash));
    }

    /**
     * Lấy orderId từ vnp_TxnRef (format: orderId_timestamp)
     */
    public function parseOrderId(string $txnRef): int
    {
        return (int) explode('_', $txnRef)[0];
    }
}