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
        // Primero cambiamos los valores existentes para que coincidan con el nuevo enum
        DB::statement("UPDATE solicitud_dispositivos SET estado = 'Aprobado' WHERE estado = 'Aprobada'");
        DB::statement("UPDATE solicitud_dispositivos SET estado = 'Rechazado' WHERE estado = 'Rechazada'");

        // Luego modificamos la columna para usar los nuevos valores del enum
        DB::statement("ALTER TABLE solicitud_dispositivos MODIFY COLUMN estado ENUM('Pendiente', 'Aprobado', 'Rechazado', 'Completado') NOT NULL DEFAULT 'Pendiente'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Cambiamos de vuelta los valores
        DB::statement("UPDATE solicitud_dispositivos SET estado = 'Aprobada' WHERE estado = 'Aprobado'");
        DB::statement("UPDATE solicitud_dispositivos SET estado = 'Rechazada' WHERE estado = 'Rechazado'");

        // Restauramos el enum original (sin Completado)
        DB::statement("ALTER TABLE solicitud_dispositivos MODIFY COLUMN estado ENUM('Pendiente', 'Aprobada', 'Rechazada') NOT NULL DEFAULT 'Pendiente'");
    }
};
