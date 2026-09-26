<?php

use App\Http\Controllers\ClienteController;
use App\Http\Controllers\ClienteDashboardController;
use App\Http\Controllers\EquipoController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PrincipalController;
use Illuminate\Support\Facades\Route;

// Ruta principal conectada al controlador y con nombre 'principal'
Route::get('/principal', [PrincipalController::class, 'index'])->name('principal');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth'])->group(function () {
    // Perfil
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Clientes
    Route::get('/clientes', [ClienteController::class, 'index'])->name('clientes.index');
    Route::get('/clientes/{codigo}/dashboard', [ClienteDashboardController::class, 'show'])->name('clientes.dashboard');

    // Equipos
    Route::get('/clientes/{codigo}/equipos/nuevo', [EquipoController::class, 'create'])->name('equipos.create');
    Route::post('/clientes/{codigo}/equipos', [EquipoController::class, 'store'])->name('equipos.store');
    Route::get('/clientes/{codigo}/equipos/{id}/editar', [EquipoController::class, 'edit'])->name('equipos.edit');
    Route::put('/clientes/{codigo}/equipos/{id}', [EquipoController::class, 'update'])->name('equipos.update');
    
    // --- RUTA PARA DAR DE BAJA (ELIMINAR) ---
    Route::delete('/clientes/{codigo}/equipos/{id}', [EquipoController::class, 'destroy'])->name('equipos.destroy');

    // Exportaciones y Pruebas
    Route::get('/clientes/{codigo}/exportar-excel', [ClienteDashboardController::class, 'exportarExcel'])->name('clientes.exportar.excel');
    Route::get('/clientes/{codigo}/exportar-pdf', [ClienteDashboardController::class, 'exportarPdf'])->name('clientes.exportar.pdf');
    Route::get('/vistacliente', [ClienteDashboardController::class, 'vistaPrueba'])->name('clientes.vistaprueba');
});

require __DIR__ . '/auth.php';