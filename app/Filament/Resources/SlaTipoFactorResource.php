<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SlaTipoFactorResource\Pages;
use App\Filament\Resources\SlaTipoFactorResource\RelationManagers;
use App\Models\SlaTipoFactor;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SlaTipoFactorResource extends Resource
{
    protected static ?string $model = SlaTipoFactor::class;

    protected static ?string $navigationIcon = 'heroicon-o-tag';

    protected static ?string $navigationLabel = 'Factores de Tipo SLA';

    protected static ?string $modelLabel = 'Factor de Tipo';

    protected static ?string $pluralModelLabel = 'Factores de Tipo';

    protected static ?string $navigationGroup = 'SLA';

    protected static ?int $navigationSort = 21;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Información del Factor de Tipo')
                    ->schema([
                        Forms\Components\TextInput::make('codigo')
                            ->label('Código')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(50)
                            ->placeholder('ej: incidente, general, requerimiento, cambio')
                            ->helperText('Código único para identificar el tipo'),

                        Forms\Components\TextInput::make('nombre')
                            ->label('Nombre')
                            ->required()
                            ->maxLength(100)
                            ->placeholder('ej: Incidente, General, Requerimiento, Cambio'),

                        Forms\Components\Textarea::make('descripcion')
                            ->label('Descripción')
                            ->maxLength(500)
                            ->rows(3)
                            ->placeholder('Descripción del factor de tipo'),

                        Forms\Components\TextInput::make('factor')
                            ->label('Factor Multiplicador')
                            ->required()
                            ->numeric()
                            ->step(0.01)
                            ->minValue(0.01)
                            ->maxValue(10.00)
                            ->placeholder('ej: 0.60, 0.80, 1.20, 1.50')
                            ->helperText('Factor por el cual se multiplican los tiempos SLA'),

                        Forms\Components\TextInput::make('orden')
                            ->label('Orden de Visualización')
                            ->numeric()
                            ->default(0)
                            ->helperText('Orden para mostrar en listas'),

                        Forms\Components\Toggle::make('activo')
                            ->label('Activo')
                            ->default(true)
                            ->helperText('Define si el factor está activo para su uso'),
                    ])
                    ->columns(2)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('codigo')
                    ->label('Código')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('nombre')
                    ->label('Nombre')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('factor')
                    ->label('Factor')
                    ->sortable()
                    ->numeric(
                        decimalPlaces: 2,
                    ),

                Tables\Columns\TextColumn::make('descripcion')
                    ->label('Descripción')
                    ->limit(50)
                    ->tooltip(function (Tables\Columns\TextColumn $column): ?string {
                        $state = $column->getState();
                        if (strlen($state) <= 50) {
                            return null;
                        }
                        return $state;
                    }),

                Tables\Columns\TextColumn::make('orden')
                    ->label('Orden')
                    ->sortable(),

                Tables\Columns\IconColumn::make('activo')
                    ->label('Activo')
                    ->boolean()
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Creado')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Actualizado')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('activo')
                    ->label('Estado')
                    ->boolean()
                    ->trueLabel('Solo activos')
                    ->falseLabel('Solo inactivos')
                    ->native(false),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('orden', 'asc');
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSlaTipoFactors::route('/'),
            'create' => Pages\CreateSlaTipoFactor::route('/create'),
            'edit' => Pages\EditSlaTipoFactor::route('/{record}/edit'),
        ];
    }
}
