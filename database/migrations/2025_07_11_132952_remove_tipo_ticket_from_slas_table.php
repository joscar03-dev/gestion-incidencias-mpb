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
            $table->dropColumn('tipo_ticket');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('slas', function (Blueprint $table) {
            $table->string('tipo_ticket')->default('General')->after('tiempo_resolucion');
        });
    }
};
