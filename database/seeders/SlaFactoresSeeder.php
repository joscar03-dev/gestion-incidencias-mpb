<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\SlaPrioridadFactor;
use App\Models\SlaTipoFactor;

class SlaFactoresSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Insertar factores de prioridad
        $prioridadFactores = [
            [
                'codigo' => 'critica',
                'nombre' => 'Crítica',
                'descripcion' => 'Muy Urgente - 20% del tiempo normal',
                'factor' => 0.20,
                'orden' => 1,
                'activo' => true
            ],
            [
                'codigo' => 'alta',
                'nombre' => 'Alta',
                'descripcion' => 'Urgente - 50% del tiempo normal',
                'factor' => 0.50,
                'orden' => 2,
                'activo' => true
            ],
            [
                'codigo' => 'media',
                'nombre' => 'Media',
                'descripcion' => 'Normal - 100% del tiempo normal',
                'factor' => 1.00,
                'orden' => 3,
                'activo' => true
            ],
            [
                'codigo' => 'baja',
                'nombre' => 'Baja',
                'descripcion' => 'Menos Urgente - 150% del tiempo normal',
                'factor' => 1.50,
                'orden' => 4,
                'activo' => true
            ]
        ];

        foreach ($prioridadFactores as $factor) {
            SlaPrioridadFactor::updateOrCreate(
                ['codigo' => $factor['codigo']],
                $factor
            );
        }

        // Insertar factores de tipo
        $tipoFactores = [
            [
                'codigo' => 'incidente',
                'nombre' => 'Incidente',
                'descripcion' => 'Respuesta rápida - 60% del tiempo',
                'factor' => 0.60,
                'orden' => 1,
                'activo' => true
            ],
            [
                'codigo' => 'general',
                'nombre' => 'General',
                'descripcion' => 'Consulta importante - 80% del tiempo',
                'factor' => 0.80,
                'orden' => 2,
                'activo' => true
            ],
            [
                'codigo' => 'requerimiento',
                'nombre' => 'Requerimiento',
                'descripcion' => 'Planificación - 120% del tiempo',
                'factor' => 1.20,
                'orden' => 3,
                'activo' => true
            ],
            [
                'codigo' => 'cambio',
                'nombre' => 'Cambio',
                'descripcion' => 'Requiere análisis - 150% del tiempo',
                'factor' => 1.50,
                'orden' => 4,
                'activo' => true
            ]
        ];

        foreach ($tipoFactores as $factor) {
            SlaTipoFactor::updateOrCreate(
                ['codigo' => $factor['codigo']],
                $factor
            );
        }
    }
}
