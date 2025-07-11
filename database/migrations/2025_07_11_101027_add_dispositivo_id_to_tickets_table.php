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
        Schema::table('tickets', function (Blueprint $table) {
            $table->foreignId('dispositivo_id')->nullable()->constrained()->onDelete('set null')->after('usuario_id');
            $table->boolean('requiere_reemplazo')->default(false)->after('dispositivo_id');

            // Índices
            $table->index(['dispositivo_id']);
            $table->index(['dispositivo_id', 'estado']);
            $table->index(['requiere_reemplazo']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->dropForeign(['dispositivo_id']);
            $table->dropColumn(['dispositivo_id', 'requiere_reemplazo']);
        });
    }
};
