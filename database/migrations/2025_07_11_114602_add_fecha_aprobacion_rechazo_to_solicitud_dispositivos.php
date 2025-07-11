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
        Schema::table('solicitud_dispositivos', function (Blueprint $table) {
            $table->timestamp('fecha_aprobacion')->nullable()->after('fecha_respuesta');
            $table->timestamp('fecha_rechazo')->nullable()->after('fecha_aprobacion');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('solicitud_dispositivos', function (Blueprint $table) {
            $table->dropColumn(['fecha_aprobacion', 'fecha_rechazo']);
        });
    }
};
