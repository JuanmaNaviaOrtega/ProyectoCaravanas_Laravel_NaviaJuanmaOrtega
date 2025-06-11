<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Reserva;
use App\Models\Vehiculo;
use App\Models\HistorialReserva;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\ReservaConfirmada;

class AdminController extends Controller
{
     public function __construct()
    {
        
        $this->middleware(function ($request, $next) {
            if (!auth()->check() || !auth()->user()->is_admin) {
                return redirect('/dashboard')->with('error', 'No tienes acceso a esta área');
            }
            return $next($request);
        });
    }

    // Dashboard principal para administradores
    public function dashboard()
    {
       
      if (!auth()->check() || !auth()->user()->is_admin) {
        return redirect('/dashboard')->with('error', 'No tienes acceso a esta área');
    }
        $stats = [
            'total_users'          => User::count(),
            'total_vehiculos'      => Vehiculo::count(),
            'reservas_pendientes'  => Reserva::where('estado', 'pendiente')->count(),
            'reservas_activas'     => Reserva::where('fecha_fin', '>=', now())->count(),
            'ingresos_mes'         => Reserva::whereMonth('created_at', now()->month)
                                        ->where('estado', 'confirmada')
                                        ->sum('precio_total'),
        ];

        $recentReservas = Reserva::with(['user', 'vehiculo'])
                            ->latest()
                            ->take(5)
                            ->get();

        return view('admin.dashboard', compact('stats', 'recentReservas'));
    }

    // Gestión de Usuarios
    public function users()
    {
        if (!auth()->check() || !auth()->user()->is_admin) {
        return redirect('/dashboard')->with('error', 'No tienes acceso a esta área');
    }
        $users = User::withCount(['reservas' => function($query) {
                        $query->where('fecha_fin', '>=', now());
                    }])
                    ->latest()
                    ->paginate(10);

        return view('admin.users.index', compact('users'));
    }

    public function editUser(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    public function updateUser(Request $request, User $user)
    {
        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email,'.$user->id,
            'telefono' => 'nullable|string|max:20',
            'is_admin' => 'required|boolean',
        ]);

        $user->update($validated);

        return redirect()->route('admin.users.index')
               ->with('success', 'Usuario actualizado correctamente');
    }

    // Gestión de Reservas
    public function reservas()
    {
        $reservas = Reserva::with(['user', 'vehiculo'])
                      ->where('fecha_fin', '>=', now())
                      ->latest()
                      ->paginate(10);

        return view('admin.reservas.index', compact('reservas'));
    }

    public function confirmarReserva(Reserva $reserva)
    {
        $reserva->update(['estado' => 'confirmada']);
        Mail::to($reserva->user)->send(new ReservaConfirmada($reserva));

        return back()->with('success', 'Reserva confirmada y notificación enviada');
    }

    // Historial de Reservas
    public function historial()
    {
        $reservas = HistorialReserva::with(['user', 'vehiculo'])
                       ->latest()
                       ->paginate(15);

        return view('admin.reservas.historial', compact('reservas'));
    }

    // Método para mover reservas pasadas al historial
    public function moverAlHistorial()
    {
        $reservasPasadas = Reserva::where('fecha_fin', '<', now())->get();

        foreach ($reservasPasadas as $reserva) {
            HistorialReserva::create($reserva->toArray());
            $reserva->delete();
        }

        return back()->with('success', $reservasPasadas->count().' reservas movidas al historial');
    }
    
}
