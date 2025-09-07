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
        $this->command->info('🎯 Iniciando creación de SLAs para todas las áreas municipales...');

        // Configuraciones de SLA según criticidad y función del área
        // Tiempos base más realistas según el tipo de área
        $configuracionesSla = [
            // ÓRGANOS DE GOBIERNO - Máxima prioridad (respuesta inmediata)
            'CONCEJO MUNICIPAL' => [
                'nivel' => 'Crítico',
                'tiempo_respuesta' => 10,    // 10 minutos - Máxima urgencia
                'tiempo_resolucion' => 60,   // 1 hora - Resolución rápida
                'descripcion' => 'SLA crítico para órgano normativo y fiscalizador',
                'escalamiento_automatico' => true,
                'tiempo_escalamiento' => 15, // Escala en 15 minutos
                'override_area' => true,
                'prioridad_base' => 'critico'
            ],
            'ALCALDÍA' => [
                'nivel' => 'Crítico',
                'tiempo_respuesta' => 10,    // 10 minutos - Máxima urgencia
                'tiempo_resolucion' => 60,   // 1 hora - Resolución rápida
                'descripcion' => 'SLA crítico para órgano ejecutivo municipal',
                'escalamiento_automatico' => true,
                'tiempo_escalamiento' => 15, // Escala en 15 minutos
                'override_area' => true,
                'prioridad_base' => 'critico'
            ],

            // ÓRGANOS DE CONTROL - Alta prioridad
            'ÓRGANO DE CONTROL INSTITUCIONAL' => [
                'nivel' => 'Alto',
                'tiempo_respuesta' => 20,    // 20 minutos
                'tiempo_resolucion' => 180,  // 3 horas
                'descripcion' => 'SLA alto para control interno institucional',
                'escalamiento_automatico' => true,
                'tiempo_escalamiento' => 45,
                'override_area' => true,
                'prioridad_base' => 'alto'
            ],
            'PROCURADURÍA PÚBLICA MUNICIPAL' => [
                'nivel' => 'Alto',
                'tiempo_respuesta' => 20,    // 20 minutos
                'tiempo_resolucion' => 180,  // 3 horas
                'descripcion' => 'SLA alto para defensa legal municipal',
                'escalamiento_automatico' => true,
                'tiempo_escalamiento' => 45,
                'override_area' => true,
                'prioridad_base' => 'alto'
            ],

            // GERENCIA MUNICIPAL - Alta prioridad
            'GERENCIA MUNICIPAL' => [
                'nivel' => 'Alto',
                'tiempo_respuesta' => 20,    // 20 minutos
                'tiempo_resolucion' => 180,  // 3 horas
                'descripcion' => 'SLA alto para gerencia municipal',
                'escalamiento_automatico' => true,
                'tiempo_escalamiento' => 45,
                'override_area' => true,
                'prioridad_base' => 'alto'
            ],

            // OFICINAS CRÍTICAS - IT es crítico, otros son altos
            'OFICINA DE TECNOLOGÍAS DE LA INFORMACIÓN' => [
                'nivel' => 'Crítico',
                'tiempo_respuesta' => 15,    // 15 minutos - IT es crítico
                'tiempo_resolucion' => 120,  // 2 horas
                'descripcion' => 'SLA crítico para tecnologías de la información',
                'escalamiento_automatico' => true,
                'tiempo_escalamiento' => 30,
                'override_area' => true,
                'prioridad_base' => 'critico'
            ],
            'OFICINA DE SECRETARÍA GENERAL: ATENCIÓN AL CIUDADANO Y GESTIÓN DOCUMENTARIA' => [
                'nivel' => 'Alto',
                'tiempo_respuesta' => 30,    // 30 minutos - Atención ciudadana
                'tiempo_resolucion' => 240,  // 4 horas
                'descripcion' => 'SLA alto para atención ciudadana y gestión documentaria',
                'escalamiento_automatico' => true,
                'tiempo_escalamiento' => 60,
                'override_area' => true,
                'prioridad_base' => 'alto'
            ],
            'OFICINA DE CONTABILIDAD' => [
                'nivel' => 'Alto',
                'tiempo_respuesta' => 30,    // 30 minutos
                'tiempo_resolucion' => 240,  // 4 horas
                'descripcion' => 'SLA alto para contabilidad municipal',
                'escalamiento_automatico' => true,
                'tiempo_escalamiento' => 60,
                'override_area' => true,
                'prioridad_base' => 'alto'
            ],
            'OFICINA DE TESORERÍA' => [
                'nivel' => 'Alto',
                'tiempo_respuesta' => 30,    // 30 minutos
                'tiempo_resolucion' => 240,  // 4 horas
                'descripcion' => 'SLA alto para tesorería municipal',
                'escalamiento_automatico' => true,
                'tiempo_escalamiento' => 60,
                'override_area' => true,
                'prioridad_base' => 'alto'
            ],
            'OFICINA DE GESTIÓN DE RECURSOS HUMANOS' => [
                'nivel' => 'Medio',
                'tiempo_respuesta' => 60,    // 1 hora
                'tiempo_resolucion' => 480,  // 8 horas
                'descripcion' => 'SLA medio para gestión de recursos humanos',
                'escalamiento_automatico' => true,
                'tiempo_escalamiento' => 120,
                'override_area' => false,
                'prioridad_base' => 'medio'
            ],

            // GERENCIAS OPERATIVAS - Tributaria es crítica por ingresos
            'Gerencia de Administración Tributaria' => [
                'nivel' => 'Crítico',
                'tiempo_respuesta' => 15,    // 15 minutos - Ingresos municipales
                'tiempo_resolucion' => 120,  // 2 horas
                'descripcion' => 'SLA crítico para administración tributaria',
                'escalamiento_automatico' => true,
                'tiempo_escalamiento' => 30,
                'override_area' => true,
                'prioridad_base' => 'critico'
            ],
            'SUB GERENCIA DE RENTAS' => [
                'nivel' => 'Alto',
                'tiempo_respuesta' => 30,    // 30 minutos
                'tiempo_resolucion' => 240,  // 4 horas
                'descripcion' => 'SLA alto para recaudación de rentas',
                'escalamiento_automatico' => true,
                'tiempo_escalamiento' => 60,
                'override_area' => true,
                'prioridad_base' => 'alto'
            ],
            'SUB GERENCIA DE FISCALIZACIÓN TRIBUTARIA' => [
                'nivel' => 'Alto',
                'tiempo_respuesta' => 30,    // 30 minutos
                'tiempo_resolucion' => 240,  // 4 horas
                'descripcion' => 'SLA alto para fiscalización tributaria',
                'escalamiento_automatico' => true,
                'tiempo_escalamiento' => 60,
                'override_area' => true,
                'prioridad_base' => 'alto'
            ],

            // SERVICIOS MUNICIPALES - Seguridad es crítica
            'Gerencia de Servicios Municipales' => [
                'nivel' => 'Alto',
                'tiempo_respuesta' => 30,    // 30 minutos
                'tiempo_resolucion' => 360,  // 6 horas
                'descripcion' => 'SLA alto para servicios municipales',
                'escalamiento_automatico' => true,
                'tiempo_escalamiento' => 90,
                'override_area' => true,
                'prioridad_base' => 'alto'
            ],
            'Área de Registro Civil y Cementerio' => [
                'nivel' => 'Alto',
                'tiempo_respuesta' => 20,    // 20 minutos - Servicios vitales
                'tiempo_resolucion' => 180,  // 3 horas
                'descripcion' => 'SLA alto para servicios de registro civil',
                'escalamiento_automatico' => true,
                'tiempo_escalamiento' => 45,
                'override_area' => true,
                'prioridad_base' => 'alto'
            ],
            'Área de Seguridad Ciudadana y Serenazgo' => [
                'nivel' => 'Crítico',
                'tiempo_respuesta' => 5,     // 5 minutos - Emergencias
                'tiempo_resolucion' => 30,   // 30 minutos
                'descripcion' => 'SLA crítico para seguridad ciudadana',
                'escalamiento_automatico' => true,
                'tiempo_escalamiento' => 10, // Escala en 10 minutos
                'override_area' => true,
                'prioridad_base' => 'critico'
            ],

            // GESTIÓN AMBIENTAL - Residuos es prioritario
            'Gerencia de Gestión Ambiental y Residuos Sólidos' => [
                'nivel' => 'Medio',
                'tiempo_respuesta' => 90,    // 1.5 horas
                'tiempo_resolucion' => 720,  // 12 horas
                'descripcion' => 'SLA medio para gestión ambiental',
                'escalamiento_automatico' => true,
                'tiempo_escalamiento' => 180,
                'override_area' => false,
                'prioridad_base' => 'medio'
            ],
            'Área de Tratamiento, Recolección y Disposición Final de Residuos Sólidos' => [
                'nivel' => 'Alto',
                'tiempo_respuesta' => 45,    // 45 minutos - Servicio público esencial
                'tiempo_resolucion' => 360,  // 6 horas
                'descripcion' => 'SLA alto para gestión de residuos sólidos',
                'escalamiento_automatico' => true,
                'tiempo_escalamiento' => 120,
                'override_area' => true,
                'prioridad_base' => 'alto'
            ],

            // DESARROLLO TERRITORIAL - Obras puede ser urgente
            'Gerencia de Desarrollo Territorial e Infraestructura' => [
                'nivel' => 'Medio',
                'tiempo_respuesta' => 90,    // 1.5 horas
                'tiempo_resolucion' => 720,  // 12 horas
                'descripcion' => 'SLA medio para desarrollo territorial',
                'escalamiento_automatico' => true,
                'tiempo_escalamiento' => 240,
                'override_area' => false,
                'prioridad_base' => 'medio'
            ],
            'Área de Obras Públicas' => [
                'nivel' => 'Alto',
                'tiempo_respuesta' => 60,    // 1 hora - Emergencias de infraestructura
                'tiempo_resolucion' => 480,  // 8 horas
                'descripcion' => 'SLA alto para obras públicas',
                'escalamiento_automatico' => true,
                'tiempo_escalamiento' => 180,
                'override_area' => true,
                'prioridad_base' => 'alto'
            ],
            'Área de Catastro' => [
                'nivel' => 'Medio',
                'tiempo_respuesta' => 120,   // 2 horas
                'tiempo_resolucion' => 960,  // 16 horas
                'descripcion' => 'SLA medio para catastro municipal',
                'escalamiento_automatico' => false,
                'tiempo_escalamiento' => null,
                'override_area' => false,
                'prioridad_base' => 'medio'
            ],

            // DESARROLLO SOCIAL - DEMUNA es crítico por menores
            'Gerencia de Desarrollo Social' => [
                'nivel' => 'Medio',
                'tiempo_respuesta' => 90,    // 1.5 horas
                'tiempo_resolucion' => 480,  // 8 horas
                'descripcion' => 'SLA medio para desarrollo social',
                'escalamiento_automatico' => true,
                'tiempo_escalamiento' => 180,
                'override_area' => false,
                'prioridad_base' => 'medio'
            ],
            'Área de DEMUNA' => [
                'nivel' => 'Crítico',
                'tiempo_respuesta' => 15,    // 15 minutos - Protección de menores
                'tiempo_resolucion' => 120,  // 2 horas
                'descripcion' => 'SLA crítico para defensoría del niño y adolescente',
                'escalamiento_automatico' => true,
                'tiempo_escalamiento' => 30,
                'override_area' => true,
                'prioridad_base' => 'critico'
            ],
            'Área de Vaso de Leche y Comedores Populares' => [
                'nivel' => 'Alto',
                'tiempo_respuesta' => 45,    // 45 minutos - Programas alimentarios
                'tiempo_resolucion' => 240,  // 4 horas
                'descripcion' => 'SLA alto para programas alimentarios',
                'escalamiento_automatico' => true,
                'tiempo_escalamiento' => 90,
                'override_area' => true,
                'prioridad_base' => 'alto'
            ],

            // DESARROLLO ECONÓMICO - Licencias son importantes para empresas
            'Gerencia de Desarrollo Económico' => [
                'nivel' => 'Medio',
                'tiempo_respuesta' => 120,   // 2 horas
                'tiempo_resolucion' => 720,  // 12 horas
                'descripcion' => 'SLA medio para desarrollo económico',
                'escalamiento_automatico' => true,
                'tiempo_escalamiento' => 240,
                'override_area' => false,
                'prioridad_base' => 'medio'
            ],
            'Área de Licencias de Funcionamiento' => [
                'nivel' => 'Alto',
                'tiempo_respuesta' => 45,    // 45 minutos - Afecta actividad económica
                'tiempo_resolucion' => 360,  // 6 horas
                'descripcion' => 'SLA alto para licencias de funcionamiento',
                'escalamiento_automatico' => true,
                'tiempo_escalamiento' => 120,
                'override_area' => true,
                'prioridad_base' => 'alto'
            ],
            'Área de Control Sanitario' => [
                'nivel' => 'Alto',
                'tiempo_respuesta' => 30,    // 30 minutos - Salud pública
                'tiempo_resolucion' => 240,  // 4 horas
                'descripcion' => 'SLA alto para control sanitario',
                'escalamiento_automatico' => true,
                'tiempo_escalamiento' => 90,
                'override_area' => true,
                'prioridad_base' => 'alto'
            ],

            // ÁREAS DE APOYO - Menos críticas
            'OFICINA DE ABASTECIMIENTO' => [
                'nivel' => 'Medio',
                'tiempo_respuesta' => 120,   // 2 horas
                'tiempo_resolucion' => 960,  // 16 horas
                'descripcion' => 'SLA medio para abastecimiento',
                'escalamiento_automatico' => false,
                'tiempo_escalamiento' => null,
                'override_area' => false,
                'prioridad_base' => 'medio'
            ],
            'OFICINA DE BIENES PATRIMONIALES' => [
                'nivel' => 'Bajo',
                'tiempo_respuesta' => 240,   // 4 horas
                'tiempo_resolucion' => 1440, // 24 horas
                'descripcion' => 'SLA bajo para bienes patrimoniales',
                'escalamiento_automatico' => false,
                'tiempo_escalamiento' => null,
                'override_area' => false,
                'prioridad_base' => 'bajo'
            ]
        ];

        $this->createSlas($configuracionesSla);
        $this->showSummary();
    }

    private function createSlas($configuraciones)
    {
        $createdCount = 0;
        $existingCount = 0;

        foreach ($configuraciones as $nombreArea => $configSla) {
            // Buscar el área por nombre
            $area = Area::where('nombre', $nombreArea)->first();

            if (!$area) {
                $this->command->warn("⚠️  Área no encontrada: {$nombreArea}");
                continue;
            }

            // Verificar si ya existe un SLA para esta área
            $existingSla = Sla::where('area_id', $area->id)->first();

            if ($existingSla) {
                $this->command->line("ℹ️  SLA ya existe para: {$nombreArea}");
                $existingCount++;
                continue;
            }

            // Mapear nivel descriptivo a prioridad de ticket (enum de la BD)
            $prioridadTicket = match ($configSla['nivel']) {
                'Crítico' => 'critico',
                'Alto' => 'alto',
                'Medio' => 'medio',
                'Bajo' => 'bajo',
                default => 'medio'
            };

            // Crear SLA para el área
            Sla::create([
                'area_id' => $area->id,
                'nivel' => $configSla['nivel'],
                'tiempo_respuesta' => $configSla['tiempo_respuesta'],
                'tiempo_resolucion' => $configSla['tiempo_resolucion'],
                'canal' => 'Sistema Municipal',
                'descripcion' => $configSla['descripcion'],
                'activo' => true,
                'escalamiento_automatico' => $configSla['escalamiento_automatico'],
                'tiempo_escalamiento' => $configSla['tiempo_escalamiento'],
                'override_area' => $configSla['override_area'],
                'prioridad_ticket' => $prioridadTicket // Usar la prioridad mapeada correctamente
            ]);

            $nivel = $configSla['nivel'];
            $respuesta = $this->formatTime($configSla['tiempo_respuesta']);
            $resolucion = $this->formatTime($configSla['tiempo_resolucion']);

            $this->command->line("✅ SLA {$nivel} creado para: {$nombreArea} ({$respuesta} / {$resolucion}) - Prioridad: {$prioridadTicket}");
            $createdCount++;
        }

        $this->command->info("� SLAs creados: {$createdCount} | Ya existían: {$existingCount}");
    }

    private function formatTime($minutes)
    {
        if ($minutes < 60) {
            return "{$minutes}min";
        } elseif ($minutes < 1440) {
            $hours = round($minutes / 60, 1);
            return "{$hours}h";
        } else {
            $days = round($minutes / 1440, 1);
            return "{$days}d";
        }
    }

    private function showSummary()
    {
        $this->command->info('');
        $this->command->info('🎯 CONFIGURACIÓN DE SLAs COMPLETADA');
        $this->command->line('');

        $slasByLevel = Sla::with('area')
            ->selectRaw('nivel, COUNT(*) as count')
            ->groupBy('nivel')
            ->get();

        $this->command->table(
            ['Nivel SLA', 'Cantidad de Áreas'],
            $slasByLevel->map(function ($sla) {
                return [$sla->nivel, $sla->count];
            })->toArray()
        );

        $this->command->info('');
        $this->command->info('� CARACTERÍSTICAS DEL SISTEMA SLA:');
        $this->command->line('🔴 CRÍTICO: Respuesta ≤ 20min, Resolución ≤ 3h (Alcaldía, IT, Seguridad)');
        $this->command->line('🟠 ALTO: Respuesta ≤ 1h, Resolución ≤ 8h (Tributaria, Servicios)');
        $this->command->line('🟡 MEDIO: Respuesta ≤ 3h, Resolución ≤ 24h (Desarrollo, Obras)');
        $this->command->line('🟢 BAJO: Respuesta ≥ 4h, Resolución ≥ 48h (Apoyo, Patrimonio)');
        $this->command->line('');
        $this->command->info('⚡ Escalamiento automático habilitado para áreas críticas y altas');
        $this->command->info('🎛️  Override de prioridad habilitado para áreas estratégicas');
    }
}
