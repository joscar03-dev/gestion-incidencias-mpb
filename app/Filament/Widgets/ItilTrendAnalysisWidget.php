<?php

namespace App\Filament\Widgets;

use App\Models\Ticket;
use App\Models\Categoria;
use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;

class ItilTrendAnalysisWidget extends ApexChartWidget
{
    protected static ?string $chartId = 'topCategoriasChart';
    protected static ?int $sort = 3;
    protected int | string | array $columnSpan = 'full';

    protected function getHeading(): string
    {
        $ticketsWithCategories = Ticket::whereHas('categorias')->count();
        $totalTickets = Ticket::count();

        if ($ticketsWithCategories > 0) {
            return "Top Categorías de Tickets ({$ticketsWithCategories} de {$totalTickets} categorizados)";
        }

        return "Distribución de Tickets por Estado ({$totalTickets} tickets)";
    }

    protected function getOptions(): array
    {
        $data = $this->getTopCategories();

        return [
            'chart' => [
                'type' => 'bar',
                'height' => 350,
            ],
            'series' => [
                [
                    'name' => 'Tickets',
                    'data' => $data['values'],
                ],
            ],
            'xaxis' => [
                'categories' => $data['categories'],
            ],
            'plotOptions' => [
                'bar' => [
                    'horizontal' => false,
                    'columnWidth' => '60%',
                ],
            ],
            'colors' => ['#3b82f6'],
            'dataLabels' => [
                'enabled' => true,
            ],
            'yaxis' => [
                'title' => [
                    'text' => 'Número de Tickets',
                ],
            ],
            'title' => [
                'text' => $data['total'] > 0 ? "Total: {$data['total']} tickets" : 'Sin datos disponibles',
                'align' => 'center',
                'style' => [
                    'fontSize' => '14px',
                    'color' => '#666',
                ],
            ],
        ];
    }

    private function getTopCategories(): array
    {
        // Obtener tickets con categorías
        $categoryStats = Ticket::whereHas('categorias')
            ->with('categorias')
            ->get()
            ->flatMap(function ($ticket) {
                return $ticket->categorias;
            })
            ->groupBy('nombre')
            ->map(function ($categories) {
                return $categories->count();
            })
            ->sortDesc()
            ->take(10); // Top 10 categorías

        $total = $categoryStats->sum();

        if ($categoryStats->isEmpty()) {
            // Si no hay datos de categorías, mostrar distribución por estado
            return $this->getFallbackData();
        }

        $categories = $categoryStats->keys()->toArray();
        $values = $categoryStats->values()->toArray();

        return [
            'categories' => $categories,
            'values' => $values,
            'total' => $total,
        ];
    }

    private function getFallbackData(): array
    {
        // Fallback: mostrar distribución por estado si no hay categorías
        $statusStats = Ticket::selectRaw('estado, COUNT(*) as count')
            ->groupBy('estado')
            ->pluck('count', 'estado');

        if ($statusStats->isEmpty()) {
            return [
                'categories' => ['Sin datos'],
                'values' => [0],
                'total' => 0,
            ];
        }

        return [
            'categories' => $statusStats->keys()->toArray(),
            'values' => $statusStats->values()->toArray(),
            'total' => $statusStats->sum(),
        ];
    }
}
