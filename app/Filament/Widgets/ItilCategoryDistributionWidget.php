<?php

namespace App\Filament\Widgets;

use App\Models\ItilDashboard;
use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;

class ItilCategoryDistributionWidget extends ApexChartWidget
{
    protected static ?string $chartId = 'itilCategoryDistribution';
    protected static ?string $heading = 'Distribución por Categorías ITIL';
    protected static ?int $sort = 2;

    protected function getOptions(): array
    {
        $categories = ItilDashboard::getCategoryDistribution();

        $data = [];
        $labels = [];

        foreach ($categories as $category) {
            $data[] = $category['count'];
            $labels[] = $category['name'];
        }

        return [
            'chart' => [
                'type' => 'pie',
                'height' => 350,
            ],
            'series' => $data,
            'labels' => $labels,
            'colors' => [
                '#3b82f6', '#10b981', '#f59e0b', '#ef4444',
                '#8b5cf6', '#06b6d4', '#84cc16', '#f97316'
            ],
            'legend' => [
                'position' => 'bottom',
            ],
            'dataLabels' => [
                'enabled' => true,
                'formatter' => [
                    'function(val) { return Math.round(val) + "%"; }'
                ],
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
