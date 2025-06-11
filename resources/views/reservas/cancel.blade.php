@extends('layouts.app')
@section('content')
<div class="container">
    <h2>Pago cancelado</h2>
    <p>El pago de la reserva fue cancelado. Puedes intentarlo de nuevo si lo deseas.</p>
    <a href="{{ route('reservas.create') }}" class="btn btn-warning">Volver a reservar</a>
</div>
@endsection