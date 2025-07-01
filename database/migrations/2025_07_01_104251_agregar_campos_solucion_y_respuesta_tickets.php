<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->time('tiempo_respuesta')->nullable()->after('prioridad'); // Tiempo de respuesta en horas
            $table->time('tiempo_solucion')->nullable()->after('tiempo_respuesta');  // Tiempo de solución en horas
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->dropColumn(['tiempo_respuesta', 'tiempo_solucion']);
        });
    }
};
