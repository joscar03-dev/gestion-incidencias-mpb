<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SolicitudTransferenciaResource\Pages;
use App\Filament\Resources\SolicitudTransferenciaResource\RelationManagers;
use App\Models\SolicitudTransferencia;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SolicitudTransferenciaResource extends Resource
{
    protected static ?string $model = SolicitudTransferencia::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('dispositivo_id')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('usuario_origen_id')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('usuario_destino_id')
                    ->required()
                    ->numeric(),
                Forms\Components\Textarea::make('motivo')
                    ->required()
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('estado')
                    ->required(),
                Forms\Components\DateTimePicker::make('fecha_solicitud')
                    ->required(),
                Forms\Components\DateTimePicker::make('fecha_respuesta'),
                Forms\Components\DateTimePicker::make('fecha_ejecucion'),
                Forms\Components\Textarea::make('observaciones_admin')
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('admin_respuesta_id')
                    ->numeric(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('dispositivo_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('usuario_origen_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('usuario_destino_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('estado'),
                Tables\Columns\TextColumn::make('fecha_solicitud')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('fecha_respuesta')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('fecha_ejecucion')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('admin_respuesta_id')
                    ->numeric()
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
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSolicitudTransferencias::route('/'),
            'create' => Pages\CreateSolicitudTransferencia::route('/create'),
            'edit' => Pages\EditSolicitudTransferencia::route('/{record}/edit'),
        ];
    }
}
