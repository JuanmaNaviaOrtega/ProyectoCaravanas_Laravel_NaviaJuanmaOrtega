@extends('layouts.app')

@section('title', 'Panel de Administración')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-8">
        <h1 class="text-3xl font-bold text-gray-800">Panel de Administración</h1>
        <div class="text-sm text-gray-500">Bienvenido, {{ auth()->user()->name }}</div>
    </div>

    <!-- Estadísticas -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="font-medium text-gray-500 mb-2">Total Usuarios</h3>
            <p class="text-3xl font-bold text-indigo-600">{{ $stats['total_users'] ?? 0 }}</p>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="font-medium text-gray-500 mb-2">Vehículos Registrados</h3>
            <p class="text-3xl font-bold text-indigo-600">{{ $stats['total_vehiculos'] ?? 0 }}</p>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="font-medium text-gray-500 mb-2">Reservas Activas</h3>
            <p class="text-3xl font-bold text-indigo-600">{{ $stats['reservas_activas'] ?? 0 }}</p>
        </div>
    </div>

    <!-- Acciones rápidas -->
    <div class="flex flex-wrap gap-4 justify-end mb-4">
        <a href="{{ route('admin.vehiculos.create') }}" class="btn btn-sm btn-success flex items-center gap-2 shadow">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
            </svg>
            Añadir Vehículo
        </a>
        <a href="{{ route('admin.reservas.index') }}" class="btn btn-sm btn-primary flex items-center gap-2 shadow">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
            </svg>
            Gestionar Reservas
        </a>
    </div>

    <!-- Últimas reservas -->
    <div class="bg-white rounded-lg shadow overflow-hidden mb-8">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="font-medium text-lg">Últimas Reservas</h3>
        </div>
        <div>
           @forelse($recentReservas as $reserva)
<div class="flex items-center justify-between px-6 py-4 my-2 bg-gray-50 rounded border border-gray-200">
    <div class="flex items-center">
        @if($reserva->vehiculo->imagen)
            <img src="{{ asset('storage/' . $reserva->vehiculo->imagen) }}" alt="Imagen"
                class="w-12 h-8 object-cover rounded mr-4 border border-gray-300"
                style="min-width:48px; min-height:32px;">
        @else
            <div class="w-12 h-8 bg-gray-200 rounded mr-4 flex items-center justify-center text-gray-400 border border-gray-300"
                style="min-width:48px; min-height:32px;">
                <span>Sin imagen</span>
            </div>
        @endif
        <div>
            <p class="font-medium">{{ $reserva->vehiculo->modelo }}</p>
            <p class="text-sm text-gray-500">
                {{ $reserva->user->name }} - 
                {{ $reserva->fecha_inicio->format('d/m/Y') }} al {{ $reserva->fecha_fin->format('d/m/Y') }}
            </p>
        </div>
    </div>
    <span class="px-3 py-1 rounded-full text-sm font-medium 
        {{ $reserva->estado == 'confirmada' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
        {{ ucfirst($reserva->estado) }}
    </span>
</div>
            @empty
            <div class="px-6 py-4 text-center text-gray-500">
                No hay reservas recientes
            </div>
            @endforelse
        </div>
    </div>
</div>
@endsection