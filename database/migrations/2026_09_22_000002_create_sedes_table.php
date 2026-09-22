<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sedes', function (Blueprint $table) {
            $table->id();
            $table->string('codigo_sede')->unique();
            $table->string('codigo_cliente');
            $table->foreign('codigo_cliente')->references('codigo_cliente')->on('clientes')->cascadeOnUpdate();
            $table->string('nombre_sede')->nullable();
            $table->string('direccion')->nullable();
            $table->string('distrito')->nullable();
            $table->string('provincia')->nullable();
            $table->string('departamento')->nullable();
            $table->string('contacto')->nullable();
            $table->string('correo')->nullable();
            $table->string('telefono')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sedes');
    }
};
