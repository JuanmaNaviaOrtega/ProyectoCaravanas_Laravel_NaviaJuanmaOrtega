@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Verifica tu correo electrónico</h2>
    <p>Te hemos enviado un enlace de verificación a tu correo. Revisa tu bandeja de entrada.</p>
    <form method="POST" action="{{ route('verification.send') }}">
        @csrf
        <button type="submit" class="btn btn-primary">Reenviar correo de verificación</button>
    </form>
    @if (session('message'))
        <div class="alert alert-success mt-3">
            {{ session('message') }}
        </div>
    @endif
</div>
@endsection