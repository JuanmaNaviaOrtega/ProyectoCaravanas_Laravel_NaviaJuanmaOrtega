
protected $commands = [
    \App\Console\Commands\MoveReservasToHistory::class,
];

protected function schedule(\Illuminate\Console\Scheduling\Schedule $schedule)
{
    $schedule->command('reservas:move-to-history')->daily();
}