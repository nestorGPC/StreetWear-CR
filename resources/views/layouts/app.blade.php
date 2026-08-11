<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        @yield('title', 'StreetWear CR')
    </title>

    <link
        rel="icon"
        type="image/svg+xml"
        href="{{ asset('favicon.svg') }}"
    >

    @vite([
        'resources/css/app.css',
        'resources/js/app.js'
    ])
</head>

<body class="d-flex flex-column min-vh-100">

    {{-- NAVBAR --}}
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">

        <div class="container">

            {{-- MARCA --}}
            <a
                class="navbar-brand fw-bold"
                href="{{ route('products.index') }}"
            >
                StreetWear CR
            </a>


            {{-- BOTÓN RESPONSIVE --}}
            <button
                class="navbar-toggler"
                type="button"
                data-bs-toggle="collapse"
                data-bs-target="#navbarMenu"
                aria-controls="navbarMenu"
                aria-expanded="false"
                aria-label="Abrir menú"
            >
                <span class="navbar-toggler-icon"></span>
            </button>


            <div
                class="collapse navbar-collapse"
                id="navbarMenu"
            >

                <ul class="navbar-nav ms-auto align-items-lg-center">

                    {{-- PRODUCTOS --}}
                    <li class="nav-item">

                        <a
                            class="nav-link"
                            href="{{ route('products.index') }}"
                        >
                            Productos
                        </a>

                    </li>


                    {{-- CARRITO --}}
                    <li class="nav-item">

                        <a
                            class="nav-link position-relative"
                            href="{{ route('cart.index') }}"
                            aria-label="Carrito de compras"
                            title="Carrito"
                        >

                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                width="22"
                                height="22"
                                fill="currentColor"
                                viewBox="0 0 16 16"
                            >
                                <path d="M0 1.5A.5.5 0 0 1 .5 1H2a.5.5 0 0 1 .485.379L2.89 3H14.5a.5.5 0 0 1 .491.592l-1.5 8A.5.5 0 0 1 13 12H4a.5.5 0 0 1-.491-.408L1.61 2H.5a.5.5 0 0 1-.5-.5zM3.102 4l1.313 7h8.17l1.313-7H3.102z"/>
                                <path d="M5 12a2 2 0 1 0 0 4 2 2 0 0 0 0-4zm7 0a2 2 0 1 0 0 4 2 2 0 0 0 0-4z"/>
                            </svg>


                            @php
                                $cartCount = collect(session('cart', []))
                                    ->sum('quantity');
                            @endphp


                            @if ($cartCount > 0)

                                <span
                                    class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger"
                                >
                                    {{ $cartCount }}
                                </span>

                            @endif

                        </a>

                    </li>


                    {{-- USUARIO NO AUTENTICADO --}}
                    @guest

                        <li class="nav-item dropdown">

                            <a
                                class="nav-link dropdown-toggle"
                                href="#"
                                id="cuentaDropdown"
                                role="button"
                                data-bs-toggle="dropdown"
                                aria-expanded="false"
                            >
                                Cuenta
                            </a>

                            <ul
                                class="dropdown-menu dropdown-menu-end"
                                aria-labelledby="cuentaDropdown"
                            >

                                <li>
                                    <a
                                        class="dropdown-item"
                                        href="{{ route('login') }}"
                                    >
                                        Iniciar sesión
                                    </a>
                                </li>

                                <li>
                                    <a
                                        class="dropdown-item"
                                        href="{{ route('register') }}"
                                    >
                                        Crear cuenta
                                    </a>
                                </li>

                            </ul>

                        </li>

                    @endguest


                    {{-- USUARIO AUTENTICADO --}}
                    @auth

                        <li class="nav-item dropdown">

                            <a
                                class="nav-link dropdown-toggle"
                                href="#"
                                id="usuarioDropdown"
                                role="button"
                                data-bs-toggle="dropdown"
                                aria-expanded="false"
                            >
                                {{ auth()->user()->name }}
                            </a>


                            <ul
                                class="dropdown-menu dropdown-menu-end"
                                aria-labelledby="usuarioDropdown"
                            >

                                {{-- INFORMACIÓN --}}
                                <li>

                                    <span class="dropdown-item-text">

                                        <strong>
                                            {{ auth()->user()->name }}
                                        </strong>

                                        <br>

                                        <small class="text-muted">
                                            {{ auth()->user()->email }}
                                        </small>

                                    </span>

                                </li>


                                <li>
                                    <hr class="dropdown-divider">
                                </li>


                                {{-- MI CUENTA --}}
                                <li>

                                    <a
                                        class="dropdown-item"
                                        href="{{ route('account.dashboard') }}"
                                    >
                                        Mi cuenta
                                    </a>

                                </li>


                                {{-- MI PERFIL --}}
                                <li>

                                    <a
                                        class="dropdown-item"
                                        href="{{ route('account.profile') }}"
                                    >
                                        Mi perfil
                                    </a>

                                </li>


                                {{-- MIS PEDIDOS --}}
                                <li>

                                    <a
                                        class="dropdown-item"
                                        href="{{ route('account.orders') }}"
                                    >
                                        Mis pedidos
                                    </a>

                                </li>


                                <li>
                                    <hr class="dropdown-divider">
                                </li>


                                {{-- OPCIÓN EXCLUSIVA DEL ADMINISTRADOR --}}
                                @if (auth()->user()->hasRole('super_admin'))

                                    <li>

                                        <a
                                            class="dropdown-item fw-semibold"
                                            href="{{ url('/admin') }}"
                                        >
                                            Panel administrativo
                                        </a>

                                    </li>

                                    <li>
                                        <hr class="dropdown-divider">
                                    </li>

                                @endif


                                {{-- CERRAR SESIÓN --}}
                                <li>

                                    <form
                                        method="POST"
                                        action="{{ route('logout') }}"
                                    >

                                        @csrf

                                        <button
                                            type="submit"
                                            class="dropdown-item text-danger"
                                        >
                                            Cerrar sesión
                                        </button>

                                    </form>

                                </li>

                            </ul>

                        </li>

                    @endauth

                </ul>

            </div>

        </div>

    </nav>


    {{-- CONTENIDO --}}
    <main class="container py-5 flex-grow-1">


        {{-- MENSAJES DE ÉXITO --}}
        @if (session('success'))

            <div
                class="alert alert-success alert-dismissible fade show"
                role="alert"
            >

                {{ session('success') }}

                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="alert"
                    aria-label="Cerrar"
                ></button>

            </div>

        @endif


        {{-- MENSAJES DE ERROR --}}
        @if (session('error'))

            <div
                class="alert alert-danger alert-dismissible fade show"
                role="alert"
            >

                {{ session('error') }}

                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="alert"
                    aria-label="Cerrar"
                ></button>

            </div>

        @endif


        @yield('content')

    </main>


    {{-- FOOTER --}}
    <footer class="bg-dark text-white text-center py-4 mt-auto">

        <div class="container">

            <p class="mb-1 fw-semibold">
                StreetWear CR
            </p>

            <p class="mb-0 small">
                © 2026 Todos los derechos reservados.
            </p>

        </div>

    </footer>

</body>

</html>