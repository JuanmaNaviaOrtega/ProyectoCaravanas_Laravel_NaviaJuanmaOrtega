<?php
// app/Console/Commands/MoveReservasToHistory.php

namespace App\Console\Commands;

use App\Models\Reserva;
use App\Models\HistorialReserva;
use Illuminate\Console\Command;

class MoveReservasToHistory extends Command
{
    protected $signature = 'reservas:move-to-history';
    protected $description = 'Mueve las reservas pasadas al historial';

    public function handle()
    {
        $reservas = Reserva::where('fecha_fin', '<', now())->get();
        
        if ($reservas->isEmpty()) {
            $this->info('No hay reservas para mover al historial');
            return;
        }
        
        $bar = $this->output->createProgressBar($reservas->count());
        
        $reservas->each(function ($reserva) use ($bar) {
            HistorialReserva::create($reserva->toArray());
            $reserva->delete();
            $bar->advance();
        });
        
        $bar->finish();
        $this->newLine();
        $this->info($reservas->count().' reservas movidas al historial correctamente');
        
        return Command::SUCCESS;
    }
}