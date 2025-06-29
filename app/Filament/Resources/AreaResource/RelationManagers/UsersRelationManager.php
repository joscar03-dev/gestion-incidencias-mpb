<?php

namespace App\Filament\Resources\AreaResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Actions\AssociateAction;
use Filament\Tables\Actions\AttachAction;
use Filament\Tables\Actions\DetachAction;
use Filament\Tables\Actions\DissociateAction;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class UsersRelationManager extends RelationManager
{
    protected static string $relationship = 'usuarios';

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->label('Nombre'),
                Tables\Columns\TextColumn::make('email')->label('Correo Electrónico'),
            ])
            ->headerActions([
                AssociateAction::make()
                    ->preloadRecordSelect()
                    ->recordTitleAttribute('name') // Mostrar el nombre del usuario
                    ->recordSelectOptionsQuery(fn($query) => $query->whereNull('area_id')), // Solo usuarios sin área
            ])
            ->actions([
                DissociateAction::make(),
            ]);
    }
}
