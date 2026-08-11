<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ReportTest extends TestCase
{
    use RefreshDatabase;


    private function crearAdmin(): User
    {
        Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);

        $admin = User::factory()->create();

        $admin->assignRole('super_admin');

        return $admin;
    }


    private function crearCliente(): User
    {
        return User::factory()->create();
    }


    private function crearPedidoPago(User $cliente): Order
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
            'status' => 'pending',
        ]);

        OrderItem::factory()->create([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'product_name' => $product->name,
            'price' => 15000,
            'quantity' => 1,
            'subtotal' => 15000,
        ]);

        $order->update([
            'subtotal' => 15000,
            'tax' => 1950,
            'shipping' => 3000,
            'total' => 19950,
        ]);

        Payment::factory()->create([
            'order_id' => $order->id,
            'method' => 'card',
            'status' => 'pending',
            'amount' => 19950,
        ]);

        return $order;
    }


    public function test_un_cliente_no_puede_ver_la_pagina_de_reportes(): void
    {
        $cliente = $this->crearCliente();

        $response = $this->actingAs($cliente)
            ->get('/reportes');

        $response->assertStatus(403);
    }


    public function test_un_cliente_no_puede_descargar_reportes_pdf(): void
    {
        $cliente = $this->crearCliente();

        $this->crearPedidoPago($cliente);

        $this->actingAs($cliente)
            ->get('/reportes/pedidos')
            ->assertStatus(403);

        $this->actingAs($cliente)
            ->get('/reportes/ventas')
            ->assertStatus(403);

        $this->actingAs($cliente)
            ->get('/reportes/productos')
            ->assertStatus(403);
    }


    public function test_un_invitado_es_redirigido_al_login_en_reportes(): void
    {
        $response = $this->get('/reportes');

        $response->assertRedirect(route('login'));
    }


    public function test_un_admin_puede_descargar_el_reporte_de_pedidos(): void
    {
        $admin = $this->crearAdmin();
        $cliente = $this->crearCliente();

        $this->crearPedidoPago($cliente);

        $response = $this->actingAs($admin)
            ->get('/reportes/pedidos');

        $response->assertStatus(200);

        $this->assertStringContainsString(
            'application/pdf',
            $response->headers->get('Content-Type')
        );
    }
}
