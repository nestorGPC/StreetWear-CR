@extends('layouts.app')

@section('title', 'Catálogo de productos')

@section('content')

<div class="mb-5">
    <h1 class="fw-bold">Catálogo de productos</h1>

    <p class="text-muted">
        Descubre nuestros productos disponibles.
    </p>
</div>

<div class="row g-4">

    @forelse ($products as $product)

        <div class="col-12 col-md-6 col-lg-4">

            <div class="card h-100 shadow-sm product-card">

                @if ($product->image)
                    <img
                        src="{{ asset('storage/' . $product->image) }}"
                        class="card-img-top"
                        alt="{{ $product->name }}"
                    >
                @endif

                <div class="card-body d-flex flex-column">

                    <span class="badge bg-secondary align-self-start mb-2">
                        {{ $product->category->name }}
                    </span>

                    <h5 class="card-title">
                        {{ $product->name }}
                    </h5>

                    <p class="card-text text-muted">
                        {{ \Illuminate\Support\Str::limit($product->description, 100) }}
                    </p>

                    <h4 class="fw-bold mt-auto">
                        ₡{{ number_format($product->price, 0, ',', '.') }}
                    </h4>

                    <p class="text-muted">
                        Stock: {{ $product->stock }}
                    </p>

                    <a
                        href="{{ route('products.show', $product) }}"
                        class="btn btn-dark w-100"
                    >
                        Ver producto
                    </a>

                </div>

            </div>

        </div>

    @empty

        <div class="col-12">

            <div class="alert alert-info">
                Actualmente no hay productos disponibles.
            </div>

        </div>

    @endforelse

</div>

@endsection