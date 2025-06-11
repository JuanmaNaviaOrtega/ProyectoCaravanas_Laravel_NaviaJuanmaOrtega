<?php

namespace App\Http\Controllers;

use App\Models\Reserva;
use App\Models\Vehiculo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Stripe\Stripe;
use Stripe\Checkout\Session as StripeSession;
use Illuminate\Support\Facades\Mail;
use App\Mail\ReservaConfirmada;
class ReservaController extends Controller
{
   public function index()
    {
        $reservas = Auth::user()->reservas()
                    ->with('vehiculo')
                    ->orderBy('fecha_inicio', 'desc')
                    ->paginate(5);
        return view('reservas.index', compact('reservas'));
    }

   public function create()
    {
        $vehiculo = request()->has('vehiculo_id') 
            ? Vehiculo::findOrFail(request('vehiculo_id'))
            : null;
        return view('reservas.create', [
            'vehiculos' => Vehiculo::where('disponible', true)->get(),
            'vehiculoSeleccionado' => $vehiculo
        ]);
    }


   public function store(Request $request)
    {
        $validated = $this->validateReserva($request);
        $vehiculo = Vehiculo::findOrFail($validated['vehiculo_id']);
$existeSolapada = Reserva::where('vehiculo_id', $request->vehiculo_id)
    ->where('fecha_fin', '>=', $request->fecha_inicio)
    ->where('fecha_inicio', '<=', $request->fecha_fin)
    ->exists();

if ($existeSolapada) {
    return back()->withErrors(['fecha_inicio' => 'La autocaravana no está disponible en esas fechas.'])->withInput();
}

$reservasFuturas = Reserva::where('user_id', auth()->id())
    ->where('fecha_inicio', '>', now())
    ->count();

if ($reservasFuturas >= 5) {
    return back()->withErrors(['max_reservas' => 'Solo puedes tener 5 reservas futuras activas.'])->withInput();
}

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
        // Validación de fechas
        $this->validarDisponibilidad($vehiculo, $validated['fecha_inicio'], $validated['fecha_fin']);
$existeSolapadaUsuario = Reserva::where('user_id', Auth::id())
    ->where(function($query) use ($validated) {
        $query->whereBetween('fecha_inicio', [$validated['fecha_inicio'], $validated['fecha_fin']])
              ->orWhereBetween('fecha_fin', [$validated['fecha_inicio'], $validated['fecha_fin']])
              ->orWhere(function($q) use ($validated) {
                  $q->where('fecha_inicio', '<', $validated['fecha_inicio'])
                    ->where('fecha_fin', '>', $validated['fecha_fin']);
              });
    })
    ->exists();

if ($existeSolapadaUsuario) {
    return back()->withErrors([
        'fecha_inicio' => 'Ya tienes una reserva activa en esas fechas. No puedes reservar más de una caravana a la vez.'
    ])->withInput();
}
        $dias = $this->calcularDiasReserva($validated['fecha_inicio'], $validated['fecha_fin']);
        $precioTotal = $dias * $vehiculo->precio_dia;
        $deposito = $precioTotal * 0.2;

        // Creamos la reserva en estado pendiente (sin pago)
        $reserva = Reserva::create([
            'user_id' => Auth::id(),
            'vehiculo_id' => $vehiculo->id,
            'fecha_inicio' => $validated['fecha_inicio'],
            'fecha_fin' => $validated['fecha_fin'],
            'precio_total' => $precioTotal,
            'deposito' => $deposito,
            'estado' => 'pendiente',
            'transaccion_id' => null
        ]);

        try {
            Stripe::setApiKey(config('services.stripe.secret'));

            $session = StripeSession::create([
                'payment_method_types' => ['card'],
                'line_items' => [[
                    'price_data' => [
                        'currency' => 'eur',
                        'product_data' => [
                            'name' => "Depósito reserva {$vehiculo->modelo}",
                        ],
                        'unit_amount' => intval($deposito * 100),
                    ],
                    'quantity' => 1,
                ]],
                'mode' => 'payment',

'success_url' => route('reservas.success', $reserva->id) . '?session_id={CHECKOUT_SESSION_ID}',                'cancel_url' => route('reservas.cancel', ['reserva' => $reserva->id]),
                'metadata' => [
                    'reserva_id' => $reserva->id,
                    'vehiculo_id' => $vehiculo->id,
                    'user_id' => Auth::id(),
                ],
            ]);

            
            $reserva->update(['transaccion_id' => $session->id]);

            // Redirige al usuario a Stripe Checkout
            return redirect($session->url);

        } catch (\Exception $e) {
            $reserva->delete(); 
            return back()->with('error', 'Error al procesar el pago: ' . $e->getMessage());
        }
    }
// Stripe redirige aquí si el pago es exitoso
   public function success(Request $request, $reservaId)
{
    $reserva = Reserva::findOrFail($reservaId);

    // Actualiza el estado
    $reserva->update(['estado' => 'confirmada']);

    // Envía el correo de confirmación
    Mail::to($reserva->user->email)->send(new ReservaConfirmada($reserva));

    return view('reservas.success', compact('reserva'));
}

