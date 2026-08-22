<!DOCTYPE html>
<html lang="es">

<head>

    <meta charset="utf-8">

    <title>
        Reporte de ventas - StreetWear CR
    </title>

    <style>

        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 12px;
            color: #222;
        }

        h1 {
            font-size: 18px;
            margin-bottom: 0;
        }

        h2 {
            font-size: 14px;
            margin-top: 25px;
            margin-bottom: 8px;
        }

        .subtitulo {
            color: #666;
            margin-top: 4px;
            margin-bottom: 20px;
        }

        .resumen {
            width: 100%;
            margin-bottom: 10px;
        }

        .resumen td {
            padding: 6px 10px;
            border: 1px solid #ccc;
        }

        table.datos {
            width: 100%;
            border-collapse: collapse;
        }

        table.datos th,
        table.datos td {
            border: 1px solid #ccc;
            padding: 6px 8px;
            text-align: left;
        }

        table.datos th {
            background-color: #222;
            color: #fff;
        }

        .text-right {
            text-align: right;
        }

    </style>

</head>

<body>

    <h1>
        StreetWear CR
    </h1>


    <p class="subtitulo">

        Reporte de ventas —
        Generado el {{ now()->format('d/m/Y H:i') }}

        @if ($desde)

            &middot;
            Desde: {{ $desde }}

        @endif

        @if ($hasta)

            &middot;
            Hasta: {{ $hasta }}

        @endif

        @if ($estado)

            &middot;
            Estado: {{ $estado }}

        @endif

    </p>


    <table class="resumen">

        <tr>

            <td>
                <strong>Cantidad de pedidos:</strong>
                {{ $totalPedidos }}
            </td>

            <td>
                <strong>Total vendido:</strong>
                ₡{{ number_format($totalVendido, 0, ',', '.') }}
            </td>

        </tr>

    </table>


    <h2>
        Ventas por día
    </h2>


    <table class="datos">

        <thead>

            <tr>

                <th>
                    Día
                </th>

                <th>
                    Cantidad de pedidos
                </th>

                <th class="text-right">
                    Total vendido
                </th>

            </tr>

        </thead>


        <tbody>

            @forelse ($ventasPorDia as $dia => $datos)

                <tr>

                    <td>
                        {{ $dia }}
                    </td>

                    <td>
                        {{ $datos['cantidad_pedidos'] }}
                    </td>

                    <td class="text-right">
                        ₡{{ number_format($datos['total_vendido'], 0, ',', '.') }}
                    </td>

                </tr>

            @empty

                <tr>

                    <td colspan="3">
                        No hay ventas que coincidan con los filtros seleccionados.
                    </td>

                </tr>

            @endforelse

        </tbody>

    </table>


    <h2>
        Ventas por mes
    </h2>


    <table class="datos">

        <thead>

            <tr>

                <th>
                    Mes
                </th>

                <th>
                    Cantidad de pedidos
                </th>

                <th class="text-right">
                    Total vendido
                </th>

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

                    <td class="text-right">
                        ₡{{ number_format($datos['total_vendido'], 0, ',', '.') }}
                    </td>

                </tr>

            @empty

                <tr>

                    <td colspan="3">
                        No hay ventas que coincidan con los filtros seleccionados.
                    </td>

                </tr>

            @endforelse

        </tbody>

    </table>


    <h2>
        Ventas por cliente
    </h2>


    <table class="datos">

        <thead>

            <tr>

                <th>
                    Cliente
                </th>

                <th>
                    Cantidad de pedidos
                </th>

                <th class="text-right">
                    Total comprado
                </th>

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

                    <td class="text-right">
                        ₡{{ number_format($datos['total_vendido'], 0, ',', '.') }}
                    </td>

                </tr>

            @empty

                <tr>

                    <td colspan="3">
                        No hay ventas que coincidan con los filtros seleccionados.
                    </td>

                </tr>

            @endforelse

        </tbody>

    </table>

</body>

</html>
