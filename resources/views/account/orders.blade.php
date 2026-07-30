@extends('layouts.app')

@section('title', 'Mis pedidos | StreetWear CR')

@section('content')

<div class="mb-4">

    <h1 class="fw-bold">
        Mis pedidos
    </h1>

    <p class="text-muted">
        Historial de compras realizadas en StreetWear CR.
    </p>

</div>


<div class="alert alert-info">

    Todavía no tienes pedidos registrados.

</div>


<a
    href="{{ route('products.index') }}"
    class="btn btn-dark"
>
    Ir al catálogo
</a>

@endsection