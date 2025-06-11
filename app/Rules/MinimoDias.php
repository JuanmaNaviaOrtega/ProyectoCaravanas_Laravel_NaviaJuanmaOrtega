<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Carbon\Carbon;

class MinimoDias implements Rule
{
    protected $fechaInicio;

    public function __construct($fechaInicio)
    {
        $this->fechaInicio = $fechaInicio;
    }

    public function passes($attribute, $value)
    {
        $inicio = Carbon::parse($this->fechaInicio);
        $fin = Carbon::parse($value);
        $dias = $inicio->diffInDays($fin);

        // Julio y Agosto: mínimo 7 días
        if ($inicio->month >= 7 && $inicio->month <= 8) {
            return $dias >= 7;
        }
        return $dias >= 2; // Mínimo 2 días para otros meses
    }

    public function message()
    {
        return 'La reserva no cumple con la duración mínima requerida.';
    }
}
