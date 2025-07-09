<?php

namespace Database\Seeders;

use App\Models\Area;
use App\Models\Sla;
use Illuminate\Database\Seeder;

class SlaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Configuraciones de SLA por área (en minutos)
        $configuracionesSla = [
            'Tecnología' => [
                'nivel' => 'Alto',
                'tiempo_respuesta_min' => 30,    // 30 minutos
                'tiempo_resolucion_min' => 240,  // 4 horas
                'descripcion' => 'SLA para área de tecnología con alta prioridad',
                'escalamiento_automatico' => true,
                'tiempo_escalamiento' => 60,     // 1 hora para escalamiento
                'override_area' => true
            ],
            'Recursos Humanos' => [
                'nivel' => 'Medio',
                'tiempo_respuesta_min' => 60,    // 1 hora
                'tiempo_resolucion_min' => 480,  // 8 horas
                'descripcion' => 'SLA para área de recursos humanos',
                'escalamiento_automatico' => true,
                'tiempo_escalamiento' => 120,    // 2 horas para escalamiento
                'override_area' => false
            ],
            'Administración' => [
                'nivel' => 'Medio',
                'tiempo_respuesta_min' => 90,    // 1.5 horas
                'tiempo_resolucion_min' => 720,  // 12 horas
                'descripcion' => 'SLA para área administrativa',
                'escalamiento_automatico' => true,
                'tiempo_escalamiento' => 180,    // 3 horas para escalamiento
                'override_area' => false
            ],
            'Mantenimiento' => [
                'nivel' => 'Bajo',
                'tiempo_respuesta_min' => 120,   // 2 horas
                'tiempo_resolucion_min' => 1440, // 24 horas
                'descripcion' => 'SLA para área de mantenimiento',
                'escalamiento_automatico' => false,
                'tiempo_escalamiento' => null,
                'override_area' => false
            ],
            'Limpieza' => [
                'nivel' => 'Bajo',
                'tiempo_respuesta_min' => 240,   // 4 horas
                'tiempo_resolucion_min' => 2880, // 48 horas
                'descripcion' => 'SLA para área de limpieza',
                'escalamiento_automatico' => false,
                'tiempo_escalamiento' => null,
                'override_area' => false
            ]
        ];

        foreach ($configuracionesSla as $nombreArea => $configSla) {
            // Buscar o crear el área
            $area = Area::firstOrCreate(
                ['nombre' => $nombreArea],
                ['descripcion' => "Área de {$nombreArea}"]
            );

            // Crear SLA para el área (ahora con valores enteros en minutos)
            Sla::firstOrCreate(
                ['area_id' => $area->id],
                [
                    'nivel' => $configSla['nivel'],
                    'tiempo_respuesta' => $configSla['tiempo_respuesta_min'],
                    'tiempo_resolucion' => $configSla['tiempo_resolucion_min'],
                    'tipo_ticket' => 'General',
                    'canal' => 'Sistema',
                    'descripcion' => $configSla['descripcion'],
                    'activo' => true,
                    'escalamiento_automatico' => $configSla['escalamiento_automatico'],
                    'tiempo_escalamiento' => $configSla['tiempo_escalamiento'],
                    'override_area' => $configSla['override_area'],
                ]
            );

            $this->command->info("SLA creado para área: {$nombreArea}");
        }

        $this->command->info('Configuración de SLA completada!');
        $this->command->info('');
        $this->command->info('📊 Resumen de tiempos por prioridad:');
        $this->command->info('');
        $this->command->info('🔴 CRÍTICA (20% del tiempo base):');
        $this->command->info('   - IT: 6min respuesta / 48min resolución');
        $this->command->info('   - RRHH: 12min respuesta / 96min resolución');
        $this->command->info('');
        $this->command->info('🟠 ALTA (50% del tiempo base):');
        $this->command->info('   - IT: 15min respuesta / 2h resolución');
        $this->command->info('   - RRHH: 30min respuesta / 4h resolución');
        $this->command->info('');
        $this->command->info('🟡 MEDIA (100% del tiempo base):');
        $this->command->info('   - IT: 30min respuesta / 4h resolución');
        $this->command->info('   - RRHH: 1h respuesta / 8h resolución');
        $this->command->info('');
        $this->command->info('🟢 BAJA (150% del tiempo base):');
        $this->command->info('   - IT: 45min respuesta / 6h resolución');
        $this->command->info('   - RRHH: 1.5h respuesta / 12h resolución');
    }
}
