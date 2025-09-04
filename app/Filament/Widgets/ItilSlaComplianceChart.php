<?php

namespace App\Filament\Widgets;

use App\Models\ItilDashboard;
use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;

class ItilSlaComplianceChart extends ApexChartWidget
{
    protected static ?string $chartId = 'itilSlaComplianceChart';
    protected static ?string $heading = 'Cumplimiento SLA';
    protected static ?int $sort = 6;

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

        return "Cumplimiento SLA - {$selectedLabel} ({$metrics['total_incidents']} tickets)";
    }

    protected function getOptions(): array
    {
        // Usar el filtro seleccionado por el usuario
        $metrics = ItilDashboard::getIncidentMetrics($this->filter);

        return [
            'chart' => [
                'type' => 'radialBar',
                'height' => 350,
            ],
            'series' => [$metrics['sla_compliance']],
            'plotOptions' => [
                'radialBar' => [
                    'startAngle' => -90,
                    'endAngle' => 90,
                    'hollow' => [
                        'size' => '70%',
                    ],
                    'dataLabels' => [
                        'name' => [
                            'show' => true,
                            'offsetY' => -10,
                        ],
                        'value' => [
                            'offsetY' => 0,
                            'fontSize' => '22px',
                            'fontWeight' => 600,
                            'show' => true,
                        ],
                    ],
                ],
            ],
            'colors' => [$this->getSlaColor($metrics['sla_compliance'])],
            'labels' => ['SLA'],
            'fill' => [
                'type' => 'gradient',
                'gradient' => [
                    'shade' => 'light',
                    'shadeIntensity' => 0.4,
                    'inverseColors' => false,
                    'opacityFrom' => 1,
                    'opacityTo' => 1,
                ],
            ],
            'tooltip' => [
                'enabled' => true,
                'custom' => "function(opts) {
                    const metrics = " . json_encode($metrics) . ";
                    return '<div class=\"p-2\">' +
                           '<div><strong>Total de tickets:</strong> ' + metrics.total_incidents + '</div>' +
                           '<div><strong>SLA cumplido:</strong> ' + (metrics.total_incidents - metrics.sla_breached) + '</div>' +
                           '<div><strong>SLA vencido:</strong> ' + metrics.sla_breached + '</div>' +
                           '<div><strong>Cumplimiento:</strong> ' + metrics.sla_compliance + '%</div>' +
                           '</div>';
                }"
            ],
        ];
    }

    private function getSlaColor(float $percentage): string
    {
        if ($percentage >= 95) return '#10b981'; // Verde - Excelente
        if ($percentage >= 85) return '#f59e0b'; // Amarillo - Bueno
        if ($percentage >= 70) return '#f97316'; // Naranja - Regular
        return '#ef4444'; // Rojo - Malo
    }
}
