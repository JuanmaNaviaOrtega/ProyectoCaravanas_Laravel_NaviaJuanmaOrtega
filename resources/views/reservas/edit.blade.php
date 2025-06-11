{{-- filepath: resources/views/reservas/edit.blade.php --}}
@extends('layouts.app')

@section('title', 'Editar Reserva')

@section('content')
<div class="container py-4">
    <h2>Editar Reserva</h2>
    <form action="{{ route('reservas.update', $reserva) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label for="vehiculo_id" class="form-label">Vehículo</label>
            <select name="vehiculo_id" id="vehiculo_id" class="form-control" required>
                @foreach(\App\Models\Vehiculo::where('disponible', true)->orWhere('id', $reserva->vehiculo_id)->get() as $vehiculo)
                    <option value="{{ $vehiculo->id }}" {{ $reserva->vehiculo_id == $vehiculo->id ? 'selected' : '' }}>
                        {{ $vehiculo->modelo }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label for="fecha_inicio" class="form-label">Fecha de inicio</label>
            <input type="date" name="fecha_inicio" id="fecha_inicio" class="form-control" value="{{ old('fecha_inicio', $reserva->fecha_inicio->format('Y-m-d')) }}" required>
        </div>

        <div class="mb-3">
            <label for="fecha_fin" class="form-label">Fecha de fin</label>
            <input type="date" name="fecha_fin" id="fecha_fin" class="form-control" value="{{ old('fecha_fin', $reserva->fecha_fin->format('Y-m-d')) }}" required>
        </div>

        <button type="submit" class="btn btn-primary">Actualizar Reserva</button>
        <a href="{{ route('dashboard') }}" class="btn btn-secondary">Cancelar</a>
    </form>
</div>
@endsection