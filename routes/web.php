<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\VehiculoController;
use App\Http\Controllers\ReservaController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\StripeWebhookController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AdminVehiculoController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ContactController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;

// Página principal
Route::get('/', function () {
    return view('welcome', [
        'vehiculosDestacados' => App\Models\Vehiculo::where('disponible', true)
            ->inRandomOrder()
            ->take(3)
            ->get()
    ]);
})->name('home');

// Autenticación
Route::controller(AuthController::class)->group(function () {
    Route::get('/login', 'showLoginForm')->name('login');
    Route::post('/login', 'login');
    Route::get('/register', 'showRegistrationForm')->name('register');
    Route::post('/register', 'register');
    Route::post('/logout', 'logout')->name('logout');
});

// Vehículos (acceso público)
Route::controller(VehiculoController::class)->group(function () {
    Route::get('/vehiculos', 'index')->name('vehiculos.index');
    Route::get('/vehiculos/disponibles', 'disponibles')->name('vehiculos.disponibles');
    Route::get('/vehiculos/publico', 'publico')->name('vehiculos.publico');
    Route::get('/vehiculos/{vehiculo}', 'show')->name('vehiculos.show');
});

// Webhook de Stripe
Route::post('/stripe/webhook', [StripeWebhookController::class, 'handleWebhook'])
    ->name('stripe.webhook');

// Área privada para usuarios autenticados y verificados
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Reservas
    Route::controller(ReservaController::class)->group(function () {
        Route::get('/reservas', 'index')->name('reservas.index');
        Route::get('/reservas/create', 'create')->name('reservas.create');
        Route::post('/reservas/check', 'checkDisponibilidad')->name('reservas.check');
        Route::post('/reservas', 'store')->name('reservas.store');
        Route::get('/reservas/{reserva}', 'show')->name('reservas.show');
        Route::get('/reservas/{reserva}/edit', 'edit')->name('reservas.edit');
        Route::put('/reservas/{reserva}', 'update')->name('reservas.update');
        Route::delete('/reservas/{reserva}', 'destroy')->name('reservas.destroy');
        Route::post('/reservas/{reserva}/pagar', 'procesarPago')->name('reservas.pagar');
        Route::get('/reservas/success/{reserva}', 'success')->name('reservas.success');
        Route::get('/reservas/cancel', 'cancel')->name('reservas.cancel');
    });
});

// Área de administración
Route::middleware(['auth'])->prefix('admin')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    Route::get('/users', [AdminController::class, 'users'])->name('admin.users.index');
    Route::get('/users/{user}/edit', [AdminController::class, 'editUser'])->name('admin.users.edit');
    Route::put('/users/{user}', [AdminController::class, 'updateUser'])->name('admin.users.update');

    // Gestión de Vehículos
    Route::get('/vehiculos', [AdminVehiculoController::class, 'index'])->name('admin.vehiculos.index');
    Route::get('/vehiculos/create', [AdminVehiculoController::class, 'create'])->name('admin.vehiculos.create');
    Route::post('/vehiculos', [AdminVehiculoController::class, 'store'])->name('admin.vehiculos.store');
    Route::get('/vehiculos/{vehiculo}/edit', [AdminVehiculoController::class, 'edit'])->name('admin.vehiculos.edit');
    Route::put('/vehiculos/{vehiculo}', [AdminVehiculoController::class, 'update'])->name('admin.vehiculos.update');
    Route::delete('/vehiculos/{vehiculo}', [AdminVehiculoController::class, 'destroy'])->name('admin.vehiculos.destroy');

    // Gestión de Reservas
    Route::get('/reservas', [AdminController::class, 'reservas'])->name('admin.reservas.index');
    Route::post('/reservas/{reserva}/confirmar', [AdminController::class, 'confirmarReserva'])->name('admin.reservas.confirmar');
    Route::get('/reservas/{reserva}/edit', [App\Http\Controllers\Admin\ReservaController::class, 'edit'])->name('admin.reservas.edit');
    Route::delete('/reservas/{reserva}', [App\Http\Controllers\Admin\ReservaController::class, 'destroy'])->name('admin.reservas.destroy');
    Route::put('/reservas/{reserva}', [App\Http\Controllers\Admin\ReservaController::class, 'update'])->name('admin.reservas.update');
    Route::get('/historial', [AdminController::class, 'historial'])->name('admin.reservas.historial');
});

// Páginas estáticas
Route::view('/about', 'about')->name('about');
/*
Route::view('/contact', 'contact')->name('contact');
Route::post('/contact', [ContactController::class, 'send'])->name('contact.send');
*/

// Verificación de email
Route::get('/email/verify', function () {
    return view('auth.verify');
})->middleware('auth')->name('verification.notice');

Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill();
    return redirect('/dashboard'); 
})->middleware(['auth', 'signed'])->name('verification.verify');

Route::post('/email/verification-notification', function (Request $request) {
    $request->user()->sendEmailVerificationNotification();
    return back()->with('message', 'Correo de verificación reenviado.');
})->middleware(['auth', 'throttle:6,1'])->name('verification.send');

// Fallback para 404
Route::fallback(function () {
    return response()->view('errors.404', [], 404);
});
