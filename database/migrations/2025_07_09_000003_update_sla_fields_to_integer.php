<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // Primero, convertir los datos existentes de TIME a minutos
        DB::statement("
            UPDATE slas 
            SET tiempo_respuesta = TIME_TO_SEC(tiempo_respuesta) / 60,
                tiempo_resolucion = TIME_TO_SEC(tiempo_resolucion) / 60
        ");

        // Cambiar el tipo de columna de TIME a INTEGER
        Schema::table('slas', function (Blueprint $table) {
            $table->integer('tiempo_respuesta')->change();
            $table->integer('tiempo_resolucion')->change();
        });
    }

    public function down()
    {
        // Convertir de vuelta a formato TIME
        DB::statement("
            UPDATE slas 
            SET tiempo_respuesta = SEC_TO_TIME(tiempo_respuesta * 60),
                tiempo_resolucion = SEC_TO_TIME(tiempo_resolucion * 60)
        ");

        // Cambiar el tipo de columna de INTEGER a TIME
        Schema::table('slas', function (Blueprint $table) {
            $table->time('tiempo_respuesta')->change();
            $table->time('tiempo_resolucion')->change();
        });
    }
};