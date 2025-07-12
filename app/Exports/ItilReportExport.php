<?php

namespace App\Exports;

use App\Models\Ticket;
use App\Models\ItilDashboard;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class ItilReportExport implements WithMultipleSheets
{
    protected $filters;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }

    public function sheets(): array
    {
        return [
            new ItilTicketsSheet($this->filters),
            new ItilMetricsSheet($this->filters),
            new ItilCategorySheet($this->filters),
            new ItilSlaSheet($this->filters),
            new ItilWorkloadSheet($this->filters),
        ];
    }
}

class ItilTicketsSheet implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle
{
    protected $filters;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }

    public function collection()
    {
        $query = Ticket::with(['asignadoA', 'area', 'categorias', 'creadoPor']);

        // Aplicar filtros
        if (isset($this->filters['fecha_desde'])) {
            $query->whereDate('created_at', '>=', $this->filters['fecha_desde']);
        }

        if (isset($this->filters['fecha_hasta'])) {
            $query->whereDate('created_at', '<=', $this->filters['fecha_hasta']);
        }

        if (isset($this->filters['tipo'])) {
            $query->where('tipo', $this->filters['tipo']);
        }

        if (isset($this->filters['estado'])) {
            $query->where('estado', $this->filters['estado']);
        }

        if (isset($this->filters['tickets'])) {
            return collect($this->filters['tickets']);
        }

        return $query->get();
    }

    public function headings(): array
    {
        return [
            'ID Ticket',
            'Título',
            'Tipo ITIL',
            'Categoría Principal',
            'Prioridad',
            'Estado',
            'Creado Por',
            'Asignado A',
            'Área',
            'Fecha Creación',
            'Fecha Resolución',
            'Tiempo Resolución (hrs)',
            'SLA Vencido',
            'Escalado',
            'Descripción',
            'Comentarios',
        ];
    }

    public function map($ticket): array
    {
        $tiempoResolucion = null;
        if ($ticket->fecha_resolucion) {
            $tiempoResolucion = $ticket->created_at->diffInHours($ticket->fecha_resolucion);
        }

        return [
            $ticket->id,
            $ticket->titulo,
            $ticket->tipo,
            $ticket->categorias->first()->nombre ?? 'Sin categoría',
            $ticket->prioridad,
            $ticket->estado,
            $ticket->creadoPor->name ?? 'Sistema',
            $ticket->asignadoA->name ?? 'Sin asignar',
            $ticket->area->nombre ?? 'Sin área',
            $ticket->created_at->format('d/m/Y H:i'),
            $ticket->fecha_resolucion ? $ticket->fecha_resolucion->format('d/m/Y H:i') : 'Sin resolver',
            $tiempoResolucion,
            $ticket->sla_vencido ? 'Sí' : 'No',
            $ticket->escalado ? 'Sí' : 'No',
            strip_tags($ticket->descripcion),
            strip_tags($ticket->comentario),
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }

    public function title(): string
    {
        return 'Tickets ITIL';
    }
}

class ItilMetricsSheet implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle
{
    protected $filters;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }

    public function collection()
    {
        $metrics = ItilDashboard::getIncidentMetrics();
        $resolutionMetrics = ItilDashboard::getResolutionTimeMetrics();
        $serviceMetrics = ItilDashboard::getServiceAvailabilityMetrics();
        $satisfactionMetrics = ItilDashboard::getUserSatisfactionMetrics();

        return collect([
            ['metric' => 'Total de Incidentes', 'value' => $metrics['total_incidents']],
            ['metric' => 'Incidentes Resueltos', 'value' => $metrics['resolved_incidents']],
            ['metric' => 'Incidentes Abiertos', 'value' => $metrics['open_incidents']],
            ['metric' => 'Incidentes Escalados', 'value' => $metrics['escalated_incidents']],
            ['metric' => 'SLA Incumplidos', 'value' => $metrics['sla_breached']],
            ['metric' => 'Tasa de Resolución (%)', 'value' => $metrics['resolution_rate']],
            ['metric' => 'Tasa de Escalamiento (%)', 'value' => $metrics['escalation_rate']],
            ['metric' => 'Cumplimiento SLA (%)', 'value' => $metrics['sla_compliance']],
            ['metric' => 'Tiempo Promedio Resolución (hrs)', 'value' => round($resolutionMetrics['mean_time_to_resolve'] ?? 0, 2)],
            ['metric' => 'Tiempo Mediano Resolución (hrs)', 'value' => round($resolutionMetrics['median_time_to_resolve'] ?? 0, 2)],
            ['metric' => 'Disponibilidad del Servicio (%)', 'value' => $serviceMetrics['availability_percentage']],
            ['metric' => 'Puntuación Satisfacción', 'value' => $satisfactionMetrics['satisfaction_score']],
        ]);
    }

    public function headings(): array
    {
        return [
            'Métrica ITIL',
            'Valor',
        ];
    }

    public function map($item): array
    {
        return [
            $item['metric'],
            $item['value'],
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }

    public function title(): string
    {
        return 'Métricas ITIL';
    }
}

