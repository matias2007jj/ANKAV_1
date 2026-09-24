<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tipos_extintor', function (Blueprint $table) {
            $table->id();
            $table->string('nombre')->unique();
            $table->timestamps();
        });

        Schema::create('marcas', function (Blueprint $table) {
            $table->id();
            $table->string('nombre')->unique();
            $table->timestamps();
        });

        Schema::create('capacidades', function (Blueprint $table) {
            $table->id();
            $table->string('valor')->unique(); // ej. "06 KG", "2.5 LB", "1.6 GL"
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('capacidades');
        Schema::dropIfExists('marcas');
        Schema::dropIfExists('tipos_extintor');
    }
};
