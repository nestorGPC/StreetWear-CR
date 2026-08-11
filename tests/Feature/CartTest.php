<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CartTest extends TestCase
{
    use RefreshDatabase;


    private function crearProducto(int $stock = 5): Product
    {
        $category = Category::create([
            'name' => 'Ropa',
            'description' => 'Ropa urbana',
        ]);

        return Product::create([
            'category_id' => $category->id,
            'name' => 'Camiseta Oversize Negra',
            'description' => 'Camiseta de prueba',
            'price' => 15000,
            'stock' => $stock,
            'active' => true,
        ]);
    }


    public function test_se_puede_agregar_un_producto_al_carrito(): void
    {
        $product = $this->crearProducto();

        $response = $this->post('/carrito/agregar/' . $product->id);

        $response->assertRedirect(route('cart.index'));

        $this->assertEquals(
            1,
            session('cart')[$product->id]['quantity']
        );
    }


    public function test_no_se_puede_agregar_un_producto_agotado(): void
    {
        $product = $this->crearProducto(0);

        $response = $this->post('/carrito/agregar/' . $product->id);

        $response->assertRedirect();

        $response->assertSessionHas('error');

        $this->assertEmpty(session('cart', []));
    }


    public function test_no_se_puede_superar_el_stock_disponible_al_agregar(): void
    {
        $product = $this->crearProducto(1);

        $this->post('/carrito/agregar/' . $product->id);

        $response = $this->post('/carrito/agregar/' . $product->id);

        $response->assertSessionHas('error');

        $this->assertEquals(
            1,
            session('cart')[$product->id]['quantity']
        );
    }


    public function test_se_puede_actualizar_la_cantidad_de_un_producto(): void
    {
        $product = $this->crearProducto(5);

        $this->post('/carrito/agregar/' . $product->id);

        $response = $this->put('/carrito/actualizar/' . $product->id, [
            'quantity' => 3,
        ]);

        $response->assertRedirect(route('cart.index'));

        $this->assertEquals(
            3,
            session('cart')[$product->id]['quantity']
        );
    }


    public function test_no_se_puede_actualizar_la_cantidad_por_encima_del_stock(): void
    {
        $product = $this->crearProducto(2);

        $this->post('/carrito/agregar/' . $product->id);

        $response = $this->put('/carrito/actualizar/' . $product->id, [
            'quantity' => 10,
        ]);

        $response->assertSessionHasErrors('quantity');
    }


    public function test_no_se_puede_actualizar_un_producto_inactivo(): void
    {
        $product = $this->crearProducto(5);

        $this->post('/carrito/agregar/' . $product->id);

        $product->update(['active' => false]);

        $response = $this->put('/carrito/actualizar/' . $product->id, [
            'quantity' => 2,
        ]);

        $response->assertSessionHas('error');
    }


    public function test_se_puede_eliminar_un_producto_del_carrito(): void
    {
        $product = $this->crearProducto();

        $this->post('/carrito/agregar/' . $product->id);

        $response = $this->delete('/carrito/eliminar/' . $product->id);

        $response->assertRedirect(route('cart.index'));

        $this->assertEmpty(session('cart', []));
    }
}
