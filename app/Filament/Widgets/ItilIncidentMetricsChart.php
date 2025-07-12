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
            ],
            'labels' => ['Abiertos', 'Resueltos', 'Escalados'],
            'colors' => ['#f59e0b', '#10b981', '#ef4444'],
            'legend' => [
                'position' => 'bottom',
            ],
            'dataLabels' => [
                'enabled' => true,
            ],
            'responsive' => [
                [
                    'breakpoint' => 480,
                    'options' => [
                        'chart' => [
                            'width' => 200,
                        ],
                        'legend' => [
                            'position' => 'bottom',
                        ],
                    ],
                ],
            ],
        ];
    }
}
