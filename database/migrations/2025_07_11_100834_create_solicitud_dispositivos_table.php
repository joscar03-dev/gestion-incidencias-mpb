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
        Schema::create('solicitud_dispositivos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('categoria_dispositivo_id')->constrained()->onDelete('cascade');
            $table->text('justificacion');
            $table->enum('prioridad', ['Baja', 'Media', 'Alta'])->default('Media');
            $table->enum('estado', ['Pendiente', 'Aprobada', 'Rechazada'])->default('Pendiente');
            $table->datetime('fecha_solicitud');
            $table->datetime('fecha_respuesta')->nullable();
            $table->text('observaciones_admin')->nullable();
            $table->foreignId('admin_respuesta_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('dispositivo_asignado_id')->nullable()->constrained('dispositivos')->onDelete('set null');
            $table->timestamps();

            // Índices para optimizar consultas
            $table->index(['user_id', 'estado']);
            $table->index(['categoria_dispositivo_id', 'estado']);
            $table->index(['fecha_solicitud']);
            $table->index(['prioridad', 'estado']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('solicitud_dispositivos');
    }
};
