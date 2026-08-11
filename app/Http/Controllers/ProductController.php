<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;

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
            $category = Category::find($request->category);

            if ($category) {
                $query->where('category_id', $category->id);
            }
        }

        // Precio mínimo
        if (
            $request->filled('min_price')
            && is_numeric($request->min_price)
            && $request->min_price >= 0
        ) {
            $query->where(
                'price',
                '>=',
                $request->min_price
            );
        }

        // Precio máximo
        if (
            $request->filled('max_price')
            && is_numeric($request->max_price)
            && $request->max_price >= 0
        ) {
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

        /*
         * Obtenemos la cookie con los productos vistos.
         * Si todavía no existe, utilizamos un arreglo vacío.
         */
        $recentIds = json_decode(
            request()->cookie('recent_products', '[]'),
            true
        );

        if (!is_array($recentIds)) {
            $recentIds = [];
        }

        /*
         * Quitamos el producto actual para evitar duplicados.
         */
        $recentIds = array_values(
            array_filter(
                $recentIds,
                fn ($id) => (int) $id !== $product->id
            )
        );

        /*
         * Agregamos el producto actual al inicio.
         */
        array_unshift(
            $recentIds,
            $product->id
        );

        /*
         * Guardamos solamente los últimos 5 productos.
         */
        $recentIds = array_slice(
            $recentIds,
            0,
            5
        );

        /*
         * La cookie durará 30 días.
         */
        Cookie::queue(
            'recent_products',
            json_encode($recentIds),
            60 * 24 * 30
        );

        /*
         * Para mostrar productos recientes quitamos
         * el producto que estamos viendo actualmente.
         */
        $viewedIds = array_values(
            array_filter(
                $recentIds,
                fn ($id) => (int) $id !== $product->id
            )
        );

        $recentProducts = Product::with('category')
            ->where('active', true)
            ->whereIn('id', $viewedIds)
            ->get()
            ->sortBy(
                fn ($recentProduct) =>
                    array_search(
                        $recentProduct->id,
                        $viewedIds
                    )
            )
            ->values();

        return view(
            'products.show',
            compact(
                'product',
                'recentProducts'
            )
        );
    }
}