<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Ticket;
use App\Models\User;
use App\Models\Area;
use App\Models\Categoria;
use Carbon\Carbon;

class TicketsSeeder extends Seeder
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

        $categorias = Categoria::all();
        if ($categorias->isEmpty()) {
            $this->command->error('No hay categorías disponibles');
            return;
        }

        // Plantillas de tickets para generar variedad
        $plantillasTickets = [
            // INCIDENTES CRÍTICOS (SLA: 3h * 0.8 = 2.4h)
            [
                'tipo' => 'incidente',
                'prioridad' => 'Critica',
                'titulo' => 'Falla total del servidor principal',
                'descripcion' => 'El servidor principal no responde. Los usuarios no pueden acceder a los sistemas críticos de gestión municipal.',
                'estado_final' => 'cerrado',
                'tiempo_resolucion_horas' => 1.5  // Dentro del SLA de 2.4h
            ],
            [
                'tipo' => 'incidente',
                'prioridad' => 'Critica',
                'titulo' => 'Caída completa de la red municipal',
                'descripcion' => 'Pérdida total de conectividad en todas las oficinas municipales. Servicios críticos afectados.',
                'estado_final' => 'cerrado',
                'tiempo_resolucion_horas' => 2.0  // Dentro del SLA de 2.4h
            ],

            // INCIDENTES ALTOS (SLA: 8h * 0.8 = 6.4h)
            [
                'tipo' => 'incidente',
                'prioridad' => 'alto',
                'titulo' => 'Error en sistema de gestión de expedientes',
                'descripcion' => 'El sistema muestra error 500 al generar reportes. Los demás módulos funcionan normalmente.',
                'estado_final' => 'cerrado',
                'tiempo_resolucion_horas' => 4.0  // Dentro del SLA de 6.4h
            ],
            [
                'tipo' => 'incidente',
                'prioridad' => 'alto',
                'titulo' => 'Base de datos de nóminas inaccesible',
                'descripcion' => 'No se puede acceder a la base de datos de nóminas para procesar pagos del personal.',
                'estado_final' => 'cerrado',
                'tiempo_resolucion_horas' => 5.0  // Dentro del SLA de 6.4h
            ],

            // INCIDENTES MEDIOS (SLA: 24h * 0.8 = 19.2h)
            [
                'tipo' => 'incidente',
                'prioridad' => 'medio',
                'titulo' => 'Computadora lenta en área de contabilidad',
                'descripcion' => 'PC principal del área contable presenta lentitud extrema al abrir aplicaciones.',
                'estado_final' => 'cerrado',
                'tiempo_resolucion_horas' => 8.0  // Dentro del SLA de 19.2h
            ],
            [
                'tipo' => 'incidente',
                'prioridad' => 'medio',
                'titulo' => 'Impresora sin tóner en recepción',
                'descripcion' => 'La impresora principal de atención al ciudadano quedó sin tóner negro.',
                'estado_final' => 'cerrado',
                'tiempo_resolucion_horas' => 4.0  // Dentro del SLA de 19.2h
            ],
            [
                'tipo' => 'incidente',
                'prioridad' => 'bajo',
                'titulo' => 'Problemas menores de conectividad WiFi',
                'descripcion' => 'Algunos usuarios reportan desconexiones esporádicas de la red WiFi.',
                'estado_final' => 'cerrado',
                'tiempo_resolucion_horas' => 24.0  // Dentro del SLA de 38.4h (48h * 0.8)
            ],

            // REQUERIMIENTOS (SLA más holgado con factor 1.2)
            [
                'tipo' => 'requerimiento',
                'prioridad' => 'medio',
                'titulo' => 'Creación de usuario para nuevo empleado',
                'descripcion' => 'Solicitud de usuario y correo corporativo para personal nuevo en RRHH.',
                'estado_final' => 'cerrado',
                'tiempo_resolucion_horas' => 20.0  // Dentro del SLA de 28.8h (24h * 1.2)
            ],
            [
                'tipo' => 'requerimiento',
                'prioridad' => 'medio',
                'titulo' => 'Instalación de software contable',
                'descripcion' => 'Solicitud de instalación del sistema contable en nueva PC del área de tesorería.',
                'estado_final' => 'cerrado',
                'tiempo_resolucion_horas' => 12.0  // Dentro del SLA de 28.8h
            ],
            [
                'tipo' => 'requerimiento',
                'prioridad' => 'bajo',
                'titulo' => 'Restablecimiento de contraseña',
                'descripcion' => 'Usuario solicita restablecimiento de contraseña de correo corporativo.',
                'estado_final' => 'cerrado',
                'tiempo_resolucion_horas' => 2.0   // Dentro del SLA de 57.6h (48h * 1.2)
            ],
            [
                'tipo' => 'requerimiento',
                'prioridad' => 'alto',
                'titulo' => 'Configuración urgente de acceso remoto',
                'descripcion' => 'Usuario necesita acceso VPN urgente para trabajar desde casa.',
                'estado_final' => 'cerrado',
                'tiempo_resolucion_horas' => 6.0   // Dentro del SLA de 9.6h (8h * 1.2)
            ],

            // CAMBIOS (SLA más largo con factor 1.5)
            [
                'tipo' => 'cambio',
                'prioridad' => 'medio',
                'titulo' => 'Actualización del sistema operativo',
                'descripcion' => 'Actualización programada de servidores a nueva versión del sistema operativo.',
                'estado_final' => 'cerrado',
                'tiempo_resolucion_horas' => 30.0  // Dentro del SLA de 36h (24h * 1.5)
            ],
            [
                'tipo' => 'cambio',
                'prioridad' => 'alto',
                'titulo' => 'Migración de base de datos',
                'descripcion' => 'Migración de la base de datos principal a nuevo servidor con mayor capacidad.',
                'estado_final' => 'cerrado',
                'tiempo_resolucion_horas' => 10.0  // Dentro del SLA de 12h (8h * 1.5)
            ],
            [
                'tipo' => 'cambio',
                'prioridad' => 'bajo',
                'titulo' => 'Actualización de software no crítico',
                'descripcion' => 'Actualización de aplicaciones de oficina a nuevas versiones.',
                'estado_final' => 'cerrado',
                'tiempo_resolucion_horas' => 48.0  // Dentro del SLA de 72h (48h * 1.5)
            ],

            // CONSULTAS GENERALES (SLA rápido con factor 0.5)
            [
                'tipo' => 'general',
                'prioridad' => 'bajo',
                'titulo' => 'Consulta sobre backup de archivos',
                'descripcion' => 'Usuario consulta sobre procedimiento para backup de archivos personales.',
                'estado_final' => 'cerrado',
                'tiempo_resolucion_horas' => 2.0   // Dentro del SLA de 24h (48h * 0.5)
            ],
            [
                'tipo' => 'general',
                'prioridad' => 'bajo',
                'titulo' => 'Información sobre políticas de uso',
                'descripcion' => 'Consulta sobre políticas de uso de internet y recursos informáticos.',
                'estado_final' => 'cerrado',
                'tiempo_resolucion_horas' => 1.0   // Dentro del SLA de 24h
            ],
            [
                'tipo' => 'general',
                'prioridad' => 'medio',
                'titulo' => 'Capacitación en nuevo sistema',
                'descripcion' => 'Solicitud de capacitación para el nuevo sistema de gestión documental.',
                'estado_final' => 'cerrado',
                'tiempo_resolucion_horas' => 8.0   // Dentro del SLA de 12h (24h * 0.5)
            ]
        ];
        $contadorTickets = 0;
        $ticketsCreados = [];

        // Generar tickets para cada mes (Enero - Agosto 2025)
        for ($mes = 1; $mes <= 8; $mes++) {
            $this->command->info("📅 Generando tickets para " . Carbon::create(2025, $mes, 1)->format('F Y'));

            // Generar 15 tickets por mes
            for ($ticketDelMes = 1; $ticketDelMes <= 15; $ticketDelMes++) {
                // Seleccionar plantilla aleatoria
                $plantilla = $plantillasTickets[array_rand($plantillasTickets)];

                // Generar fecha aleatoria dentro del mes
                $diaAleatorio = rand(1, Carbon::create(2025, $mes, 1)->daysInMonth);
                $horaAleatoria = rand(8, 17); // Horario laboral
                $minutoAleatorio = rand(0, 59);

                $fechaCreacion = Carbon::create(2025, $mes, $diaAleatorio, $horaAleatoria, $minutoAleatorio);

                // Seleccionar usuarios aleatorios
                $creador = $usuariosNormales->random();
                $tecnico = $tecnicos->random();
                $categoria = $categorias->random();

                // Calcular fechas de respuesta y resolución basadas en SLA
                $tiempoRespuestaSLA = $this->getTiempoRespuestaSLA($plantilla['prioridad']);
                $tiempoResolucionSLA = $this->getTiempoResolucionSLA($plantilla['prioridad'], $plantilla['tipo']);

                $fechaRespuesta = $fechaCreacion->copy()->addMinutes($tiempoRespuestaSLA);

                // Agregar variabilidad a los tiempos de resolución (85% dentro del SLA, 15% con variaciones)
                $factorVariabilidad = rand(1, 100) <= 85 ? rand(70, 95) / 100 : rand(95, 110) / 100;
                $tiempoResolucionReal = $plantilla['tiempo_resolucion_horas'] * $factorVariabilidad;
                $fechaResolucion = $fechaCreacion->copy()->addHours($tiempoResolucionReal);

                // 90% de tickets cerrados, 10% en otros estados
                $estadoFinal = rand(1, 100) <= 90 ? 'cerrado' : ['abierto', 'en_progreso', 'escalado'][array_rand(['abierto', 'en_progreso', 'escalado'])];

                $ticket = Ticket::create([
                    'titulo' => $plantilla['titulo'] . " (#{$ticketDelMes}-{$mes})",
                    'descripcion' => $plantilla['descripcion'],
                    'tipo' => $plantilla['tipo'],
                    'prioridad' => $plantilla['prioridad'],
                    'estado' => $estadoFinal,
                    'creado_por' => $creador->id,
                    'asignado_a' => $tecnico->id,
                    'area_id' => $creador->area_id,
                    'fecha_resolucion' => $estadoFinal === 'cerrado' ? $fechaResolucion : null,
                    'fecha_cierre' => $estadoFinal === 'cerrado' ? $fechaResolucion->copy()->addMinutes(15) : null,
                    'comentario' => $estadoFinal === 'cerrado' ? 'Ticket resuelto satisfactoriamente según SLA.' : null,
                    'escalado' => $estadoFinal === 'escalado',
                    'fecha_escalamiento' => $estadoFinal === 'escalado' ? $fechaCreacion->copy()->addHours(rand(2, 8)) : null,
                    'sla_vencido' => false, // Como la mayoría están cerrados y dentro del SLA
                    'created_at' => $fechaCreacion,
                    'updated_at' => $estadoFinal === 'cerrado' ? $fechaResolucion : $fechaCreacion->copy()->addHours(2),
                ]);

                // Asociar categoría (relación many-to-many)
                $ticket->categorias()->attach($categoria->id);

                $contadorTickets++;
                $ticketsCreados[] = [
                    'id' => $ticket->id,
                    'titulo' => $ticket->titulo,
                    'mes' => $mes,
                    'estado' => $estadoFinal,
                    'creador' => $creador->name,
                    'area' => $creador->area->nombre ?? 'Sin área'
                ];
            }
        }

        // Estadísticas finales
        $totalTickets = Ticket::count();
        $ticketsCerrados = Ticket::where('estado', 'cerrado')->count();
        $porcentajeCerrados = round(($ticketsCerrados / $totalTickets) * 100, 1);

        $this->command->info('🎯 CREACIÓN DE TICKETS HISTÓRICOS COMPLETADA');
        $this->command->info("📊 Total tickets creados: {$contadorTickets}");
        $this->command->info("✅ Tickets cerrados: {$ticketsCerrados} ({$porcentajeCerrados}%)");
        $this->command->info("📅 Período: Enero - Agosto 2025");
        $this->command->info("👥 Usuarios participantes: " . $usuariosNormales->count());
        $this->command->info("🔧 Técnicos asignados: " . $tecnicos->count());
        $this->command->info("📂 Categorías utilizadas: " . $categorias->count());
    }

    /**
     * Obtener tiempo de respuesta SLA en minutos según prioridad
     */
    private function getTiempoRespuestaSLA($prioridad): int
    {
        return match ($prioridad) {
            'critico' => 15,    // 15 minutos
            'alto' => 30,       // 30 minutos
            'medio' => 120,     // 2 horas
            'bajo' => 240,      // 4 horas
            default => 120
        };
    }

    /**
     * Obtener tiempo de resolución SLA en minutos según prioridad y tipo
     */
    private function getTiempoResolucionSLA($prioridad, $tipo): int
    {
        $tiempoBase = match ($prioridad) {
            'critico' => 180,   // 3 horas
            'alto' => 480,      // 8 horas
            'medio' => 1440,    // 24 horas
            'bajo' => 2880,     // 48 horas
            default => 1440
        };

        // Aplicar factor según tipo
        $factor = match ($tipo) {
            'incidente' => 0.8,      // Más rápido
            'requerimiento' => 1.2,  // Más tiempo
            'cambio' => 1.5,         // Mucho más tiempo
            'general' => 0.5,        // Consultas rápidas
            default => 1.0
        };

        return (int)($tiempoBase * $factor);
    }
}
