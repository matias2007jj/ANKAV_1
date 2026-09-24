<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // 'administrador' ve todos los clientes. 'cliente' solo ve el suyo (codigo_cliente).
            $table->enum('role', ['administrador', 'cliente'])->default('cliente')->after('email');
            $table->string('codigo_cliente')->nullable()->after('role');
            $table->foreign('codigo_cliente')->references('codigo_cliente')->on('clientes')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['codigo_cliente']);
            $table->dropColumn(['role', 'codigo_cliente']);
        });
    }
};
