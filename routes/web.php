<?php

use App\Http\Controllers\EquipoController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ClienteDashboardController;
use App\Http\Controllers\PrincipalController;
use Illuminate\Support\Facades\Route;

// Ruta principal conectada al controlador y con nombre 'principal'
Route::get('/principal', [PrincipalController::class, 'index'])->name('principal');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Rutas organizadas con controladores
    Route::get('/clientes/{codigo}/dashboard', [ClienteDashboardController::class, 'show'])
        ->name('clientes.dashboard');

    Route::get('/clientes/{codigo}/equipos/nuevo', [EquipoController::class, 'create'])
        ->name('equipos.create');

    Route::post('/clientes/{codigo}/equipos', [EquipoController::class, 'store'])
        ->name('equipos.store');

    // Ruta para tu vista de cliente usando el controlador
    Route::get('/vistacliente', [ClienteDashboardController::class, 'vistaPrueba'])
        ->name('clientes.vistaprueba');
});

require __DIR__.'/auth.php';