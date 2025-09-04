<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CategoriaResource\Pages;
use App\Filament\Resources\CategoriaResource\RelationManagers;
use App\Models\Categoria;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Grid;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Columns\ColorColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;

class CategoriaResource extends Resource
{
    protected static ?string $model = Categoria::class;

    protected static ?string $navigationIcon = 'heroicon-o-tag';

    protected static ?string $navigationGroup = 'Centro de Soporte';

    protected static ?string $navigationLabel = 'Categorías';

    protected static ?string $modelLabel = 'Categoría';

    protected static ?string $pluralModelLabel = 'Categorías';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Información Básica')
                    ->description('Información principal de la categoría')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('nombre')
                                    ->label('Nombre')
                                    ->required()
                                    ->unique(ignoreRecord: true)
                                    ->autofocus()
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(fn(Set $set, ?string $state) => $set('slug', str()->slug($state)))
                                    ->columnSpan(1),

                                TextInput::make('slug')
                                    ->label('Slug')
                                    ->required()
                                    ->unique(ignoreRecord: true)
                                    ->columnSpan(1),
                            ]),

                        Textarea::make('descripcion')
                            ->label('Descripción')
                            ->rows(3)
                            ->placeholder('Descripción detallada de la categoría...'),
                    ]),

                Section::make('Configuración ITIL')
                    ->description('Configuración específica para ITIL v4')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                Select::make('tipo_categoria')
                                    ->label('Tipo de Categoría')
                                    ->options([
                                        'incidente' => '🔴 Incidente',
                                        'solicitud_servicio' => '🔵 Solicitud de Servicio',
                                        'cambio' => '🟡 Cambio',
                                        'problema' => '🟢 Problema',
                                        'general' => '⚪ General',
                                    ])
                                    ->default('general')
                                    ->required()
                                    ->live()
                                    ->columnSpan(1),

                                Select::make('prioridad_default')
                                    ->label('Prioridad por Defecto')
                                    ->options([
                                        'baja' => '🟢 Baja',
                                        'media' => '🟡 Media',
                                        'alta' => '🟠 Alta',
                                        'critica' => '🔴 Crítica',
                                    ])
                                    ->default('media')
                                    ->required()
                                    ->columnSpan(2),
                            ]),

                        Grid::make(3)
                            ->schema([
                                ColorPicker::make('color')
                                    ->label('Color Identificativo')
                                    ->default('#6B7280')
                                    ->columnSpan(1),

                                TextInput::make('icono')
                                    ->label('Icono (Heroicon)')
                                    ->placeholder('heroicon-o-bug-ant')
                                    ->helperText('Nombre del icono de Heroicons')
                                    ->columnSpan(1),

                                Toggle::make('itil_category')
                                    ->label('Categoría ITIL')
                                    ->helperText('Marcar si es una categoría oficial ITIL')
                                    ->columnSpan(1),
                            ]),
                    ]),

                Section::make('Estado')
                    ->schema([
                        Toggle::make('is_active')
                            ->label('Activo')
                            ->default(true)
                            ->helperText('Solo las categorías activas aparecerán en los formularios'),
                    ])
                    ->visible(fn() => Auth::check() && Auth::user()->hasRole('Admin')),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nombre')
                    ->label('Nombre')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                BadgeColumn::make('tipo_categoria')
                    ->label('Tipo')
                    ->colors([
                        'danger' => 'incidente',
                        'info' => 'solicitud_servicio',
                        'warning' => 'cambio',
                        'success' => 'problema',
                        'secondary' => 'general',
                    ])
                    ->icons([
                        'heroicon-o-exclamation-triangle' => 'incidente',
                        'heroicon-o-clipboard-document-list' => 'solicitud_servicio',
                        'heroicon-o-arrow-path' => 'cambio',
                        'heroicon-o-bug-ant' => 'problema',
                        'heroicon-o-tag' => 'general',
                    ])
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'incidente' => 'Incidente',
                        'solicitud_servicio' => 'Solicitud',
                        'cambio' => 'Cambio',
                        'problema' => 'Problema',
                        'general' => 'General',
                        default => $state,
                    }),

                BadgeColumn::make('prioridad_default')
                    ->label('Prioridad')
                    ->colors([
                        'success' => 'baja',
                        'warning' => 'media',
                        'danger' => 'alta',
                        'danger' => 'critica',
                    ])
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'baja' => '🟢 Baja',
                        'media' => '🟡 Media',
                        'alta' => '🟠 Alta',
                        'critica' => '🔴 Crítica',
                        default => $state,
                    }),

                ColorColumn::make('color')
                    ->label('Color')
                    ->alignCenter(),

                IconColumn::make('itil_category')
                    ->label('ITIL')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-badge')
                    ->falseIcon('heroicon-o-x-mark')
                    ->trueColor('success')
                    ->falseColor('gray'),

                ToggleColumn::make('is_active')
                    ->label('Estado')
                    ->visible(fn() => Auth::check() && Auth::user()->hasRole('Admin')),

                TextColumn::make('created_at')
                    ->label('Creado')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('tipo_categoria')
                    ->label('Tipo de Categoría')
                    ->options([
                        'incidente' => 'Incidentes',
                        'solicitud_servicio' => 'Solicitudes de Servicio',
                        'cambio' => 'Cambios',
                        'problema' => 'Problemas',
                        'general' => 'General',
                    ])
                    ->multiple(),

                SelectFilter::make('prioridad_default')
                    ->label('Prioridad')
                    ->options([
                        'baja' => 'Baja',
                        'media' => 'Media',
                        'alta' => 'Alta',
                        'critica' => 'Crítica',
                    ])
                    ->multiple(),

                TernaryFilter::make('itil_category')
                    ->label('Categorías ITIL')
                    ->placeholder('Todas las categorías')
                    ->trueLabel('Solo ITIL')
                    ->falseLabel('No ITIL'),

                TernaryFilter::make('is_active')
                    ->label('Estado')
                    ->placeholder('Todas')
                    ->trueLabel('Activas')
                    ->falseLabel('Inactivas'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->visible(fn() => Auth::check() && Auth::user()->can('borrar-categoria')),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->visible(fn() => Auth::check() && Auth::user()->can('borrar-categoria')),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->striped();
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
            'index' => Pages\ListCategorias::route('/'),
            'create' => Pages\CreateCategoria::route('/create'),
            'edit' => Pages\EditCategoria::route('/{record}/edit'),
        ];
    }
}
