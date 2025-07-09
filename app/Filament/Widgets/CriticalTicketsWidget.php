<?php

namespace App\Filament\Widgets;

use App\Models\Ticket;
use Filament\Tables;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class CriticalTicketsWidget extends BaseWidget
{
    protected static ?string $heading = 'Tickets Críticos y Vencidos';
    protected static ?int $sort = 2;
    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Ticket::query()
                    ->where(function ($query) {
                        $query->where('prioridad', 'Critica')
                              ->orWhere('sla_vencido', true)
                              ->orWhere('escalado', true);
                    })
                    ->whereNotIn('estado', ['Cerrado', 'Archivado'])
                    ->orderByRaw("
                        CASE
                            WHEN prioridad = 'Critica' THEN 1
                            WHEN sla_vencido = 1 THEN 2
                            WHEN escalado = 1 THEN 3
                            ELSE 4
                        END
                    ")
                    ->orderBy('created_at', 'asc')
            )
            ->columns([
                BadgeColumn::make('urgencia')
                    ->label('Urgencia')
                    ->getStateUsing(function (Ticket $record) {
                        if ($record->prioridad === 'Critica') return 'CRÍTICO';
                        if ($record->sla_vencido) return 'VENCIDO';
                        if ($record->escalado) return 'ESCALADO';
                        return 'NORMAL';
                    })
                    ->colors([
                        'danger' => 'CRÍTICO',
                        'danger' => 'VENCIDO',
                        'warning' => 'ESCALADO',
                        'success' => 'NORMAL',
                    ])
                    ->icons([
                        'heroicon-o-fire' => 'CRÍTICO',
                        'heroicon-o-x-circle' => 'VENCIDO',
                        'heroicon-o-arrow-trending-up' => 'ESCALADO',
                        'heroicon-o-check-circle' => 'NORMAL',
                    ]),

                TextColumn::make('titulo')
                    ->label('Título')
                    ->limit(40)
                    ->weight('bold')
                    ->searchable(),

                TextColumn::make('area.nombre')
                    ->label('Área')
                    ->badge(),

                BadgeColumn::make('prioridad')
                    ->colors([
                        'danger' => 'Critica',
                        'warning' => 'Alta',
                        'success' => 'Media',
                        'secondary' => 'Baja',
                    ]),

                TextColumn::make('tiempo_vencimiento')
                    ->label('Tiempo Vencido/Restante')
                    ->getStateUsing(function (Ticket $record) {
                        $tiempo = $record->getTiempoRestanteSla('respuesta');
                        if ($tiempo === null) return 'Sin SLA';

                        if ($tiempo <= 0) {
                            $tiempoVencido = abs($tiempo);
                            $horas = floor($tiempoVencido / 60);
                            $minutos = $tiempoVencido % 60;
                            return $horas > 0 ? "Vencido {$horas}h {$minutos}m" : "Vencido {$minutos}m";
                        }

                        $horas = floor($tiempo / 60);
                        $minutos = $tiempo % 60;
                        return $horas > 0 ? "Resta {$horas}h {$minutos}m" : "Resta {$minutos}m";
                    })
                    ->color(function (Ticket $record) {
                        $tiempo = $record->getTiempoRestanteSla('respuesta');
                        if ($tiempo === null) return 'secondary';
                        if ($tiempo <= 0) return 'danger';
                        if ($tiempo <= 30) return 'warning';
                        return 'success';
                    }),

                TextColumn::make('asignadoA.name')
                    ->label('Asignado a')
                    ->default('Sin asignar')
                    ->badge()
                    ->color(fn ($state) => $state === 'Sin asignar' ? 'danger' : 'success'),

                TextColumn::make('created_at')
                    ->label('Creado')
                    ->since()
                    ->dateTime('d/m/Y H:i')
                    ->tooltip(fn ($record) => $record->created_at->format('d/m/Y H:i:s')),
            ])
            ->actions([
                Tables\Actions\Action::make('ver')
                    ->label('Ver')
                    ->icon('heroicon-o-eye')
                    ->url(fn (Ticket $record) => route('filament.admin.resources.tickets.view', $record))
                    ->openUrlInNewTab(),

                Tables\Actions\Action::make('editar')
                    ->label('Editar')
                    ->icon('heroicon-o-pencil')
                    ->url(fn (Ticket $record) => route('filament.admin.resources.tickets.edit', $record))
                    ->openUrlInNewTab(),
            ])
            ->emptyStateHeading('¡Excelente!')
            ->emptyStateDescription('No hay tickets críticos o vencidos en este momento.')
            ->emptyStateIcon('heroicon-o-check-circle')
            ->defaultPaginationPageOption(10);
    }
}
