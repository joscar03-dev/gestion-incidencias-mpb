<?php

namespace App\Filament\Widgets;

use App\Models\Role;
use App\Models\Ticket;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\TextInputColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class UltimosTickets extends BaseWidget
{
    protected int | string | array $columnSpan = 'full';
    public function table(Table $table): Table
    {
        return $table

            ->query(
                auth()->user()->hasRole('Admin')
                    ? Ticket::query()
                    : Ticket::query()->where('asignado_a', auth()->id())
            )
            ->columns([
                TextColumn::make('titulo')
                    ->description(fn(Ticket $record): ?string => $record?->descripcion ?? null)
                    ->label('Título')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('estado')
                    ->badge()
                    ->colors([
                        'warning' => Ticket::ESTADOS['Archivado'],
                        'success' => Ticket::ESTADOS['Cerrado'],
                        'danger' => Ticket::ESTADOS['Abierto'],
                    ])
                    ->label('Estado'),
                TextColumn::make('prioridad')
                    ->badge()
                    ->colors([
                        'warning' => Ticket::PRIORIDAD['Alta'],
                        'info' => Ticket::PRIORIDAD['Media'],
                        'danger' => Ticket::PRIORIDAD['Baja'],
                    ])
                    ->label('Prioridad'),
                TextColumn::make('creadoPor.name')
                    ->label('Reporta')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('creadoPor.area.nombre')
                    ->label('Area Reporta')
                    ->searchable()
                    ->sortable(),
                TextInputColumn::make('comentario')
                    ->label('Comentario')
                    ->searchable(),
                TextColumn::make('created_at')
                    ->label('Creado en')

            ]);
    }
}
