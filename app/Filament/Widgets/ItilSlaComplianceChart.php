<?php

namespace App\Filament\Widgets;

use App\Models\ItilDashboard;
use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;

class ItilSlaComplianceChart extends ApexChartWidget
{
    protected static ?string $chartId = 'itilSlaComplianceChart';
    protected static ?string $heading = 'Cumplimiento SLA';
    protected static ?int $sort = 6;

    protected function getOptions(): array
    {
        $metrics = ItilDashboard::getIncidentMetrics();

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
            'colors' => [$metrics['sla_compliance'] >= 90 ? '#10b981' : '#ef4444'],
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
        ];
    }
}
