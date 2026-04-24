<?php
namespace App\Services;
use Illuminate\Support\Facades\Http;

class MoMoService
{
    private string $partnerCode;
    private string $accessKey;
    private string $secretKey;
    private string $endpoint;
    private string $returnUrl;
    private string $notifyUrl;

    public function __construct() {
        $this->partnerCode = config('services.momo.partner_code');
        $this->accessKey   = config('services.momo.access_key');
        $this->secretKey   = config('services.momo.secret_key');
        $this->endpoint    = config('services.momo.endpoint');
        $this->returnUrl   = config('services.momo.return_url');
        $this->notifyUrl   = config('services.momo.notify_url');
    }

    public function createPayment(string $orderCode, int $amount, string $orderInfo): array
    {
        $requestId   = $orderCode.'_'.time();
        $extraData   = base64_encode(json_encode(['orderCode' => $orderCode]));
        $redirectUrl = $this->returnUrl;
        $ipnUrl      = $this->notifyUrl;

        $rawHash = "accessKey={$this->accessKey}"
                 . "&amount={$amount}"
                 . "&extraData={$extraData}"
                 . "&ipnUrl={$ipnUrl}"
                 . "&orderId={$requestId}"
                 . "&orderInfo={$orderInfo}"
                 . "&partnerCode={$this->partnerCode}"
                 . "&redirectUrl={$redirectUrl}"
                 . "&requestId={$requestId}"
                 . "&requestType=payWithMethod";

        $signature = hash_hmac('sha256', $rawHash, $this->secretKey);

        $body = [
            'partnerCode' => $this->partnerCode,
            'partnerName' => 'MotoShop',
            'storeId'     => 'MotoShop_Store',
            'requestType' => 'payWithMethod',
            'ipnUrl'      => $ipnUrl,
            'redirectUrl' => $redirectUrl,
            'orderId'     => $requestId,
            'amount'      => $amount,
            'lang'        => 'vi',
            'autoCapture' => true,
            'orderInfo'   => $orderInfo,
            'requestId'   => $requestId,
            'extraData'   => $extraData,
            'signature'   => $signature,
        ];

        $response = Http::withHeaders(['Content-Type' => 'application/json'])
            ->timeout(30)
            ->post($this->endpoint, $body);

        return $response->json();
    }

    public function verifyReturn(array $data): bool
    {
        $rawHash = "accessKey={$this->accessKey}"
                 . "&amount={$data['amount']}"
                 . "&extraData={$data['extraData']}"
                 . "&message={$data['message']}"
                 . "&orderId={$data['orderId']}"
                 . "&orderInfo={$data['orderInfo']}"
                 . "&orderType={$data['orderType']}"
                 . "&partnerCode={$data['partnerCode']}"
                 . "&payType={$data['payType']}"
                 . "&requestId={$data['requestId']}"
                 . "&responseTime={$data['responseTime']}"
                 . "&resultCode={$data['resultCode']}"
                 . "&transId={$data['transId']}";

        $signature = hash_hmac('sha256', $rawHash, $this->secretKey);
        return hash_equals($signature, $data['signature'] ?? '');
    }

    public function isSuccess(array $data): bool
    {
        return ($data['resultCode'] ?? -1) === 0;
    }
}