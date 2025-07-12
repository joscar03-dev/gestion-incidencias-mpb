<?php

namespace App\Filament\Widgets;

use App\Models\ItilDashboard;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;

class ItilWorkloadTableWidget extends BaseWidget
{
    protected static ?string $heading = 'Carga de Trabajo del Equipo ITIL';
    protected static ?int $sort = 4;
    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        $workload = collect(ItilDashboard::getWorkloadAnalysis());

        // Ordenar la colección de workload por tickets abiertos (descendente)
        $sortedWorkload = $workload->sortByDesc('open_tickets');
        $userIds = $sortedWorkload->pluck('user_id')->toArray();

        return $table
            ->query(
                // Crear un builder con los datos de workload ordenados
                \App\Models\User::query()
                    ->whereIn('id', $userIds)
                    ->when(!empty($userIds), function ($query) use ($userIds) {
                        $orderBy = 'FIELD(id, ' . implode(',', $userIds) . ')';
                        return $query->orderByRaw($orderBy);
                    })
            )
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Técnico')
                    ->searchable(),

                Tables\Columns\TextColumn::make('open_tickets_count')
                    ->label('Tickets Abiertos')
                    ->getStateUsing(function ($record) use ($workload) {
                        $analyst = $workload->firstWhere('user_id', $record->id);
                        return $analyst['open_tickets'] ?? 0;
                    })
                    ->badge()
                    ->color(fn (string $state): string => match (true) {
                        $state > 10 => 'danger',
                        $state > 5 => 'warning',
                        default => 'success',
                    }),

                Tables\Columns\TextColumn::make('total_tickets_count')
                    ->label('Total Asignados')
                    ->getStateUsing(function ($record) use ($workload) {
                        $analyst = $workload->firstWhere('user_id', $record->id);
                        return $analyst['total_tickets'] ?? 0;
                    })
                    ->badge()
                    ->color('info'),

                Tables\Columns\TextColumn::make('resolved_tickets_count')
                    ->label('Resueltos')
                    ->getStateUsing(function ($record) use ($workload) {
                        $analyst = $workload->firstWhere('user_id', $record->id);
                        return $analyst['resolved_tickets'] ?? 0;
                    })
                    ->badge()
                    ->color('success'),

                Tables\Columns\TextColumn::make('escalated_tickets_count')
                    ->label('Escalados')
                    ->getStateUsing(function ($record) use ($workload) {
                        $analyst = $workload->firstWhere('user_id', $record->id);
                        return $analyst['escalated_tickets'] ?? 0;
                    })
                    ->badge()
                    ->color(fn (string $state): string => $state > 0 ? 'danger' : 'success'),

                Tables\Columns\TextColumn::make('resolution_rate_display')
                    ->label('Tasa Resolución')
                    ->getStateUsing(function ($record) use ($workload) {
                        $analyst = $workload->firstWhere('user_id', $record->id);
                        return ($analyst['resolution_rate'] ?? 0) . '%';
                    })
                    ->badge()
                    ->color(fn (string $state): string => match (true) {
                        (int) str_replace('%', '', $state) >= 80 => 'success',
                        (int) str_replace('%', '', $state) >= 60 => 'warning',
                        default => 'danger',
                    }),

                Tables\Columns\TextColumn::make('escalation_rate_display')
                    ->label('Tasa Escalación')
                    ->getStateUsing(function ($record) use ($workload) {
                        $analyst = $workload->firstWhere('user_id', $record->id);
                        return ($analyst['escalation_rate'] ?? 0) . '%';
                    })
                    ->badge()
                    ->color(fn (string $state): string => match (true) {
                        (int) str_replace('%', '', $state) >= 20 => 'danger',
                        (int) str_replace('%', '', $state) >= 10 => 'warning',
                        default => 'success',
                    }),
            ])
            ->paginated(false); // Desactivar paginación para mostrar todos los técnicos
    }
}
