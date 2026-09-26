<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('equipos', function (Blueprint $table) {
            // Guardamos como texto "MES-AÑO" (ej. "JUL-2027"), igual que proximo_mantenimiento,
            // para poder mostrar el formato pedido y también admitir 'S/N'.
            $table->string('fecha_prueba_hidrostatica')->nullable()->after('vencimiento_ph');
            $table->string('fecha_ejecucion_servicio')->nullable()->after('fecha_ultimo_servicio');
        });

        // Los 348 equipos que ya existían no tienen este dato: los llenamos con S/N
        DB::table('equipos')->update([
            'fecha_prueba_hidrostatica' => 'S/N',
            'fecha_ejecucion_servicio' => 'S/N',
        ]);
    }

    public function down(): void
    {
        Schema::table('equipos', function (Blueprint $table) {
            $table->dropColumn(['fecha_prueba_hidrostatica', 'fecha_ejecucion_servicio']);
        });
    }
};
