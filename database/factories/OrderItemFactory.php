<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class OrderItemFactory extends Factory
{
    public function definition(): array
    {
        return [
            'product_id' => null,
            'product_name' => fake()->words(3, true),
            'price' => 0,
            'quantity' => 1,
            'subtotal' => 0,
        ];
    }
}
