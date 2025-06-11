{{-- filepath: resources/views/admin/reservas/index.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Gestión de Reservas</h2>
    <table class="table">
        <thead>
            <tr>
                <th>Usuario</th>
                <th>Vehículo</th>
                <th>Fecha inicio</th>
                <th>Fecha fin</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            @forelse($reservas as $reserva)
            <tr>
                <td>{{ $reserva->user->name }}</td>
                <td>{{ $reserva->vehiculo->modelo }}</td>
                <td>{{ $reserva->fecha_inicio->format('d/m/Y') }}</td>
                <td>{{ $reserva->fecha_fin->format('d/m/Y') }}</td>
                <td>{{ ucfirst($reserva->estado) }}</td>
                <td>
                    <a href="{{ route('admin.reservas.edit', $reserva) }}" class="btn btn-sm btn-primary">
                        <i class="fas fa-edit"></i> Editar
                    </a>
                    <form action="{{ route('admin.reservas.destroy', $reserva) }}" method="POST" style="display:inline-block;" onsubmit="return confirm('¿Seguro que quieres eliminar esta reserva?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger">
                            <i class="fas fa-trash"></i> Eliminar
                        </button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6">No hay reservas.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
    {{ $reservas->links() }}
</div>
@endsection