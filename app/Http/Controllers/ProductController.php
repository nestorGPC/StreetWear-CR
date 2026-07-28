<?php

namespace App\Http\Controllers;

use App\Models\Product;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::with('category')
            ->where('active', true)
            ->get();

        return view('products.index', compact('products'));
    }

    public function show(Product $product)
    {
        $product->load('category');

        return view('products.show', compact('product'));
    }
}