@extends('layouts.app')

@section('title', 'Finalizar compra | StreetWear CR')

@section('content')

<div class="mb-5">

    <h1 class="fw-bold">
        Finalizar compra
    </h1>

    <p class="text-muted">
        Revisa tu pedido y completa los datos de envío.
    </p>

</div>


@if ($errors->any())

    <div class="alert alert-danger">

        <ul class="mb-0">

            @foreach ($errors->all() as $error)

                <li>
                    {{ $error }}
                </li>

            @endforeach

        </ul>

    </div>

@endif


<form
    method="POST"
    action="{{ route('checkout.store') }}"
>

    @csrf

    <input
        type="hidden"
        name="checkout_token"
        value="{{ $checkoutToken }}"
    >

    <div class="row g-4">

        {{-- DATOS DE COMPRA --}}
        <div class="col-12 col-lg-7">

            {{-- CLIENTE --}}
            <div class="card border-0 shadow-sm mb-4">

                <div class="card-body p-4">

                    <h4 class="fw-bold mb-3">
                        Cliente
                    </h4>

                    <p class="mb-1">
                        <strong>
                            {{ auth()->user()->name }}
                        </strong>
                    </p>

                    <p class="text-muted mb-0">
                        {{ auth()->user()->email }}
                    </p>

                </div>

            </div>


            {{-- DIRECCIÓN --}}
            <div class="card border-0 shadow-sm mb-4">

                <div class="card-body p-4">

                    <h4 class="fw-bold mb-3">
                        Dirección de envío
                    </h4>

                    <label
                        for="shipping_address"
                        class="form-label"
                    >
                        Dirección completa
                    </label>

                    <textarea
                        id="shipping_address"
                        name="shipping_address"
                        class="form-control"
                        rows="5"
                        placeholder="Provincia, cantón, distrito, señas exactas..."
                        required
                    >{{ old('shipping_address') }}</textarea>

                </div>

            </div>


            {{-- MÉTODO DE PAGO --}}
            <div class="card border-0 shadow-sm">

                <div class="card-body p-4">

                    <h4 class="fw-bold mb-3">
                        Método de pago
                    </h4>


                    <div class="form-check border rounded p-3 mb-3">

                        <input
                            class="form-check-input ms-0 me-2"
                            type="radio"
                            name="payment_method"
                            id="card"
                            value="card"
                            @checked(old('payment_method') === 'card')
                            required
                        >

                        <label
                            class="form-check-label"
                            for="card"
                        >
                            Tarjeta de crédito o débito
                        </label>

                    </div>


                    <div class="form-check border rounded p-3">

                        <input
                            class="form-check-input ms-0 me-2"
                            type="radio"
                            name="payment_method"
                            id="paypal"
                            value="paypal"
                            @checked(old('payment_method') === 'paypal')
                        >

                        <label
                            class="form-check-label"
                            for="paypal"
                        >
                            PayPal
                        </label>

                    </div>


                    <div class="alert alert-warning mt-4 mb-0">

                        El pago está actualmente en modo de demostración.
                        No introduzcas números reales de tarjetas.

                    </div>

                </div>

            </div>

        </div>


        {{-- RESUMEN --}}
        <div class="col-12 col-lg-5">

            <div class="card border-0 shadow-sm">

                <div class="card-body p-4">

                    <h4 class="fw-bold mb-4">
                        Resumen del pedido
                    </h4>


                    @foreach ($cart as $item)

                        <div class="d-flex justify-content-between mb-3">

                            <div>

                                <strong>
                                    {{ $item['name'] }}
                                </strong>

                                <br>

                                <small class="text-muted">
                                    Cantidad:
                                    {{ $item['quantity'] }}
                                </small>

                            </div>

                            <span>

                                ₡{{ number_format(
                                    $item['price'] * $item['quantity'],
                                    0,
                                    ',',
                                    '.'
                                ) }}

                            </span>

                        </div>

                    @endforeach


                    <hr>


                    <div class="d-flex justify-content-between mb-2">

                        <span>
                            Subtotal
                        </span>

                        <span>
                            ₡{{ number_format($subtotal, 0, ',', '.') }}
                        </span>

                    </div>


                    <div class="d-flex justify-content-between mb-2">

                        <span>
                            Impuestos
                        </span>

                        <span>
                            ₡{{ number_format($tax, 0, ',', '.') }}
                        </span>

                    </div>


                    <div class="d-flex justify-content-between mb-3">

                        <span>
                            Envío
                        </span>

                        <span>
                            ₡{{ number_format($shipping, 0, ',', '.') }}
                        </span>

                    </div>


                    <hr>


                    <div class="d-flex justify-content-between fs-4 fw-bold mb-4">

                        <span>
                            Total
                        </span>

                        <span>
                            ₡{{ number_format($total, 0, ',', '.') }}
                        </span>

                    </div>


                    <button
                        type="submit"
                        class="btn btn-dark btn-lg w-100"
                    >
                        Confirmar pedido
                    </button>


                    <a
                        href="{{ route('cart.index') }}"
                        class="btn btn-outline-secondary w-100 mt-2"
                    >
                        Volver al carrito
                    </a>

                </div>

            </div>

        </div>

    </div>

</form>

@endsection