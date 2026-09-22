<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('equipos', function (Blueprint $table) {
            $table->id();
            $table->string('codigo_cliente');
            $table->foreign('codigo_cliente')->references('codigo_cliente')->on('clientes')->cascadeOnUpdate();
            $table->string('codigo_sede')->nullable();
            $table->foreign('codigo_sede')->references('codigo_sede')->on('sedes')->nullOnDelete()->cascadeOnUpdate();
            $table->string('numero_serie')->nullable();
            $table->string('numero_interno')->nullable();
            $table->string('tipo_extintor')->nullable();
            $table->string('capacidad_carga')->nullable();
            $table->string('marca')->nullable();
            $table->unsignedSmallInteger('anio_fabricacion')->nullable();
            // Texto tal cual venía del Excel (ej. "ABR-2027"), no siempre es una fecha válida
            $table->string('proximo_mantenimiento')->nullable();
            $table->date('proximo_mantenimiento_real')->nullable();
            $table->date('vencimiento_ph')->nullable();
            $table->string('estado')->nullable();
            $table->date('fecha_ultimo_servicio')->nullable();
            $table->string('numero_certificado')->nullable();
            // Rutas locales en el disco del hosting (NO urls externas / Drive)
            $table->string('ruta_cert_operatividad')->nullable();
            $table->string('ruta_informe_tecnico')->nullable();
            $table->string('ruta_cert_ph')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('equipos');
    }
};
