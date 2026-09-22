<?php

use App\Http\Controllers\ClienteDashboardController;

// Agrega esta línea dentro de tu routes/web.php existente
// (idealmente protegida con middleware ['auth'] si el sistema requiere login)
Route::get('/clientes/{codigo}/dashboard', [ClienteDashboardController::class, 'show'])
    ->name('clientes.dashboard');
