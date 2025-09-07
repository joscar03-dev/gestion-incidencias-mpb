<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Ticket;
use App\Models\User;
use App\Models\Area;
use App\Models\Categoria;
use Carbon\Carbon;

class TicketsSeederCorregido extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🎫 Iniciando creación de tickets históricos (Enero - Agosto 2025)...');

        // Limpiar tickets existentes
        Ticket::query()->delete();

        // Obtener datos existentes
        $usuarios = User::with(['area', 'roles'])->get();
        $tecnicos = $usuarios->filter(fn($u) => $u->hasRole('Técnico'));
        $usuariosNormales = $usuarios->filter(fn($u) => $u->hasRole('Usuario'));
        $admins = $usuarios->filter(fn($u) => $u->hasRole(['Admin', 'Super Admin']));

        // Plantillas de tickets para generar variedad (resolución instantánea para SLA perfecto)
        $plantillasTickets = [
            // INCIDENTES CRÍTICOS (resolución: instantánea)
            [
                'tipo' => 'incidente',
                'prioridad' => 'Critica',
                'titulo' => 'Falla total del servidor principal',
                'descripcion' => 'El servidor principal no responde. Los usuarios no pueden acceder a los sistemas críticos de gestión municipal.',
                'estado_final' => 'cerrado'
            ],
            [
                'tipo' => 'incidente',
                'prioridad' => 'Critica',
                'titulo' => 'Caída completa de la red municipal',
                'descripcion' => 'Pérdida total de conectividad en todas las oficinas municipales. Servicios críticos afectados.',
                'estado_final' => 'cerrado'
            ],

            // INCIDENTES ALTOS (resolución: instantánea)
            [
                'tipo' => 'incidente',
                'prioridad' => 'Alta',
                'titulo' => 'Error en sistema de gestión de expedientes',
                'descripcion' => 'El sistema muestra error 500 al generar reportes. Los demás módulos funcionan normalmente.',
                'estado_final' => 'cerrado'
            ],
            [
                'tipo' => 'incidente',
                'prioridad' => 'Alta',
                'titulo' => 'Base de datos de nóminas inaccesible',
                'descripcion' => 'No se puede acceder a la base de datos de nóminas para procesar pagos del personal.',
                'estado_final' => 'cerrado'
            ],

            // INCIDENTES MEDIOS (resolución: instantánea)
            [
                'tipo' => 'incidente',
                'prioridad' => 'Media',
                'titulo' => 'Computadora lenta en área de contabilidad',
                'descripcion' => 'PC principal del área contable presenta lentitud extrema al abrir aplicaciones.',
                'estado_final' => 'cerrado'
            ],
            [
                'tipo' => 'incidente',
                'prioridad' => 'Media',
                'titulo' => 'Impresora sin tóner en recepción',
                'descripcion' => 'La impresora principal de atención al ciudadano quedó sin tóner negro.',
                'estado_final' => 'cerrado'
            ],
            [
                'tipo' => 'incidente',
                'prioridad' => 'Baja',
                'titulo' => 'Problemas menores de conectividad WiFi',
                'descripcion' => 'Algunos usuarios reportan desconexiones esporádicas de la red WiFi.',
                'estado_final' => 'cerrado'
            ],

            // REQUERIMIENTOS (resolución: instantánea)
            [
                'tipo' => 'requerimiento',
                'prioridad' => 'Media',
                'titulo' => 'Creación de usuario para nuevo empleado',
                'descripcion' => 'Solicitud de usuario y correo corporativo para personal nuevo en RRHH.',
                'estado_final' => 'cerrado'
            ],
            [
                'tipo' => 'requerimiento',
                'prioridad' => 'Media',
                'titulo' => 'Instalación de software contable',
                'descripcion' => 'Solicitud de instalación del sistema contable en nueva PC del área de tesorería.',
                'estado_final' => 'cerrado'
            ],
            [
                'tipo' => 'requerimiento',
                'prioridad' => 'Baja',
                'titulo' => 'Restablecimiento de contraseña',
                'descripcion' => 'Usuario solicita restablecimiento de contraseña de correo corporativo.',
                'estado_final' => 'cerrado'
            ],
            [
                'tipo' => 'requerimiento',
                'prioridad' => 'Alta',
                'titulo' => 'Configuración urgente de acceso remoto',
                'descripcion' => 'Usuario necesita acceso VPN urgente para trabajar desde casa.',
                'estado_final' => 'cerrado'
            ],

            // CAMBIOS (resolución: instantánea)
            [
                'tipo' => 'cambio',
                'prioridad' => 'Media',
                'titulo' => 'Actualización del sistema operativo',
                'descripcion' => 'Actualización programada de servidores a nueva versión del sistema operativo.',
                'estado_final' => 'cerrado'
            ],
            [
                'tipo' => 'cambio',
                'prioridad' => 'Alta',
                'titulo' => 'Migración de base de datos',
                'descripcion' => 'Migración de la base de datos principal a nuevo servidor con mayor capacidad.',
                'estado_final' => 'cerrado'
            ],
            [
                'tipo' => 'cambio',
                'prioridad' => 'Baja',
                'titulo' => 'Actualización de software no crítico',
                'descripcion' => 'Actualización de aplicaciones de oficina a nuevas versiones.',
                'estado_final' => 'cerrado'
            ],

            // CONSULTAS GENERALES (resolución: instantánea)
            [
                'tipo' => 'general',
                'prioridad' => 'Baja',
                'titulo' => 'Consulta sobre backup de archivos',
                'descripcion' => 'Usuario consulta sobre procedimiento para backup de archivos personales.',
                'estado_final' => 'cerrado'
            ],
            [
                'tipo' => 'general',
                'prioridad' => 'Baja',
                'titulo' => 'Información sobre políticas de uso',
                'descripcion' => 'Consulta sobre políticas de uso de internet y recursos informáticos.',
                'estado_final' => 'cerrado'
            ],
            [
                'tipo' => 'general',
                'prioridad' => 'Media',
                'titulo' => 'Capacitación en nuevo sistema',
                'descripcion' => 'Solicitud de capacitación para el nuevo sistema de gestión documental.',
                'estado_final' => 'cerrado'
            ],

            // INCIDENTES MEDIOS (resolución: 1-5 minutos)
            [
                'tipo' => 'incidente',
                'prioridad' => 'Media',
                'titulo' => 'Computadora lenta en área de contabilidad',
                'descripcion' => 'PC principal del área contable presenta lentitud extrema al abrir aplicaciones.',
                'estado_final' => 'cerrado'
            ],
            [
                'tipo' => 'incidente',
                'prioridad' => 'Media',
                'titulo' => 'Impresora sin tóner en recepción',
                'descripcion' => 'La impresora principal de atención al ciudadano quedó sin tóner negro.',
                'estado_final' => 'cerrado'
            ],
            [
                'tipo' => 'incidente',
                'prioridad' => 'Baja',
                'titulo' => 'Problemas menores de conectividad WiFi',
                'descripcion' => 'Algunos usuarios reportan desconexiones esporádicas de la red WiFi.',
                'estado_final' => 'cerrado'
            ],

            // REQUERIMIENTOS (resolución: 1-5 minutos)
            [
                'tipo' => 'requerimiento',
                'prioridad' => 'Media',
                'titulo' => 'Creación de usuario para nuevo empleado',
                'descripcion' => 'Solicitud de usuario y correo corporativo para personal nuevo en RRHH.',
                'estado_final' => 'cerrado'
            ],
            [
                'tipo' => 'requerimiento',
                'prioridad' => 'Media',
                'titulo' => 'Instalación de software contable',
                'descripcion' => 'Solicitud de instalación del sistema contable en nueva PC del área de tesorería.',
                'estado_final' => 'cerrado'
            ],
            [
                'tipo' => 'requerimiento',
                'prioridad' => 'Baja',
                'titulo' => 'Restablecimiento de contraseña',
                'descripcion' => 'Usuario solicita restablecimiento de contraseña de correo corporativo.',
                'estado_final' => 'cerrado'
            ],
            [
                'tipo' => 'requerimiento',
                'prioridad' => 'Alta',
                'titulo' => 'Configuración urgente de acceso remoto',
                'descripcion' => 'Usuario necesita acceso VPN urgente para trabajar desde casa.',
                'estado_final' => 'cerrado'
            ],

            // CAMBIOS (resolución: 1-5 minutos)
            [
                'tipo' => 'cambio',
                'prioridad' => 'Media',
                'titulo' => 'Actualización del sistema operativo',
                'descripcion' => 'Actualización programada de servidores a nueva versión del sistema operativo.',
                'estado_final' => 'cerrado'
            ],
            [
                'tipo' => 'cambio',
                'prioridad' => 'Alta',
                'titulo' => 'Migración de base de datos',
                'descripcion' => 'Migración de la base de datos principal a nuevo servidor con mayor capacidad.',
                'estado_final' => 'cerrado'
            ],
            [
                'tipo' => 'cambio',
                'prioridad' => 'Baja',
                'titulo' => 'Actualización de software no crítico',
                'descripcion' => 'Actualización de aplicaciones de oficina a nuevas versiones.',
                'estado_final' => 'cerrado'
            ],

            // CONSULTAS GENERALES (resolución: 1-5 minutos)
            [
                'tipo' => 'general',
                'prioridad' => 'Baja',
                'titulo' => 'Consulta sobre backup de archivos',
                'descripcion' => 'Usuario consulta sobre procedimiento para backup de archivos personales.',
                'estado_final' => 'cerrado'
            ],
            [
                'tipo' => 'general',
                'prioridad' => 'Baja',
                'titulo' => 'Información sobre políticas de uso',
                'descripcion' => 'Consulta sobre políticas de uso de internet y recursos informáticos.',
                'estado_final' => 'cerrado'
            ],
            [
                'tipo' => 'general',
                'prioridad' => 'Media',
                'titulo' => 'Capacitación en nuevo sistema',
                'descripcion' => 'Solicitud de capacitación para el nuevo sistema de gestión documental.',
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

                // RESOLUCIÓN INSTANTÁNEA: usar el mismo timestamp para creación y resolución
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

                // Para tickets cerrados, establecer resolución instantánea (0 tiempo transcurrido)
                if ($plantilla['estado_final'] === 'cerrado') {
                    $datos['fecha_resolucion'] = $fechaInstantanea; // MISMO timestamp
                }                // Algunos tickets permanecen abiertos (10%)
                if (rand(1, 100) <= 10) {
                    $datos['estado'] = ['abierto', 'en_progreso'][array_rand(['abierto', 'en_progreso'])];
                    unset($datos['fecha_resolucion']);
                    $datos['updated_at'] = $fechaCreacion->copy()->addHours(rand(1, 48));
                }

                // Crear el ticket
                $ticket = Ticket::create($datos);

                // Forzar SLA cumplido para TODOS los tickets cerrados, ignorando cálculos automáticos
                if ($datos['estado'] === 'cerrado') {
                    // Usar updateQuietly para evitar observers que puedan recalcular
                    $ticket->updateQuietly(['sla_vencido' => false]);

                    // También actualizar directamente en la base de datos como respaldo
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
        $this->command->info("   ⚡ Resolución INSTANTÁNEA: 0 tiempo transcurrido");
        $this->command->info("   🔒 SLA garantizado: 100% cumplimiento ABSOLUTO");
    }
}
