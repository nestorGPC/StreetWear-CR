<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>{{ $product->name }}</title>
</head>

<body>

    <a href="{{ route('products.index') }}">
        ← Volver al catálogo
    </a>

    <h1>{{ $product->name }}</h1>

    @if ($product->image)
        <img
            src="{{ asset('storage/' . $product->image) }}"
            alt="{{ $product->name }}"
            width="400"
        >
    @endif

    <p>
        <strong>Categoría:</strong>
        {{ $product->category->name }}
    </p>

    <p>
        {{ $product->description }}
    </p>

    <h2>
        ₡{{ number_format($product->price, 0, ',', '.') }}
    </h2>

    <p>
        Stock disponible: {{ $product->stock }}
    </p>

    @if ($product->stock > 0)

        <form action="{{ route('cart.add', $product) }}" method="POST">

            @csrf

            <button type="submit">
                Agregar al carrito
            </button>

        </form>

    @else

        <p>
            Producto agotado
        </p>

    @endif

</body>
</html>