@extends('layouts.app')

@section('title', 'Mi perfil | StreetWear CR')

@section('content')

<div class="row justify-content-center">

    <div class="col-12 col-lg-7">

        <div class="card shadow-sm border-0">

            <div class="card-body p-4">

                <h1 class="h3 fw-bold mb-4">
                    Mi perfil
                </h1>


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
                    action="{{ route('account.profile.update') }}"
                >

                    @csrf
                    @method('PUT')


                    <div class="mb-3">

                        <label class="form-label">
                            Nombre
                        </label>

                        <input
                            type="text"
                            name="name"
                            class="form-control"
                            value="{{ old('name', auth()->user()->name) }}"
                            required
                        >

                    </div>


                    <div class="mb-4">

                        <label class="form-label">
                            Correo electrónico
                        </label>

                        <input
                            type="email"
                            name="email"
                            class="form-control"
                            value="{{ old('email', auth()->user()->email) }}"
                            required
                        >

                    </div>


                    <button
                        type="submit"
                        class="btn btn-dark"
                    >
                        Guardar cambios
                    </button>

                    <a
                        href="{{ route('account.dashboard') }}"
                        class="btn btn-outline-secondary"
                    >
                        Volver
                    </a>

                </form>

            </div>

        </div>

    </div>

</div>

@endsection