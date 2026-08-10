<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de Ventas</title>

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

        h2 {
            margin-top: 25px;
            margin-bottom: 10px;
        }

        .filtros {
            margin-bottom: 20px;
        }

        .filtros p {
            margin: 4px 0;
        }

        .resumen {
            width: 100%;
            margin-bottom: 20px;
        }

        .resumen td {
            border: 1px solid #999;
            padding: 10px;
            text-align: center;
        }

        .resumen .titulo {
            background-color: #eeeeee;
            font-weight: bold;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
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

        .numero {
            text-align: right;
        }
    </style>
</head>

<body>

    <h1>Reporte de Ventas</h1>

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

    <table class="resumen">
        <tr>
            <td class="titulo">
                Total de pedidos
            </td>

            <td class="titulo">
                Total vendido
            </td>
        </tr>

        <tr>
            <td>
                {{ $totalPedidos }}
            </td>

            <td>
                ₡{{ number_format($totalVendido, 2) }}
            </td>
        </tr>
    </table>

    <h2>Ventas por mes</h2>

    <table>
        <thead>
            <tr>
                <th>Mes</th>
                <th>Cantidad de pedidos</th>
                <th>Total vendido</th>
            </tr>
        </thead>

        <tbody>
            @forelse ($ventasPorMes as $mes => $datos)
                <tr>
                    <td>
                        {{ $mes }}
                    </td>

                    <td>
                        {{ $datos['cantidad_pedidos'] }}
                    </td>

                    <td class="numero">
                        ₡{{ number_format($datos['total_vendido'], 2) }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="3">
                        No hay ventas para mostrar.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <h2>Ventas por cliente</h2>

    <table>
        <thead>
            <tr>
                <th>Cliente</th>
                <th>Cantidad de pedidos</th>
                <th>Total vendido</th>
            </tr>
        </thead>

        <tbody>
            @forelse ($ventasPorCliente as $cliente => $datos)
                <tr>
                    <td>
                        {{ $cliente }}
                    </td>

                    <td>
                        {{ $datos['cantidad_pedidos'] }}
                    </td>

                    <td class="numero">
                        ₡{{ number_format($datos['total_vendido'], 2) }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="3">
                        No hay ventas para mostrar.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

</body>
</html>
