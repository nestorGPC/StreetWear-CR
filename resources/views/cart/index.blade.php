@extends('layouts.app')

@section('title', 'Carrito')

@section('content')

<h1 class="fw-bold mb-4">
    Carrito de compras
</h1>


@if (session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif


@if (session('error'))
    <div class="alert alert-danger">
        {{ session('error') }}
    </div>
@endif


@if ($errors->any())
    <div class="alert alert-danger">
        {{ $errors->first() }}
    </div>
@endif


@if (count($cart) > 0)

    <div class="table-responsive">

        <table class="table align-middle">

            <thead>
                <tr>
                    <th>Producto</th>
                    <th>Precio</th>
                    <th>Cantidad</th>
                    <th>Subtotal</th>
                    <th>Acciones</th>
                </tr>
            </thead>

            <tbody>

                @foreach ($cart as $item)

                    <tr>

                        <td>

                            <div class="d-flex align-items-center gap-3">

                                @if ($item['image'])
                                    <img
                                        src="{{ asset('storage/' . $item['image']) }}"
                                        width="80"
                                        alt="{{ $item['name'] }}"
                                    >
                                @endif

                                <strong>
                                    {{ $item['name'] }}
                                </strong>

                            </div>

                        </td>


                        <td>
                            ₡{{ number_format($item['price'], 0, ',', '.') }}
                        </td>


                        <td>

                            <form
                                action="{{ route('cart.update', $item['id']) }}"
                                method="POST"
                                class="d-flex gap-2"
                            >

                                @csrf
                                @method('PUT')

                                <input
                                    type="number"
                                    name="quantity"
                                    value="{{ $item['quantity'] }}"
                                    min="1"
                                    class="form-control"
                                    style="width: 90px;"
                                >

                                <button
                                    type="submit"
                                    class="btn btn-outline-dark"
                                >
                                    Actualizar
                                </button>

                            </form>

                        </td>


                        <td>

                            ₡{{ number_format(
                                $item['price'] * $item['quantity'],
                                0,
                                ',',
                                '.'
                            ) }}

                        </td>


                        <td>

                            <form
                                action="{{ route('cart.remove', $item['id']) }}"
                                method="POST"
                            >

                                @csrf
                                @method('DELETE')

                                <button
                                    type="submit"
                                    class="btn btn-danger"
                                >
                                    Eliminar
                                </button>

                            </form>

                        </td>

                    </tr>

                @endforeach

            </tbody>

        </table>

    </div>


    <div class="row justify-content-end">

        <div class="col-md-5">

            <div class="card shadow-sm">

                <div class="card-body">

                    <h4 class="mb-4">
                        Resumen de compra
                    </h4>

                    <div class="d-flex justify-content-between">
                        <span>Subtotal:</span>

                        <strong>
                            ₡{{ number_format($subtotal, 0, ',', '.') }}
                        </strong>
                    </div>

                    <a
                        href="{{ route('checkout.index') }}"
                        class="btn btn-dark btn-lg w-100 mt-4"
                    >
                        Continuar con la compra
                    </a>


                    <div class="d-flex justify-content-between">
                        <span>IVA (13%):</span>

                        <strong>
                            ₡{{ number_format($tax, 0, ',', '.') }}
                        </strong>
                    </div>


                    <div class="d-flex justify-content-between">
                        <span>Envío:</span>

                        <strong>
                            ₡{{ number_format($shipping, 0, ',', '.') }}
                        </strong>
                    </div>

                    <hr>


                    <div class="d-flex justify-content-between">

                        <h4>Total:</h4>

                        <h4>
                            ₡{{ number_format($total, 0, ',', '.') }}
                        </h4>

                    </div>

                </div>

            </div>

        </div>

    </div>

@else

    <div class="alert alert-info">
        Tu carrito está vacío.
    </div>

@endif

@endsection