<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PermissionResource\Pages;
use App\Filament\Resources\PermissionResource\RelationManagers;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists\Components\Card;
use Filament\Forms\Components\Section;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Models\Permission as ModelsPermission;

class PermissionResource extends Resource
{
    protected static ?string $model = ModelsPermission::class;


    protected static ?string $navigationIcon = 'heroicon-o-key';

    protected static ?string $navigationGroup = 'Usuarios y Permisos';

    protected static ?int $navigationSort = 2;

    protected static ?string $label = 'Permiso';
    // // protected static ?string $pluralLabel = 'Permisos';


    // protected static ?string $slug = 'pending-orders';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Detalles de Permiso')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Nombre')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255),
                        Forms\Components\TextInput::make('guard_name')
                            ->label('Guard')
                            ->required()
                            ->default('web')
                            ->maxLength(255),
                    ]),
                Section::make('Detalles de Permiso')
                    ->schema([])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nombre')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('guard_name')
                    ->label('Guard')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Creado el')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Actualizado el')
                    ->dateTime()
                    ->sortable(),
            ])->filters([
                Tables\Filters\Filter::make('created_at')
                    ->label('Creado en')
                    ->form([
                        Forms\Components\DatePicker::make('created_at')
                            ->label('Fecha de creación')
                            ->required(),
                    ])
                    ->query(function (Builder $query, array $data) {
                        return $query->whereDate('created_at', $data['created_at']);
                    }),
            ])->headerActions([
                //Tables\Actions\CreateAction::make(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->visible(fn() => auth()->user()?->hasRole('Admin')),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->visible(fn() => auth()->user()?->hasRole('Admin')),
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
            'index' => Pages\ListPermissions::route('/'),
            'create' => Pages\CreatePermission::route('/create'),
            'edit' => Pages\EditPermission::route('/{record}/edit'),
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
