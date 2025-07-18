<?php

namespace App\Filament\Widgets;

use App\Models\ItilDashboard;
use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;

class ItilIncidentMetricsChart extends ApexChartWidget
{
    protected static ?string $chartId = 'itilIncidentMetricsChart';
    protected static ?string $heading = 'Distribución de Incidentes ITIL';
    protected static ?int $sort = 5;

    protected function getOptions(): array
    {
        $metrics = ItilDashboard::getIncidentMetrics();

        return [
            'chart' => [
                'type' => 'donut',
                'height' => 300,
            ],
            'series' => [
                $metrics['open_incidents'],
                $metrics['resolved_incidents'],
                $metrics['escalated_incidents'],
                $metrics['cancelled_incidents'],
            ],
            'labels' => [
                'Abiertos (' . $metrics['open_incidents'] . ')',
                'Resueltos (' . $metrics['resolved_incidents'] . ')',
                'Escalados (' . $metrics['escalated_incidents'] . ')',
                'Cancelados (' . $metrics['cancelled_incidents'] . ')',
            ],
            'colors' => ['#f59e0b', '#10b981', '#ef4444', '#6b7280'],
            'legend' => [
                'position' => 'bottom',
            ],
            'dataLabels' => [
                'enabled' => true,
            ],
        ];
    }
}
