@extends('layouts.app')

@section('title', 'Bienvenido - Autocaravanas')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-10 text-center">
            <div class="hero-section mb-5">
                <h1 class="display-4 mb-4 text-primary">
                    <i class="fas fa-caravan"></i> Una Vida para Disfrutar
                </h1>
                <p class="lead mb-5">Reserva tu autocaravana y descubre la libertad de viajar a tu ritmo</p>
                
                <div class="d-grid gap-3 d-sm-flex justify-content-sm-center mb-5">
                    @guest
                        <a href="{{ route('login') }}" class="btn btn-primary btn-lg px-4 gap-3">
                            <i class="fas fa-sign-in-alt"></i> Iniciar Sesión
                        </a>
                        <a href="{{ route('register') }}" class="btn btn-outline-primary btn-lg px-4">
                            <i class="fas fa-user-plus"></i> Registrarse
                        </a>
                    @else
                        <a href="{{ route('dashboard') }}" class="btn btn-success btn-lg px-4 gap-3">
                            <i class="fas fa-tachometer-alt"></i> Ir al Panel
                        </a>
                        <a href="{{ route('reservas.create') }}" class="btn btn-primary btn-lg px-4">
                            <i class="fas fa-plus-circle"></i> Nueva Reserva
                        </a>
                    @endguest
                </div>
            </div>

            @if($vehiculosDestacados->count())
            <section class="featured-vehicles mb-5">
                <h2 class="mb-4 section-title">
                    <i class="fas fa-star"></i> Nuestras Autocaravanas Destacadas
                </h2>
                <div class="row g-4">
                    @foreach($vehiculosDestacados as $vehiculo)
                    <div class="col-md-4">
                        <div class="card h-100 shadow-sm">
                            <img src="{{ $vehiculo->imagenUrl() }}" class="card-img-top vehicle-image" alt="{{ $vehiculo->modelo }}">
                            <div class="card-body">
                                <h5 class="card-title">{{ $vehiculo->modelo }}</h5>
                                <p class="card-text text-muted">
                                    <i class="fas fa-users"></i> {{ $vehiculo->capacidad_personas }} personas | 
                                    <i class="fas fa-bed"></i> {{ $vehiculo->numero_camas }} camas
                                </p>
                                <p class="card-text">{{ Str::limit($vehiculo->descripcion, 100) }}</p>
                            </div>
                            <div class="card-footer bg-white">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="price-badge">
                                        {{ number_format($vehiculo->precio_dia, 2) }} €/día
                                    </span>
                                    @auth
                                    <a href="{{ route('reservas.create', ['vehiculo_id' => $vehiculo->id]) }}" 
                                       class="btn btn-sm btn-outline-primary">
                                        Reservar
                                    </a>
                                    @endauth
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </section>
            @endif

            <section class="about-us mt-5 pt-4 border-top">
                <h2 class="mb-4 section-title">
                    <i class="fas fa-info-circle"></i> ¿Por qué elegirnos?
                </h2>
                <div class="row g-4">
                    <div class="col-md-4">
                        <div class="feature-box p-4 text-center">
                            <i class="fas fa-shield-alt fa-3x text-primary mb-3"></i>
                            <h3>Seguridad</h3>
                            <p>Vehículos revisados y con todos los seguros necesarios para tu tranquilidad.</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="feature-box p-4 text-center">
                            <i class="fas fa-euro-sign fa-3x text-primary mb-3"></i>
                            <h3>Precios competitivos</h3>
                            <p>Las mejores tarifas del mercado con opciones para todos los bolsillos.</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="feature-box p-4 text-center">
                            <i class="fas fa-headset fa-3x text-primary mb-3"></i>
                            <h3>Soporte 24/7</h3>
                            <p>Asistencia en carretera disponible las 24 horas durante tu viaje.</p>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
    .hero-section {
        background: linear-gradient(rgba(255,255,255,0.9), rgba(255,255,255,0.9)), 
                    url('/images/caravan-background.jpg');
        background-size: cover;
        padding: 3rem;
        border-radius: 1rem;
    }
    .vehicle-image {
        height: 200px;
        object-fit: cover;
    }
    .price-badge {
        font-weight: bold;
        color: #2a6496;
    }
    .section-title {
        position: relative;
        padding-bottom: 10px;
    }
    .section-title:after {
        content: "";
        position: absolute;
        bottom: 0;
        left: 50%;
        transform: translateX(-50%);
        width: 80px;
        height: 3px;
        background: #2a6496;
    }
    .feature-box {
        border-radius: 0.5rem;
        transition: transform 0.3s;
    }
    .feature-box:hover {
        transform: translateY(-5px);
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }
</style>
@endsection
