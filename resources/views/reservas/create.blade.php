@extends('layouts.app')

@section('title', 'Nueva Reserva')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0"><i class="fas fa-calendar-plus"></i> Nueva Reserva</h4>
                </div>
                <div class="card-body">
                    <div>DEBUG: ESTA ES LA VISTA CORRECTA</div>
                    <form action="{{ route('reservas.store') }}" method="POST" id="reservaForm">
                        @csrf
                        
                        <div class="mb-3">
                            <label for="vehiculo_id" class="form-label">Autocaravana</label>
                            <select class="form-select @error('vehiculo_id') is-invalid @enderror" id="vehiculo_id" name="vehiculo_id" required>
                                <option value="">Seleccione una autocaravana</option>
                                @foreach($vehiculos as $vehiculo)
                                    <option value="{{ $vehiculo->id }}" 
                                        {{ old('vehiculo_id', $vehiculoSeleccionado?->id) == $vehiculo->id ? 'selected' : '' }}
                                        data-precio="{{ $vehiculo->precio_dia }}">
                                        {{ $vehiculo->modelo }} ({{ $vehiculo->precio_dia }}€/día)
                                    </option>
                                @endforeach
                            </select>
                            @error('vehiculo_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="fecha_inicio" class="form-label">Fecha de inicio</label>
                                <input type="date" class="form-control @error('fecha_inicio') is-invalid @enderror" 
                                       id="fecha_inicio" name="fecha_inicio" 
                                       min="{{ date('Y-m-d') }}" 
                                       value="{{ old('fecha_inicio') }}" required>
                                @error('fecha_inicio')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="fecha_fin" class="form-label">Fecha de fin</label>
                                <input type="date" class="form-control @error('fecha_fin') is-invalid @enderror" 
                                       id="fecha_fin" name="fecha_fin" 
                                       min="{{ date('Y-m-d', strtotime('+1 day')) }}" 
                                       value="{{ old('fecha_fin') }}" required>
                                @error('fecha_fin')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div id="resumenReserva" class="card mb-3" style="display: none;">
                            <div class="card-body">
                                <h5 class="card-title">Resumen de Reserva</h5>
                                <div id="resumenContenido"></div>
                            </div>
                        </div>
                        
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-check-circle"></i> Confirmar Reserva
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('reservaForm');
    const vehiculoSelect = document.getElementById('vehiculo_id');
    const fechaInicio = document.getElementById('fecha_inicio');
    const fechaFin = document.getElementById('fecha_fin');
    const resumenReserva = document.getElementById('resumenReserva');
    const resumenContenido = document.getElementById('resumenContenido');
    
    function actualizarResumen() {
        if (!vehiculoSelect.value || !fechaInicio.value || !fechaFin.value) {
            resumenReserva.style.display = 'none';
            return;
        }
        
        const precioDia = parseFloat(vehiculoSelect.selectedOptions[0].dataset.precio);
        const inicio = new Date(fechaInicio.value);
        const fin = new Date(fechaFin.value);
        const dias = Math.ceil((fin - inicio) / (1000 * 60 * 60 * 24)) || 1;
        const total = precioDia * dias;
        const deposito = total * 0.2;
        
        resumenContenido.innerHTML = `
            <p><strong>Duración:</strong> ${dias} días</p>
            <p><strong>Precio por día:</strong> ${precioDia.toFixed(2)}€</p>
            <p><strong>Total reserva:</strong> ${total.toFixed(2)}€</p>
            <p class="fw-bold"><strong>Depósito requerido (20%):</strong> ${deposito.toFixed(2)}€</p>
        `;
        
        resumenReserva.style.display = 'block';
    }
    
    vehiculoSelect.addEventListener('change', actualizarResumen);
    fechaInicio.addEventListener('change', function() {
        if (fechaInicio.value && fechaFin.value) {
            const minFinDate = new Date(fechaInicio.value);
            minFinDate.setDate(minFinDate.getDate() + 1);
            fechaFin.min = minFinDate.toISOString().split('T')[0];
        }
        actualizarResumen();
    });
    fechaFin.addEventListener('change', actualizarResumen);
    
    // Inicializar si hay valores antiguos
    if (vehiculoSelect.value && fechaInicio.value && fechaFin.value) {
        actualizarResumen();
    }
});
</script>
@endsection
