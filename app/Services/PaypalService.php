<?php

namespace App\Services;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class PayPalService
{
    // --------------------
    // PayPal API Base URL
    // --------------------
    private function baseUrl(): string
    {
        return config('paypal.mode') === 'live'
            ? 'https://api-m.paypal.com'
            : 'https://api-m.sandbox.paypal.com';
    }

    // --------------------
    // Get Access Token
    // --------------------
    private function accessToken(): string
    {
        return Cache::remember(
            'paypal_access_token',
            now()->addMinutes(50),
            function () {
                $response = Http::asForm()
                    ->withBasicAuth(
                        config('paypal.client_id'),
                        config('paypal.client_secret')
                    )
                    ->post(
                        $this->baseUrl() . '/v1/oauth2/token',
                        [
                            'grant_type' => 'client_credentials',
                        ]
                    )
                    ->throw();

                return $response->json('access_token');
            }
        );
    }

    // --------------------
    // PayPal API Request
    // --------------------
    private function request(string $requestId): PendingRequest
    {
        return Http::acceptJson()
            ->withToken($this->accessToken())
            ->withHeaders([
                'PayPal-Request-Id' => $requestId,
                'Prefer' => 'return=representation',
            ])
            ->timeout(20)
            ->retry(2, 250);
    }

    // --------------------
    // Create PayPal Order
    // --------------------
    public function createOrder(
        float $amount,
        string $requestId
    ): array {
        return $this->request($requestId)
            ->post(
                $this->baseUrl() . '/v2/checkout/orders',
                [
                    'intent' => 'CAPTURE',

                    'purchase_units' => [
                        [
                            'amount' => [
                                'currency_code' =>
                                config('paypal.currency'),

                                'value' => number_format(
                                    $amount,
                                    2,
                                    '.',
                                    ''
                                ),
                            ],
                        ],
                    ],
                ]
            )
            ->throw()
            ->json();
    }

    // --------------------
    // Capture PayPal Order
    // --------------------
    public function captureOrder(
        string $orderId,
        string $requestId
    ): array {
        return $this->request($requestId)
            ->withBody('{}', 'application/json')
            ->post(
                $this->baseUrl()
                    . "/v2/checkout/orders/{$orderId}/capture"
            )
            ->throw()
            ->json();
    }
}
