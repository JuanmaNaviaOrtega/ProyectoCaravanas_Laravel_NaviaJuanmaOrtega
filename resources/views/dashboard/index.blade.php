@extends('layouts.app')

@section('title', 'Panel de Control')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h3 class="mb-0">
                        <i class="fas fa-tachometer-alt"></i> Panel de Control
                    </h3>
                </div>

                <div class="card-body">
                    <h4 class="mb-4">Bienvenido, {{ auth()->user()->name }}</h4>
                    
                    <!-- Sección de Reservas Activas -->
                    <div class="mb-5">
                        <h5 class="border-bottom pb-2">
                            <i class="fas fa-calendar-check"></i> Tus Reservas Activas
                        </h5>
                        
                        @if($reservasActivas->count())
                        <div class="table-responsive mt-3">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Vehículo</th>
                                        <th>Fecha Inicio</th>
                                        <th>Fecha Fin</th>
                                        <th>Total</th>
                                        <th>Estado</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($reservasActivas as $reserva)
                                    <tr>
                                        <td>
                                            <a href="{{ route('vehiculos.show', $reserva->vehiculo) }}">
                                                {{ $reserva->vehiculo->modelo }}
                                            </a>
                                        </td>
                                        <td>{{ $reserva->fecha_inicio->format('d/m/Y') }}</td>
                                        <td>{{ $reserva->fecha_fin->format('d/m/Y') }}</td>
                                        <td>{{ number_format($reserva->precio_total, 2) }} €</td>
                                        <td>
                                            <span class="badge bg-{{ 
                                                $reserva->estado == 'confirmada' ? 'success' : 
                                                ($reserva->estado == 'pendiente' ? 'warning' : 'danger') 
                                            }}">
                                                {{ ucfirst($reserva->estado) }}
                                            </span>
                                        </td>
                                        <td>
                                            <a href="{{ route('reservas.show', $reserva) }}" 
                                               class="btn btn-sm btn-outline-primary" title="Ver">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('reservas.edit', $reserva) }}" 
                                               class="btn btn-sm btn-outline-warning" title="Editar">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('reservas.destroy', $reserva) }}" method="POST" style="display:inline;">
                                                @csrf
                                                @method('DELETE')
                                                <button class="btn btn-sm btn-outline-danger" title="Eliminar" onclick="return confirm('¿Seguro que quieres eliminar esta reserva?')">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @else
                        <div class="alert alert-info mt-3">
                            <i class="fas fa-info-circle"></i> No tienes reservas activas actualmente.
                        </div>
                        @endif
                    </div>

                    <!-- Sección de Vehículos Disponibles -->
                    <div class="mt-5">
                        <h5 class="border-bottom pb-2">
                            <i class="fas fa-car"></i> Vehículos Disponibles
                        </h5>
                        
                        @if($vehiculosDisponibles->count())
                        <div class="row mt-3">
                            @foreach($vehiculosDisponibles as $vehiculo)
                            <div class="col-md-4 mb-4">
                                <div class="card h-100">
                                    <img src="{{ $vehiculo->imagenUrl() }}" 
                                         class="card-img-top" 
                                         alt="{{ $vehiculo->modelo }}"
                                         style="height: 180px; object-fit: cover;">
                                    <div class="card-body">
                                        <h5 class="card-title">{{ $vehiculo->modelo }}</h5>
                                        <p class="card-text">
                                            <i class="fas fa-users"></i> {{ $vehiculo->capacidad_personas }} personas<br>
                                            <i class="fas fa-bed"></i> {{ $vehiculo->numero_camas }} camas
                                        </p>
                                        <p class="h5 text-primary">
                                            {{ number_format($vehiculo->precio_dia, 2) }} €/día
                                        </p>
                                    </div>
                                    <div class="card-footer bg-white">
                                        <a href="{{ route('reservas.create', ['vehiculo_id' => $vehiculo->id]) }}" 
                                           class="btn btn-primary w-100">
                                            <i class="fas fa-calendar-plus"></i> Reservar
                                        </a>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        @else
                        <div class="alert alert-warning mt-3">
                            <i class="fas fa-exclamation-triangle"></i> No hay vehículos disponibles actualmente.
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection