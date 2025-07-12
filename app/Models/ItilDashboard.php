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
        $query = Ticket::query();

        // Filtrar por período
        switch ($period) {
            case 'today':
                $query->whereDate('created_at', today());
                break;
            case 'week':
                $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
                break;
            case 'month':
                $query->whereMonth('created_at', now()->month);
                break;
            case 'quarter':
                $query->whereBetween('created_at', [now()->startOfQuarter(), now()->endOfQuarter()]);
                break;
        }

        $total = $query->count();
        $resolved = $query->where('estado', 'Cerrado')->count();
        $open = $query->whereIn('estado', ['Abierto', 'En Progreso'])->count();
        $escalated = $query->where('escalado', true)->count();
        $sla_breached = $query->where('sla_vencido', true)->count();

        return [
            'total_incidents' => $total,
            'resolved_incidents' => $resolved,
            'open_incidents' => $open,
            'escalated_incidents' => $escalated,
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
        $assignees = Ticket::select('asignado_a')
            ->whereNotNull('asignado_a')
            ->with('asignadoA:id,name')
            ->get()
            ->groupBy('asignado_a');

        $workload = [];

        foreach ($assignees as $userId => $tickets) {
            $openTickets = $tickets->whereIn('estado', ['Abierto', 'En Progreso'])->count();
            $totalTickets = $tickets->count();
            $resolvedTickets = $tickets->where('estado', 'Cerrado')->count();

            $workload[] = [
                'user_id' => $userId,
                'user_name' => $tickets->first()->asignadoA->name ?? 'Sin asignar',
                'open_tickets' => $openTickets,
                'total_tickets' => $totalTickets,
                'resolved_tickets' => $resolvedTickets,
                'resolution_rate' => $totalTickets > 0 ? round(($resolvedTickets / $totalTickets) * 100, 2) : 0,
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
}
