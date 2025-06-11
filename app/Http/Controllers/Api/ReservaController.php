<?php


namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Reserva;
use App\Models\Vehiculo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReservaController extends Controller
{
    public function index(Request $request)
{
    $reservas = $request->user()
        ->reservas()
        ->with('vehiculo')
        ->orderBy('fecha_inicio', 'asc')
        ->get();

    // Formatear fechas para cada reserva
    $reservas = $reservas->map(function ($reserva) {
        return [
            'id' => $reserva->id,
            'vehiculo' => [
                'id' => $reserva->vehiculo->id,
                'modelo' => $reserva->vehiculo->modelo,
            ],
            'fecha_inicio' => (new \DateTime($reserva->fecha_inicio))->format('Y-m-d'),
            'fecha_fin' => (new \DateTime($reserva->fecha_fin))->format('Y-m-d'),
            'precio_total' => $reserva->precio_total,
            'deposito' => $reserva->deposito,
            'estado' => $reserva->estado,
        ];
    });

    return response()->json($reservas);
}
    public function store(Request $request)
    {
        $validated = $this->validateReserva($request);
        $vehiculo = Vehiculo::findOrFail($validated['vehiculo_id']);

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
            return response()->json([
                'message' => 'Ya tienes una reserva activa en esas fechas. No puedes reservar más de una caravana a la vez.',
                'errors' => [
                    'fecha_inicio' => ['Ya tienes una reserva activa en esas fechas. No puedes reservar más de una caravana a la vez.']
                ]
            ], 422);
        }

        $dias = $this->calcularDiasReserva($validated['fecha_inicio'], $validated['fecha_fin']);

        // Validación de días mínimos
        $mesInicio = (new \DateTime($validated['fecha_inicio']))->format('m');
        $mesFin = (new \DateTime($validated['fecha_fin']))->format('m');
        $minDias = (in_array($mesInicio, ['07', '08']) || in_array($mesFin, ['07', '08'])) ? 7 : 2;

        if ($dias < $minDias) {
            return response()->json([
                'message' => "La reserva debe ser de al menos $minDias días.",
                'errors' => [
                    'fecha_fin' => ["La reserva debe ser de al menos $minDias días."]
                ]
            ], 422);
        }

        // Validación de antelación máxima (60 días)
        $hoy = new \DateTime();
        $fechaInicio = new \DateTime($validated['fecha_inicio']);
        $diffDias = $hoy->diff($fechaInicio)->days;
        if ($fechaInicio < $hoy) {
            return response()->json([
                'message' => "La fecha de inicio debe ser hoy o posterior.",
                'errors' => [
                    'fecha_inicio' => ["La fecha de inicio debe ser hoy o posterior."]
                ]
            ], 422);
        }
        if ($diffDias > 60) {
            return response()->json([
                'message' => "Solo se puede reservar con un máximo de 60 días de antelación.",
                'errors' => [
                    'fecha_inicio' => ["Solo se puede reservar con un máximo de 60 días de antelación."]
                ]
            ], 422);
        }

        $precioTotal = $dias * $vehiculo->precio_dia;
        $deposito = $precioTotal * 0.2;

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

        return response()->json([
    'reserva' => [
        'id' => $reserva->id,
        'vehiculo' => [
            'id' => $vehiculo->id,
            'modelo' => $vehiculo->modelo,
        ],
        'fecha_inicio' => (new \DateTime($reserva->fecha_inicio))->format('Y-m-d'),
        'fecha_fin' => (new \DateTime($reserva->fecha_fin))->format('Y-m-d'),
        'precio_total' => $reserva->precio_total,
        'deposito' => $reserva->deposito,
        'estado' => $reserva->estado,
    ],
    'stripe_checkout_url' => null
]);
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

  public function show($id)
{
    $reserva = Reserva::with('vehiculo')->findOrFail($id);

    return response()->json([
        'id' => $reserva->id,
        'vehiculo' => [
            'id' => $reserva->vehiculo->id,
            'modelo' => $reserva->vehiculo->modelo,
        ],
        'fecha_inicio' => (new \DateTime($reserva->fecha_inicio))->format('Y-m-d'),
        'fecha_fin' => (new \DateTime($reserva->fecha_fin))->format('Y-m-d'),
        'precio_total' => $reserva->precio_total,
        'deposito' => $reserva->deposito,
        'estado' => $reserva->estado,
    ]);
}
public function destroy($id)
{
    $reserva = Reserva::find($id);
    if (!$reserva) {
        return response()->json(['message' => 'Reserva no encontrada'], 404);
    }

    // comprobar si el usuario tiene permiso para borrar la reserva aquí

    $reserva->delete();

    return response()->json(['message' => 'Reserva eliminada correctamente'], 200);
}

public function update(Request $request, $id)
{
    $reserva = Reserva::find($id);
    if (!$reserva) {
        return response()->json(['message' => 'Reserva no encontrada'], 404);
    }

    // Validar datos
    $validated = $request->validate([
        'vehiculo_id' => 'required|exists:vehiculos,id',
        'fecha_inicio' => 'required|date|after_or_equal:today',
        'fecha_fin' => 'required|date|after:fecha_inicio',
    ], [
        'fecha_inicio.after_or_equal' => 'La fecha de inicio debe ser hoy o posterior',
        'fecha_fin.after' => 'La fecha de fin debe ser posterior a la de inicio',
    ]);

    $vehiculo = Vehiculo::findOrFail($validated['vehiculo_id']);

    // Comprobar disponibilidad (excepto la reserva actual)
    $existeReserva = $vehiculo->reservas()
        ->where('id', '!=', $reserva->id)
        ->where(function($query) use ($validated) {
            $query->whereBetween('fecha_inicio', [$validated['fecha_inicio'], $validated['fecha_fin']])
                ->orWhereBetween('fecha_fin', [$validated['fecha_inicio'], $validated['fecha_fin']])
                ->orWhere(function($q) use ($validated) {
                    $q->where('fecha_inicio', '<', $validated['fecha_inicio'])
                        ->where('fecha_fin', '>', $validated['fecha_fin']);
                });
        })
        ->exists();

    if ($existeReserva) {
        return response()->json([
            'message' => 'El vehículo no está disponible en las fechas seleccionadas',
            'errors' => [
                'fecha_inicio' => ['El vehículo no está disponible en las fechas seleccionadas']
            ]
        ], 422);
    }

    // Validación de días mínimos
    $dias = $this->calcularDiasReserva($validated['fecha_inicio'], $validated['fecha_fin']);
    $mesInicio = (new \DateTime($validated['fecha_inicio']))->format('m');
    $mesFin = (new \DateTime($validated['fecha_fin']))->format('m');
    $minDias = (in_array($mesInicio, ['07', '08']) || in_array($mesFin, ['07', '08'])) ? 7 : 2;

    if ($dias < $minDias) {
        return response()->json([
            'message' => "La reserva debe ser de al menos $minDias días.",
            'errors' => [
                'fecha_fin' => ["La reserva debe ser de al menos $minDias días."]
            ]
        ], 422);
    }

    // Validación de antelación máxima (60 días)
    $hoy = new \DateTime();
    $fechaInicio = new \DateTime($validated['fecha_inicio']);
    $diffDias = $hoy->diff($fechaInicio)->days;
    if ($fechaInicio < $hoy) {
        return response()->json([
            'message' => "La fecha de inicio debe ser hoy o posterior.",
            'errors' => [
                'fecha_inicio' => ["La fecha de inicio debe ser hoy o posterior."]
            ]
        ], 422);
    }
    if ($diffDias > 60) {
        return response()->json([
            'message' => "Solo se puede reservar con un máximo de 60 días de antelación.",
            'errors' => [
                'fecha_inicio' => ["Solo se puede reservar con un máximo de 60 días de antelación."]
            ]
        ], 422);
    }

    $precioTotal = $dias * $vehiculo->precio_dia;
    $deposito = $precioTotal * 0.2;

    $reserva->update([
        'vehiculo_id' => $vehiculo->id,
        'fecha_inicio' => $validated['fecha_inicio'],
        'fecha_fin' => $validated['fecha_fin'],
        'precio_total' => $precioTotal,
        'deposito' => $deposito,
    ]);

    return response()->json([
        'message' => 'Reserva actualizada correctamente',
        'reserva' => [
            'id' => $reserva->id,
            'vehiculo' => [
                'id' => $vehiculo->id,
                'modelo' => $vehiculo->modelo,
            ],
            'fecha_inicio' => (new \DateTime($reserva->fecha_inicio))->format('Y-m-d'),
            'fecha_fin' => (new \DateTime($reserva->fecha_fin))->format('Y-m-d'),
            'precio_total' => $reserva->precio_total,
            'deposito' => $reserva->deposito,
            'estado' => $reserva->estado,
        ]
    ]);
}


}