    // Stripe redirige aquí si el usuario cancela el pago
    public function cancel($reservaId)
    {
        $reserva = Reserva::findOrFail($reservaId);
        $reserva->delete(); // Mejora: elimina la reserva si no se paga
        return view('reservas.cancel');
    }

      public function show(Reserva $reserva)
    {
        if (auth()->id() !== $reserva->user_id && !auth()->user()->is_admin) {
            abort(403, 'No tienes permiso para ver esta reserva.');
        }
        return view('reservas.show', compact('reserva'));
    }
    
    
    private function validateReserva(Request $request)
    {
        return $request->validate([
            'vehiculo_id' => 'required|exists:vehiculos,id',
            'fecha_inicio' => 'required|date|after_or_equal:today',
            'fecha_fin' => 'required|date|after:fecha_inicio',
        ], [
            'fecha_inicio.after_or_equal' => 'La fecha de inicio debe ser hoy o posterior',
            'fecha_fin.after' => 'La fecha de fin debe ser posterior a la de inicio',
        ]);
    }
    
    private function validarDisponibilidad($vehiculo, $fechaInicio, $fechaFin)
    {
        $existeReserva = $vehiculo->reservas()
            ->where(function($query) use ($fechaInicio, $fechaFin) {
                $query->whereBetween('fecha_inicio', [$fechaInicio, $fechaFin])
                      ->orWhereBetween('fecha_fin', [$fechaInicio, $fechaFin])
                      ->orWhere(function($q) use ($fechaInicio, $fechaFin) {
                          $q->where('fecha_inicio', '<', $fechaInicio)
                            ->where('fecha_fin', '>', $fechaFin);
                      });
            })
            ->exists();
            
        if ($existeReserva) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'fecha_inicio' => 'El vehículo no está disponible en las fechas seleccionadas'
            ]);
        }
    }
    
    private function calcularDiasReserva($inicio, $fin)
    {
        $inicio = new \DateTime($inicio);
        $fin = new \DateTime($fin);
        return $inicio->diff($fin)->days;
    }
public function edit(\App\Models\Reserva $reserva)
{
    if ($reserva->user_id !== auth()->id()) {
        abort(403, 'No autorizado');
    }
    return view('reservas.edit', compact('reserva'));
}


public function update(Request $request, \App\Models\Reserva $reserva)
{
    if ($reserva->user_id !== auth()->id()) {
        abort(403, 'No autorizado');
    }

    $validated = $request->validate([
        'vehiculo_id' => 'required|exists:vehiculos,id',
        'fecha_inicio' => 'required|date|after_or_equal:today',
        'fecha_fin' => 'required|date|after:fecha_inicio',
    ]);

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
$existeSolapadaUsuario = Reserva::where('user_id', Auth::id())
    ->where('id', '!=', $reserva->id)
    ->where(function($query) use ($request) {
        $query->whereBetween('fecha_inicio', [$request->fecha_inicio, $request->fecha_fin])
              ->orWhereBetween('fecha_fin', [$request->fecha_inicio, $request->fecha_fin])
              ->orWhere(function($q) use ($request) {
                  $q->where('fecha_inicio', '<', $request->fecha_inicio)
                    ->where('fecha_fin', '>', $request->fecha_fin);
              });
    })
    ->exists();

if ($existeSolapadaUsuario) {
    return back()->withErrors([
        'fecha_inicio' => 'Ya tienes una reserva activa en esas fechas. No puedes reservar más de una caravana a la vez.'
    ])->withInput();
}
    $precioTotal = $vehiculo->precio_dia * max($dias, 1);

    $reserva->update([
        'vehiculo_id' => $vehiculo->id,
        'fecha_inicio' => $request->fecha_inicio,
        'fecha_fin' => $request->fecha_fin,
        'precio_total' => $precioTotal,
    ]);

    return redirect()->route('dashboard')->with('success', 'Reserva actualizada correctamente.');
}

public function destroy(\App\Models\Reserva $reserva)
{
    // Solo el dueño puede borrar su reserva
    if ($reserva->user_id !== auth()->id()) {
        abort(403, 'No autorizado');
    }

    $reserva->delete();

    return redirect()->route('dashboard')->with('success', 'Reserva eliminada correctamente.');
}
}


