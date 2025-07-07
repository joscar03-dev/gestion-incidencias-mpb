<?php

namespace App\Filament\Resources\DispositivoResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class AsignacionesRelationManager extends RelationManager
{
    protected static string $relationship = 'asignaciones';

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('user_id')
            ->columns([
                Tables\Columns\TextColumn::make('user.name')->label('Usuario'),
                Tables\Columns\TextColumn::make('fecha_asignacion')->label('Fecha de asignación')->dateTime(),
                Tables\Columns\TextColumn::make('fecha_desasignacion')->label('Fecha de desasignación')->dateTime()->default(null),
            ])
            ->filters([])
            ->headerActions([])
            ->actions([])
            ->bulkActions([]);
    }
    public function isReadOnly(): bool
    {
        return false;
    }
    public function getContentTabIcon(): ?string
    {
        return 'heroicon-m-cog';
    }
    public function getListeners(): array // Define los listeners para eventos personalizados
    {
        return [
            'refreshRelationManager' => 'refreshTable',
        ];
    }
    // Método para refrescar la tabla cuando se recibe el evento
    public function refreshTable()
    {
        $this->resetTable();
    }
}
