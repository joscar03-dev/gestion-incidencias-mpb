<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DispositivoResource\Pages;
use App\Filament\Resources\DispositivoResource\RelationManagers;
use App\Filament\Resources\DispositivoResource\RelationManagers\AsignacionesRelationManager;
use App\Models\Dispositivo;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class DispositivoResource extends Resource
{
    protected static ?string $model = Dispositivo::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('nombre')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('descripcion')
                    ->maxLength(255),
                Forms\Components\Select::make('categoria_id')
                    ->label('Categoría')
                    ->relationship('categoria_dispositivo', 'nombre')
                    ->required(),
                Forms\Components\TextInput::make('numero_serie')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Select::make('area_id')
                    ->label('Área')
                    ->live()
                    ->relationship('area', 'nombre')
                    ->required(),         
                Forms\Components\Select::make('usuario_id')
                    ->label('Usuario')
                    ->options(function (callable $get) {
                        $areaId = $get('area_id');
                        if (!$areaId) {
                            return [];
                        }
                        return User::where('area_id', $areaId)->pluck('name', 'id');
                    })
                    ->searchable()
                    ->visible(fn (callable $get) => !empty($get('area_id')))
                    ->reactive(),
                Forms\Components\Select::make('estado')
                    ->label('Estado')
                    ->options(function (callable $get) {
                        // Si hay usuario seleccionado, solo mostrar 'Asignado'
                        if ($get('usuario_id')) {
                            return [
                                'Asignado' => 'Asignado',
                            ];
                        }
                        // Si no hay usuario, mostrar solo 'Disponible' y 'Reparación'
                        return [
                            'Disponible' => 'Disponible',
                            'Reparación' => 'Reparación',
                        ];
                    })
                    ->disabled(fn (callable $get) => !empty($get('usuario_id')))
                    ->afterStateUpdated(function (callable $set, $state, callable $get) {
                        // Si se desasigna el usuario, resetear el estado a 'Disponible'
                        if (empty($get('usuario_id')) && $state === 'Asignado') {
                            $set('estado', 'Disponible');
                        }
                    })
                    ->reactive()
                    ->live(),
                Forms\Components\DatePicker::make('fecha_compra'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nombre')
                    ->searchable(),
                Tables\Columns\TextColumn::make('descripcion')
                    ->searchable(),
                Tables\Columns\TextColumn::make('categoria_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('numero_serie')
                    ->searchable(),
                Tables\Columns\TextColumn::make('estado')
                    ->searchable(),
                Tables\Columns\TextColumn::make('area_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('usuario_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('fecha_compra')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\Filter::make('usuario_asignado')
                    ->label('Con usuario asignado')
                    ->query(fn (Builder $query) => $query->whereNotNull('usuario_id')),
                Tables\Filters\Filter::make('disponible')
                    ->label('Disponibles')
                    ->query(fn (Builder $query) => $query->where('estado', 'Disponible')),
                Tables\Filters\Filter::make('asignados')
                    ->label('Asignados')
                    ->query(fn (Builder $query) => $query->where('estado', 'Asignado')),
                Tables\Filters\Filter::make('reparacion')
                    ->label('En reparación')
                    ->query(fn (Builder $query) => $query->where('estado', 'Reparación')),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            AsignacionesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDispositivos::route('/'),
            'create' => Pages\CreateDispositivo::route('/create'),
            'edit' => Pages\EditDispositivo::route('/{record}/edit'),
        ];
    }
}
