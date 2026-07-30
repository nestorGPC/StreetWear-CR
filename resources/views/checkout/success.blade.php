@extends('layouts.app')

@section('title', 'Pedido confirmado | StreetWear CR')

@section('content')

<div class="row justify-content-center">

    <div class="col-12 col-lg-8">

        <div class="card border-0 shadow">

            <div class="card-body p-4 p-md-5">

                {{-- CONFIRMACIÓN --}}
                <div class="text-center mb-5">

                    <div class="display-3 text-success mb-3">
                        ✓
                    </div>

                    <h1 class="fw-bold">
                        ¡Pedido recibido!
                    </h1>

                    <p class="text-muted">
                        Tu pedido fue registrado correctamente en StreetWear CR.
                    </p>

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


                {{-- INFORMACIÓN GENERAL --}}
                <div class="row g-4 mb-4">

                    <div class="col-12 col-md-6">

                        <p class="text-muted mb-1">
                            Cliente
                        </p>

                        <strong>
                            {{ auth()->user()->name }}
                        </strong>

                    </div>


                    <div class="col-12 col-md-6">

                        <p class="text-muted mb-1">
                            Estado del pedido
                        </p>

                        <span class="badge bg-warning text-dark">
                            Pendiente
                        </span>

                    </div>


                    <div class="col-12 col-md-6">

                        <p class="text-muted mb-1">
                            Fecha
                        </p>

                        <strong>
                            {{ $order->created_at->format('d/m/Y H:i') }}
                        </strong>

                    </div>


                    <div class="col-12 col-md-6">

                        <p class="text-muted mb-1">
                            Método de pago
                        </p>

                        <strong>
                            @if ($order->payment?->method === 'card')
                                Tarjeta
                            @elseif ($order->payment?->method === 'paypal')
                                PayPal
                            @else
                                No especificado
                            @endif
                        </strong>

                    </div>

                </div>


                {{-- DIRECCIÓN --}}
                <div class="mb-4">

                    <h5 class="fw-bold">
                        Dirección de envío
                    </h5>

                    <p class="text-muted">
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

                    @if ($order->payment?->status === 'paid')

                        Pagado

                    @elseif ($order->payment?->status === 'failed')

                        Fallido

                    @else

                        Pendiente

                    @endif

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
                        Ver mis pedidos
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