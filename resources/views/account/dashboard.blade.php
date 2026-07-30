@extends('layouts.app')

@section('title', 'Mi cuenta | StreetWear CR')

@section('content')

<div class="mb-5">

    <h1 class="fw-bold">
        Mi cuenta
    </h1>

    <p class="text-muted">
        Bienvenido, {{ auth()->user()->name }}.
    </p>

</div>


<div class="row g-4">


    {{-- PERFIL --}}
    <div class="col-12 col-md-6 col-lg-3">

        <div class="card h-100 shadow-sm border-0">

            <div class="card-body">

                <h5 class="fw-bold">
                    Mi perfil
                </h5>

                <p class="text-muted">
                    Consulta y modifica tus datos personales.
                </p>

                <a
                    href="{{ route('account.profile') }}"
                    class="btn btn-dark"
                >
                    Ver perfil
                </a>

            </div>

        </div>

    </div>


    {{-- PEDIDOS --}}
    <div class="col-12 col-md-6 col-lg-3">

        <div class="card h-100 shadow-sm border-0">

            <div class="card-body">

                <h5 class="fw-bold">
                    Mis pedidos
                </h5>

                <p class="text-muted">
                    Consulta tu historial de compras.
                </p>

                <a
                    href="{{ route('account.orders') }}"
                    class="btn btn-dark"
                >
                    Ver pedidos
                </a>

            </div>

        </div>

    </div>


    {{-- CATÁLOGO --}}
    <div class="col-12 col-md-6 col-lg-3">

        <div class="card h-100 shadow-sm border-0">

            <div class="card-body">

                <h5 class="fw-bold">
                    Productos
                </h5>

                <p class="text-muted">
                    Explora el catálogo de StreetWear CR.
                </p>

                <a
                    href="{{ route('products.index') }}"
                    class="btn btn-dark"
                >
                    Ver productos
                </a>

            </div>

        </div>

    </div>


    {{-- CARRITO --}}
    <div class="col-12 col-md-6 col-lg-3">

        <div class="card h-100 shadow-sm border-0">

            <div class="card-body">

                <h5 class="fw-bold">
                    Mi carrito
                </h5>

                <p class="text-muted">
                    Revisa los productos que deseas comprar.
                </p>

                <a
                    href="{{ route('cart.index') }}"
                    class="btn btn-dark"
                >
                    Ver carrito
                </a>

            </div>

        </div>

    </div>

</div>

@endsection