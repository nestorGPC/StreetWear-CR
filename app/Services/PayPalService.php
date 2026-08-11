<?php

namespace App\Services;

use App\Models\Order;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class PayPalService
{
    public function getAccessToken(): string
    {
        $response = Http::asForm()
            ->withBasicAuth(
                config('paypal.client_id'),
                config('paypal.client_secret')
            )
            ->post(
                config('paypal.base_url') . '/v1/oauth2/token',
                [
                    'grant_type' => 'client_credentials',
                ]
            );

        if (! $response->successful()) {
            throw new RuntimeException(
                'PayPal no pudo generar el token de acceso. ' .
                $this->getPayPalError($response)
            );
        }

        $accessToken = $response->json('access_token');

        if (! $accessToken) {
            throw new RuntimeException(
                'PayPal no devolvió un token de acceso.'
            );
        }

        return $accessToken;
    }

    public function createOrder(Order $order): array
    {
        $montoEnDolares = round(
            $order->total / config('paypal.exchange_rate'),
            2
        );

        $response = Http::withToken(
            $this->getAccessToken()
        )->post(
            config('paypal.base_url') . '/v2/checkout/orders',
            [
                'intent' => 'CAPTURE',

                'purchase_units' => [
                    [
                        'amount' => [
                            'currency_code' => 'USD',
                            'value' => number_format(
                                $montoEnDolares,
                                2,
                                '.',
                                ''
                            ),
                        ],
                    ],
                ],

                'application_context' => [
                    'return_url' => route(
                        'checkout.paypal.return',
                        [
                            'order' => $order->id,
                        ]
                    ),

                    'cancel_url' => route(
                        'checkout.paypal.cancel',
                        [
                            'order' => $order->id,
                        ]
                    ),
                ],
            ]
        );

        if (! $response->successful()) {
            throw new RuntimeException(
                'No se pudo crear la orden en PayPal Sandbox. ' .
                $this->getPayPalError($response)
            );
        }

        $data = $response->json();

        $approveUrl = collect(
            $data['links'] ?? []
        )->firstWhere('rel', 'approve')['href'] ?? null;

        if (! $approveUrl) {
            throw new RuntimeException(
                'PayPal no devolvió un enlace de aprobación.'
            );
        }

        return [
            'id' => $data['id'],
            'approve_url' => $approveUrl,
        ];
    }

    public function captureOrder(
        string $paypalOrderId
    ): array {
        $url =
            config('paypal.base_url') .
            '/v2/checkout/orders/' .
            $paypalOrderId .
            '/capture';

        /*
         * PayPal espera un cuerpo JSON válido
         * en la solicitud de captura.
         */
        $response = Http::withToken(
            $this->getAccessToken()
        )
            ->withBody(
                '{}',
                'application/json'
            )
            ->post($url);

        if (! $response->successful()) {
            throw new RuntimeException(
                'PayPal rechazó la captura. ' .
                $this->getPayPalError($response)
            );
        }

        $data = $response->json();

        $captureId =
            $data['purchase_units'][0]['payments']['captures'][0]['id']
            ?? null;

        return [
            'status' => $data['status'] ?? 'FAILED',
            'capture_id' => $captureId,
        ];
    }

    private function getPayPalError($response): string
    {
        $data = $response->json();

        $name = $data['name'] ?? null;
        $message = $data['message'] ?? null;
        $details = $data['details'][0]['description'] ?? null;
        $debugId = $data['debug_id'] ?? null;

        $error = [];

        if ($name) {
            $error[] = 'Código: ' . $name;
        }

        if ($message) {
            $error[] = 'Mensaje: ' . $message;
        }

        if ($details) {
            $error[] = 'Detalle: ' . $details;
        }

        if ($debugId) {
            $error[] = 'Debug ID: ' . $debugId;
        }

        if (empty($error)) {
            $error[] = 'HTTP ' . $response->status();
        }

        return implode(' | ', $error);
    }
}
