<?php

namespace App\Filament\Resources\TicketResource\Widgets;

use Illuminate\Support\Carbon;
use App\Models\Ticket;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\LineChartWidget;

class TicketsTiempoResolucionChart extends ChartWidget
{
    protected static ?string $heading = 'Tiempo de Resolución de Tickets';

    public ?string $filter = 'all';

    protected function getFilters(): ?array
    {
        return [
            'all' => 'Todas las prioridades',
            'alta' => 'Alta',
            'media' => 'Media',
            'baja' => 'Baja',
        ];
    }
    protected function getData(): array
    {
        // Rango de fechas: últimos 30 días
        $inicio = now()->subDays(30)->startOfDay();
        $fin = now()->endOfDay();

        // Construir la consulta base
        $query = Ticket::where('estado', Ticket::ESTADOS['Cerrado'])
            ->whereBetween('fecha_cierre', [$inicio, $fin]);

        // Aplicar filtro de prioridad si no es 'all'
        if ($this->filter !== 'all') {
            $query->where('prioridad', $this->filter);
        }

        $tickets = $query->orderBy('fecha_cierre')->get();

        $labels = [];
        $tiemposReales = [];
        $tiemposSla = [];

        foreach ($tickets as $ticket) {
            $fechaCierre = $ticket->fecha_cierre ? Carbon::parse($ticket->fecha_cierre) : Carbon::parse($ticket->updated_at);
            $labels[] = $fechaCierre->format('d-m-Y');
            $minutosReales = Carbon::parse($ticket->created_at)->diffInMinutes($fechaCierre);
            $tiemposReales[] = $minutosReales;

            $tiempoSolucion = null;
            if ($ticket->creadoPor && $ticket->creadoPor->area) {
            $sla = $ticket->creadoPor->area->slas()
                ->where('activo', true)
                ->first();
            if ($sla && $sla->tiempo_resolucion) {
                // Convierte 'HH:MM:SS' a minutos decimales
                [$h, $m, $s] = explode(':', $sla->tiempo_resolucion);
                $tiempoSolucion = round(($h * 60) + $m + ($s / 60), 2);
            }
            }
            $tiemposSla[] = $tiempoSolucion ?? 480; // 8 horas = 480 minutos
        }

        return [
            'datasets' => [
                [
                    'label' => 'Tiempo Real de Resolución (h)',
                    'data' => $tiemposReales,
                    'borderColor' => '#3b82f6',
                    'backgroundColor' => 'rgba(59,130,246,0.2)',
                ],
                [
                    'label' => 'Tiempo de Solución SLA (h)',
                    'data' => $tiemposSla,
                    'borderColor' => '#f59e42',
                    'backgroundColor' => 'rgba(245,158,66,0.2)',
                ],
            ],
            'labels' => $labels,
        ];
    }



    protected function getType(): string
    {
        return 'line';
    }
}
