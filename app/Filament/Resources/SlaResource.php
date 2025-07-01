<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SlaResource\Pages;
use App\Filament\Resources\SlaResource\RelationManagers;
use App\Models\Sla;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SlaResource extends Resource
{
    protected static ?string $model = Sla::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // 'area_id',
                // 'nivel',
                // 'tiempo_respuesta_horas',
                // 'tiempo_resolucion_horas',
                // 'tipo_ticket',
                // 'canal',
                // 'descripcion',
                // 'activo',
                Forms\Components\Select::make('area_id')
                    ->relationship('area', 'nombre')
                    ->required()
                    ->preload()
                    ->searchable(),
                Forms\Components\Select::make('nivel')
                    ->options([
                        'Alta' => 'Alta',
                        'Media' => 'Media',
                        'Baja' => 'Baja',
                    ])
                    ->required(),
                Forms\Components\TimePicker::make('tiempo_respuesta')
                    ->label('Tiempo de Respuesta (HH:MM:SS)')
                    ->required()
                    ->default(now()->addHours(1)),
                Forms\Components\TimePicker::make('tiempo_resolucion')
                    ->label('Tiempo de Resolución (HH:MM:SS)')
                    ->required()
                    ->default(now()->addHours(2)),
                Forms\Components\TextInput::make('tipo_ticket')
                    ->label('Tipo de Ticket')
                    ->nullable()
                    ->maxLength(255),
                Forms\Components\TextInput::make('canal')
                    ->label('Canal')
                    ->nullable()
                    ->maxLength(255),
                Forms\Components\Textarea::make('descripcion')
                    ->label('Descripción')
                    ->nullable()
                    ->maxLength(65535),
                Forms\Components\Toggle::make('activo')
                    ->label('Activo')
                    ->default(true)
                    ->inline(false)
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('area.nombre')
                    ->label('Área')
                    ->searchable(),
                Tables\Columns\TextColumn::make('nivel')
                    ->label('Nivel')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('tiempo_respuesta')
                    ->label('Tiempo de Respuesta (HH:MM:SS)')
                    ->time(),
                Tables\Columns\TextColumn::make('tiempo_resolucion')
                    ->label('Tiempo de Resolución (HH:MM:SS)')
                    ->time(),
                Tables\Columns\TextColumn::make('tipo_ticket')
                    ->label('Tipo de Ticket'),
                Tables\Columns\TextColumn::make('canal')
                    ->label('Canal'),
                Tables\Columns\ToggleColumn::make('activo')
                    ->label('Activo'),
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
            'index' => Pages\ManageSlas::route('/'),
        ];
    }
}
