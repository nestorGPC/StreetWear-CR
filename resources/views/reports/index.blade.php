@extends('layouts.app')

@section('title', 'Reportes | StreetWear CR')

@section('content')

<div class="mb-4">

    <h1 class="fw-bold">
        Reportes
    </h1>

    <p class="text-muted">
        Genera reportes en PDF con la información de la tienda.
    </p>

</div>


<div class="card border-0 shadow-sm">

    <div class="card-body p-4">

        <form method="GET" action="{{ route('reports.orders') }}">

            <div class="row g-3 mb-4">

                <div class="col-12 col-md-3">
                    <label class="form-label">Fecha inicial</label>

                    <input
                        type="date"
                        name="desde"
                        class="form-control"
                    >
                </div>


                <div class="col-12 col-md-3">
                    <label class="form-label">Fecha final</label>

                    <input
                        type="date"
                        name="hasta"
                        class="form-control"
                    >
                </div>


                <div class="col-12 col-md-3">
                    <label class="form-label">Estado del pedido</label>

                    <select
                        name="estado"
                        class="form-select"
                    >

                        <option value="">Todos</option>

                        @foreach ($estados as $valor => $etiqueta)

                            <option value="{{ $valor }}">
                                {{ $etiqueta }}
                            </option>

                        @endforeach

                    </select>
                </div>


                <div class="col-12 col-md-3">
                    <label class="form-label">Cliente</label>

                    <select
                        name="cliente"
                        class="form-select"
                    >

                        <option value="">Todos</option>

                        @foreach ($clientes as $cliente)

                            <option value="{{ $cliente->id }}">
                                {{ $cliente->name }}
                            </option>

                        @endforeach

                    </select>
                </div>

            </div>


            <div class="d-flex flex-wrap gap-2">

                <button
                    type="submit"
                    formaction="{{ route('reports.orders') }}"
                    class="btn btn-dark"
                >
                    Descargar reporte de pedidos
                </button>


                <button
                    type="submit"
                    formaction="{{ route('reports.sales') }}"
                    class="btn btn-dark"
                >
                    Descargar reporte de ventas
                </button>


                <button
                    type="submit"
                    formaction="{{ route('reports.products') }}"
                    class="btn btn-dark"
                >
                    Descargar reporte de productos vendidos
                </button>

            </div>

        </form>

    </div>

</div>

@endsection


