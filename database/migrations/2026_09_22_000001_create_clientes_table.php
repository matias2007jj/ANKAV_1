<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('clientes', function (Blueprint $table) {
            $table->id();
            $table->string('codigo_cliente')->unique();
            $table->string('razon_social');
            $table->string('nombre_comercial')->nullable();
            $table->string('ruc', 20)->nullable();
            $table->string('direccion')->nullable();
            $table->string('provincia')->nullable();
            $table->string('contacto')->nullable();
            $table->string('cargo')->nullable();
            $table->string('correo')->nullable();
            $table->string('telefono')->nullable();
            $table->string('forma_pago')->nullable();
            $table->string('moneda')->nullable();
            $table->string('lista_precios')->nullable();
            $table->string('ejecutivo_asignado')->nullable();
            $table->string('estado')->nullable();
            $table->text('observaciones')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('clientes');
    }
};
