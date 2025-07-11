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
        Schema::table('dispositivo_asignacions', function (Blueprint $table) {
            $table->boolean('confirmado')->default(false)->after('fecha_desasignacion');
            $table->datetime('fecha_confirmacion')->nullable()->after('confirmado');
            $table->text('motivo_asignacion')->nullable()->after('fecha_confirmacion');
            $table->text('motivo_desasignacion')->nullable()->after('motivo_asignacion');
            $table->text('observaciones')->nullable()->after('motivo_desasignacion');
            $table->foreignId('solicitud_dispositivo_id')->nullable()->constrained()->onDelete('set null')->after('observaciones');
            $table->foreignId('solicitud_transferencia_id')->nullable()->constrained()->onDelete('set null')->after('solicitud_dispositivo_id');

            // Índices
            $table->index(['confirmado']);
            $table->index(['fecha_confirmacion']);
            $table->index(['solicitud_dispositivo_id']);
            $table->index(['solicitud_transferencia_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('dispositivo_asignacions', function (Blueprint $table) {
            $table->dropForeign(['solicitud_dispositivo_id']);
            $table->dropForeign(['solicitud_transferencia_id']);
            $table->dropColumn([
                'confirmado',
                'fecha_confirmacion',
                'motivo_asignacion',
                'motivo_desasignacion',
                'observaciones',
                'solicitud_dispositivo_id',
                'solicitud_transferencia_id'
            ]);
        });
    }
};
