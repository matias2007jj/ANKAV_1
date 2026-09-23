<?php

use App\Http\Controllers\ClienteDashboardController;

Route::get('/clientes/{codigo}/dashboard', [ClienteDashboardController::class, 'show'])
    ->name('clientes.dashboard');
