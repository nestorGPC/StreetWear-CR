<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;

class OrderSeeder extends Seeder
{

    public function run(): void
    {

        $cliente = User::where(
            'email',
            'cliente@streetwearcr.test'
        )->first();


        if (!$cliente) {
            return;
        }


        /*
         * Si el cliente ya tiene pedidos de una ejecución anterior,
         * no duplicamos datos al volver a ejecutar el seeder.
         */
        if ($cliente->orders()->exists()) {
            return;
        }


        $productos = Product::all();


        if ($productos->count() < 5) {
            return;
        }



        $this->crearPedido(

            $cliente,

            'pending',

            [
                [$productos[0],2],
                [$productos[1],1]
            ],

            'pending',

            'card'

        );




        $this->crearPedido(

            $cliente,

            'processing',

            [
                [$productos[2],1]
            ],

            'paid',

            'paypal'

        );





        $this->crearPedido(

            $cliente,

            'shipped',

            [
                [$productos[3],1],
                [$productos[4],1]
            ],

            'paid',

            'card'

        );


    }



    private function crearPedido(
        $cliente,
        $estado,
        $productos,
        $estadoPago,
        $metodoPago
    )
    {


        $order = Order::factory()->create([

            'user_id'=>$cliente->id,

            'status'=>$estado,

            'shipping_address'=>'San José, Costa Rica'

        ]);



        $subtotal = 0;



        foreach($productos as $item)
        {

            $producto = $item[0];

            $cantidad = $item[1];


            $subtotalItem =
            $producto->price * $cantidad;



            OrderItem::factory()->create([


                'order_id'=>$order->id,

                'product_id'=>$producto->id,

                'product_name'=>$producto->name,

                'price'=>$producto->price,

                'quantity'=>$cantidad,

                'subtotal'=>$subtotalItem


            ]);



            $subtotal += $subtotalItem;

        }



        $tax = $subtotal * 0.13;

        $shipping = 3000;

        $total =
        $subtotal + $tax + $shipping;



        $order->update([

            'subtotal'=>$subtotal,

            'tax'=>$tax,

            'shipping'=>$shipping,

            'total'=>$total

        ]);





        Payment::factory()->create([


            'order_id'=>$order->id,

            'method'=>$metodoPago,

            'status'=>$estadoPago,

            'transaction_id'=>

            $estadoPago == 'paid'

            ? 'TEST-'.strtoupper(uniqid())

            : null,


            'amount'=>$total,


            'paid_at'=>

            $estadoPago == 'paid'

            ? now()

            : null


        ]);



    }

}
