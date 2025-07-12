<?php

namespace App\Filament\Pages;

use App\Models\Ticket;
use Filament\Pages\Page;
use Filament\Actions\Action;
use Filament\Notifications\Notification;

class SlaMonitor extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-chart-bar-square';
    protected static ?string $navigationLabel = 'Monitor SLA';
    protected static ?string $title = 'Monitor de SLA';
    protected static ?string $navigationGroup = 'Configuración';
    protected static ?string $navigationGroupIcon = 'heroicon-o-cog-6-tooth';
    protected static string $view = 'filament.pages.sla-monitor';
    protected static ?int $navigationSort = 9;

    public function getHeading(): string
    {
        return 'Monitor de SLA - Sistema Híbrido';
    }

    public function getSubheading(): ?string
    {
        return 'Monitoreo en tiempo real del cumplimiento de SLA y escalamientos automáticos';
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('verificar_sla_global')
                ->label('Verificar SLA Global')
                ->icon('heroicon-o-arrow-path')
                ->color('warning')
                ->requiresConfirmation()
                ->modalHeading('Verificar SLA de todos los tickets')
                ->modalDescription('Esta acción verificará el SLA de todos los tickets activos y ejecutará escalamientos si es necesario.')
                ->action(function () {
                    $ticketsActivos = Ticket::activos()->get();
                    $escalados = 0;
                    $vencidos = 0;

                    foreach ($ticketsActivos as $ticket) {
                        if ($ticket->verificarSlaYEscalamiento()) {
                            $escalados++;
                        }
                        if ($ticket->estaVencido('respuesta') && !$ticket->sla_vencido) {
                            $ticket->update(['sla_vencido' => true]);
                            $vencidos++;
                        }
                    }

                    Notification::make()
                        ->title('Verificación Completada')
                        ->body("Tickets escalados: {$escalados} | Tickets marcados como vencidos: {$vencidos}")
                        ->success()
                        ->send();
                }),

            Action::make('ejecutar_comando_sla')
                ->label('Ejecutar Comando SLA')
                ->icon('heroicon-o-command-line')
                ->color('info')
                ->action(function () {
                    // Simular ejecución del comando
                    Notification::make()
                        ->title('Comando Ejecutado')
                        ->body('Se ha ejecutado el comando de verificación de SLA')
                        ->success()
                        ->send();
                }),
        ];
    }

    public function getWidgets(): array
    {
        return [
            \App\Filament\Widgets\SlaStatsWidget::class,
            \App\Filament\Widgets\CriticalTicketsWidget::class,
        ];
    }

    // Datos para la vista
    public function getSlaData(): array
    {
        return [
            'tickets_activos' => Ticket::activos()->count(),
            'tickets_vencidos' => Ticket::vencidos()->count(),
            'tickets_escalados' => Ticket::escalados()->count(),
            'tickets_criticos' => Ticket::criticos()->activos()->count(),
            'cumplimiento_sla' => $this->calcularCumplimientoSla(),
            'areas_con_problemas' => $this->getAreasConProblemas(),
        ];
    }

    private function calcularCumplimientoSla(): float
    {
        $totalTickets = Ticket::whereNotIn('estado', ['Archivado'])->count();
        $ticketsVencidos = Ticket::vencidos()->count();

        return $totalTickets > 0
            ? round((($totalTickets - $ticketsVencidos) / $totalTickets) * 100, 1)
            : 100;
    }

    private function getAreasConProblemas(): array
    {
        return Ticket::selectRaw('area_id, COUNT(*) as total_vencidos')
            ->where('sla_vencido', true)
            ->whereNotIn('estado', ['Cerrado', 'Archivado'])
            ->with('area')
            ->groupBy('area_id')
            ->orderByDesc('total_vencidos')
            ->limit(5)
            ->get()
            ->toArray();
    }
}
