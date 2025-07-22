<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // En MySQL necesitamos cambiar la columna para incluir el nuevo valor en el enum
        DB::statement("ALTER TABLE solicitud_dispositivos MODIFY prioridad ENUM('Baja', 'Media', 'Alta', 'Critica') NOT NULL DEFAULT 'Media'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Volvemos al enum original, podría causar errores si ya hay datos con 'Critica'
        DB::statement("ALTER TABLE solicitud_dispositivos MODIFY prioridad ENUM('Baja', 'Media', 'Alta') NOT NULL DEFAULT 'Media'");
    }
};
