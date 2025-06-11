<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Reserva;
use Illuminate\Http\Request;

class ReservaController extends Controller
{
public function edit(\App\Models\Reserva $reserva)
{
    if ($reserva->user_id !== auth()->id() && !auth()->user()->is_admin) {
        abort(403, 'No autorizado');
    }
    $vehiculos = \App\Models\Vehiculo::all();
    return view('admin.reservas.edit', compact('reserva', 'vehiculos'));
}

   


 


    public function destroy(Reserva $reserva)
    {
        $reserva->delete();
        return redirect()->route('admin.reservas.index')->with('success', 'Reserva eliminada correctamente.');
    }

public function update(Request $request, \App\Models\Reserva $reserva)
{
    if ($reserva->user_id !== auth()->id() && !auth()->user()->is_admin) {
        abort(403, 'No autorizado');
    }

    $rules = [
        'vehiculo_id' => 'required|exists:vehiculos,id',
        'fecha_inicio' => 'required|date|after_or_equal:today',
        'fecha_fin' => 'required|date|after:fecha_inicio',
    ];

    if (auth()->user()->is_admin) {
        $rules['estado'] = 'required|in:pendiente,confirmada,cancelada';
    }

    $validated = $request->validate($rules);

    $vehiculo = \App\Models\Vehiculo::findOrFail($request->vehiculo_id);

    $fechaInicio = \Carbon\Carbon::parse($request->fecha_inicio);
    $fechaFin = \Carbon\Carbon::parse($request->fecha_fin);
    $dias = $fechaInicio->diffInDays($fechaFin);

    if ($fechaInicio->month == 7 || $fechaInicio->month == 8) {
        if ($dias < 7) {
            return back()->withErrors(['fecha_fin' => 'En julio y agosto la reserva debe ser de al menos 7 días.'])->withInput();
        }
    } else {
        if ($dias < 2) {
            return back()->withErrors(['fecha_fin' => 'La reserva debe ser de al menos 2 días.'])->withInput();
        }
    }
    if ($fechaInicio->gt(now()->addDays(60))) {
        return back()->withErrors(['fecha_inicio' => 'Solo puedes reservar con un máximo de 60 días de antelación.'])->withInput();
    }

    $precioTotal = $vehiculo->precio_dia * max($dias, 1);

    $reserva->update([
        'vehiculo_id' => $vehiculo->id,
        'fecha_inicio' => $request->fecha_inicio,
        'fecha_fin' => $request->fecha_fin,
        'precio_total' => $precioTotal,
        'estado' => auth()->user()->is_admin ? $request->estado : $reserva->estado,
    ]);

    if (auth()->user()->is_admin) {
        return redirect()->route('admin.reservas.index')->with('success', 'Reserva actualizada correctamente.');
    }
    return redirect()->route('dashboard')->with('success', 'Reserva actualizada correctamente.');
}
public function index()
{
    $reservas = \App\Models\Reserva::with(['user', 'vehiculo'])
        ->orderBy('fecha_inicio', 'asc')
        ->paginate(15);

    return view('admin.reservas.index', compact('reservas'));
}


}