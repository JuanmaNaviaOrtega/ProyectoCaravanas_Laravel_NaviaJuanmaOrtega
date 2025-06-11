@extends('layouts.app')

@section('content')
<div class="bg-white shadow rounded-lg p-8 max-w-2xl mx-auto">
    <h2 class="text-2xl font-bold mb-6 text-indigo-800 flex items-center">
        <i class="fas fa-plus mr-2"></i> Añadir Nuevo Vehículo
    </h2>
    @if ($errors->any())
        <div class="alert alert-danger mb-6">
            <ul class="list-disc pl-5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    <form action="{{ route('admin.vehiculos.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="form-group">
            <label for="matricula" class="form-label">Matrícula *</label>
            <input type="text" name="matricula" id="matricula" value="{{ old('matricula') }}" required class="form-input">
        </div>
        <div class="form-group">
            <label for="modelo" class="form-label">Modelo *</label>
            <input type="text" name="modelo" id="modelo" value="{{ old('modelo') }}" required class="form-input">
        </div>
        <div class="form-group">
            <label for="precio_dia" class="form-label">Precio por día (€) *</label>
            <input type="number" name="precio_dia" id="precio_dia" step="0.01" value="{{ old('precio_dia') }}" required class="form-input">
        </div>
        <div class="form-group">
            <label for="capacidad_personas" class="form-label">Capacidad (personas) *</label>
            <input type="number" name="capacidad_personas" id="capacidad_personas" value="{{ old('capacidad_personas') }}" required class="form-input">
        </div>
        <div class="form-group">
            <label for="numero_camas" class="form-label">Número de camas *</label>
            <input type="number" name="numero_camas" id="numero_camas" value="{{ old('numero_camas') }}" required class="form-input">
        </div>
        <div class="form-group">
            <label for="imagen" class="form-label">Imagen</label>
            <input type="file" name="imagen" id="imagen" class="form-input">
        </div>
        <div class="form-group">
            <label for="descripcion" class="form-label">Descripción</label>
            <textarea name="descripcion" id="descripcion" class="form-input">{{ old('descripcion') }}</textarea>
        </div>
        <div class="form-group flex items-center">
            <input type="checkbox" name="disponible" id="disponible" {{ old('disponible', true) ? 'checked' : '' }} class="form-checkbox">
            <label for="disponible" class="ml-2 font-medium">Vehículo disponible</label>
        </div>
        <div class="flex justify-end space-x-2 mt-6">
            <a href="{{ route('admin.vehiculos.index') }}" class="btn btn-secondary">Cancelar</a>
            <button type="submit" class="btn btn-primary">Guardar Vehículo</button>
        </div>
    </form>
</div>
@endsection