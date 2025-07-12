<?php

namespace App\Filament\Widgets;

use App\Models\ItilDashboard;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class WorkloadStatsWidget extends BaseWidget
{
    protected static ?int $sort = 5;

    protected function getStats(): array
    {
        $workload = collect(ItilDashboard::getWorkloadAnalysis());

        $totalTechnicians = $workload->count();
        $totalOpenTickets = $workload->sum('open_tickets');
        $avgResolutionRate = $workload->avg('resolution_rate');

        return [
            Stat::make('Técnicos Activos', $totalTechnicians)
                ->description('Con tickets asignados')
                ->color('primary'),

            Stat::make('Tickets Abiertos Total', $totalOpenTickets)
                ->description('En todos los técnicos')
                ->color('warning'),

            Stat::make('Promedio Resolución', round($avgResolutionRate, 1) . '%')
                ->description('Tasa promedio del equipo')
                ->color($avgResolutionRate >= 70 ? 'success' : 'danger'),
        ];
    }
}
