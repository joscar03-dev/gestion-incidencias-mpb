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
            $table->string('documento_requerimiento')->nullable()->after('justificacion');
            $table->index(['documento_requerimiento']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('solicitud_dispositivos', function (Blueprint $table) {
            $table->dropColumn('documento_requerimiento');
        });
    }
};
