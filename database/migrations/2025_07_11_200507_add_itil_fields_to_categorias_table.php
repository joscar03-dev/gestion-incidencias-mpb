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
        Schema::table('categorias', function (Blueprint $table) {
            $table->text('descripcion')->nullable()->after('nombre');
            $table->enum('tipo_categoria', ['incidente', 'solicitud_servicio', 'cambio', 'problema', 'general'])
                  ->default('general')->after('descripcion');
            $table->boolean('itil_category')->default(false)->after('tipo_categoria');
            $table->enum('prioridad_default', ['baja', 'media', 'alta', 'critica'])
                  ->default('media')->after('itil_category');
            $table->integer('sla_horas')->default(24)->after('prioridad_default');
            $table->string('color', 7)->default('#6B7280')->after('sla_horas');
            $table->string('icono')->nullable()->after('color');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('categorias', function (Blueprint $table) {
            $table->dropColumn([
                'descripcion',
                'tipo_categoria',
                'itil_category',
                'prioridad_default',
                'sla_horas',
                'color',
                'icono'
            ]);
        });
    }
};
