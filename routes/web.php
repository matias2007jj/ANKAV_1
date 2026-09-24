<?php

use App\Http\Controllers\EquipoController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ClienteDashboardController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Rutas organizadas con controladores (como las demás)
    Route::get('/clientes/{codigo}/dashboard', [ClienteDashboardController::class, 'show'])
        ->name('clientes.dashboard');

    Route::get('/clientes/{codigo}/equipos/nuevo', [EquipoController::class, 'create'])
        ->name('equipos.create');

    Route::post('/clientes/{codigo}/equipos', [EquipoController::class, 'store'])
        ->name('equipos.store');

    // Nueva ruta para tu vista de cliente usando el controlador
    Route::get('/vistacliente', [ClienteDashboardController::class, 'vistaPrueba'])
        ->name('clientes.vistaprueba');
});

require __DIR__.'/auth.php';