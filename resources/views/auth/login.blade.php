@extends('layouts.app')

@section('title', 'Iniciar sesión | StreetWear CR')

@section('content')

<div class="row justify-content-center">

    <div class="col-12 col-md-8 col-lg-5">

        <div class="card border-0 shadow">

            <div class="card-body p-4 p-md-5">

                <div class="text-center mb-4">

                    <h1 class="h2 fw-bold">
                        StreetWear CR
                    </h1>

                    <p class="text-muted">
                        Inicia sesión en tu cuenta de cliente.
                    </p>

                </div>


                @if ($errors->any())

                    <div class="alert alert-danger">

                        @foreach ($errors->all() as $error)

                            <div>
                                {{ $error }}
                            </div>

                        @endforeach

                    </div>

                @endif


                <form
                    method="POST"
                    action="{{ route('login.store') }}"
                >

                    @csrf


                    <div class="mb-3">

                        <label
                            for="email"
                            class="form-label"
                        >
                            Correo electrónico
                        </label>

                        <input
                            type="email"
                            id="email"
                            name="email"
                            class="form-control"
                            value="{{ old('email') }}"
                            required
                            autofocus
                        >

                    </div>


                    <div class="mb-3">

                        <label
                            for="password"
                            class="form-label"
                        >
                            Contraseña
                        </label>

                        <input
                            type="password"
                            id="password"
                            name="password"
                            class="form-control"
                            required
                        >

                    </div>


                    <div class="form-check mb-4">

                        <input
                            type="checkbox"
                            id="remember"
                            name="remember"
                            class="form-check-input"
                        >

                        <label
                            for="remember"
                            class="form-check-label"
                        >
                            Recordarme
                        </label>

                    </div>


                    <button
                        type="submit"
                        class="btn btn-dark w-100 py-2"
                    >
                        Iniciar sesión
                    </button>

                </form>


                <hr class="my-4">


                <p class="text-center mb-2">

                    ¿Todavía no tienes una cuenta?

                </p>

                <a
                    href="{{ route('register') }}"
                    class="btn btn-outline-dark w-100"
                >
                    Crear cuenta
                </a>


                <div class="text-center mt-4">

                    <small class="text-muted">
                        ¿Eres administrador?
                    </small>

                    <br>

                    <a href="{{ url('/admin') }}">
                        Acceder al panel administrativo
                    </a>

                </div>

            </div>

        </div>

    </div>

</div>

@endsection