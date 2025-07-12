<?php

namespace App\Filament\Widgets;

use App\Models\ItilDashboard;
use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;

class ItilTrendAnalysisWidget extends ApexChartWidget
{
    protected static ?string $chartId = 'itilTrendAnalysis';
    protected static ?string $heading = 'Tendencia de Incidentes (Últimos 30 días)';
    protected static ?int $sort = 3;

    protected function getOptions(): array
    {
        $trends = ItilDashboard::getTrendAnalysis(30);

        $dates = [];
        $created = [];
        $resolved = [];
        $escalated = [];

        foreach ($trends as $trend) {
            $dates[] = \Carbon\Carbon::parse($trend['date'])->format('d/m');
            $created[] = $trend['incidents_created'];
            $resolved[] = $trend['incidents_resolved'];
            $escalated[] = $trend['incidents_escalated'];
        }

        return [
            'chart' => [
                'type' => 'line',
                'height' => 350,
                'toolbar' => [
                    'show' => true,
                ],
            ],
            'series' => [
                [
                    'name' => 'Creados',
                    'data' => $created,
                    'color' => '#3b82f6',
                ],
                [
                    'name' => 'Resueltos',
                    'data' => $resolved,
                    'color' => '#10b981',
                ],
                [
                    'name' => 'Escalados',
                    'data' => $escalated,
                    'color' => '#ef4444',
                ],
            ],
            'xaxis' => [
                'categories' => $dates,
                'title' => [
                    'text' => 'Fecha',
                ],
            ],
            'yaxis' => [
                'title' => [
                    'text' => 'Cantidad de Incidentes',
                ],
            ],
            'stroke' => [
                'curve' => 'smooth',
                'width' => 3,
            ],
            'markers' => [
                'size' => 4,
                'hover' => [
                    'sizeOffset' => 2,
                ],
            ],
            'grid' => [
                'borderColor' => '#e7e7e7',
                'row' => [
                    'colors' => ['#f3f3f3', 'transparent'],
                    'opacity' => 0.5,
                ],
            ],
            'legend' => [
                'position' => 'top',
                'horizontalAlign' => 'right',
            ],
        ];
    }
}
