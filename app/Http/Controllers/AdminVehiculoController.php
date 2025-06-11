<?php


namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Vehiculo;

class AdminVehiculoController extends Controller
{
public function index()
{
    $vehiculos = Vehiculo::withCount(['reservas' => function ($query) {
        $query->where('fecha_fin', '>=', now());
    }])->paginate(10);

    return view('admin.vehiculos.index', compact('vehiculos'));
}

    public function create()
    {
        // Muestra el formulario para crear un vehículo
        return view('admin.vehiculos.create');
    }

public function store(Request $request)
{
    $validated = $request->validate([
        'modelo' => 'required|string|max:255',
        'matricula' => 'required|string|max:255|unique:vehiculos,matricula',
        'capacidad_personas' => 'required|integer|min:1',
        'numero_camas' => 'required|integer|min:1',
        'precio_dia' => 'required|numeric|min:0',
        'descripcion' => 'nullable|string',
        'disponible' => 'nullable',
        'imagen' => 'nullable|image|max:2048',
        'caracteristicas' => 'nullable|array',
    ]);

    $vehiculo = new Vehiculo();
    $vehiculo->modelo = $validated['modelo'];
    $vehiculo->matricula = $validated['matricula'];
    $vehiculo->capacidad_personas = $validated['capacidad_personas'];
    $vehiculo->numero_camas = $validated['numero_camas'];
    $vehiculo->precio_dia = $validated['precio_dia'];
    $vehiculo->descripcion = $validated['descripcion'] ?? null;
    $vehiculo->disponible = $request->has('disponible') ? 1 : 0;

    // Imagen
    if ($request->hasFile('imagen')) {
        $vehiculo->imagen = $request->file('imagen')->store('vehiculos', 'public');
    }

    // Características (JSON)
    if ($request->filled('caracteristicas')) {
        $vehiculo->caracteristicas = json_encode($validated['caracteristicas']);
    }

    $vehiculo->save();

    return redirect()->route('admin.vehiculos.index')->with('success', 'Vehículo creado correctamente.');
}


  public function edit(Vehiculo $vehiculo)
{
    return view('admin.vehiculos.edit', compact('vehiculo'));
}

  public function update(Request $request, Vehiculo $vehiculo)
{
    $validated = $request->validate([
        'modelo' => 'required|string|max:255',
        'matricula' => 'required|string|max:255|unique:vehiculos,matricula,'.$vehiculo->id,
        'capacidad_personas' => 'required|integer|min:1',
        'numero_camas' => 'required|integer|min:1',
        'precio_dia' => 'required|numeric|min:0',
        'descripcion' => 'nullable|string',
        'disponible' => 'nullable',
        'imagen' => 'nullable|image|max:2048',
        'caracteristicas' => 'nullable|array',
    ]);

    $vehiculo->fill($validated);
    $vehiculo->disponible = $request->has('disponible') ? 1 : 0;

    if ($request->hasFile('imagen')) {
        $vehiculo->imagen = $request->file('imagen')->store('vehiculos', 'public');
    }

    if ($request->filled('caracteristicas')) {
        $vehiculo->caracteristicas = json_encode($validated['caracteristicas']);
    }

    $vehiculo->save();

    return redirect()->route('admin.vehiculos.index')->with('success', 'Vehículo actualizado correctamente.');
}

public function destroy(Vehiculo $vehiculo)
{
    $vehiculo->forceDelete(); 
    return redirect()->route('admin.vehiculos.index')->with('success', 'Vehículo eliminado definitivamente.');
}
}