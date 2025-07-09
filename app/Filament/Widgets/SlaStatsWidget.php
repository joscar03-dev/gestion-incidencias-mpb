<?php

namespace App\Filament\Widgets;

use App\Models\Ticket;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class SlaStatsWidget extends BaseWidget
{
    protected static ?string $pollingInterval = '30s';

    protected function getStats(): array
    {
        // Obtener tickets activos
        $ticketsActivos = Ticket::activos()->count();

        // Obtener tickets vencidos
        $ticketsVencidos = Ticket::vencidos()->count();

        // Obtener tickets escalados
        $ticketsEscalados = Ticket::escalados()->count();

        // Obtener tickets críticos
        $ticketsCriticos = Ticket::criticos()->activos()->count();

        // Calcular porcentaje de cumplimiento de SLA
        $totalTickets = Ticket::whereNotIn('estado', ['Archivado'])->count();
        $porcentajeCumplimiento = $totalTickets > 0
            ? round((($totalTickets - $ticketsVencidos) / $totalTickets) * 100, 1)
            : 100;

        return [
            Stat::make('Tickets Activos', $ticketsActivos)
                ->description('Tickets abiertos y en progreso')
                ->descriptionIcon('heroicon-m-clock')
                ->color('info')
                ->chart($this->getTicketsTrendData()),

            Stat::make('SLA Vencidos', $ticketsVencidos)
                ->description('Tickets que han superado su SLA')
                ->descriptionIcon('heroicon-m-x-circle')
                ->color('danger')
                ->extraAttributes([
                    'style' => $ticketsVencidos > 0 ? 'animation: pulse 2s infinite;' : '',
                ]),

            Stat::make('Tickets Escalados', $ticketsEscalados)
                ->description('Tickets escalados automáticamente')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('warning'),

            Stat::make('Tickets Críticos', $ticketsCriticos)
                ->description('Prioridad crítica activos')
                ->descriptionIcon('heroicon-m-fire')
                ->color($ticketsCriticos > 0 ? 'danger' : 'success'),

            Stat::make('Cumplimiento SLA', $porcentajeCumplimiento . '%')
                ->description('Porcentaje de tickets dentro del SLA')
                ->descriptionIcon('heroicon-m-chart-bar')
                ->color($this->getSlaColor($porcentajeCumplimiento))
                ->chart($this->getSlaComplianceData()),
        ];
    }

    private function getSlaColor(float $percentage): string
    {
        if ($percentage >= 95) return 'success';
        if ($percentage >= 85) return 'warning';
        return 'danger';
    }

    private function getTicketsTrendData(): array
    {
        // Datos de tendencia de los últimos 7 días
        $data = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i)->startOfDay();
            $count = Ticket::whereDate('created_at', $date)->count();
            $data[] = $count;
        }
        return $data;
    }

    private function getSlaComplianceData(): array
    {
        // Datos de cumplimiento de SLA de los últimos 7 días
        $data = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i)->startOfDay();
            $endDate = $date->copy()->endOfDay();

            $totalDay = Ticket::whereBetween('created_at', [$date, $endDate])->count();
            $vencidosDay = Ticket::whereBetween('created_at', [$date, $endDate])
                ->where('sla_vencido', true)->count();

            $compliance = $totalDay > 0 ? (($totalDay - $vencidosDay) / $totalDay) * 100 : 100;
            $data[] = round($compliance);
        }
        return $data;
    }
}
