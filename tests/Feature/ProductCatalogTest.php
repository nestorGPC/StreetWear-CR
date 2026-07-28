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
}