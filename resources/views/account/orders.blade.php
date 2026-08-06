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


@forelse ($orders as $order)

<div class="card mb-3 shadow-sm">

    <div class="card-body">

        <h5 class="fw-bold">
            Pedido:
            {{ $order->tracking_number }}
        </h5>


        <p class="mb-1">
            Fecha:
            {{ $order->created_at->format('d/m/Y') }}
        </p>


        <p class="mb-1">
            Estado:
            {{ $order->status }}
        </p>


        <p class="fw-bold">
            Total:
            ₡{{ number_format($order->total, 0, ',', '.') }}
        </p>


    </div>

</div>


@empty

<div class="alert alert-info">

    Todavía no tienes pedidos registrados.

</div>


<a
    href="{{ route('products.index') }}"
    class="btn btn-dark"
>
    Ir al catálogo
</a>


@endforelse


@endsection
