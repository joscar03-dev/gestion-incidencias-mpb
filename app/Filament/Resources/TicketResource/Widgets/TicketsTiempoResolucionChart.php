<?php

namespace App\Filament\Resources\TicketResource\Widgets;

use Illuminate\Support\Carbon;
use App\Models\Ticket;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\LineChartWidget;

class TicketsTiempoResolucionChart extends ChartWidget
{
    protected static ?string $heading = 'Tiempo de Resolución vs SLA Efectivo';
    protected int | string | array $columnSpan = 'full';
    public function getDescription(): ?string
    {
        return 'Comparación entre tiempo real de resolución y SLA efectivo (considerando prioridad). Los puntos rojos indican tickets que excedieron el SLA.';
    }


    public ?string $filter = 'all';

    protected function getFilters(): ?array
    {
        return [
            'all' => 'Todas las prioridades',
            'Critica' => 'Crítica',
            'Alta' => 'Alta',
            'Media' => 'Media',
            'Baja' => 'Baja',
        ];
    }
    protected function getData(): array
    {
        // Rango de fechas: últimos 30 días
        $inicio = now()->subDays(30)->startOfDay();
        $fin = now()->endOfDay();

        // Construir la consulta base con relaciones necesarias
        $query = Ticket::where('estado', Ticket::ESTADOS['Cerrado'])
            ->whereBetween('fecha_cierre', [$inicio, $fin])
            ->with(['area.slas']); // Eager loading para mejor performance

        // Aplicar filtro de prioridad si no es 'all'
        if ($this->filter !== 'all') {
            $query->where('prioridad', $this->filter);
        }

        $tickets = $query->orderBy('fecha_cierre')->get();

        $labels = [];
        $tiemposReales = [];
        $tiemposSla = [];
        $ticketsVencidos = []; // Para marcar tickets que no cumplieron SLA

        foreach ($tickets as $ticket) {
            $fechaCierre = $ticket->fecha_cierre ? Carbon::parse($ticket->fecha_cierre) : Carbon::parse($ticket->updated_at);
            $labels[] = $fechaCierre->format('d/m');
            $minutosReales = Carbon::parse($ticket->created_at)->diffInMinutes($fechaCierre);
            $horasReales = round($minutosReales / 60, 2);
            $tiemposReales[] = $horasReales;

            // Obtener SLA efectivo del ticket usando el método del modelo
            $slaEfectivo = $ticket->getSlaEfectivo();
            $tiempoSlaMinutos = null;

            if ($slaEfectivo && isset($slaEfectivo['tiempo_resolucion'])) {
                // Los tiempos del SLA ya están en minutos (integer)
                $tiempoSlaMinutos = $slaEfectivo['tiempo_resolucion'];
            } else {
                // Fallback: usar SLA por defecto según área
                $tiempoSlaMinutos = 480; // 8 horas por defecto
            }

            $horasSla = round($tiempoSlaMinutos / 60, 2);
            $tiemposSla[] = $horasSla;

            // Marcar si el ticket venció el SLA
            $vencido = $minutosReales > $tiempoSlaMinutos;
            $ticketsVencidos[] = $vencido ? $horasReales : null;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Tiempo Real de Resolución (h)',
                    'data' => $tiemposReales,
                    'borderColor' => '#3b82f6',
                    'backgroundColor' => 'rgba(59,130,246,0.2)',
                    'fill' => false,
                    'tension' => 0.1,
                ],
                [
                    'label' => 'Tiempo SLA Efectivo (h)',
                    'data' => $tiemposSla,
                    'borderColor' => '#10b981',
                    'backgroundColor' => 'rgba(16,185,129,0.2)',
                    'fill' => false,
                    'borderDash' => [5, 5], // Línea punteada para SLA
                ],
                [
                    'label' => 'Tickets Vencidos (h)',
                    'data' => $ticketsVencidos,
                    'borderColor' => '#ef4444',
                    'backgroundColor' => '#ef4444',
                    'pointBackgroundColor' => '#ef4444',
                    'pointRadius' => 6,
                    'showLine' => false, // Solo mostrar puntos para vencidos
                    'type' => 'scatter',
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
