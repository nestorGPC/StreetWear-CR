<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with('category')
            ->where('active', true);

        // Buscar por nombre
        if ($request->filled('search')) {
            $query->where(
                'name',
                'like',
                '%' . $request->search . '%'
            );
        }

        // Filtrar por categoría
        if ($request->filled('category')) {
            $query->where(
                'category_id',
                $request->category
            );
        }

        // Precio mínimo
        if ($request->filled('min_price')) {
            $query->where(
                'price',
                '>=',
                $request->min_price
            );
        }

        // Precio máximo
        if ($request->filled('max_price')) {
            $query->where(
                'price',
                '<=',
                $request->max_price
            );
        }

        $products = $query->get();

        $categories = Category::orderBy('name')->get();

        return view(
            'products.index',
            compact('products', 'categories')
        );
    }

    public function show(Product $product)
    {
        $product->load('category');

        return view(
            'products.show',
            compact('product')
        );
    }
}