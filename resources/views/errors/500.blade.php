<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Error del servidor | StreetWear CR</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: system-ui, -apple-system, "Segoe UI", Roboto, sans-serif;
            background-color: #f8f9fa;
            color: #212529;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            padding: 2rem;
        }
        .container { max-width: 520px; }
        .codigo {
            font-size: 6rem;
            font-weight: 800;
            line-height: 1;
            color: #fd7e14;
        }
        h1 { font-size: 1.6rem; margin: 1rem 0 0.5rem; }
        p { color: #6c757d; margin-bottom: 1.75rem; }
        a {
            display: inline-block;
            background-color: #212529;
            color: #fff;
            text-decoration: none;
            padding: 0.75rem 1.75rem;
            border-radius: 0.375rem;
            font-weight: 600;
        }
        a:hover { background-color: #343a40; }
        a:focus-visible { outline: 3px solid #0d6efd; outline-offset: 2px; }
    </style>
</head>

<body>
    <div class="container">
        <div class="codigo">500</div>
        <h1>Ups, algo salió mal</h1>
        <p>Ocurrió un error interno. Intenta de nuevo más tarde.</p>
        <a href="/">Volver a la tienda</a>
    </div>
</body>

</html>
