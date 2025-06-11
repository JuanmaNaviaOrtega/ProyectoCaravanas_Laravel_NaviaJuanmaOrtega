<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ReservaController;
use App\Http\Controllers\Api\VehiculoController;
use Illuminate\Http\Request;

Route::post('/login', [AuthController::class, 'loginApi']);
Route::post('/register', [AuthController::class, 'register']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // Reservas (con nombres únicos para evitar conflicto con rutas web, he tenido serios problemas con esto profesor)
    Route::apiResource('reservas', ReservaController::class)->names([
        'index' => 'api.reservas.index',
        'store' => 'api.reservas.store',
        'show' => 'api.reservas.show',
        'update' => 'api.reservas.update',
        'destroy' => 'api.reservas.destroy',
    ]);
    Route::post('/reservas/{reserva}/pagar', [ReservaController::class, 'procesarPago'])->name('api.reservas.pagar');
    Route::get('/reservas/check', [ReservaController::class, 'checkDisponibilidad'])->name('api.reservas.check');

    // Vehículos
    Route::get('/vehiculos/disponibles', [VehiculoController::class, 'disponibles'])->name('api.vehiculos.disponibles');
    Route::get('/vehiculos', [VehiculoController::class, 'index'])->name('api.vehiculos.index');
    Route::get('/user/profile', [AuthController::class, 'profile'])->name('api.user.profile');
});