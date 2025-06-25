<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TicketResource\Pages;
use App\Filament\Resources\TicketResource\RelationManagers;
use App\Filament\Resources\TicketResource\RelationManagers\CategoriasRelationManager;
use App\Models\Rol;
use App\Models\Ticket;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\SelectColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\TextInputColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TicketResource extends Resource
{
    protected static ?string $model = Ticket::class;


    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->columns(3)
            ->schema([
                TextInput::make('titulo')
                    ->label('Título')
                    ->required()
                    ->autofocus(),
                Textarea::make('descripcion')
                    ->label('Descripción')
                    ->rows(3),
                Select::make('estado')
                    ->label('Estado')
                    ->options(self::$model::ESTADOS)
                    ->default(self::$model::ESTADOS['Abierto'])
                    ->required()
                    ->in(array_keys(self::$model::ESTADOS)),
                FileUpload::make('attachment')
                    ->label('Archivo')
                    ->preserveFilenames()
                    ->downloadable()
                    ->uploadingMessage('Subiendo archivo...')
                    ->directory('tickets')
                    ->acceptedFileTypes(['application/pdf', 'image/*'])
                    ->maxSize(1024),
                Select::make('prioridad')
                    ->label('Prioridad')
                    ->options(self::$model::PRIORIDAD)
                    ->required()
                    ->in(array_keys(self::$model::PRIORIDAD)),
                Select::make('asignado_a')
                    ->label('Asignado a')
                    ->options(
                        User::role(['Moderador', 'Tecnico'])->pluck('name', 'id')->toArray()
                    )
                    ->visible(fn () => auth()->user()?->hasRole(['Admin', 'Moderador'])),
                Textarea::make('comentario')
                    ->label('Comentarios')
                    ->rows(3),
            ])->statePath('data');
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(

                fn(Builder $query) =>
                auth()->user()->hasRole('Admin') ?
                    $query : $query->where('asignado_a', auth()->id())

            )
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('titulo')
                    ->description(fn(Ticket $record): ?string => $record?->descripcion ?? null)
                    ->label('Título')
                    ->searchable()
                    ->sortable(),
                SelectColumn::make('estado')
                    ->options(self::$model::ESTADOS)
                    ->label('Estado'),
                TextColumn::make('prioridad')
                    ->badge()
                    ->colors ([
                        'warning' => self::$model::PRIORIDAD['Alta'],
                        'info' => self::$model::PRIORIDAD['Media'],
                        'danger' => self::$model::PRIORIDAD['Baja'],
                    ])
                    ->label('Prioridad'),
                TextColumn::make('creadoPor.name')
                    ->label('Creado por')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('asignadoA.name')
                    ->label('Asignado a')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('asignadoPor.name')
                    ->label('Asignado por')
                    ->default('Sistema')
                    ->searchable()
                    ->sortable(),
                TextInputColumn::make('comentario')
                    ->label('Comentario')
                    ->searchable(),
                TextColumn::make('created_at')
                    ->label('Creado en')
                    ->sortable()



            ])
            ->filters([

                SelectFilter::make('estado')
                    ->options(self::$model::ESTADOS)
                    ->label('Estado')
                    ->placeholder('Filtro por estado'),
                SelectFilter::make('prioridad')
                    ->options(self::$model::PRIORIDAD)
                    ->label('Prioridad')
                    ->placeholder('Filtro por prioridad'),

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
            CategoriasRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTickets::route('/'),
            'create' => Pages\CreateTicket::route('/create'),
            'edit' => Pages\EditTicket::route('/{record}/edit'),
        ];
    }
}
