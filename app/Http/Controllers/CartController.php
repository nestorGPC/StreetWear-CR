<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index()
    {
        $cart = session()->get('cart', []);

        $subtotal = 0;

        foreach ($cart as $item) {
            $subtotal += $item['price'] * $item['quantity'];
        }

        $tax = $subtotal * 0.13;

        $shipping = $subtotal > 0 ? 3000 : 0;

        $total = $subtotal + $tax + $shipping;

        return view('cart.index', compact(
            'cart',
            'subtotal',
            'tax',
            'shipping',
            'total'
        ));
    }

    public function add(Product $product)
    {
        $cart = session()->get('cart', []);

        if ($product->stock <= 0) {
            return back()->with('error', 'Este producto está agotado.');
        }

        if (isset($cart[$product->id])) {

            if ($cart[$product->id]['quantity'] >= $product->stock) {
                return back()->with(
                    'error',
                    'No hay más unidades disponibles.'
                );
            }

            $cart[$product->id]['quantity']++;

        } else {

            $cart[$product->id] = [
                'id' => $product->id,
                'name' => $product->name,
                'price' => $product->price,
                'quantity' => 1,
                'image' => $product->image,
            ];
        }

        session()->put('cart', $cart);

        return redirect()
            ->route('cart.index')
            ->with('success', 'Producto agregado al carrito.');
    }

    public function update(Request $request, Product $product)
    {
        if (! $product->active) {
            return back()->with(
                'error',
                'Este producto ya no está disponible.'
            );
        }

        $request->validate([
            'quantity' => [
                'required',
                'integer',
                'min:1',
                'max:' . $product->stock,
            ],
        ]);

        $cart = session()->get('cart', []);

        if (isset($cart[$product->id])) {
            $cart[$product->id]['quantity'] = $request->quantity;

            session()->put('cart', $cart);
        }

        return redirect()
            ->route('cart.index')
            ->with('success', 'Cantidad actualizada.');
    }

    public function remove(Product $product)
    {
        $cart = session()->get('cart', []);

        if (isset($cart[$product->id])) {
            unset($cart[$product->id]);

            session()->put('cart', $cart);
        }

        return redirect()
            ->route('cart.index')
            ->with('success', 'Producto eliminado del carrito.');
    }
}