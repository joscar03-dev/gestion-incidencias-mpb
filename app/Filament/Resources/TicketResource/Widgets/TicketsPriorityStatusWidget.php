<?php

namespace App\Filament\Resources\TicketResource\Widgets;

use App\Models\Ticket;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class TicketsPriorityStatusWidget extends BaseWidget
{
    protected static ?int $sort = 3;
    protected static ?string $pollingInterval = '30s';
    protected int | string | array $columnSpan = 'full';

    protected function getStats(): array
    {
        // Base query - filtrar según el rol del usuario
        $baseQuery = auth()->user()->hasRole('Técnico')
            ? Ticket::where('asignado_a', auth()->id()) // Solo tickets asignados al técnico
            : Ticket::query(); // Todos los tickets para otros roles

        // Determinar el prefijo según el rol
        $prefijo = auth()->user()->hasRole('Técnico') ? 'Mis ' : '';

        // Métricas por prioridad y estado crítico
        $alta = (clone $baseQuery)->where('prioridad', Ticket::PRIORIDAD['Alta'])->count();
        $media = (clone $baseQuery)->where('prioridad', Ticket::PRIORIDAD['Media'])->count();
        $baja = (clone $baseQuery)->where('prioridad', Ticket::PRIORIDAD['Baja'])->count();

        // Tickets críticos abiertos (Alta prioridad + Abiertos)
        $criticos = (clone $baseQuery)
            ->where('prioridad', Ticket::PRIORIDAD['Alta'])
            ->where('estado', Ticket::ESTADOS['Abierto'])
            ->count();

        // Tickets próximos a vencer - calcular basado en SLA real
        $proximosVencer = (clone $baseQuery)
            ->where('estado', '!=', Ticket::ESTADOS['Cerrado'])
            ->where('sla_vencido', false)
            ->with(['area.slas'])
            ->get()
            ->filter(function ($ticket) {
                $slaEfectivo = $ticket->getSlaEfectivo();
                if (!$slaEfectivo) {
                    return false;
                }

                // Calcular tiempo transcurrido en minutos
                $tiempoTranscurrido = $ticket->created_at->diffInMinutes(now());
                $tiempoLimite = $slaEfectivo['tiempo_respuesta'];

                // Considerar "próximo a vencer" si queda menos del 20% del tiempo
                $umbralAdvertencia = $tiempoLimite * 0.8;

                return $tiempoTranscurrido >= $umbralAdvertencia && $tiempoTranscurrido < $tiempoLimite;
            })
            ->count();

        // Tickets asignados hoy
        $hoy = (clone $baseQuery)
            ->whereDate('created_at', today())
            ->count();

        return [
            Stat::make($prefijo . 'Críticos Abiertos', $criticos)
                ->description('Alta prioridad sin resolver')
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color($criticos > 0 ? 'danger' : 'success'),

            Stat::make('Próximos a Vencer', $proximosVencer)
                ->descriptionIcon('heroicon-m-clock')
                ->color($proximosVencer > 0 ? 'warning' : 'success'),

            Stat::make($prefijo . 'Creados Hoy', $hoy)
                ->description('Tickets del día')
                ->descriptionIcon('heroicon-m-calendar-days')
                ->color('info'),
        ];
    }
}
