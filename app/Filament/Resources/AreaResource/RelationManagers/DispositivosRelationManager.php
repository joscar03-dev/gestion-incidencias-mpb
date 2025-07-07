<?php

namespace App\Filament\Resources\AreaResource\RelationManagers;

use Dom\Text;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Actions\AssociateAction;
use Filament\Tables\Actions\DissociateAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class DispositivosRelationManager extends RelationManager
{
    protected static string $relationship = 'dispositivos';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('nombre')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nombre')->label('Nombre'),
                TextColumn::make('estado')->label('Estado'),
                TextColumn::make('usuario.name')->label('Usuario Asignado')
                    ->default('No asignado')
                    ->sortable()
                    ->searchable(),

            ])
            ->headerActions([
                AssociateAction::make()
                    ->preloadRecordSelect()
                    ->recordTitleAttribute('nombre')
                    ->recordSelectOptionsQuery(fn($query) => $query), // Mostrar todas las áreas disponibles
            ])
            ->actions([
                DissociateAction::make()
                    ->after(function ($record) {
                        // Desasociar usuario solo si pertenece al área actual
                        $record->usuario()->dissociate();
                        $record->save();
                    }),
            ]);
    }
}
