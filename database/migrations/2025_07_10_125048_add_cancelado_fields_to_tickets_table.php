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
            // Agregar campo para fecha de resolución (para cerrado y cancelado)
            $table->timestamp('fecha_resolucion')->nullable()->after('fecha_cierre');

            // Agregar campo para comentarios de resolución
            $table->text('comentarios_resolucion')->nullable()->after('fecha_resolucion');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->dropColumn(['fecha_resolucion', 'comentarios_resolucion']);
        });
    }
};
