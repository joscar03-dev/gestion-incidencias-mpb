<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;

// Import the Ticket model for ITIL metrics
use App\Models\Ticket;
use App\Models\User;
use App\Models\Area;
use App\Models\Categoria;
use App\Models\Dispositivo;

class ItilDashboard extends Model
{
    use HasFactory;

    protected $table = 'tickets'; // Usa la tabla de tickets existente

    // ITIL Service Categories según mejores prácticas
    const ITIL_SERVICE_CATEGORIES = [
        'incident_management' => 'Gestión de Incidentes',
        'service_request' => 'Solicitud de Servicio',
        'change_management' => 'Gestión de Cambios',
        'problem_management' => 'Gestión de Problemas',
        'configuration_management' => 'Gestión de Configuración',
        'release_management' => 'Gestión de Versiones',
        'knowledge_management' => 'Gestión del Conocimiento',
        'service_level_management' => 'Gestión de Niveles de Servicio'
    ];

    // ITIL Incident Categories
    const ITIL_INCIDENT_CATEGORIES = [
        'hardware' => 'Hardware',
        'software' => 'Software',
        'network' => 'Red',
        'security' => 'Seguridad',
        'access_rights' => 'Derechos de Acceso',
        'data' => 'Datos',
        'service_availability' => 'Disponibilidad del Servicio',
        'performance' => 'Rendimiento'
    ];

    // ITIL Service Request Categories
    const ITIL_SERVICE_REQUEST_CATEGORIES = [
        'access_request' => 'Solicitud de Acceso',
        'password_reset' => 'Restablecimiento de Contraseña',
        'new_user_setup' => 'Configuración de Nuevo Usuario',
        'software_installation' => 'Instalación de Software',
        'hardware_request' => 'Solicitud de Hardware',
        'information_request' => 'Solicitud de Información',
        'service_request' => 'Solicitud de Servicio',
        'training_request' => 'Solicitud de Capacitación'
    ];

    // ITIL Priority Matrix
    const ITIL_PRIORITY_MATRIX = [
        'critical' => [
            'impact' => 'Alto',
            'urgency' => 'Alto',
            'priority' => 'Crítica',
            'sla_hours' => 2
        ],
        'high' => [
            'impact' => 'Alto',
            'urgency' => 'Medio',
            'priority' => 'Alta',
            'sla_hours' => 4
        ],
        'medium' => [
            'impact' => 'Medio',
            'urgency' => 'Medio',
            'priority' => 'Media',
            'sla_hours' => 24
        ],
        'low' => [
            'impact' => 'Bajo',
            'urgency' => 'Bajo',
            'priority' => 'Baja',
            'sla_hours' => 72
        ]
    ];

    // ITIL Change Types
    const ITIL_CHANGE_TYPES = [
        'normal' => 'Cambio Normal',
        'standard' => 'Cambio Estándar',
        'emergency' => 'Cambio de Emergencia',
        'maintenance' => 'Mantenimiento Programado'
    ];

    // ITIL Service Levels
    const ITIL_SERVICE_LEVELS = [
        'gold' => ['name' => 'Oro', 'response_time' => 15, 'resolution_time' => 2],
        'silver' => ['name' => 'Plata', 'response_time' => 30, 'resolution_time' => 4],
        'bronze' => ['name' => 'Bronce', 'response_time' => 60, 'resolution_time' => 8]
    ];

    /**
     * Métricas ITIL para Incidentes
     */
    public static function getIncidentMetrics($period = 'month')
    {
        $baseQuery = Ticket::query();

        // Filtrar por período
        switch ($period) {
            case 'today':
                $baseQuery->whereDate('created_at', today());
                break;
            case 'week':
                $baseQuery->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
                break;
            case 'month':
                $baseQuery->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year);
                break;
            case 'quarter':
                $baseQuery->whereBetween('created_at', [now()->startOfQuarter(), now()->endOfQuarter()]);
                break;
        }

