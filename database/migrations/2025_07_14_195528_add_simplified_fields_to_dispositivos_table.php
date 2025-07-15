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
        Schema::table('dispositivos', function (Blueprint $table) {
            // Información del Fabricante y Modelo
            $table->string('marca')->nullable()->after('numero_serie');
            $table->string('modelo')->nullable()->after('marca');
            
            // Información de Identificación
            $table->string('codigo_activo')->nullable()->unique()->after('modelo');
            $table->string('etiqueta_inventario')->nullable()->after('codigo_activo');
            
            // Información Financiera
            $table->decimal('costo_adquisicion', 12, 2)->nullable()->after('fecha_compra');
            $table->string('moneda', 3)->default('PEN')->after('costo_adquisicion');
            $table->string('proveedor')->nullable()->after('moneda');
            
            // Información de Garantía
            $table->date('fecha_garantia')->nullable()->after('proveedor');
            $table->string('tipo_garantia')->nullable()->after('fecha_garantia');
            
            // Información de Ciclo de Vida
            $table->date('fecha_instalacion')->nullable()->after('tipo_garantia');
            $table->integer('vida_util_anos')->nullable()->after('fecha_instalacion');
            
            // Especificaciones Técnicas Generales
            $table->json('especificaciones_tecnicas')->nullable()->after('vida_util_anos');
            $table->string('color')->nullable()->after('especificaciones_tecnicas');
            
            // Conectividad
            $table->string('tipo_conexion')->nullable()->after('color');
            
            // Observaciones
            $table->text('observaciones')->nullable()->after('tipo_conexion');
            $table->text('accesorios_incluidos')->nullable()->after('observaciones');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('dispositivos', function (Blueprint $table) {
            $table->dropColumn([
                'marca',
                'modelo',
                'codigo_activo',
                'etiqueta_inventario',
                'costo_adquisicion',
                'moneda',
                'proveedor',
                'fecha_garantia',
                'tipo_garantia',
                'fecha_instalacion',
                'vida_util_anos',
                'especificaciones_tecnicas',
                'color',
                'tipo_conexion',
                'observaciones',
                'accesorios_incluidos'
            ]);
        });
    }
};
