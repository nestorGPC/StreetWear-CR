<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductCatalogTest extends TestCase
{
    use RefreshDatabase;


    public function test_catalogo_muestra_productos_activos(): void
    {
        $category = Category::create([
            'name' => 'Electrónica',
            'description' => 'Productos electrónicos',
        ]);

        Product::create([
            'category_id' => $category->id,
            'name' => 'Laptop Dell',
            'description' => 'Laptop de prueba',
            'price' => 425000,
            'stock' => 10,
            'active' => true,
        ]);

        $response = $this->get('/productos');

        $response->assertStatus(200);
        $response->assertSee('Laptop Dell');
        $response->assertSee('Electrónica');
    }


    public function test_el_catalogo_permite_buscar_productos_por_nombre(): void
    {
        $category = Category::create([
            'name' => 'Ropa',
            'description' => 'Ropa urbana',
        ]);

        Product::create([
            'category_id' => $category->id,
            'name' => 'Camiseta Oversize Negra',
            'description' => 'Camiseta de prueba',
            'price' => 15000,
            'stock' => 10,
            'active' => true,
        ]);

        Product::create([
            'category_id' => $category->id,
            'name' => 'Gorra Snapback Negra',
            'description' => 'Gorra de prueba',
            'price' => 18000,
            'stock' => 10,
            'active' => true,
        ]);

        $response = $this->get('/productos?search=Camiseta');

        $response->assertStatus(200);
        $response->assertSee('Camiseta Oversize Negra');
        $response->assertDontSee('Gorra Snapback Negra');
    }


    public function test_el_catalogo_permite_filtrar_por_categoria(): void
    {
        $ropa = Category::create([
            'name' => 'Ropa',
            'description' => 'Ropa urbana',
        ]);

        $calzado = Category::create([
            'name' => 'Calzado',
            'description' => 'Zapatos',
        ]);

        Product::create([
            'category_id' => $ropa->id,
            'name' => 'Sudadera StreetWear',
            'description' => 'Sudadera de prueba',
            'price' => 32000,
            'stock' => 5,
            'active' => true,
        ]);

        Product::create([
            'category_id' => $calzado->id,
            'name' => 'Tenis Urbanos',
            'description' => 'Tenis de prueba',
            'price' => 48000,
            'stock' => 5,
            'active' => true,
        ]);

        $response = $this->get('/productos?category=' . $ropa->id);

        $response->assertStatus(200);
        $response->assertSee('Sudadera StreetWear');
        $response->assertDontSee('Tenis Urbanos');
    }


    public function test_el_catalogo_permite_filtrar_por_rango_de_precio(): void
    {
        $category = Category::create([
            'name' => 'Ropa',
            'description' => 'Ropa urbana',
        ]);

        Product::create([
            'category_id' => $category->id,
            'name' => 'Camiseta Económica',
            'description' => 'Camiseta barata',
            'price' => 8000,
            'stock' => 5,
            'active' => true,
        ]);

        Product::create([
            'category_id' => $category->id,
            'name' => 'Chaqueta Premium',
            'description' => 'Chaqueta cara',
            'price' => 60000,
            'stock' => 5,
            'active' => true,
        ]);

        $response = $this->get('/productos?min_price=5000&max_price=10000');

        $response->assertStatus(200);
        $response->assertSee('Camiseta Económica');
        $response->assertDontSee('Chaqueta Premium');
    }


    public function test_se_puede_ver_el_detalle_de_un_producto(): void
    {
        $category = Category::create([
            'name' => 'Ropa',
            'description' => 'Ropa urbana',
        ]);

        $product = Product::create([
            'category_id' => $category->id,
            'name' => 'Bolso StreetWear',
            'description' => 'Bolso de prueba',
            'price' => 22000,
            'stock' => 5,
            'active' => true,
        ]);

        $response = $this->get('/productos/' . $product->id);

        $response->assertStatus(200);
        $response->assertSee('Bolso StreetWear');
    }
}
