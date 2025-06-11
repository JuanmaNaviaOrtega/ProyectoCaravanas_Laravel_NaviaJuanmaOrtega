@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Detalle de Reserva</h2>
    <ul>
        <li><strong>Vehículo:</strong> {{ $reserva->vehiculo->modelo ?? '-' }}</li>
        <li><strong>Fecha inicio:</strong> {{ $reserva->fecha_inicio }}</li>
        <li><strong>Fecha fin:</strong> {{ $reserva->fecha_fin }}</li>
        <li><strong>Precio total:</strong> {{ $reserva->precio_total }} €</li>
        <li><strong>Depósito:</strong> {{ $reserva->deposito }} €</li>
        <li><strong>Estado:</strong> {{ $reserva->estado }}</li>
        <li><strong>ID transacción:</strong> {{ $reserva->transaccion_id }}</li>
    </ul>
</div>
@endsection