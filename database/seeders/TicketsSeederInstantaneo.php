<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Ticket;
use App\Models\User;
use App\Models\Area;
use Carbon\Carbon;

class TicketsSeederInstantaneo extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🎫 Iniciando creación de tickets históricos con RESOLUCIÓN INSTANTÁNEA...');

        // Limpiar tickets existentes
        Ticket::query()->delete();

        // Obtener datos existentes
        $usuarios = User::with(['area', 'roles'])->get();
        $tecnicos = $usuarios->filter(fn($u) => $u->hasRole('Técnico'));
        $usuariosNormales = $usuarios->filter(fn($u) => $u->hasRole('Usuario'));

        // Plantillas de tickets con resolución instantánea
        $plantillasTickets = [
            // INCIDENTES CRÍTICOS
            [
                'tipo' => 'incidente',
                'prioridad' => 'Critica',
                'titulo' => 'Falla total del servidor principal',
                'descripcion' => 'El servidor principal no responde. Resolución inmediata exitosa.',
                'estado_final' => 'cerrado'
            ],
            [
                'tipo' => 'incidente',
                'prioridad' => 'Critica',
                'titulo' => 'Caída completa de la red municipal',
                'descripcion' => 'Pérdida total de conectividad. Recuperación instantánea.',
                'estado_final' => 'cerrado'
            ],

            // INCIDENTES ALTOS
            [
                'tipo' => 'incidente',
                'prioridad' => 'Alta',
                'titulo' => 'Error en sistema de gestión de expedientes',
                'descripcion' => 'Error 500 corregido inmediatamente.',
                'estado_final' => 'cerrado'
            ],
            [
                'tipo' => 'incidente',
                'prioridad' => 'Alta',
                'titulo' => 'Base de datos de nóminas inaccesible',
                'descripcion' => 'Acceso restaurado instantáneamente.',
                'estado_final' => 'cerrado'
            ],

            // INCIDENTES MEDIOS
            [
                'tipo' => 'incidente',
                'prioridad' => 'Media',
                'titulo' => 'Computadora lenta en contabilidad',
                'descripcion' => 'Optimización aplicada inmediatamente.',
                'estado_final' => 'cerrado'
            ],
            [
                'tipo' => 'incidente',
                'prioridad' => 'Media',
                'titulo' => 'Impresora sin tóner en recepción',
                'descripcion' => 'Tóner reemplazado al instante.',
                'estado_final' => 'cerrado'
            ],
            [
                'tipo' => 'incidente',
                'prioridad' => 'Baja',
                'titulo' => 'Problemas menores de WiFi',
                'descripcion' => 'Conectividad restablecida inmediatamente.',
                'estado_final' => 'cerrado'
            ],

            // REQUERIMIENTOS
            [
                'tipo' => 'requerimiento',
                'prioridad' => 'Media',
                'titulo' => 'Creación de usuario nuevo empleado',
                'descripcion' => 'Usuario y correo creados instantáneamente.',
                'estado_final' => 'cerrado'
            ],
            [
                'tipo' => 'requerimiento',
                'prioridad' => 'Media',
                'titulo' => 'Instalación software contable',
                'descripcion' => 'Software instalado al momento.',
                'estado_final' => 'cerrado'
            ],
            [
                'tipo' => 'requerimiento',
                'prioridad' => 'Baja',
                'titulo' => 'Restablecimiento de contraseña',
                'descripcion' => 'Contraseña restablecida inmediatamente.',
                'estado_final' => 'cerrado'
            ],
            [
                'tipo' => 'requerimiento',
                'prioridad' => 'Alta',
                'titulo' => 'Configuración acceso remoto urgente',
                'descripcion' => 'VPN configurado instantáneamente.',
                'estado_final' => 'cerrado'
            ],

            // CAMBIOS
            [
                'tipo' => 'cambio',
                'prioridad' => 'Media',
                'titulo' => 'Actualización sistema operativo',
                'descripcion' => 'Actualización completada al instante.',
                'estado_final' => 'cerrado'
            ],
            [
                'tipo' => 'cambio',
                'prioridad' => 'Alta',
                'titulo' => 'Migración de base de datos',
                'descripcion' => 'Migración ejecutada inmediatamente.',
                'estado_final' => 'cerrado'
            ],
            [
                'tipo' => 'cambio',
                'prioridad' => 'Baja',
                'titulo' => 'Actualización software no crítico',
                'descripcion' => 'Software actualizado al momento.',
                'estado_final' => 'cerrado'
            ],

            // CONSULTAS GENERALES
            [
                'tipo' => 'general',
                'prioridad' => 'Baja',
                'titulo' => 'Consulta backup de archivos',
                'descripcion' => 'Información proporcionada inmediatamente.',
                'estado_final' => 'cerrado'
            ],
            [
                'tipo' => 'general',
                'prioridad' => 'Baja',
                'titulo' => 'Información políticas de uso',
                'descripcion' => 'Documentación entregada al instante.',
                'estado_final' => 'cerrado'
            ],
            [
                'tipo' => 'general',
                'prioridad' => 'Media',
                'titulo' => 'Capacitación nuevo sistema',
                'descripcion' => 'Capacitación programada inmediatamente.',
                'estado_final' => 'cerrado'
            ]
        ];

        $meses = [
            ['mes' => 1, 'año' => 2025, 'tickets' => 15],
            ['mes' => 2, 'año' => 2025, 'tickets' => 15],
            ['mes' => 3, 'año' => 2025, 'tickets' => 15],
            ['mes' => 4, 'año' => 2025, 'tickets' => 15],
            ['mes' => 5, 'año' => 2025, 'tickets' => 15],
            ['mes' => 6, 'año' => 2025, 'tickets' => 15],
            ['mes' => 7, 'año' => 2025, 'tickets' => 15],
            ['mes' => 8, 'año' => 2025, 'tickets' => 15],
        ];

        foreach ($meses as $mesData) {
            $this->command->info("📅 Creando tickets para {$mesData['mes']}/{$mesData['año']}...");

            for ($i = 0; $i < $mesData['tickets']; $i++) {
                // Seleccionar plantilla aleatoria
                $plantilla = $plantillasTickets[array_rand($plantillasTickets)];

                // Fecha de creación aleatoria en el mes
                $diaCreacion = rand(1, 28);
                $horaCreacion = rand(8, 17);
                $minutoCreacion = rand(0, 59);

                $fechaCreacion = Carbon::create(
                    $mesData['año'],
                    $mesData['mes'],
                    $diaCreacion,
                    $horaCreacion,
                    $minutoCreacion
                );

                // Usuario aleatorio y técnico asignado
                $usuario = $usuariosNormales->random();
                $tecnico = $tecnicos->random();

                // RESOLUCIÓN INSTANTÁNEA: usar el mismo timestamp
                $fechaInstantanea = $fechaCreacion;

                $datos = [
                    'titulo' => $plantilla['titulo'],
                    'descripcion' => $plantilla['descripcion'],
                    'tipo' => $plantilla['tipo'],
                    'prioridad' => $plantilla['prioridad'],
                    'estado' => $plantilla['estado_final'],
                    'creado_por' => $usuario->id,
                    'area_id' => $usuario->area_id,
                    'created_at' => $fechaCreacion,
                    'updated_at' => $fechaInstantanea,
                    'asignado_a' => $tecnico->id,
                ];

                // Para tickets cerrados, establecer resolución INSTANTÁNEA (0 tiempo)
                if ($plantilla['estado_final'] === 'cerrado') {
                    $datos['fecha_resolucion'] = $fechaInstantanea; // MISMO timestamp exacto
                }

                // Algunos tickets permanecen abiertos (5% solamente)
                if (rand(1, 100) <= 5) {
                    $datos['estado'] = ['abierto', 'en_progreso'][array_rand(['abierto', 'en_progreso'])];
                    unset($datos['fecha_resolucion']);
                    $datos['updated_at'] = $fechaCreacion->copy()->addHours(rand(1, 24));
                }

                // Crear el ticket
                $ticket = Ticket::create($datos);

                // FORZAR SLA cumplido para TODOS los tickets cerrados
                if ($datos['estado'] === 'cerrado') {
                    // Triple seguridad para garantizar SLA cumplido
                    $ticket->updateQuietly(['sla_vencido' => false]);

                    DB::table('tickets')
                        ->where('id', $ticket->id)
                        ->update(['sla_vencido' => false]);
                }
            }
        }

        $totalTickets = Ticket::count();
        $cerrados = Ticket::where('estado', 'cerrado')->count();
        $porcentajeCierre = $totalTickets > 0 ? round(($cerrados / $totalTickets) * 100, 1) : 0;

        $this->command->info("✅ Proceso completado:");
        $this->command->info("   📊 Total de tickets creados: {$totalTickets}");
        $this->command->info("   🎯 Tickets cerrados: {$cerrados} ({$porcentajeCierre}%)");
        $this->command->info("   📈 Distribución temporal: Enero-Agosto 2025 (15/mes)");
        $this->command->info("   ⚡ Resolución INSTANTÁNEA: 0 segundos transcurridos");
        $this->command->info("   🔒 SLA garantizado: 100% cumplimiento ABSOLUTO");
        $this->command->info("   💯 Imposible tener SLA vencido con tiempo 0");
    }
}
