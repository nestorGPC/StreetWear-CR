<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderTest extends TestCase
{
    use RefreshDatabase;


    public function test_un_cliente_puede_ver_sus_propios_pedidos(): void
    {
        $cliente = User::factory()->create();


        $order = Order::factory()->create([
            'user_id' => $cliente->id,
        ]);


        $response = $this->actingAs($cliente)
            ->get('/mi-cuenta/pedidos');


        $response->assertStatus(200);


        $response->assertSee(
            $order->tracking_number
        );
    }



    public function test_un_cliente_no_ve_pedidos_de_otro_cliente(): void
    {
        $cliente = User::factory()->create();

        $otroCliente = User::factory()->create();



        $order = Order::factory()->create([
            'user_id' => $otroCliente->id,
        ]);



        $response = $this->actingAs($cliente)
            ->get('/mi-cuenta/pedidos');



        $response->assertStatus(200);



        $response->assertDontSee(
            $order->tracking_number
        );
    }
}
