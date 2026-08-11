<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AccountTest extends TestCase
{
    use RefreshDatabase;


    private function crearCliente(): User
    {
        return User::factory()->create();
    }


    private function crearPedidoConItems(User $cliente): Order
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

        $order = Order::factory()->create([
            'user_id' => $cliente->id,
        ]);

        OrderItem::factory()->create([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'product_name' => $product->name,
            'price' => 15000,
            'quantity' => 2,
            'subtotal' => 30000,
        ]);

        return $order;
    }


    public function test_un_cliente_puede_ver_el_detalle_de_su_pedido(): void
    {
        $cliente = $this->crearCliente();
        $order = $this->crearPedidoConItems($cliente);

        $response = $this->actingAs($cliente)
            ->get('/mi-cuenta/pedidos/' . $order->id);

        $response->assertStatus(200);
        $response->assertSee($order->tracking_number);
        $response->assertSee('Camiseta Oversize Negra');
    }


    public function test_un_cliente_no_puede_ver_el_pedido_de_otro_cliente(): void
    {
        $cliente = $this->crearCliente();
        $otroCliente = $this->crearCliente();

        $order = Order::factory()->create([
            'user_id' => $otroCliente->id,
        ]);

        $response = $this->actingAs($cliente)
            ->get('/mi-cuenta/pedidos/' . $order->id);

        $response->assertStatus(403);
    }


    public function test_un_invitado_no_puede_ver_sus_pedidos(): void
    {
        $response = $this->get('/mi-cuenta/pedidos');

        $response->assertRedirect(route('login'));
    }


    public function test_un_cliente_puede_actualizar_su_perfil(): void
    {
        $cliente = $this->crearCliente();

        $response = $this->actingAs($cliente)
            ->put('/mi-cuenta/perfil', [
                'name' => 'Nombre Nuevo',
                'email' => $cliente->email,
            ]);

        $response->assertRedirect(route('account.profile'));

        $this->assertEquals(
            'Nombre Nuevo',
            $cliente->fresh()->name
        );
    }


    public function test_no_se_puede_usar_un_email_de_otro_usuario(): void
    {
        $cliente = $this->crearCliente();
        $otroCliente = $this->crearCliente();

        $response = $this->actingAs($cliente)
            ->put('/mi-cuenta/perfil', [
                'name' => 'Nombre Nuevo',
                'email' => $otroCliente->email,
            ]);

        $response->assertSessionHasErrors('email');
    }
}
