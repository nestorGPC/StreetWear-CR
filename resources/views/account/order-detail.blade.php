@extends('layouts.app')

@section('title', 'Pedido ' . $order->tracking_number . ' | StreetWear CR')

@php
    $orderStatuses = [
        'pending' => ['label' => 'Pendiente', 'class' => 'bg-warning text-dark'],
        'processing' => ['label' => 'Preparando', 'class' => 'bg-info text-dark'],
        'shipped' => ['label' => 'Enviado', 'class' => 'bg-primary'],
        'delivered' => ['label' => 'Entregado', 'class' => 'bg-success'],
        'cancelled' => ['label' => 'Cancelado', 'class' => 'bg-danger'],
    ];

    $paymentStatuses = [
        'pending' => ['label' => 'Pendiente', 'class' => 'bg-warning text-dark'],
        'paid' => ['label' => 'Pagado', 'class' => 'bg-success'],
        'failed' => ['label' => 'Fallido', 'class' => 'bg-danger'],
        'refunded' => ['label' => 'Reembolsado', 'class' => 'bg-secondary'],
    ];

    $orderBadge = $orderStatuses[$order->status] ?? ['label' => $order->status, 'class' => 'bg-secondary'];
    $paymentBadge = $paymentStatuses[$order->payment?->status] ?? ['label' => $order->payment?->status ?? 'Sin pago', 'class' => 'bg-secondary'];
@endphp

@section('content')

<div class="row justify-content-center">

    <div class="col-12 col-lg-8">

        <div class="card border-0 shadow">

            <div class="card-body p-4 p-md-5">

                {{-- ENCABEZADO --}}
                <div class="d-flex justify-content-between align-items-start mb-4">

                    <div>

                        <h1 class="fw-bold mb-1">
                            Pedido
                            {{ $order->tracking_number }}
                        </h1>

                        <p class="text-muted mb-0">
                            Realizado el
                            {{ $order->created_at->format('d/m/Y H:i') }}
                        </p>

                    </div>

                    <span class="badge {{ $orderBadge['class'] }} fs-6">
                        {{ $orderBadge['label'] }}
                    </span>

                </div>


                {{-- SEGUIMIENTO --}}
                <div class="alert alert-success">

                    <div class="fw-bold mb-1">
                        Número de seguimiento
                    </div>

                    <span class="fs-5">
                        {{ $order->tracking_number }}
                    </span>

                </div>


                {{-- DIRECCIÓN --}}
                <div class="mb-4">

                    <h5 class="fw-bold">
                        Dirección de envío
                    </h5>

                    <p class="text-muted mb-0">
                        {{ $order->shipping_address }}
                    </p>

                </div>


                {{-- PRODUCTOS --}}
                <h4 class="fw-bold mb-3">
                    Productos
                </h4>

                <div class="mb-4">

                    @foreach ($order->items as $item)

                        <div
                            class="d-flex justify-content-between align-items-center border-bottom py-3"
                        >

                            <div>

                                <strong>
                                    {{ $item->product_name }}
                                </strong>

                                <br>

                                <small class="text-muted">
                                    Cantidad: {{ $item->quantity }}
                                    ×
                                    ₡{{ number_format(
                                        $item->price,
                                        0,
                                        ',',
                                        '.'
                                    ) }}
                                </small>

                            </div>


                            <strong>

                                ₡{{ number_format(
                                    $item->subtotal,
                                    0,
                                    ',',
                                    '.'
                                ) }}

                            </strong>

                        </div>

                    @endforeach

                </div>


                {{-- RESUMEN --}}
                <div class="card bg-light border-0">

                    <div class="card-body">

                        <div class="d-flex justify-content-between mb-2">

                            <span>
                                Subtotal
                            </span>

                            <span>

                                ₡{{ number_format(
                                    $order->subtotal,
                                    0,
                                    ',',
                                    '.'
                                ) }}

                            </span>

                        </div>


                        <div class="d-flex justify-content-between mb-2">

                            <span>
                                Impuestos
                            </span>

                            <span>

                                ₡{{ number_format(
                                    $order->tax,
                                    0,
                                    ',',
                                    '.'
                                ) }}

                            </span>

                        </div>


                        <div class="d-flex justify-content-between mb-3">

                            <span>
                                Envío
                            </span>

                            <span>

                                ₡{{ number_format(
                                    $order->shipping,
                                    0,
                                    ',',
                                    '.'
                                ) }}

                            </span>

                        </div>


                        <hr>


                        <div class="d-flex justify-content-between fs-4 fw-bold">

                            <span>
                                Total
                            </span>

                            <span>

                                ₡{{ number_format(
                                    $order->total,
                                    0,
                                    ',',
                                    '.'
                                ) }}

                            </span>

                        </div>

                    </div>

                </div>


                {{-- PAGO --}}
                <div class="alert alert-warning mt-4">

                    <strong>
                        Estado del pago:
                    </strong>

                    <span class="badge {{ $paymentBadge['class'] }}">
                        {{ $paymentBadge['label'] }}
                    </span>

                    <br>

                    <small>
                        El sistema de pago se encuentra actualmente en modo de demostración.
                    </small>

                </div>


                {{-- ACCIONES --}}
                <div class="d-grid gap-2 d-md-flex mt-4">

                    <a
                        href="{{ route('account.orders') }}"
                        class="btn btn-dark"
                    >
                        Volver a mis pedidos
                    </a>

                    <a
                        href="{{ route('products.index') }}"
                        class="btn btn-outline-dark"
                    >
                        Seguir comprando
                    </a>

                </div>

            </div>

        </div>

    </div>

</div>

@endsection
