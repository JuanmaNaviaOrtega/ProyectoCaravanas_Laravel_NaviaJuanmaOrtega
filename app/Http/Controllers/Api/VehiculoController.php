<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller; 
use App\Models\Reserva;
use App\Models\Vehiculo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Stripe\Stripe;
use Stripe\PaymentIntent;

class VehiculoController extends Controller
{
    public function index()
{
    $vehiculos = Vehiculo::all();

    return response()->json($vehiculos->map(function ($vehiculo) {
        return [
            'id' => $vehiculo->id,
            'modelo' => $vehiculo->modelo,
            'precio_dia' => $vehiculo->precio_dia,
            'disponible' => $vehiculo->disponible,
        ];
    }));
}

    public function show(Vehiculo $vehiculo)
    {
        $fechasReservadas = $vehiculo->reservas()
            ->where('fecha_fin', '>=', now())
            ->get(['fecha_inicio', 'fecha_fin']);
            
        return view('vehiculos.show', [
            'vehiculo' => $vehiculo,
            'fechasReservadas' => $fechasReservadas
        ]);
    }

  public function disponibles(Request $request)
{
    $request->validate([
        'fecha_inicio' => 'required|date',
        'fecha_fin' => 'required|date|after:fecha_inicio',
    ]);

    $fechaInicio = $request->fecha_inicio;
    $fechaFin = $request->fecha_fin;

    $vehiculos = Vehiculo::where('disponible', true)
        ->whereDoesntHave('reservas', function($query) use ($fechaInicio, $fechaFin) {
            $query->where(function($q) use ($fechaInicio, $fechaFin) {
                $q->whereBetween('fecha_inicio', [$fechaInicio, $fechaFin])
                  ->orWhereBetween('fecha_fin', [$fechaInicio, $fechaFin])
                  ->orWhere(function($q2) use ($fechaInicio, $fechaFin) {
                      $q2->where('fecha_inicio', '<', $fechaInicio)
                         ->where('fecha_fin', '>', $fechaFin);
                  });
            });
        })
        ->get();

    return response()->json($vehiculos->map(function ($vehiculo) {
        return [
            'id' => $vehiculo->id,
            'modelo' => $vehiculo->modelo,
            'precio_dia' => $vehiculo->precio_dia,
            'disponible' => $vehiculo->disponible,
        ];
    }));
}

}
