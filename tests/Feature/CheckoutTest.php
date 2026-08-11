<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CheckoutTest extends TestCase
{
    use RefreshDatabase;


    private function crearProducto(
        string $nombre,
        float $precio,
        int $stock
    ): Product
    {
        $category = Category::create([
            'name' => 'Ropa',
            'description' => 'Ropa urbana',
        ]);

        return Product::create([
            'category_id' => $category->id,
            'name' => $nombre,
            'description' => 'Producto de prueba',
            'price' => $precio,
            'stock' => $stock,
            'active' => true,
        ]);
    }


    public function test_un_cliente_puede_completar_el_checkout(): void
    {
        $user = User::factory()->create();

        $camiseta = $this->crearProducto(
            'Camiseta Oversize Negra',
            15000,
            5
        );

        $gorra = $this->crearProducto(
            'Gorra Snapback Negra',
            18000,
            5
        );


        $this->actingAs($user)
            ->post('/carrito/agregar/' . $camiseta->id);


        $this->actingAs($user)
            ->post('/carrito/agregar/' . $gorra->id);


        session([
            'checkout_token' => 'token-checkout-test',
        ]);

        $response = $this->actingAs($user)
            ->post('/checkout', [
                'shipping_address' => 'San José, Costa Rica',
                'payment_method' => 'card',
                'checkout_token' => 'token-checkout-test',
            ]);


        $order = Order::first();


        $response->assertRedirect(
            route('checkout.success', $order)
        );


        $this->assertDatabaseCount('orders', 1);

        $this->assertDatabaseCount('order_items', 2);

        $this->assertDatabaseCount('payments', 1);


        $this->assertEquals(
            $user->id,
            $order->user_id
        );


        $this->assertNotNull(
            $order->tracking_number
        );


        $this->assertEquals(
            33000,
            (float) $order->subtotal
        );


        $this->assertEquals(
            4290,
            (float) $order->tax
        );


        $this->assertEquals(
            3000,
            (float) $order->shipping
        );


        $this->assertEquals(
            40290,
            (float) $order->total
        );


        $this->assertEquals(
            'pending',
            $order->payment->status
        );


        $this->assertEquals(
            'card',
            $order->payment->method
        );


        $camiseta->refresh();
        $gorra->refresh();


        $this->assertEquals(
            4,
            $camiseta->stock
        );


        $this->assertEquals(
            4,
            $gorra->stock
        );


        $this->assertEmpty(
            session('cart', [])
        );
    }



    public function test_no_se_puede_comprar_si_no_hay_suficiente_inventario(): void
    {
        $user = User::factory()->create();


        $producto = $this->crearProducto(
            'Tenis Urbanos',
            48000,
            1
        );


        $this->actingAs($user)
            ->post('/carrito/agregar/' . $producto->id);



        $producto->update([
            'stock' => 0,
        ]);



        session([
            'checkout_token' => 'token-checkout-test',
        ]);

        $response = $this->actingAs($user)
            ->post('/checkout', [
                'shipping_address' => 'San José, Costa Rica',
                'payment_method' => 'card',
                'checkout_token' => 'token-checkout-test',
            ]);



        $response->assertRedirect(
            route('cart.index')
        );



        $this->assertDatabaseCount(
            'orders',
            0
        );


        $this->assertDatabaseCount(
            'order_items',
            0
        );


        $this->assertDatabaseCount(
            'payments',
            0
        );
    }



    public function test_no_se_puede_hacer_checkout_con_el_carrito_vacio(): void
    {
        $user = User::factory()->create();


        $response = $this->actingAs($user)
            ->post('/checkout', [
                'shipping_address' => 'San José, Costa Rica',
                'payment_method' => 'card',
            ]);



        $response->assertRedirect(
            route('cart.index')
        );



        $this->assertDatabaseCount(
            'orders',
            0
        );
    }
}
