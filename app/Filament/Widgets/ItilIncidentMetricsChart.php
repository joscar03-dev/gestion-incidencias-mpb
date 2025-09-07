<?php

namespace App\Filament\Widgets;

use App\Models\ItilDashboard;
use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;

class ItilIncidentMetricsChart extends ApexChartWidget
{
    protected static ?string $chartId = 'itilIncidentMetricsChart';
    protected static ?string $heading = 'Distribución de Incidentes ITIL';
    protected static ?int $sort = 5;

    public static function canView(): bool
    {
        return !auth()->user()->hasRole('Técnico');
    }

    public ?string $filter = 'all';

    protected function getFilters(): ?array
    {
        return [
            'today' => 'Hoy',
            'week' => 'Esta semana',
            'month' => 'Este mes',
            'quarter' => 'Este trimestre',
            'all' => 'Todos los tiempos',
        ];
    }

    protected function getHeading(): ?string
    {
        $metrics = ItilDashboard::getIncidentMetrics($this->filter);
        $filterLabels = $this->getFilters();
        $selectedLabel = $filterLabels[$this->filter] ?? 'Todos los tiempos';

        return "Distribución de Incidentes ITIL - {$selectedLabel} ({$metrics['total_incidents']} tickets)";
    }

    protected function getOptions(): array
    {
        $metrics = ItilDashboard::getIncidentMetrics($this->filter);

        // Si no hay tickets, mostrar un mensaje o datos vacíos
        if ($metrics['total_incidents'] == 0) {
            return [
                'chart' => [
                    'type' => 'donut',
                    'height' => 300,
                ],
                'series' => [1], // Un valor mínimo para mostrar el gráfico
                'labels' => ['Sin datos disponibles'],
                'colors' => ['#e5e7eb'],
                'legend' => [
                    'position' => 'bottom',
                ],
                'dataLabels' => [
                    'enabled' => true,
                ],
                'plotOptions' => [
                    'pie' => [
                        'donut' => [
                            'labels' => [
                                'show' => true,
                                'total' => [
                                    'show' => true,
                                    'label' => 'Sin tickets',
                                    'formatter' => 'function() { return "0"; }'
                                ]
                            ]
                        ]
                    ]
                ]
            ];
        }

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
            'plotOptions' => [
                'pie' => [
                    'donut' => [
                        'labels' => [
                            'show' => true,
                            'total' => [
                                'show' => true,
                                'label' => 'Total',
                                'formatter' => 'function() { return "' . $metrics['total_incidents'] . '"; }'
                            ]
                        ]
                    ]
                ]
            ],
            'tooltip' => [
                'enabled' => true,
                'y' => [
                    'formatter' => 'function(val, opts) {
                        const total = ' . $metrics['total_incidents'] . ';
                        const percentage = ((val / total) * 100).toFixed(1);
                        return val + " (" + percentage + "%)";
                    }'
                ]
            ]
        ];
    }
}
