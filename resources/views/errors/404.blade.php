@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-16 text-center">
    <h1 class="text-5xl font-bold text-gray-800 mb-4">404</h1>
    <p class="text-xl text-gray-600 mb-8">Página no encontrada</p>
    <a href="{{ url('/') }}" class="px-6 py-3 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
        Volver al inicio
    </a>
</div>
@endsection
