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

    public function table(Table $table): Table
    {
        $workload = collect(ItilDashboard::getWorkloadAnalysis());

        // Crear una colección temporal para mostrar los datos
        $data = $workload->map(function ($item) {
            return (object) $item;
        });

        return $table
            ->query(
                // Usamos un builder que devuelve los datos como objetos
                \App\Models\User::query()
                    ->whereIn('id', $workload->pluck('user_id'))
                    ->with(['tickets' => function ($query) {
                        $query->select('id', 'asignado_a', 'estado');
                    }])
            )
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Técnico')
                    ->searchable()
                    ->sortable(),
                    
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
                    }),
                    
                Tables\Columns\TextColumn::make('resolved_tickets_count')
                    ->label('Resueltos')
                    ->getStateUsing(function ($record) use ($workload) {
                        $analyst = $workload->firstWhere('user_id', $record->id);
                        return $analyst['resolved_tickets'] ?? 0;
                    }),
                    
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
            ])
            ->defaultSort('name');
    }
}
