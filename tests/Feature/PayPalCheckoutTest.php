<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class PayPalCheckoutTest extends TestCase
{
    use RefreshDatabase;

    private function crearProductoYAgregarloAlCarrito(User $user): Product
    {
        $category = Category::create([
            'name' => 'Ropa',
            'description' => 'Ropa urbana',
        ]);

        $product = Product::create([
            'category_id' => $category->id,
            'name' => 'Camiseta Oversize Negra',
            'description' => 'Producto de prueba',
            'price' => 15000,
            'stock' => 5,
            'active' => true,
        ]);

        $this->actingAs($user)->post('/carrito/agregar/' . $product->id);

        return $product;
    }

    private function fakeCrearOrdenPayPal(): void
    {
        Http::fake([
            '*/v1/oauth2/token' => Http::response([
                'access_token' => 'FAKE_TOKEN',
            ], 200),

            '*/v2/checkout/orders' => Http::response([
                'id' => 'PAYPAL-ORDER-ID-123',
                'status' => 'CREATED',
                'links' => [
                    [
                        'href' => 'https://www.sandbox.paypal.com/checkoutnow?token=PAYPAL-ORDER-ID-123',
                        'rel' => 'approve',
                        'method' => 'GET',
                    ],
                ],
            ], 200),
        ]);
    }

    private function pagarConPaypal(User $user): Order
    {
        session([
            'checkout_token' => 'token-checkout-test',
        ]);

        $this->actingAs($user)->post('/checkout', [
            'shipping_address' => 'San José, Costa Rica, 100 metros sur del parque',
            'payment_method' => 'paypal',
            'checkout_token' => 'token-checkout-test',
        ]);

        return Order::first();
    }

    public function test_el_checkout_con_paypal_redirige_a_la_pagina_de_aprobacion(): void
    {
        $user = User::factory()->create();
        $this->crearProductoYAgregarloAlCarrito($user);
        $this->fakeCrearOrdenPayPal();

        session([
            'checkout_token' => 'token-checkout-test',
        ]);

        $response = $this->actingAs($user)->post('/checkout', [
            'shipping_address' => 'San José, Costa Rica, 100 metros sur del parque',
            'payment_method' => 'paypal',
            'checkout_token' => 'token-checkout-test',
        ]);

        $response->assertRedirect(
            'https://www.sandbox.paypal.com/checkoutnow?token=PAYPAL-ORDER-ID-123'
        );

        $this->assertDatabaseCount('orders', 1);
        $this->assertDatabaseCount('payments', 1);

        $order = Order::first();

        $this->assertEquals('pending', $order->payment->status);
        $this->assertNull($order->payment->transaction_id);
    }

    public function test_el_retorno_exitoso_de_paypal_marca_el_pago_como_pagado(): void
    {
        $user = User::factory()->create();
        $this->crearProductoYAgregarloAlCarrito($user);

        Http::fake([
            '*/v1/oauth2/token' => Http::response([
                'access_token' => 'FAKE_TOKEN',
            ], 200),

            '*/v2/checkout/orders' => Http::response([
                'id' => 'PAYPAL-ORDER-ID-123',
                'status' => 'CREATED',
                'links' => [
                    [
                        'href' => 'https://www.sandbox.paypal.com/checkoutnow?token=PAYPAL-ORDER-ID-123',
                        'rel' => 'approve',
                        'method' => 'GET',
                    ],
                ],
            ], 200),

            '*/v2/checkout/orders/*/capture' => Http::response([
                'id' => 'PAYPAL-ORDER-ID-123',
                'status' => 'COMPLETED',
                'purchase_units' => [
                    [
                        'payments' => [
                            'captures' => [
                                ['id' => 'CAPTURE-ID-456', 'status' => 'COMPLETED'],
                            ],
                        ],
                    ],
                ],
            ], 200),
        ]);

        $order = $this->pagarConPaypal($user);

        $response = $this->actingAs($user)->get(
            '/checkout/paypal/return/' . $order->id . '?token=PAYPAL-ORDER-ID-123'
        );

        $response->assertRedirect(route('checkout.success', $order));

        $order->refresh();

        $this->assertEquals('paid', $order->payment->status);
        $this->assertEquals('CAPTURE-ID-456', $order->payment->transaction_id);
        $this->assertNotNull($order->payment->paid_at);
        $this->assertEquals('processing', $order->status);
    }

    public function test_la_cancelacion_en_paypal_marca_el_pago_como_fallido_sin_afectar_el_pedido(): void
    {
        $user = User::factory()->create();
        $this->crearProductoYAgregarloAlCarrito($user);
        $this->fakeCrearOrdenPayPal();

        $order = $this->pagarConPaypal($user);

        $response = $this->actingAs($user)->get(
            '/checkout/paypal/cancel/' . $order->id
        );

        $response->assertRedirect(route('checkout.success', $order));

        $order->refresh();

        $this->assertEquals('failed', $order->payment->status);
        $this->assertEquals('pending', $order->status);
        $this->assertDatabaseCount('orders', 1);
        $this->assertDatabaseCount('order_items', 1);
    }

    public function test_un_cliente_no_puede_confirmar_el_pago_de_otro_cliente(): void
    {
        $user = User::factory()->create();
        $otroUsuario = User::factory()->create();

        $this->crearProductoYAgregarloAlCarrito($user);
        $this->fakeCrearOrdenPayPal();

        $order = $this->pagarConPaypal($user);

        $response = $this->actingAs($otroUsuario)->get(
            '/checkout/paypal/return/' . $order->id . '?token=PAYPAL-ORDER-ID-123'
        );

        $response->assertStatus(403);
    }
}
