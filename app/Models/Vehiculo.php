<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Vehiculo extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'matricula',
        'modelo',
        'descripcion',
        'capacidad_personas',
        'numero_camas',
        'precio_dia',
        'disponible',
        'imagen',
        'caracteristicas'
    ];

    protected $casts = [
        'disponible' => 'boolean',
        'caracteristicas' => 'array',
        'precio_dia' => 'float'
    ];

    public function reservas()
    {
        return $this->hasMany(Reserva::class);
    }

    public function imagenUrl()
    {
        return $this->imagen ? asset('storage/' . $this->imagen) : asset('images/default-vehicle.jpg');
    }
}
