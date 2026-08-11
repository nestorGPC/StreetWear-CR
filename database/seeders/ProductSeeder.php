<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $ropa = Category::where('name', 'Ropa')->firstOrFail();
        $tenis = Category::where('name', 'Tenis')->firstOrFail();
        $gorras = Category::where('name', 'Gorras')->firstOrFail();
        $accesorios = Category::where('name', 'Accesorios')->firstOrFail();


        Product::updateOrCreate(
            ['name' => 'Camiseta Oversize Negra'],
            [
                'category_id' => $ropa->id,
                'description' => 'Camiseta de estilo urbano con corte oversize.',
                'price' => 15000,
                'stock' => 20,
                'image' => 'products/seed/camiseta-oversize.svg',
                'active' => true,
            ]
        );


        Product::updateOrCreate(
            ['name' => 'Sudadera StreetWear'],
            [
                'category_id' => $ropa->id,
                'description' => 'Sudadera cómoda para un estilo casual y urbano.',
                'price' => 32000,
                'stock' => 15,
                'image' => 'products/seed/sudadera.svg',
                'active' => true,
            ]
        );


        Product::updateOrCreate(
            ['name' => 'Tenis Urbanos'],
            [
                'category_id' => $tenis->id,
                'description' => 'Tenis cómodos para uso diario.',
                'price' => 48000,
                'stock' => 12,
                'image' => 'products/seed/tenis.svg',
                'active' => true,
            ]
        );


        Product::updateOrCreate(
            ['name' => 'Gorra Snapback Negra'],
            [
                'category_id' => $gorras->id,
                'description' => 'Gorra urbana estilo snapback.',
                'price' => 18000,
                'stock' => 18,
                'image' => 'products/seed/gorra.svg',
                'active' => true,
            ]
        );


        Product::updateOrCreate(
            ['name' => 'Bolso StreetWear'],
            [
                'category_id' => $accesorios->id,
                'description' => 'Bolso compacto para complementar tu outfit.',
                'price' => 22000,
                'stock' => 10,
                'image' => 'products/seed/bolso.svg',
                'active' => true,
            ]
        );
    }
}