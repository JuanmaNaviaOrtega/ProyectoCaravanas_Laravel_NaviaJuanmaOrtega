<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Reserva;
use App\Models\Vehiculo;

class DashboardController extends Controller
{
 public function index()
    {
        $user = auth()->user();

        // Si es admin, redirige a su dashboard
        if ($user->is_admin) {
            return redirect()->route('admin.dashboard');
        }

        // Solo reservas futuras, ordenadas
        $reservasActivas = $user->reservas()
            ->where('fecha_fin', '>=', now())
            ->orderBy('fecha_inicio')
            ->with('vehiculo')
            ->get();

        return view('dashboard.index', [
            'reservasActivas' => $reservasActivas,
            'vehiculosDisponibles' => Vehiculo::where('disponible', true)->get(),
            'user' => $user
        ]);
    }
}
