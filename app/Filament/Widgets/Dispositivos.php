<?php

namespace App\Filament\Widgets;

use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;

class Dispositivos extends ApexChartWidget
{
    protected static ?int $sort = 1;
    /**
     * Chart Id
     *
     * @var string
     */
    protected static ?string $chartId = 'dispositivos';

    /**
     * Widget Title
     *
     * @var string|null
     */
    protected static ?string $heading = 'Dispositivos';

    /**
     * Chart options (series, labels, types, size, animations...)
     * https://apexcharts.com/docs/options
     *
     * @return array
     */
    protected function getOptions(): array
    {
        // Suponiendo que tienes un modelo Device con un campo 'estado'
        $estados = \App\Models\Dispositivo::select('estado')
            ->groupBy('estado')
            ->pluck('estado');

        $series = \App\Models\Dispositivo::selectRaw('estado, COUNT(*) as total')
            ->groupBy('estado')
            ->pluck('total', 'estado');

        return [
            'chart' => [
                'type' => 'pie',
                'height' => 300,
            ],
            'series' => $series->values()->toArray(),
            'labels' => $estados->toArray(),
            'legend' => [
                'labels' => [
                    'fontFamily' => 'inherit',
                ],
            ],
        ];
    }
}
