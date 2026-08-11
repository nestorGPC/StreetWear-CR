<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaymentTest extends TestCase
{
    use RefreshDatabase;


    public function test_el_checkout_crea_un_pago_en_estado_pendiente(): void
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


        $user = User::factory()->create();


        $this->actingAs($user)
            ->post('/carrito/agregar/' . $product->id);


        session([
            'checkout_token' => 'token-checkout-test',
        ]);

        $this->actingAs($user)
            ->post('/checkout', [
                'shipping_address' => 'San José, Costa Rica',
                'payment_method' => 'paypal',
                'checkout_token' => 'token-checkout-test',
            ]);


        $order = Order::first();


        $this->assertNotNull($order->payment);

        $this->assertEquals(
            'pending',
            $order->payment->status
        );


        $this->assertEquals(
            'paypal',
            $order->payment->method
        );


        $this->assertNull(
            $order->payment->transaction_id
        );


        $this->assertNull(
            $order->payment->paid_at
        );
    }



    public function test_se_puede_actualizar_el_estado_de_un_pago(): void
    {
        $user = User::factory()->create();


        $order = Order::factory()->create([
            'user_id' => $user->id,
        ]);


        $payment = Payment::factory()->create([
            'order_id' => $order->id,
        ]);


        $payment->update([
            'status' => 'paid',
            'transaction_id' => 'TEST-ABC123',
            'paid_at' => now(),
        ]);


        $this->assertEquals(
            'paid',
            $payment->fresh()->status
        );


        $this->assertEquals(
            'TEST-ABC123',
            $payment->fresh()->transaction_id
        );
    }



    public function test_el_pago_pertenece_al_pedido_correcto(): void
    {
        $user = User::factory()->create();


        $order = Order::factory()->create([
            'user_id' => $user->id,
        ]);


        $payment = Payment::factory()->create([
            'order_id' => $order->id,
        ]);


        $this->assertEquals(
            $order->id,
            $payment->order->id
        );


        $this->assertEquals(
            $payment->id,
            $order->payment->id
        );
    }
}
