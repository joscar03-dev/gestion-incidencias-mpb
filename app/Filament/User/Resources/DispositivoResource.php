<?php

namespace App\Filament\User\Resources;

use App\Filament\User\Resources\DispositivoResource\Pages;
use App\Models\Dispositivo;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class DispositivoResource extends Resource
{
    protected static ?string $model = Dispositivo::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nombre')->label('Nombre')->searchable(),
                Tables\Columns\TextColumn::make('descripcion')->label('Descripción')->limit(50),
                Tables\Columns\TextColumn::make('categoria.nombre')->label('Categoría')->searchable(),
                Tables\Columns\TextColumn::make('numero_serie')->label('N° Serie')->searchable(),
                Tables\Columns\TextColumn::make('estado')->label('Estado'),
                Tables\Columns\TextColumn::make('area.nombre')->label('Área')->searchable(),
                Tables\Columns\TextColumn::make('usuario.name')->label('Usuario')->searchable(),
                Tables\Columns\TextColumn::make('fecha_compra')->label('Fecha de Compra')->date(),
            ])
            ->filters([
                //
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

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageDispositivos::route('/'),
        ];
    }
}