        // Usar clones para evitar acumulación de filtros
        $total = (clone $baseQuery)->count();
        $resolved = (clone $baseQuery)->where('estado', 'Cerrado')->count();
        $open = (clone $baseQuery)->whereIn('estado', ['Abierto', 'En Progreso'])->count();
        $escalated = (clone $baseQuery)->where('escalado', true)->count(); // Usar propiedad booleana
        $cancelled = (clone $baseQuery)->where('estado', 'Cancelado')->count();
        $sla_breached = (clone $baseQuery)->where('sla_vencido', true)->count();

        return [
            'total_incidents' => $total,
            'resolved_incidents' => $resolved,
            'open_incidents' => $open,
            'escalated_incidents' => $escalated,
            'cancelled_incidents' => $cancelled,
            'sla_breached' => $sla_breached,
            'resolution_rate' => $total > 0 ? round(($resolved / $total) * 100, 2) : 0,
            'escalation_rate' => $total > 0 ? round(($escalated / $total) * 100, 2) : 0,
            'sla_compliance' => $total > 0 ? round((($total - $sla_breached) / $total) * 100, 2) : 100,
        ];
    }

    /**
     * Métricas de tiempo promedio de resolución
     */
    public static function getResolutionTimeMetrics()
    {
        $tickets = Ticket::whereNotNull('fecha_resolucion')
            ->whereNotNull('created_at')
            ->get();

        $resolutionTimes = $tickets->map(function ($ticket) {
            return $ticket->created_at->diffInHours($ticket->fecha_resolucion);
        });

        return [
            'mean_time_to_resolve' => $resolutionTimes->avg(),
            'median_time_to_resolve' => $resolutionTimes->median(),
            'min_time_to_resolve' => $resolutionTimes->min(),
            'max_time_to_resolve' => $resolutionTimes->max(),
        ];
    }

    /**
     * Distribución por categorías ITIL
     */
    public static function getCategoryDistribution()
    {
        $distribution = [];

        foreach (self::ITIL_INCIDENT_CATEGORIES as $key => $category) {
            $count = Ticket::whereHas('categorias', function ($query) use ($category) {
                $query->where('nombre', 'like', '%' . $category . '%');
            })->count();

            $distribution[$key] = [
                'name' => $category,
                'count' => $count
            ];
        }

        return $distribution;
    }

    /**
     * Análisis de tendencias por período
     */
    public static function getTrendAnalysis($days = 30)
    {
        $trends = [];
        $startDate = now()->subDays($days);

        for ($i = 0; $i < $days; $i++) {
            $date = $startDate->copy()->addDays($i);

            $trends[] = [
                'date' => $date->format('Y-m-d'),
                'incidents_created' => Ticket::whereDate('created_at', $date)->count(),
                'incidents_resolved' => Ticket::whereDate('fecha_resolucion', $date)->count(),
                'incidents_escalated' => Ticket::whereDate('fecha_escalamiento', $date)->count(),
            ];
        }

        return $trends;
    }

    /**
     * Métricas de satisfacción del usuario
     */
    public static function getUserSatisfactionMetrics()
    {
        // Simulamos métricas de satisfacción basadas en resolución de tickets
        $totalTickets = Ticket::count();
        $resolvedOnTime = Ticket::where('sla_vencido', false)
            ->where('estado', 'Cerrado')
            ->count();

        $satisfactionScore = $totalTickets > 0 ?
            round((($resolvedOnTime / $totalTickets) * 100), 2) : 0;

        return [
            'satisfaction_score' => $satisfactionScore,
            'total_surveys' => $totalTickets,
            'response_rate' => 85, // Simulado
            'net_promoter_score' => 7.5, // Simulado
        ];
    }

    /**
     * Análisis de disponibilidad del servicio
     */
    public static function getServiceAvailabilityMetrics()
    {
        $totalHours = 24 * 30; // 30 días
        $incidentHours = Ticket::where('prioridad', 'Critica')
            ->whereMonth('created_at', now()->month)
            ->sum('tiempo_solucion') / 60; // Convertir a horas

        $availability = $totalHours > 0 ?
            round((($totalHours - $incidentHours) / $totalHours) * 100, 3) : 100;

        return [
            'availability_percentage' => $availability,
            'downtime_hours' => round($incidentHours, 2),
            'uptime_hours' => round($totalHours - $incidentHours, 2),
            'mttr' => static::getResolutionTimeMetrics()['mean_time_to_resolve'] ?? 0,
            'mtbf' => 168, // Simulado - Mean Time Between Failures (horas)
        ];
    }

    /**
     * Análisis de carga de trabajo
     */
    public static function getWorkloadAnalysis()
    {
        // Obtener todos los usuarios que tienen tickets asignados
        $users = \App\Models\User::whereHas('ticketsAsignados')
            ->with(['ticketsAsignados' => function ($query) {
                $query->select('id', 'asignado_a', 'estado', 'escalado', 'created_at');
            }])
            ->get();

        $workload = [];

        foreach ($users as $user) {
            $tickets = $user->ticketsAsignados;
            $openTickets = $tickets->whereIn('estado', ['Abierto', 'En Progreso'])->count();
            $totalTickets = $tickets->count();
            $resolvedTickets = $tickets->where('estado', 'Cerrado')->count();
            $escalatedTickets = $tickets->where('escalado', true)->count();

            $workload[] = [
                'user_id' => $user->id,
                'user_name' => $user->name,
                'open_tickets' => $openTickets,
                'total_tickets' => $totalTickets,
                'resolved_tickets' => $resolvedTickets,
                'escalated_tickets' => $escalatedTickets,
                'resolution_rate' => $totalTickets > 0 ? round(($resolvedTickets / $totalTickets) * 100, 2) : 0,
                'escalation_rate' => $totalTickets > 0 ? round(($escalatedTickets / $totalTickets) * 100, 2) : 0,
            ];
        }

        return collect($workload)->sortByDesc('open_tickets')->values()->all();
    }

    /**
     * Relaciones del modelo
     */
    public function area()
    {
        return $this->belongsTo(Area::class);
    }

    public function asignadoA()
    {
        return $this->belongsTo(User::class, 'asignado_a');
    }

    public function creadoPor()
    {
        return $this->belongsTo(User::class, 'creado_por');
    }

    public function categorias()
    {
        return $this->belongsToMany(Categoria::class, 'categoria_ticket', 'ticket_id', 'categoria_id');
    }

    public function dispositivo()
    {
        return $this->belongsTo(Dispositivo::class);
    }

    /**
     * Indicadores de Eficiencia
     */
    public static function getEfficiencyIndicators()
    {
        $totalTickets = Ticket::count();
        $resolvedTickets = Ticket::where('estado', 'Cerrado')->count();
        $avgResolutionTime = Ticket::where('estado', 'Cerrado')
            ->whereNotNull('fecha_resolucion')
            ->selectRaw('AVG(TIMESTAMPDIFF(HOUR, created_at, fecha_resolucion)) as avg_time')
            ->value('avg_time') ?? 0;

        // Tiempo de primera respuesta (usar el primer comentario como aproximación)
        $avgFirstResponseTime = Ticket::join('comments', 'tickets.id', '=', 'comments.commentable_id')
            ->where('comments.commentable_type', 'App\\Models\\Ticket')
            ->selectRaw('AVG(TIMESTAMPDIFF(HOUR, tickets.created_at, comments.created_at)) as avg_response')
            ->value('avg_response') ?? 0;

        // Productividad por técnico
        $technicianProductivity = User::whereHas('ticketsAsignados')
            ->withCount([
                'ticketsAsignados as total_assigned',
                'ticketsAsignados as resolved_count' => function($q) {
                    $q->where('estado', 'Cerrado');
                },
                'ticketsAsignados as pending_count' => function($q) {
                    $q->whereIn('estado', ['Abierto', 'En Progreso']);
                }
            ])
            ->get()
            ->map(function($user) {
                $efficiency = $user->total_assigned > 0
                    ? round(($user->resolved_count / $user->total_assigned) * 100, 2)
                    : 0;

                return [
                    'technician' => $user->name,
                    'total_assigned' => $user->total_assigned,
                    'resolved' => $user->resolved_count,
                    'pending' => $user->pending_count,
                    'efficiency_rate' => $efficiency,
                    'workload_status' => $user->pending_count > 10 ? 'Alto' : ($user->pending_count > 5 ? 'Medio' : 'Bajo')
                ];
            })
            ->sortByDesc('efficiency_rate');

        // Eficiencia por categoría
        $categoryEfficiency = collect(self::ITIL_INCIDENT_CATEGORIES)->map(function($category, $key) {
            $categoryTickets = Ticket::whereHas('categorias', function($q) use ($category) {
                $q->where('nombre', 'like', '%' . $category . '%');
            });

            $total = $categoryTickets->count();
            $resolved = (clone $categoryTickets)->where('estado', 'Cerrado')->count();
            $avgTime = (clone $categoryTickets)->where('estado', 'Cerrado')
                ->whereNotNull('fecha_resolucion')
                ->selectRaw('AVG(TIMESTAMPDIFF(HOUR, created_at, fecha_resolucion)) as avg_time')
                ->value('avg_time') ?? 0;

            return [
                'category' => $category,
                'total_tickets' => $total,
                'resolved_tickets' => $resolved,
                'resolution_rate' => $total > 0 ? round(($resolved / $total) * 100, 2) : 0,
                'avg_resolution_time' => round($avgTime, 2),
                'efficiency_score' => $total > 0 ? round((($resolved / $total) * 100) / max($avgTime, 1), 2) : 0
            ];
        })->sortByDesc('efficiency_score');

        return [
            'overall_efficiency' => $totalTickets > 0 ? round(($resolvedTickets / $totalTickets) * 100, 2) : 0,
            'avg_resolution_time' => round($avgResolutionTime, 2),
            'avg_first_response_time' => round($avgFirstResponseTime, 2),
            'resolution_velocity' => $avgResolutionTime > 0 ? round(24 / $avgResolutionTime, 2) : 0, // tickets por día
            'technician_productivity' => $technicianProductivity->take(10),
            'category_efficiency' => $categoryEfficiency->take(8),
            'productivity_summary' => [
                'high_performers' => $technicianProductivity->where('efficiency_rate', '>=', 80)->count(),
                'average_performers' => $technicianProductivity->whereBetween('efficiency_rate', [60, 79])->count(),
                'low_performers' => $technicianProductivity->where('efficiency_rate', '<', 60)->count(),
            ]
        ];
    }

    /**
     * Indicadores de Calidad
     */
    public static function getQualityIndicators()
    {
        $totalResolved = Ticket::where('estado', 'Cerrado')->count();

        // Tickets reabiertos (aproximación: tickets que tienen comentarios sobre reapertura)
        $reopenedTickets = Ticket::whereExists(function($query) {
            $query->select('id')
                  ->from('comments')
                  ->whereColumn('commentable_id', 'tickets.id')
                  ->where('commentable_type', 'App\\Models\\Ticket')
                  ->where('body', 'like', '%reabierto%')
                  ->orWhere('body', 'like', '%reabrir%');
        })->count();
        $reopenRate = $totalResolved > 0 ? round(($reopenedTickets / $totalResolved) * 100, 2) : 0;

        // Cumplimiento de SLA
        $slaCompliance = Ticket::where('sla_vencido', false)->count();
        $totalWithSLA = Ticket::count(); // Todos los tickets tienen SLA
        $slaComplianceRate = $totalWithSLA > 0 ? round(($slaCompliance / $totalWithSLA) * 100, 2) : 0;

        // Satisfacción del cliente por categoría (simulado ya que no hay campo calificacion)
        $satisfactionByCategory = collect(self::ITIL_INCIDENT_CATEGORIES)->map(function($category) {
            $tickets = Ticket::whereHas('categorias', function($q) use ($category) {
                $q->where('nombre', 'like', '%' . $category . '%');
            })->where('estado', 'Cerrado');

            $totalTickets = $tickets->count();
            // Simulamos satisfacción basada en tiempo de resolución
            $avgResolutionTime = $tickets->whereNotNull('fecha_resolucion')
                ->selectRaw('AVG(TIMESTAMPDIFF(HOUR, created_at, fecha_resolucion)) as avg_time')
                ->value('avg_time') ?? 0;

            // Satisfacción simulada: mejor tiempo = mayor satisfacción
            $simulatedSatisfaction = $avgResolutionTime > 0 ? max(1, 5 - ($avgResolutionTime / 24)) : 5;

            return [
                'category' => $category,
                'total_tickets' => $totalTickets,
                'avg_satisfaction' => round($simulatedSatisfaction, 2),
                'satisfaction_responses' => $totalTickets, // Simular 100% respuesta
                'response_rate' => 100,
                'quality_score' => round($simulatedSatisfaction * 20, 2) // Convertir a escala 0-100
            ];
        })->sortByDesc('quality_score');

        // Análisis de escalaciones
        $escalationAnalysis = [
            'total_escalated' => Ticket::where('escalado', true)->count(),
            'escalation_rate' => Ticket::count() > 0 ? round((Ticket::where('escalado', true)->count() / Ticket::count()) * 100, 2) : 0,
            'escalation_by_category' => collect(self::ITIL_INCIDENT_CATEGORIES)->map(function($category) {
                $categoryTickets = Ticket::whereHas('categorias', function($q) use ($category) {
                    $q->where('nombre', 'like', '%' . $category . '%');
                });

                $total = $categoryTickets->count();
                $escalated = (clone $categoryTickets)->where('escalado', true)->count();

                return [
                    'category' => $category,
                    'total' => $total,
                    'escalated' => $escalated,
                    'escalation_rate' => $total > 0 ? round(($escalated / $total) * 100, 2) : 0
                ];
            })->sortByDesc('escalation_rate')->take(5)
        ];

        // Indicadores de primera resolución (tickets resueltos sin escalaciones)
        $firstCallResolution = Ticket::where('estado', 'Cerrado')
            ->where('escalado', false)
            ->count();

        $fcrRate = $totalResolved > 0 ? round(($firstCallResolution / $totalResolved) * 100, 2) : 0;

        // Calidad por técnico (simplificado sin calificaciones)
        $technicianQuality = User::whereHas('ticketsAsignados')
            ->with(['ticketsAsignados' => function($q) {
                $q->where('estado', 'Cerrado');
            }])
            ->get()
            ->map(function($user) {
                $resolvedTickets = $user->ticketsAsignados->where('estado', 'Cerrado');
                $totalResolved = $resolvedTickets->count();

                // Simular satisfacción basada en tiempo de resolución
                $avgResolutionTime = $totalResolved > 0 ?
                    $resolvedTickets->filter(function($ticket) {
                        return $ticket->fecha_resolucion && $ticket->created_at;
                    })->avg(function($ticket) {
                        return $ticket->created_at->diffInHours($ticket->fecha_resolucion);
                    }) : 0;

                $simulatedSatisfaction = $avgResolutionTime > 0 ? max(1, 5 - ($avgResolutionTime / 24)) : 5;
                $escalated = $resolvedTickets->where('escalado', true)->count();

                $qualityScore = 0;
                if ($totalResolved > 0) {
                    $satisfactionScore = ($simulatedSatisfaction / 5) * 50; // 50% weight
                    $escalationPenalty = ($escalated / $totalResolved) * 50; // 50% penalty
                    $qualityScore = max(0, $satisfactionScore - $escalationPenalty);
                }

                return [
                    'technician' => $user->name,
                    'resolved_tickets' => $totalResolved,
                    'avg_satisfaction' => round($simulatedSatisfaction, 2),
                    'reopen_rate' => 0, // Simplificado
                    'escalation_rate' => $totalResolved > 0 ? round(($escalated / $totalResolved) * 100, 2) : 0,
                    'quality_score' => round($qualityScore, 2)
                ];
            })
            ->where('resolved_tickets', '>', 0)
            ->sortByDesc('quality_score');

        return [
            'reopen_rate' => $reopenRate,
            'sla_compliance_rate' => $slaComplianceRate,
            'first_call_resolution_rate' => $fcrRate,
            'satisfaction_by_category' => $satisfactionByCategory->take(8),
            'escalation_analysis' => $escalationAnalysis,
            'technician_quality' => $technicianQuality->take(10),
            'quality_summary' => [
                'excellent_quality' => $technicianQuality->where('quality_score', '>=', 80)->count(),
                'good_quality' => $technicianQuality->whereBetween('quality_score', [60, 79])->count(),
                'needs_improvement' => $technicianQuality->where('quality_score', '<', 60)->count(),
            ],
            'overall_quality_score' => round(($slaComplianceRate + $fcrRate + (100 - $reopenRate)) / 3, 2)
        ];
    }

    /**
     * Análisis comparativo de rendimiento
     */
    public static function getPerformanceComparison($period = 'month')
    {
        $currentMetrics = self::getEfficiencyIndicators();
        $currentQuality = self::getQualityIndicators();

        // Comparar con período anterior
        $previousPeriodStart = match($period) {
            'week' => now()->subWeeks(2)->startOfWeek(),
            'month' => now()->subMonths(2)->startOfMonth(),
            'quarter' => now()->subQuarters(2)->startOfQuarter(),
            default => now()->subMonths(2)->startOfMonth()
        };

        $previousPeriodEnd = match($period) {
            'week' => now()->subWeek()->endOfWeek(),
            'month' => now()->subMonth()->endOfMonth(),
            'quarter' => now()->subQuarter()->endOfQuarter(),
            default => now()->subMonth()->endOfMonth()
        };

        $previousResolved = Ticket::whereBetween('fecha_resolucion', [$previousPeriodStart, $previousPeriodEnd])
            ->where('estado', 'Cerrado')->count();

        $previousTotal = Ticket::whereBetween('created_at', [$previousPeriodStart, $previousPeriodEnd])->count();

        $previousEfficiency = $previousTotal > 0 ? round(($previousResolved / $previousTotal) * 100, 2) : 0;

        return [
            'current_efficiency' => $currentMetrics['overall_efficiency'],
            'previous_efficiency' => $previousEfficiency,
            'efficiency_trend' => $currentMetrics['overall_efficiency'] - $previousEfficiency,
            'current_quality' => $currentQuality['overall_quality_score'],
            'performance_indicators' => [
                'efficiency_status' => $currentMetrics['overall_efficiency'] >= 85 ? 'Excelente' :
                                     ($currentMetrics['overall_efficiency'] >= 70 ? 'Bueno' : 'Necesita Mejora'),
                'quality_status' => $currentQuality['overall_quality_score'] >= 85 ? 'Excelente' :
                                  ($currentQuality['overall_quality_score'] >= 70 ? 'Bueno' : 'Necesita Mejora'),
                'trend_direction' => $currentMetrics['overall_efficiency'] - $previousEfficiency > 0 ? 'up' : 'down'
            ]
        ];
    }
}
