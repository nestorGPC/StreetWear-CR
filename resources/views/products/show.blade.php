@extends('layouts.app')

@section('title', $product->name . ' | StreetWear CR')

@section('content')

<div class="mb-4">

    <a
        href="{{ route('products.index') }}"
        class="text-decoration-none text-dark"
    >
        ← Volver al catálogo
    </a>

</div>


<div class="row g-5 align-items-start">

    {{-- IMAGEN --}}
    <div class="col-12 col-lg-6">

        <div class="card border-0 shadow-sm overflow-hidden">

            @if ($product->image)

                <img
                    src="{{ asset('storage/' . $product->image) }}"
                    class="img-fluid w-100"
                    style="max-height: 550px; object-fit: cover;"
                    alt="{{ $product->name }}"
                >

            @else

                <div
                    class="bg-light d-flex align-items-center justify-content-center"
                    style="height: 450px;"
                >
                    <span class="text-muted">
                        Producto sin imagen
                    </span>
                </div>

            @endif

        </div>

    </div>


    {{-- INFORMACIÓN --}}
    <div class="col-12 col-lg-6">

        <span class="badge bg-secondary mb-3">
            {{ $product->category->name }}
        </span>

        <h1 class="display-5 fw-bold mb-3">
            {{ $product->name }}
        </h1>

        <h2 class="fw-bold mb-4">
            ₡{{ number_format($product->price, 0, ',', '.') }}
        </h2>


        <p class="text-muted fs-5">
            {{ $product->description }}
        </p>


        <hr>


        {{-- DISPONIBILIDAD --}}
        @if ($product->stock > 0)

            <p class="text-success fw-semibold mb-4">
                ✓ Producto disponible
            </p>

        @else

            <p class="text-danger fw-semibold mb-4">
                Producto agotado
            </p>

        @endif


        {{-- BOTONES --}}
        @if ($product->stock > 0)

            <div class="d-grid gap-2">

                <form
                    action="{{ route('cart.add', $product) }}"
                    method="POST"
                >

                    @csrf

                    <button
                        type="submit"
                        class="btn btn-dark btn-lg w-100"
                    >
                        Agregar al carrito
                    </button>

                </form>


                {{-- Lo conectaremos al checkout --}}
                <a
                    href="{{ route('cart.index') }}"
                    class="btn btn-outline-dark btn-lg"
                >
                    Ir al carrito
                </a>

            </div>

        @else

            <button
                class="btn btn-secondary btn-lg w-100"
                disabled
            >
                Producto no disponible
            </button>

        @endif

    </div>

</div>

@endsection