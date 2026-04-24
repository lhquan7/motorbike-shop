<?php
namespace App\Services;

class VNPayService
{
    private string $tmnCode;
    private string $hashSecret;
    private string $url;
    private string $returnUrl;

    public function __construct() {
        $this->tmnCode   = config('services.vnpay.tmn_code');
        $this->hashSecret= config('services.vnpay.hash_secret');
        $this->url       = config('services.vnpay.url');
        $this->returnUrl = config('services.vnpay.return_url');
    }

    public function createPaymentUrl(string $orderCode, int $amount, string $orderInfo): string
    {
        $vnpParams = [
            'vnp_Version'    => '2.1.0',
            'vnp_Command'    => 'pay',
            'vnp_TmnCode'    => $this->tmnCode,
            'vnp_Amount'     => $amount * 100,   // VNPay tính đơn vị VNĐ * 100
            'vnp_CurrCode'   => 'VND',
            'vnp_TxnRef'     => $orderCode.'_'.time(),
            'vnp_OrderInfo'  => $orderInfo,
            'vnp_OrderType'  => 'billpayment',
            'vnp_Locale'     => 'vn',
            'vnp_ReturnUrl'  => $this->returnUrl,
            'vnp_IpAddr'     => request()->ip(),
            'vnp_CreateDate' => now()->format('YmdHis'),
            'vnp_ExpireDate' => now()->addMinutes(15)->format('YmdHis'),
        ];

        ksort($vnpParams);
        $query     = http_build_query($vnpParams);
        $hmac      = hash_hmac('sha512', $query, $this->hashSecret);
        return $this->url.'?'.$query.'&vnp_SecureHash='.$hmac;
    }

    public function verifyReturn(array $data): bool
    {
        $secureHash = $data['vnp_SecureHash'] ?? '';
        unset($data['vnp_SecureHash'], $data['vnp_SecureHashType']);
        ksort($data);
        $query    = http_build_query($data);
        $hmac     = hash_hmac('sha512', $query, $this->hashSecret);
        return hash_equals($hmac, $secureHash);
    }

    public function isSuccess(array $data): bool
    {
        return ($data['vnp_ResponseCode'] ?? '') === '00';
    }
}