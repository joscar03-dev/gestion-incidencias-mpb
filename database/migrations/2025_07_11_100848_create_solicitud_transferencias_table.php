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
        Schema::create('solicitud_transferencias', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dispositivo_id')->constrained()->onDelete('cascade');
            $table->foreignId('usuario_origen_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('usuario_destino_id')->constrained('users')->onDelete('cascade');
            $table->text('motivo');
            $table->enum('estado', ['Pendiente', 'Aprobada', 'Rechazada', 'Ejecutada'])->default('Pendiente');
            $table->datetime('fecha_solicitud');
            $table->datetime('fecha_respuesta')->nullable();
            $table->datetime('fecha_ejecucion')->nullable();
            $table->text('observaciones_admin')->nullable();
            $table->foreignId('admin_respuesta_id')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();

            // Índices para optimizar consultas
            $table->index(['dispositivo_id', 'estado']);
            $table->index(['usuario_origen_id', 'estado']);
            $table->index(['usuario_destino_id', 'estado']);
            $table->index(['fecha_solicitud']);
            $table->index(['estado']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('solicitud_transferencias');
    }
};
