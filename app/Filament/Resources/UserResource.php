<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\Area;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    protected static ?int $navigationSort = 1;
    protected static ?string $navigationGroup = 'Administración';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Detalles del Usuario')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Nombre')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('email')
                            ->label('Correo Electrónico')
                            ->email()
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('password')
                            ->label('Contraseña')
                            ->password()
                            ->maxLength(255)
                            ->required(fn($livewire) => $livewire instanceof \Filament\Resources\Pages\CreateRecord) // Solo requerido al crear un nuevo usuario
                            ->visible(fn($livewire) => $livewire instanceof \Filament\Resources\Pages\CreateRecord),
                        Forms\Components\Select::make('roles')
                            ->label('Rol')
                            ->relationship('roles', 'name')
                            ->preload()
                            ->multiple()
                            ->required(),
                        Forms\Components\Select::make('area_id') // Relación con el área
                            ->label('Área')
                            ->relationship('area', 'nombre')
                            ->preload()
                            ->searchable()
                            ->options(Area::all()->pluck('nombre', 'id'))

                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->label('Nombre'),
                Tables\Columns\TextColumn::make('email')
                    ->searchable()
                    ->label('Correo Electrónico'),
                Tables\Columns\TextColumn::make('roles.name')
                    ->label('Rol')
                    ->badge()
                    ->searchable(),
                Tables\Columns\TextColumn::make('area.nombre') // Relación con el área
                    ->label('Área')
                    ->searchable()
                    ->tooltip(function ($record) {
                        // Nombre completo como tooltip
                        $area = $record->area;
                        $names = [];
                        while ($area) {
                            array_unshift($names, $area->nombre);
                            $area = $area->parent;
                        }
                        return implode(' / ', $names);
                    }),
                Tables\Columns\TextColumn::make('email_verified_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->label('Verificado'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->label('Creado')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->label('Actualizado')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([])
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
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();

        if (auth()->user()?->hasRole('Admin')) {
            // El admin ve todos los usuarios
            return $query;
        }

        if (auth()->user()?->hasRole('Moderador')) {
            // El moderador ve todos menos el admin
            return $query->whereDoesntHave('roles', function ($q) {
                $q->where('name', 'Admin');
            });
        }

        // Otros roles no ven nada
        return $query->whereRaw('0 = 1');
    }
}
