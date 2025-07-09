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
        Schema::table('slas', function (Blueprint $table) {
            // Campos para el sistema híbrido de SLA
            $table->enum('prioridad_ticket', ['critico', 'alto', 'medio', 'bajo'])->nullable()->after('activo');
            $table->boolean('override_area')->default(false)->after('prioridad_ticket');
            $table->boolean('escalamiento_automatico')->default(false)->after('override_area');
            $table->integer('tiempo_escalamiento')->nullable()->comment('Tiempo en minutos para escalamiento')->after('escalamiento_automatico');

            // Índice para mejorar consultas
            $table->index(['activo', 'prioridad_ticket']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('slas', function (Blueprint $table) {
            $table->dropIndex(['activo', 'prioridad_ticket']);
            $table->dropColumn([
                'prioridad_ticket',
                'override_area',
                'escalamiento_automatico',
                'tiempo_escalamiento'
            ]);
        });
    }
};
