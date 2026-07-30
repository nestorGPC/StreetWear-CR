<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        Category::updateOrCreate(
            ['name' => 'Ropa'],
            ['description' => 'Prendas de vestir de estilo urbano.']
        );

        Category::updateOrCreate(
            ['name' => 'Tenis'],
            ['description' => 'Calzado urbano, casual y deportivo.']
        );

        Category::updateOrCreate(
            ['name' => 'Gorras'],
            ['description' => 'Gorras para complementar tu estilo.']
        );

        Category::updateOrCreate(
            ['name' => 'Accesorios'],
            ['description' => 'Accesorios y complementos de moda.']
        );
    }
}