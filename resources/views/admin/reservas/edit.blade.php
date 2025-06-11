@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Editar Reserva</h2>
    <form action="{{ route('admin.reservas.update', $reserva) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label for="vehiculo_id" class="form-label">Vehículo</label>
            <select name="vehiculo_id" id="vehiculo_id" class="form-control" required>
                @foreach($vehiculos as $vehiculo)
                    <option value="{{ $vehiculo->id }}" {{ $reserva->vehiculo_id == $vehiculo->id ? 'selected' : '' }}>
                        {{ $vehiculo->modelo }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label for="fecha_inicio" class="form-label">Fecha de inicio</label>
            <input type="date" name="fecha_inicio" id="fecha_inicio" class="form-control" value="{{ $reserva->fecha_inicio->format('Y-m-d') }}" required>
        </div>

        <div class="mb-3">
            <label for="fecha_fin" class="form-label">Fecha de fin</label>
            <input type="date" name="fecha_fin" id="fecha_fin" class="form-control" value="{{ $reserva->fecha_fin->format('Y-m-d') }}" required>
        </div>

        <div class="mb-3">
            <label for="estado" class="form-label">Estado</label>
            <select name="estado" id="estado" class="form-control" required>
                <option value="pendiente" {{ $reserva->estado == 'pendiente' ? 'selected' : '' }}>Pendiente</option>
                <option value="confirmada" {{ $reserva->estado == 'confirmada' ? 'selected' : '' }}>Confirmada</option>
                <option value="cancelada" {{ $reserva->estado == 'cancelada' ? 'selected' : '' }}>Cancelada</option>
            </select>
        </div>

        <button type="submit" class="btn btn-primary">Guardar cambios</button>
    </form>
</div>
@endsection