<?php

namespace App\Services;

class CartCalculator
{
    public function subtotal(array $cart): float
    {
        $subtotal = 0;

        foreach ($cart as $item) {
            $subtotal += $item['price'] * $item['quantity'];
        }

        return $subtotal;
    }

    public function tax(float $subtotal): float
    {
        return $subtotal * 0.13;
    }

    public function shipping(float $subtotal): float
    {
        return $subtotal > 0 ? 3000 : 0;
    }

    public function total(float $subtotal): float
    {
        return $subtotal
            + $this->tax($subtotal)
            + $this->shipping($subtotal);
    }
}