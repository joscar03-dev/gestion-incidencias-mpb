<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('tickets', function (Blueprint $table) {
            // Campos para el sistema híbrido de SLA
            $table->foreignId('area_id')->nullable()->after('creado_por')->constrained()->onDelete('set null');
            $table->boolean('escalado')->default(false)->after('fecha_cierre');
            $table->timestamp('fecha_escalamiento')->nullable()->after('escalado');
            $table->boolean('sla_vencido')->default(false)->after('fecha_escalamiento');

            // Índices para mejorar rendimiento
            $table->index(['estado', 'prioridad']);
            $table->index(['escalado', 'sla_vencido']);
            $table->index(['created_at', 'estado']);
        });
    }

    public function down()
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->dropForeign(['area_id']);
            $table->dropColumn([
                'area_id',
                'escalado',
                'fecha_escalamiento',
                'sla_vencido'
            ]);

            $table->dropIndex(['estado', 'prioridad']);
            $table->dropIndex(['escalado', 'sla_vencido']);
            $table->dropIndex(['created_at', 'estado']);
        });
    }
};
