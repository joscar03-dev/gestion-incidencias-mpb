<?php

namespace App\Filament\Resources\TicketResource\Widgets;

use App\Models\Ticket;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class TicketsStatusWidget extends BaseWidget
{
    protected static ?int $sort = 2;
    protected static ?string $pollingInterval = '30s';

    protected function getStats(): array
    {
        // Base query - filtrar según el rol del usuario
        $baseQuery = auth()->user()->hasRole('Técnico')
            ? Ticket::where('asignado_a', auth()->id()) // Solo tickets asignados al técnico
            : Ticket::query(); // Todos los tickets para otros roles

        // Calcular métricas según los estados
        $abiertos = (clone $baseQuery)->where('estado', Ticket::ESTADOS['Abierto'])->count();
        $cerrados = (clone $baseQuery)->where('estado', Ticket::ESTADOS['Cerrado'])->count();
        $enProgreso = (clone $baseQuery)->where('estado', Ticket::ESTADOS['En Progreso'])->count();
        $escalados = (clone $baseQuery)->where('escalado', true)->count();

        // Tickets vencidos - usar una consulta más simple basada en el campo sla_vencido
        $vencidos = (clone $baseQuery)
            ->where('estado', '!=', Ticket::ESTADOS['Cerrado'])
            ->where('sla_vencido', true)
            ->count();

        // Determinar el prefijo según el rol
        $prefijo = auth()->user()->hasRole('Técnico') ? 'Mis ' : '';

        return [
            Stat::make($prefijo . 'Tickets Abiertos', $abiertos)
                ->description('Pendientes de atención')
                ->descriptionIcon('heroicon-m-exclamation-circle')
                ->color('danger')
                ->chart([7, 2, 10, 3, 15, 4, 17]),

            Stat::make($prefijo . 'Tickets En Progreso', $enProgreso)
                ->description('En proceso de resolución')
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning')
                ->chart([3, 5, 8, 12, 9, 6, 11]),

            Stat::make($prefijo . 'Tickets Cerrados', $cerrados)
                ->description('Resueltos exitosamente')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success')
                ->chart([15, 18, 22, 25, 28, 30, 32]),

            Stat::make($prefijo . 'Tickets Escalados', $escalados)
                ->description('Requieren atención especial')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('info')
                ->chart([1, 2, 1, 3, 2, 4, 3]),

            Stat::make($prefijo . 'Tickets Vencidos', $vencidos)
                ->description('Fuera de tiempo SLA')
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color($vencidos > 0 ? 'danger' : 'success')
                ->chart([2, 1, 3, 2, 4, 1, 2]),
        ];
    }
}
