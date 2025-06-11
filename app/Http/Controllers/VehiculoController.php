<?php

namespace App\Http\Controllers;

use App\Models\Vehiculo;
use Illuminate\Http\Request;

class VehiculoController extends Controller
{
    public function index()
    {
        $vehiculos = Vehiculo::where('disponible', true)
                    ->filter(request(['search']))
                    ->paginate(6);
        
        return view('vehiculos.index', compact('vehiculos'));
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
   public function publico()
{
    $vehiculos = Vehiculo::where('disponible', true)->get();
    return view('vehiculos.publico', compact('vehiculos'));
}
}
