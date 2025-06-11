{{-- filepath: resources/views/vehiculos/publico.blade.php --}}
@extends('layouts.app')

@section('title', 'Listado de Caravanas')

@section('content')
<div class="container py-4">
    <h1 class="mb-4">Listado de Caravanas</h1>
    <table class="table">
        <thead>
            <tr>
                <th>Modelo</th>
                <th>Matrícula</th>
                <th>Capacidad</th>
                <th>Precio/día</th>
                <th>Estado</th>
                <th>Imagen</th>
                <th>Ver</th>
            </tr>
        </thead>
        <tbody>
            @foreach($vehiculos as $vehiculo)
            <tr>
                <td>{{ $vehiculo->modelo }}</td>
                <td>{{ $vehiculo->matricula }}</td>
                <td>{{ $vehiculo->capacidad_personas }} pers.</td>
                <td>{{ number_format($vehiculo->precio_dia, 2) }} €</td>
                <td>
                    @if($vehiculo->disponible)
                        <span class="badge bg-success">Disponible</span>
                    @else
                        <span class="badge bg-danger">No disponible</span>
                    @endif
                </td>
                <td>
                    @if($vehiculo->imagen)
                        <img src="{{ asset('storage/' . $vehiculo->imagen) }}" alt="{{ $vehiculo->modelo }}" style="width: 80px; height: 60px; object-fit: cover; border-radius: 6px;">
                    @endif
                </td>
                <td>
                    <a href="{{ route('vehiculos.show', $vehiculo) }}" class="btn btn-info btn-sm">
                        <i class="fas fa-search-plus"></i> Ver en grande
                    </a>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection