<?php

namespace App\Filament\Widgets;

use App\Models\ItilDashboard;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ItilOverviewWidget extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $metrics = ItilDashboard::getIncidentMetrics();
        $serviceMetrics = ItilDashboard::getServiceAvailabilityMetrics();

        return [
            Stat::make('Total Incidentes', $metrics['total_incidents'])
                ->description('Incidentes registrados')
                ->descriptionIcon('heroicon-m-ticket')
                ->color('info')
                ->chart([7, 3, 4, 5, 6, 3, 5, 3]),

            Stat::make('Cumplimiento SLA', $metrics['sla_compliance'] . '%')
                ->description($metrics['sla_compliance'] >= 90 ? 'Excelente' : ($metrics['sla_compliance'] >= 80 ? 'Bueno' : 'Mejorable'))
                ->descriptionIcon($metrics['sla_compliance'] >= 90 ? 'heroicon-m-check-circle' : 'heroicon-m-exclamation-triangle')
                ->color($metrics['sla_compliance'] >= 90 ? 'success' : ($metrics['sla_compliance'] >= 80 ? 'warning' : 'danger'))
                ->chart([65, 78, 82, 85, 88, 92, $metrics['sla_compliance']]),

            Stat::make('Disponibilidad', $serviceMetrics['availability_percentage'] . '%')
                ->description('Disponibilidad del servicio')
                ->descriptionIcon('heroicon-m-server')
                ->color($serviceMetrics['availability_percentage'] >= 99 ? 'success' : 'warning')
                ->chart([99.1, 99.5, 99.8, 99.2, 99.9, $serviceMetrics['availability_percentage']]),

            Stat::make('Tasa Resolución', $metrics['resolution_rate'] . '%')
                ->description('Tickets resueltos')
                ->descriptionIcon('heroicon-m-check')
                ->color($metrics['resolution_rate'] >= 80 ? 'success' : 'info')
                ->chart([70, 75, 80, 82, 85, $metrics['resolution_rate']]),
        ];
    }
}
