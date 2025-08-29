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
        Schema::create('sla_tipo_factores', function (Blueprint $table) {
            $table->id();
            $table->string('codigo')->unique(); // incidente, general, requerimiento, cambio
            $table->string('nombre'); // Incidente, General, Requerimiento, Cambio
            $table->text('descripcion')->nullable();
            $table->decimal('factor', 4, 2); // 0.60, 0.80, 1.20, 1.50
            $table->boolean('activo')->default(true);
            $table->integer('orden')->default(0); // para ordenar en la interfaz
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sla_tipo_factores');
    }
};
