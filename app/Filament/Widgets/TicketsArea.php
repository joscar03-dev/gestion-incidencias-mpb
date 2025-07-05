<?php

namespace App\Filament\Widgets;

use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;
use App\Models\Area;
use Carbon\Carbon;

class TicketsArea extends ApexChartWidget
{
    protected static ?string $chartId = 'ticketsArea';
    protected static ?string $heading = 'Tickets por Área';
    protected int | string | array $columnSpan = 'full';
    protected static ?string $loadingIndicator = 'Loading...';
    public ?string $filter = 'week';

    protected function getOptions(): array
    {

        // Definir el rango de fechas según el filtro seleccionado
        $start = match ($this->filter) {
            'today' => Carbon::now()->startOfDay(),
            'week' => Carbon::now()->subDays(7),
            'month' => Carbon::now()->subMonth(),
            'year' => Carbon::now()->startOfYear(),
            default => Carbon::now()->subDays(7),
        };

        // Obtener áreas con tickets creados en el rango seleccionado
        $areas = Area::with(['usuarios.ticketsCreados' => function ($query) use ($start) {
            $query->where('created_at', '>=', $start);
        }])
            ->get()
            ->filter(function ($area) {
                return $area->usuarios->sum(function ($usuario) {
                    return $usuario->ticketsCreados->count();
                }) > 0;
            });

        $seriesData = $areas->map(function ($area) {
            return $area->usuarios->sum(function ($usuario) {
                return $usuario->ticketsCreados->count();
            });
        })->values()->toArray();

        $areas = $areas->pluck('nombre')->values()->toArray();

        return [
            'chart' => [
                'type' => 'bar',
                'height' => 300,
            ],
            'series' => [
                [
                    'name' => 'Tickets creados',
                    'data' => $seriesData,
                ],
            ],
            'xaxis' => [
                'categories' => $areas,

                'labels' => [
                    'style' => [
                        'fontFamily' => 'inherit',
                    ],
                ],
            ],
            'yaxis' => [
                'labels' => [
                    'style' => [
                        'fontFamily' => 'inherit',
                    ],
                ],
            ],
            
            'colors' => ['#3b82f6'],
            'plotOptions' => [
                'bar' => [
                    'borderRadius' => 3,
                    'horizontal' => false,
                ],
            ],
        ];
    }

    protected function getFilters(): ?array
    {
        return [
            'today' => 'Hoy',
            'week' => 'Hace 7 días',
            'month' => 'Hace 1 mes',
            'year' => 'Este año',
        ];
    }
}
