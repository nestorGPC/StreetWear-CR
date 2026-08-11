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

@php
    $orderStatuses = [
        'pending' => ['label' => 'Pendiente', 'class' => 'bg-warning text-dark'],
        'processing' => ['label' => 'Preparando', 'class' => 'bg-info text-dark'],
        'shipped' => ['label' => 'Enviado', 'class' => 'bg-primary'],
        'delivered' => ['label' => 'Entregado', 'class' => 'bg-success'],
        'cancelled' => ['label' => 'Cancelado', 'class' => 'bg-danger'],
    ];
    $orderBadge = $orderStatuses[$order->status] ?? ['label' => $order->status, 'class' => 'bg-secondary'];
@endphp

<div class="card mb-3 shadow-sm border-0">

    <div class="card-body p-4">

        <div class="d-flex flex-wrap justify-content-between align-items-start gap-2 mb-2">

            <h5 class="fw-bold mb-0">
                Pedido:
                {{ $order->tracking_number }}
            </h5>


            <span class="badge-status badge {{ $orderBadge['class'] }}">
                {{ $orderBadge['label'] }}
            </span>

        </div>


        <p class="mb-1 text-muted">
            Fecha:
            {{ $order->created_at->format('d/m/Y') }}
        </p>


        <p class="fw-bold fs-5 mb-3">
            Total:
            ₡{{ number_format($order->total, 0, ',', '.') }}
        </p>


        <a
            href="{{ route('account.orders.show', $order) }}"
            class="btn btn-dark"
        >
            Ver detalle
        </a>

    </div>

</div>


@empty

<div class="empty-state">

    <div class="empty-icon mb-3" aria-hidden="true">
        📦
    </div>

    <h3 class="fw-bold">
        Todavía no tienes pedidos
    </h3>

    <p class="text-muted">
        Cuando realices tu primera compra, verás aquí su seguimiento.
    </p>

    <a
        href="{{ route('products.index') }}"
        class="btn btn-dark"
    >
        Ir al catálogo
    </a>

</div>


@endforelse


@endsection
