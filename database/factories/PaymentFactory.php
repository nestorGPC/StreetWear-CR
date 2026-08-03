<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class PaymentFactory extends Factory
{
    public function definition(): array
    {
        return [
            'method' => 'card',
            'status' => 'pending',
            'transaction_id' => null,
            'amount' => 0,
            'paid_at' => null,
        ];
    }
}
