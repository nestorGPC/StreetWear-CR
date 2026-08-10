<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de Pedidos</title>

    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            color: #222;
        }

        h1 {
            text-align: center;
            margin-bottom: 20px;
        }

        .filtros {
            margin-bottom: 20px;
        }

        .filtros p {
            margin: 4px 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            border: 1px solid #999;
            padding: 7px;
            text-align: left;
        }

        th {
            background-color: #eeeeee;
        }

        .total {
            margin-top: 20px;
            text-align: right;
            font-weight: bold;
        }
    </style>
</head>

<body>

    <h1>Reporte de Pedidos</h1>

    <div class="filtros">
        <p>
            <strong>Desde:</strong>
            {{ $desde ?: 'Todos' }}
        </p>

        <p>
            <strong>Hasta:</strong>
            {{ $hasta ?: 'Todos' }}
        </p>

        <p>
            <strong>Estado:</strong>
            {{ $estado ?: 'Todos' }}
        </p>
    </div>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Cliente</th>
                <th>Estado</th>
                <th>Total</th>
                <th>Fecha</th>
            </tr>
        </thead>

        <tbody>
            @forelse ($orders as $order)
                <tr>
                    <td>{{ $order->id }}</td>

                    <td>
                        {{ $order->user->name ?? 'Cliente eliminado' }}
                    </td>

                    <td>
                        {{ $order->status }}
                    </td>

                    <td>
                        ₡{{ number_format($order->total, 2) }}
                    </td>

                    <td>
                        {{ $order->created_at->format('d/m/Y H:i') }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5">
                        No se encontraron pedidos con los filtros seleccionados.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="total">
        Total de pedidos: {{ $orders->count() }}
    </div>

</body>
</html>
