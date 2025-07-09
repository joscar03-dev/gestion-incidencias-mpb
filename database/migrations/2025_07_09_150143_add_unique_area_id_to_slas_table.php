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
            // Agregar índice único para area_id
            $table->unique('area_id', 'slas_area_id_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('slas', function (Blueprint $table) {
            // Eliminar el índice único
            $table->dropUnique('slas_area_id_unique');
        });
    }
};
