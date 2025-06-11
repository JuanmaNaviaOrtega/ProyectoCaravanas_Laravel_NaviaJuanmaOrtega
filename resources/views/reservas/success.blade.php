@extends('layouts.app')

@section('content')
<div class="container text-center mt-5">
    <h2 class="text-success mb-4">¡Reserva realizada correctamente!</h2>
    <p>Tu pago se ha procesado y tu reserva ha sido confirmada.</p>
    <div class="my-4">
        <a href="{{ route('dashboard') }}" class="btn btn-primary m-2">Ver mis reservas</a>
        <a href="{{ route('home') }}" class="btn btn-secondary m-2">Ir al menú principal</a>
    </div>
</div>
@endsection