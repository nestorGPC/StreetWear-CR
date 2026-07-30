@extends('layouts.app')

@section('title', 'Crear cuenta | StreetWear CR')

@section('content')

<div class="row justify-content-center">

    <div class="col-12 col-md-8 col-lg-5">

        <div class="card border-0 shadow">

            <div class="card-body p-4 p-md-5">

                <div class="text-center mb-4">

                    <h1 class="h2 fw-bold">
                        Crear cuenta
                    </h1>

                    <p class="text-muted">
                        Regístrate como cliente de StreetWear CR.
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
                    action="{{ route('register.store') }}"
                >

                    @csrf


                    <div class="mb-3">

                        <label
                            for="name"
                            class="form-label"
                        >
                            Nombre completo
                        </label>

                        <input
                            type="text"
                            id="name"
                            name="name"
                            class="form-control"
                            value="{{ old('name') }}"
                            required
                            autofocus
                        >

                    </div>


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

                        <small class="text-muted">
                            Mínimo 8 caracteres.
                        </small>

                    </div>


                    <div class="mb-4">

                        <label
                            for="password_confirmation"
                            class="form-label"
                        >
                            Confirmar contraseña
                        </label>

                        <input
                            type="password"
                            id="password_confirmation"
                            name="password_confirmation"
                            class="form-control"
                            required
                        >

                    </div>


                    <button
                        type="submit"
                        class="btn btn-dark w-100 py-2"
                    >
                        Crear cuenta
                    </button>

                </form>


                <hr class="my-4">


                <p class="text-center mb-0">

                    ¿Ya tienes cuenta?

                    <a href="{{ route('login') }}">
                        Iniciar sesión
                    </a>

                </p>

            </div>

        </div>

    </div>

</div>

@endsection