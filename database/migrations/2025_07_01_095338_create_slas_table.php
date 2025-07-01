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
        Schema::create('slas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('area_id')->constrained('areas')->onDelete('cascade');
            $table->string('nivel'); // Prioridad: Alta, Media, Baja
            $table->time('tiempo_respuesta'); // Tiempo de respuesta en formato HH:MM:SS
            $table->time('tiempo_resolucion'); // Tiempo de resolución en formato HH:MM:SS
            $table->string('tipo_ticket')->nullable(); // Incidente, Solicitud, etc.
            $table->string('canal')->nullable(); // Email, Teléfono, Portal, etc.
            $table->text('descripcion')->nullable();
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('slas');
    }
};
