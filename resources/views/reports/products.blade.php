<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de Productos</title>

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
        }

        th {
            background-color: #eeeeee;
            text-align: left;
        }

        .numero {
            text-align: right;
        }

        .total {
            margin-top: 20px;
            text-align: right;
            font-weight: bold;
        }
    </style>
</head>

<body>

    <h1>Reporte de Productos</h1>

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
                <th>Producto</th>
                <th>Cantidad vendida</th>
                <th>Precio promedio</th>
                <th>Total generado</th>
            </tr>
        </thead>

        <tbody>
            @forelse ($productos as $nombre => $datos)
                <tr>
                    <td>
                        {{ $nombre }}
                    </td>

                    <td>
                        {{ $datos['cantidad_vendida'] }}
                    </td>

                    <td class="numero">
                        ₡{{ number_format($datos['precio'], 2) }}
                    </td>

                    <td class="numero">
                        ₡{{ number_format($datos['total_generado'], 2) }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4">
                        No hay productos vendidos para mostrar.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="total">
        Productos diferentes: {{ $productos->count() }}
    </div>

</body>
</html>
