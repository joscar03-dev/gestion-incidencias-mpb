<?php

namespace App\Filament\Widgets;

use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;

class PorcentajeDispositivos extends ApexChartWidget
{
    /**
     * Chart Id
     *
     * @var string
     */
    protected static ?int $sort = 1;
    protected static ?string $chartId = 'PorcentajeDispositivos';

    /**
     * Widget Title
     *
     * @var string|null
     */
    protected static ?string $heading = 'PorcentajeDispositivos';

    /**
     * Chart options (series, labels, types, size, animations...)
     * https://apexcharts.com/docs/options
     *
     * @return array
     */
    protected function getOptions(): array
    {
        return [
            'chart' => [
                'type' => 'pie',
                'height' => 300,
            ],
            'series' => [2, 4, 6, 10, 14],
            'labels' => ['Jan', 'Feb', 'Mar', 'Apr', 'May'],
            'legend' => [
                'labels' => [
                    'fontFamily' => 'inherit',
                ],
            ],
        ];
    }
}
