<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class PayOSService
{
    protected $baseUrl;
    protected $clientId;
    protected $apiKey;

    public function __construct()
    {
        $this->baseUrl = env('PAYOS_BASE_URL');
        $this->clientId = env('PAYOS_CLIENT_ID');
        $this->apiKey   = env('PAYOS_API_KEY');
    }

    public function createPayment($orderCode, $amount, $description, $returnUrl, $cancelUrl)
    {
        $response = Http::withHeaders([
            'x-client-id' => $this->clientId,
            'x-api-key'   => $this->apiKey,
        ])->post($this->baseUrl . '/v2/payment-requests', [
            'orderCode' => $orderCode,
            'amount'    => $amount,
            'description' => $description,
            'returnUrl'   => $returnUrl,
            'cancelUrl'   => $cancelUrl,
        ]);

        return $response->json();
    }
}
