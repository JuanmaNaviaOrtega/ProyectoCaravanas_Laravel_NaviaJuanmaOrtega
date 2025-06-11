<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable;
      use HasApiTokens, HasFactory, Notifiable;

    // Campos que se pueden asignar de forma masiva
    protected $fillable = [
        'name',
        'email',
        'password',
        'is_admin',
        'telefono',
    ];

    // Campos ocultos al serializar
    protected $hidden = [
        'password',
        'remember_token',
    ];

    // Conversión de campos 
    protected $casts = [
        'email_verified_at' => 'datetime',
        'is_admin' => 'boolean',
    ];

    // Método para determinar si el usuario es administrador
    public function isAdmin()
    {
        return $this->is_admin;
    }

    // Relación con reservas 
    public function reservas()
    {
        return $this->hasMany(Reserva::class);
    }
}
