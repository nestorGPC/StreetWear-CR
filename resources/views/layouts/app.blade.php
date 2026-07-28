<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>@yield('title', 'Tienda Virtual')</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body>

    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">

            <a class="navbar-brand fw-bold" href="{{ route('products.index') }}">
                Tienda Virtual
            </a>

            <button
                class="navbar-toggler"
                type="button"
                data-bs-toggle="collapse"
                data-bs-target="#navbarMenu"
            >
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarMenu">

                <ul class="navbar-nav ms-auto">

                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('products.index') }}">
                            Productos
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('cart.index') }}">
                            Carrito
                        </a>
                    </li>

                </ul>

            </div>

        </div>
    </nav>


    <main class="container py-5">

        @yield('content')

    </main>


    <footer class="bg-dark text-white text-center py-4 mt-5">

        <p class="mb-0">
            Tienda Virtual © 2026
        </p>

    </footer>

</body>

</html>