class ItilCategorySheet implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle
{
    protected $filters;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }

    public function collection()
    {
        $distribution = ItilDashboard::getCategoryDistribution();

        return collect($distribution)->map(function ($item, $key) {
            return [
                'category_key' => $key,
                'category_name' => $item['name'],
                'count' => $item['count']
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Clave Categoría',
            'Nombre Categoría ITIL',
            'Cantidad de Tickets',
        ];
    }

    public function map($item): array
    {
        return [
            $item['category_key'],
            $item['category_name'],
            $item['count'],
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }

    public function title(): string
    {
        return 'Distribución por Categoría';
    }
}

class ItilSlaSheet implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle
{
    protected $filters;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }

    public function collection()
    {
        $query = Ticket::query();

        // Aplicar filtros de fecha
        if (isset($this->filters['fecha_desde'])) {
            $query->whereDate('created_at', '>=', $this->filters['fecha_desde']);
        }

        if (isset($this->filters['fecha_hasta'])) {
            $query->whereDate('created_at', '<=', $this->filters['fecha_hasta']);
        }

        return $query->select(['id', 'titulo', 'prioridad', 'estado', 'created_at', 'fecha_resolucion', 'sla_vencido', 'escalado'])
                    ->get();
    }

    public function headings(): array
    {
        return [
            'ID Ticket',
            'Título',
            'Prioridad',
            'Estado',
            'Fecha Creación',
            'Fecha Resolución',
            'SLA Objetivo (hrs)',
            'Tiempo Real (hrs)',
            'Estado SLA',
            'Escalado',
        ];
    }

    public function map($ticket): array
    {
        // Calcular SLA objetivo basado en prioridad
        $slaObjective = match($ticket->prioridad) {
            'Critica' => 2,
            'Alta' => 4,
            'Media' => 24,
            'Baja' => 72,
            default => 24
        };

        $tiempoReal = null;
        if ($ticket->fecha_resolucion) {
            $tiempoReal = $ticket->created_at->diffInHours($ticket->fecha_resolucion);
        } elseif (in_array($ticket->estado, ['Abierto', 'En Progreso', 'Escalado'])) {
            $tiempoReal = $ticket->created_at->diffInHours(now());
        }

        $estadoSla = 'N/A';
        if ($tiempoReal !== null) {
            if ($ticket->sla_vencido) {
                $estadoSla = 'Vencido';
            } elseif ($tiempoReal <= $slaObjective) {
                $estadoSla = 'Cumplido';
            } else {
                $estadoSla = 'En Riesgo';
            }
        }

        return [
            $ticket->id,
            $ticket->titulo,
            $ticket->prioridad,
            $ticket->estado,
            $ticket->created_at->format('d/m/Y H:i'),
            $ticket->fecha_resolucion ? $ticket->fecha_resolucion->format('d/m/Y H:i') : 'Sin resolver',
            $slaObjective,
            $tiempoReal ? round($tiempoReal, 2) : 'N/A',
            $estadoSla,
            $ticket->escalado ? 'Sí' : 'No',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }

    public function title(): string
    {
        return 'Análisis SLA';
    }
}

class ItilWorkloadSheet implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle
{
    protected $filters;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }

    public function collection()
    {
        $workload = ItilDashboard::getWorkloadAnalysis();

        return collect($workload);
    }

    public function headings(): array
    {
        return [
            'ID Usuario',
            'Nombre Técnico',
            'Tickets Abiertos',
            'Total Tickets',
            'Tickets Resueltos',
            'Tasa Resolución (%)',
        ];
    }

    public function map($item): array
    {
        return [
            $item['user_id'],
            $item['user_name'],
            $item['open_tickets'],
            $item['total_tickets'],
            $item['resolved_tickets'],
            $item['resolution_rate'],
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }

    public function title(): string
    {
        return 'Carga de Trabajo';
    }
}
