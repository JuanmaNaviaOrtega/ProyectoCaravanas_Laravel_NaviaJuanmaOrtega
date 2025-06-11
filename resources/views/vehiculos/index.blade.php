@extends('layouts.app')

@section('title', 'Nuestras Autocaravanas')

@section('content')
<div class="container py-5">
    <div class="row mb-4">
        <div class="col-md-6">
            <h1><i class="fas fa-caravan"></i> Nuestras Autocaravanas</h1>
        </div>
        <div class="col-md-6">
            <form action="{{ route('vehiculos.index') }}" method="GET">
                <div class="input-group">
                    <input type="text" name="search" class="form-control" placeholder="Buscar por modelo..." value="{{ request('search') }}">
                    <button class="btn btn-primary" type="submit">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="row">
        @forelse($vehiculos as $vehiculo)
        <div class="col-md-4 mb-4">
            <div class="card h-100 shadow-sm">
                <img src="{{ $vehiculo->imagenUrl() }}" class="card-img-top" alt="{{ $vehiculo->modelo }}" style="height: 200px; object-fit: cover;">
                <div class="card-body">
                    <h5 class="card-title">{{ $vehiculo->modelo }}</h5>
                    <p class="card-text">
                        <i class="fas fa-users"></i> {{ $vehiculo->capacidad_personas }} personas<br>
                        <i class="fas fa-bed"></i> {{ $vehiculo->numero_camas }} camas
                    </p>
                    <p class="card-text">{{ Str::limit($vehiculo->descripcion, 100) }}</p>
                </div>
                <div class="card-footer bg-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="h5 mb-0">{{ number_format($vehiculo->precio_dia, 2) }} €/día</span>
                        <a href="{{ route('vehiculos.show', $vehiculo) }}" class="btn btn-sm btn-outline-primary">
                            Ver detalles
                        </a>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12">
            <div class="alert alert-info">No hay autocaravanas disponibles actualmente</div>
        </div>
        @endforelse
    </div>

    <div class="d-flex justify-content-center mt-4">
        {{ $vehiculos->links() }}
    </div>
</div>
@endsection
