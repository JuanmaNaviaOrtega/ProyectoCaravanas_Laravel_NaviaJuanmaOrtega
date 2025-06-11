<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reserva extends Model
{
    use HasFactory;

    // Permitir asignación masiva de estos campos
   protected $fillable = [
    'user_id',
    'vehiculo_id',
    'fecha_inicio',
    'fecha_fin',
    'precio_total',
    'deposito',
    'estado',
    'transaccion_id',
];

protected $casts = [
        'fecha_inicio' => 'datetime',
        'fecha_fin' => 'datetime',
    ];

    /**
     * Obtiene el usuario dueño de la reserva
     */
    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    /**
     * Obtiene el vehículo de la reserva
     */
    public function vehiculo()
    {
        return $this->belongsTo(\App\Models\Vehiculo::class);
    }
}