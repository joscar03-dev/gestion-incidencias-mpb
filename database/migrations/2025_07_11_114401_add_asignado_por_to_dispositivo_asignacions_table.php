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
            $table->unsignedBigInteger('asignado_por')->nullable()->after('fecha_desasignacion');
            $table->foreign('asignado_por')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('dispositivo_asignacions', function (Blueprint $table) {
            $table->dropForeign(['asignado_por']);
            $table->dropColumn('asignado_por');
        });
    }
};
