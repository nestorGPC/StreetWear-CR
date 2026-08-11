@extends('layouts.app')

@section('title', 'StreetWear CR | Catálogo')

@section('content')

{{-- ENCABEZADO DE LA TIENDA --}}
<div class="text-center mb-5">

    <h1 class="display-5 fw-bold">
        StreetWear CR
    </h1>

    <p class="lead text-muted">
        Ropa, tenis, gorras y accesorios para tu estilo.
    </p>

</div>


{{-- BUSCADOR Y FILTROS --}}
<div class="card shadow-sm mb-5">

    <div class="card-body">

        <h5 class="fw-bold mb-3">
            Buscar productos
        </h5>

        <form
            method="GET"
            action="{{ route('products.index') }}"
            class="row g-3"
        >

            {{-- Buscar por nombre --}}
            <div class="col-12 col-md-4">

                <label class="form-label">
                    Producto
                </label>

                <input
                    type="text"
                    name="search"
                    class="form-control"
                    placeholder="Ej: Nike, gorra, camiseta..."
                    value="{{ request('search') }}"
                >

            </div>


            {{-- Filtrar por categoría --}}
            <div class="col-12 col-md-3">

                <label class="form-label">
                    Categoría
                </label>

                <select
                    name="category"
                    class="form-select"
                >

                    <option value="">
                        Todas las categorías
                    </option>

                    @foreach ($categories as $category)

                        <option
                            value="{{ $category->id }}"
                            @selected(request('category') == $category->id)
                        >
                            {{ $category->name }}
                        </option>

                    @endforeach

                </select>

            </div>


            {{-- Precio mínimo --}}
            <div class="col-6 col-md-2">

                <label class="form-label">
                    Precio mínimo
                </label>

                <input
                    type="number"
                    name="min_price"
                    class="form-control"
                    placeholder="₡0"
                    min="0"
                    value="{{ request('min_price') }}"
                >

            </div>


            {{-- Precio máximo --}}
            <div class="col-6 col-md-2">

                <label class="form-label">
                    Precio máximo
                </label>

                <input
                    type="number"
                    name="max_price"
                    class="form-control"
                    placeholder="₡100000"
                    min="0"
                    value="{{ request('max_price') }}"
                >

            </div>


            {{-- Botón buscar --}}
            <div class="col-12 col-md-1 d-flex align-items-end">

                <button
                    type="submit"
                    class="btn btn-dark w-100"
                >
                    Buscar
                </button>

            </div>

        </form>


        {{-- Limpiar filtros --}}
        <div class="mt-3">

            <a
                href="{{ route('products.index') }}"
                class="btn btn-outline-secondary"
            >
                Limpiar filtros
            </a>

        </div>

    </div>

</div>


{{-- TÍTULO DEL CATÁLOGO --}}
<div class="d-flex justify-content-between align-items-center mb-4">

    <div>

        <h2 class="fw-bold mb-1">
            Catálogo de productos
        </h2>

        <p class="text-muted mb-0">
            Descubre nuestros productos disponibles.
        </p>

    </div>

    <span class="badge bg-dark fs-6">
        {{ $products->count() }} productos
    </span>

</div>


{{-- PRODUCTOS --}}
<div class="row g-4">

    @forelse ($products as $product)

        <div class="col-12 col-md-6 col-lg-4">

            <div class="card h-100 shadow-sm product-card">

                <div class="position-relative">

                    @if ($product->image)

                        <img
                            src="{{ asset('storage/' . $product->image) }}"
                            class="product-image card-img-top"
                            alt="{{ $product->name }}"
                        >

                    @else

                        <div
                            class="product-image bg-light d-flex align-items-center justify-content-center"
                        >
                            <span class="text-muted">
                                Sin imagen
                            </span>
                        </div>

                    @endif


                    @if ($product->stock <= 0)

                        <span
                            class="badge bg-danger position-absolute top-0 start-0 m-2"
                        >
                            Agotado
                        </span>

                    @endif

                </div>


                <div class="card-body d-flex flex-column">

                    {{-- Categoría --}}
                    <span class="badge bg-secondary align-self-start mb-2">

                        {{ $product->category->name }}

                    </span>


                    {{-- Nombre --}}
                    <h5 class="card-title fw-bold">

                        {{ $product->name }}

                    </h5>


                    {{-- Descripción --}}
                    <p class="card-text text-muted">

                        {{ \Illuminate\Support\Str::limit(
                            $product->description ?? '',
                            100
                        ) }}

                    </p>


                    {{-- Precio --}}
                    <h4 class="fw-bold mt-auto">

                        ₡{{ number_format(
                            $product->price,
                            0,
                            ',',
                            '.'
                        ) }}

                    </h4>


                    {{-- Stock --}}
                    @if ($product->stock > 0)

                        <p class="text-success mb-2">
                            Disponible: {{ $product->stock }}
                        </p>

                    @else

                        <p class="text-danger fw-bold mb-2">
                            Agotado
                        </p>

                    @endif


                    {{-- Ver producto --}}
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

            <div class="empty-state">

                <div class="empty-icon mb-3" aria-hidden="true">
                    🔍
                </div>

                <h3 class="fw-bold">
                    No encontramos productos
                </h3>

                <p class="text-muted">
                    Prueba con otros términos o limpia los filtros.
                </p>

                <a
                    href="{{ route('products.index') }}"
                    class="btn btn-dark"
                >
                    Ver todos los productos
                </a>

            </div>

        </div>

    @endforelse

</div>

@endsection