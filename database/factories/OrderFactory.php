<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class OrderFactory extends Factory
{
    public function definition(): array
    {
        return [
            'tracking_number' => 'SWCR-' . now()->format('Ymd') . '-' . Str::upper(Str::random(6)),
            'status' => 'pending',
            'subtotal' => 0,
            'tax' => 0,
            'shipping' => 0,
            'total' => 0,
            'shipping_address' => fake()->address(),
        ];
    }
}
