{{-- filepath: resources/views/vehiculos/show.blade.php --}}
@extends('layouts.app')

@section('title', $vehiculo->modelo)

@section('content')
<div class="container py-4">
    <div class="card shadow-lg">
        <div class="row g-0">
            <div class="col-md-6">
                <img src="{{ asset('storage/' . $vehiculo->imagen) }}" alt="{{ $vehiculo->modelo }}" class="img-fluid rounded-start" style="width:100%;object-fit:cover;max-height:400px;">
            </div>
            <div class="col-md-6 p-4">
                <h2>{{ $vehiculo->modelo }}</h2>
                <p><strong>Matrícula:</strong> {{ $vehiculo->matricula }}</p>
                <p><strong>Capacidad:</strong> {{ $vehiculo->capacidad_personas }} personas</p>
                <p><strong>Número de camas:</strong> {{ $vehiculo->numero_camas }}</p>
                <p><strong>Precio por día:</strong> {{ number_format($vehiculo->precio_dia, 2) }} €</p>
                <p><strong>Descripción:</strong> {{ $vehiculo->descripcion ?? 'Sin descripción.' }}</p>
                <hr>
                <h5>Fechas reservadas próximamente:</h5>
                <ul>
                    @forelse($fechasReservadas as $f)
                        <li>{{ \Carbon\Carbon::parse($f->fecha_inicio)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($f->fecha_fin)->format('d/m/Y') }}</li>
                    @empty
                        <li>No hay reservas próximas.</li>
                    @endforelse
                </ul>
                <a href="{{ url()->previous() }}" class="btn btn-secondary mt-3">Volver</a>
            </div>
        </div>
    </div>
</div>
@endsection