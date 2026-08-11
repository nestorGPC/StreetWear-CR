<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CheckoutIdempotencyTest extends TestCase
{
    use RefreshDatabase;


    private function crearProducto(): Product
    {
        $category = Category::create([
            'name' => 'Ropa',
            'description' => 'Ropa urbana',
        ]);

        return Product::create([
            'category_id' => $category->id,
            'name' => 'Camiseta Oversize Negra',
            'description' => 'Producto de prueba',
            'price' => 15000,
            'stock' => 10,
            'active' => true,
        ]);
    }


    public function test_un_doble_post_no_crea_dos_pedidos(): void
    {
        $user = User::factory()->create();
        $product = $this->crearProducto();

        $this->actingAs($user)
            ->post('/carrito/agregar/' . $product->id);

        session([
            'checkout_token' => 'token-checkout-test',
        ]);

        $data = [
            'shipping_address' => 'San José, Costa Rica',
            'payment_method' => 'card',
            'checkout_token' => 'token-checkout-test',
        ];

        $this->actingAs($user)->post('/checkout', $data);

        /*
         * El token se consume en el primer POST; el segundo
         * debe rechazarse sin crear otro pedido.
         */
        $this->actingAs($user)->post('/checkout', $data);

        $this->assertDatabaseCount('orders', 1);
        $this->assertDatabaseCount('payments', 1);
    }


    public function test_el_checkout_rechaza_un_token_invalido(): void
    {
        $user = User::factory()->create();
        $product = $this->crearProducto();

        $this->actingAs($user)
            ->post('/carrito/agregar/' . $product->id);

        session([
            'checkout_token' => 'token-real',
        ]);

        $response = $this->actingAs($user)
            ->post('/checkout', [
                'shipping_address' => 'San José, Costa Rica',
                'payment_method' => 'card',
                'checkout_token' => 'token-falso',
            ]);

        $response->assertRedirect(route('cart.index'));

        $this->assertDatabaseCount('orders', 0);
        $this->assertDatabaseCount('payments', 0);
    }
}
