@extends('layouts.app')

@section('content')
<div class="bg-white shadow rounded-lg p-8">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-indigo-800">Gestión de Vehículos</h2>
        <a href="{{ route('admin.vehiculos.create') }}" class="btn btn-primary">
            <i class="fas fa-plus mr-2"></i> Nuevo Vehículo
        </a>
    </div>
 
    <div class="table-responsive">
        <table class="table align-middle">
            <thead>
                <tr>
                    <th>Modelo</th>
                    <th>Matrícula</th>
                    <th>Capacidad</th>
                    <th>Precio/día</th>
                    <th>Reservas Activas</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
               @foreach($vehiculos as $vehiculo)
                <tr>
                    <td class="d-flex align-items-center">
                        @if($vehiculo->imagen)
                            <img src="{{ asset('storage/' . $vehiculo->imagen) }}" alt="{{ $vehiculo->modelo }}"
                                 style="width: 80px; height: 60px; object-fit: cover; border-radius: 6px; margin-right: 10px;">
                        @endif
                        <span>{{ $vehiculo->modelo }}</span>
                    </td>
                    <td>{{ $vehiculo->matricula }}</td>
                    <td>{{ $vehiculo->capacidad_personas }} pers.</td>
                    <td>{{ number_format($vehiculo->precio_dia, 2) }} €</td>
                    <td>{{ $vehiculo->reservas_count }}</td>
                    <td>
                        @if($vehiculo->disponible)
                            <span class="px-2 py-1 rounded bg-green-100 text-green-800 text-xs font-semibold">
                                Disponible
                            </span>
                        @else
                            <span class="px-2 py-1 rounded bg-red-100 text-red-800 text-xs font-semibold">
                                No disponible
                            </span>
                        @endif
                    </td>
                    <td class="flex space-x-2">
                        <a href="{{ route('admin.vehiculos.edit', $vehiculo) }}" class="btn btn-primary btn-sm">Editar</a>
                        <form action="{{ route('admin.vehiculos.destroy', $vehiculo) }}" method="POST" onsubmit="return confirm('¿Seguro que deseas eliminar este vehículo?')" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm">Eliminar</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="mt-4">
        {{ $vehiculos->links() }}
    </div>
</div>
@endsection