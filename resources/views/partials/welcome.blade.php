@extends('layouts.app')

@section('title', 'Inicio - Autocaravanas')

@section('content')
<div class="row">
    <div class="col-md-8 mx-auto">
        <div class="card">
            <div class="card-header">
                <h2>Bienvenido a nuestro sistema de reservas</h2>
            </div>
            <div class="card-body">
                <p>Reserva tu autocaravana fácilmente y disfruta de la libertad de viajar.</p>
                @guest
                <div class="d-grid gap-2">
                    <a href="{{ route('login') }}" class="btn btn-primary btn-lg">
                        Iniciar sesión
                    </a>
                    <a href="{{ route('register') }}" class="btn btn-outline-secondary btn-lg">
                        Registrarse
                    </a>
                </div>
                @endguest
            </div>
        </div>
    </div>
</div>
@endsection
