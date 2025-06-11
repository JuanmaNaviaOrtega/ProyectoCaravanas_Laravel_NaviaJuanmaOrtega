@extends('layouts.app')

@section('content')
<div class="bg-white shadow-lg rounded-lg p-8 max-w-3xl mx-auto">
    <h2 class="text-2xl font-bold mb-6 text-indigo-800 flex items-center">
        <i class="fas fa-edit mr-2"></i> Editar Vehículo
    </h2>
    <form action="{{ route('admin.vehiculos.update', $vehiculo) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf
        @method('PUT')

        <div>
            <label for="matricula" class="block text-sm font-semibold mb-1">Matrícula *</label>
            <input type="text" name="matricula" id="matricula" value="{{ old('matricula', $vehiculo->matricula) }}" required class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
        </div>
        <div>
            <label for="modelo" class="block text-sm font-semibold mb-1">Modelo *</label>
            <input type="text" name="modelo" id="modelo" value="{{ old('modelo', $vehiculo->modelo) }}" required class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
        </div>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label for="precio_dia" class="block text-sm font-semibold mb-1">Precio por día (€) *</label>
                <input type="number" name="precio_dia" id="precio_dia" step="0.01" value="{{ old('precio_dia', $vehiculo->precio_dia) }}" required class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>
            <div>
                <label for="capacidad_personas" class="block text-sm font-semibold mb-1">Capacidad (personas) *</label>
                <input type="number" name="capacidad_personas" id="capacidad_personas" value="{{ old('capacidad_personas', $vehiculo->capacidad_personas) }}" required class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>
            <div>
                <label for="numero_camas" class="block text-sm font-semibold mb-1">Número de camas *</label>
                <input type="number" name="numero_camas" id="numero_camas" value="{{ old('numero_camas', $vehiculo->numero_camas) }}" required class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>
            <div>
                <label for="imagen" class="block text-sm font-semibold mb-1">Imagen</label>
                <input type="file" name="imagen" id="imagen" class="w-full border border-gray-300 rounded px-3 py-2">
                @if($vehiculo->imagen)
                    <img src="{{ asset('storage/'.$vehiculo->imagen) }}" alt="Imagen actual" class="h-16 w-16 rounded-full mt-2 shadow">
                @endif
            </div>
        </div>
        <div>
            <label for="descripcion" class="block text-sm font-semibold mb-1">Descripción</label>
            <textarea name="descripcion" id="descripcion" class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">{{ old('descripcion', $vehiculo->descripcion) }}</textarea>
        </div>
        <div class="flex items-center">
            <input type="checkbox" name="disponible" id="disponible" {{ old('disponible', $vehiculo->disponible) ? 'checked' : '' }} class="mr-2">
            <label for="disponible" class="font-medium">Vehículo disponible</label>
        </div>
        <!-- Características (opcional) -->
        <!--
        <div>
            <label for="caracteristicas" class="block text-sm font-semibold mb-1">Características</label>
            <input type="text" name="caracteristicas[]" class="w-full border border-gray-300 rounded px-3 py-2 mb-2" value="{{ old('caracteristicas.0', $vehiculo->caracteristicas[0] ?? '') }}">
            <input type="text" name="caracteristicas[]" class="w-full border border-gray-300 rounded px-3 py-2 mb-2" value="{{ old('caracteristicas.1', $vehiculo->caracteristicas[1] ?? '') }}">
        </div>
        -->
        <div class="flex justify-end space-x-2">
            <a href="{{ route('admin.vehiculos.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded">Cancelar</a>
            <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded font-semibold shadow flex items-center gap-2">
                <i class="fas fa-save"></i>
                Guardar Cambios
            </button>
        </div>
    </form>
    @if ($errors->any())
        <div class="mt-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
            <ul class="list-disc pl-5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
</div>
@endsection