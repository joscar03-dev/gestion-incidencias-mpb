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
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Enums\FiltersLayout;
use Illuminate\Support\Collection;

class DispositivoResource extends Resource
{
    protected static ?string $model = Dispositivo::class;

    protected static ?string $navigationGroup = 'Gestión de Dispositivos';

    protected static ?string $navigationIcon = 'heroicon-o-computer-desktop';

    protected static ?int $navigationSort = 1;

    protected static ?string $navigationLabel = 'Dispositivos';

    protected static ?string $modelLabel = 'Dispositivo';

    protected static ?string $pluralModelLabel = 'Dispositivos';

    protected static ?string $recordTitleAttribute = 'nombre';

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('estado', 'Disponible')->count();
    }

    public static function getNavigationBadgeColor(): string|array|null
    {
        return static::getModel()::where('estado', 'Disponible')->count() > 0 ? 'success' : 'warning';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Grid::make(['default' => 1, 'lg' => 3])
                    ->schema([
                        Forms\Components\Section::make('🖥️ Información del Dispositivo')
                            ->description('Datos básicos del dispositivo')
                            ->schema([
                                Forms\Components\TextInput::make('nombre')
                                    ->label('📱 Nombre del Dispositivo')
                                    ->required()
                                    ->maxLength(255)
                                    ->placeholder('Ej: Laptop Dell Inspiron 15'),

                                Forms\Components\Textarea::make('descripcion')
                                    ->label('📝 Descripción')
                                    ->rows(3)
                                    ->placeholder('Descripción detallada del dispositivo...'),

                                Forms\Components\TextInput::make('numero_serie')
                                    ->label('🔢 Número de Serie')
                                    ->required()
                                    ->maxLength(255)
                                    ->unique(ignoreRecord: true)
                                    ->placeholder('Ej: ABC123XYZ789'),

                                Forms\Components\Select::make('categoria_id')
                                    ->label('🏷️ Categoría')
                                    ->relationship('categoria_dispositivo', 'nombre')
                                    ->required()
                                    ->preload()
                                    ->searchable()
                                    ->placeholder('Seleccionar categoría'),
                            ])
                            ->columnSpan(['default' => 1, 'lg' => 2])
                            ->collapsible(),

                        Forms\Components\Section::make('📸 Imagen del Dispositivo')
                            ->description('Fotografía del dispositivo')
                            ->schema([
                                Forms\Components\FileUpload::make('imagen')
                                    ->label('📷 Imagen')
                                    ->disk('public')
                                    ->directory('dispositivos')
                                    ->image()
                                    ->imageEditor()
                                    ->imageResizeMode('cover')
                                    ->imageCropAspectRatio('16:9')
                                    ->imageResizeTargetWidth('800')
                                    ->imageResizeTargetHeight('450')
                                    ->maxSize(2048)
                                    ->downloadable()
                                    ->previewable()
                                    ->uploadingMessage('Subiendo imagen...')
                                    ->helperText('Formatos: JPG, PNG. Tamaño máximo: 2MB'),
                            ])
                            ->columnSpan(['default' => 1, 'lg' => 1])
                            ->collapsible(),
                    ]),

                Forms\Components\Grid::make(['default' => 1, 'lg' => 2])
                    ->schema([
                        Forms\Components\Section::make('🏢 Ubicación y Asignación')
                            ->description('Área y usuario responsable')
                            ->schema([
                                Forms\Components\Select::make('area_id')
                                    ->label('🏢 Área')
                                    ->relationship('area', 'nombre')
                                    ->live()
                                    ->preload()
                                    ->searchable()
                                    ->placeholder('Seleccionar área'),

                                Forms\Components\Select::make('usuario_id')
                                    ->label('👤 Usuario Asignado')
                                    ->options(function (callable $get) {
                                        $areaId = $get('area_id');
                                        if (!$areaId) {
                                            return [];
                                        }

                                        return User::where('area_id', $areaId)->pluck('name', 'id');
                                    })
                                    ->searchable()
                                    ->preload()
                                    ->placeholder('Sin asignar')
                                    ->visible(fn (callable $get) => !empty($get('area_id')))
                                    ->live(),
                            ])
                            ->collapsible(),

                        Forms\Components\Section::make('📊 Estado y Compra')
                            ->description('Estado actual y fecha de adquisición')
                            ->schema([
                                Forms\Components\Select::make('estado')
                                    ->label('📊 Estado')
                                    ->options(function (callable $get) {
                                        if ($get('usuario_id')) {
                                            return ['Asignado' => 'Asignado'];
                                        }
                                        return Dispositivo::ESTADOS;
                                    })
                                    ->required()
                                    ->native(false)
                                    ->default('Disponible')
                                    ->live(),

                                Forms\Components\DatePicker::make('fecha_compra')
                                    ->label('📅 Fecha de Compra')
                                    ->native(false)
                                    ->displayFormat('d/m/Y')
                                    ->placeholder('Seleccionar fecha'),
                            ])
                            ->collapsible(),
                    ]),
            ])
            ->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('imagen')
                    ->label('📷')
                    ->circular()
                    ->size(60)
                    ->defaultImageUrl(asset('images/default-device.png'))
                    ->tooltip('Imagen del dispositivo'),

                Tables\Columns\TextColumn::make('nombre')
                    ->label('Dispositivo')
                    ->searchable()
                    ->sortable()
                    ->weight(FontWeight::Bold)
                    ->description(fn (Dispositivo $record): string => $record->numero_serie)
                    ->wrap(),

                Tables\Columns\TextColumn::make('categoria_dispositivo.nombre')
                    ->label('Categoría')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('primary'),

                Tables\Columns\TextColumn::make('estado')
                    ->label('Estado')
                    ->badge()
                    ->color(fn (Dispositivo $record): string => $record->estado_badge_color)
                    ->sortable(),

                Tables\Columns\TextColumn::make('area.nombre')
                    ->label('Área')
                    ->searchable()
                    ->sortable()
                    ->placeholder('-')
                    ->wrap(),

                Tables\Columns\TextColumn::make('usuario.name')
                    ->label('Usuario Asignado')
                    ->searchable()
                    ->sortable()
                    ->placeholder('Sin asignar')
                    ->description(fn (Dispositivo $record): ?string =>
                        $record->usuario ? $record->usuario->email : null)
                    ->wrap(),

                Tables\Columns\TextColumn::make('fecha_compra')
                    ->label('Fecha Compra')
                    ->date('d/m/Y')
                    ->sortable()
                    ->placeholder('-')
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Creado')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('estado')
                    ->label('Estado')
                    ->options(Dispositivo::ESTADOS)
                    ->multiple()
                    ->preload(),

                Tables\Filters\SelectFilter::make('categoria_id')
                    ->label('Categoría')
                    ->relationship('categoria_dispositivo', 'nombre')
                    ->searchable()
                    ->preload()
                    ->multiple(),

                Tables\Filters\SelectFilter::make('area_id')
                    ->label('Área')
                    ->relationship('area', 'nombre')
                    ->searchable()
                    ->preload(),

                Tables\Filters\TernaryFilter::make('usuario_id')
                    ->label('Asignación')
                    ->placeholder('Todos')
                    ->trueLabel('Con usuario asignado')
                    ->falseLabel('Sin usuario asignado')
                    ->queries(
                        true: fn (Builder $query) => $query->whereNotNull('usuario_id'),
                        false: fn (Builder $query) => $query->whereNull('usuario_id'),
                    ),

                Tables\Filters\Filter::make('fecha_compra')
                    ->form([
                        Forms\Components\DatePicker::make('desde')
                            ->label('Comprado desde')
                            ->placeholder('Fecha inicio'),
                        Forms\Components\DatePicker::make('hasta')
                            ->label('Comprado hasta')
                            ->placeholder('Fecha fin'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['desde'],
                                fn (Builder $query, $date): Builder => $query->whereDate('fecha_compra', '>=', $date),
                            )
                            ->when(
                                $data['hasta'],
                                fn (Builder $query, $date): Builder => $query->whereDate('fecha_compra', '<=', $date),
                            );
                    }),

                Tables\Filters\Filter::make('disponibles')
                    ->label('Solo Disponibles')
                    ->query(fn (Builder $query): Builder => $query->where('estado', 'Disponible'))
                    ->toggle(),

                Tables\Filters\Filter::make('reparacion')
                    ->label('En Reparación')
                    ->query(fn (Builder $query): Builder => $query->where('estado', 'Reparación'))
                    ->toggle(),
            ], layout: FiltersLayout::AboveContentCollapsible)
            ->filtersFormColumns(3)
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make()
                        ->icon('heroicon-o-eye')
                        ->color('info'),

                    Tables\Actions\EditAction::make()
                        ->icon('heroicon-o-pencil-square')
                        ->color('warning'),

                    Tables\Actions\Action::make('cambiar_estado')
                        ->label('Cambiar Estado')
                        ->icon('heroicon-o-arrow-path')
                        ->color('primary')
                        ->form([
                            Forms\Components\Select::make('nuevo_estado')
                                ->label('Nuevo Estado')
                                ->options(Dispositivo::ESTADOS)
                                ->required()
                                ->native(false),
                        ])
                        ->action(function (Dispositivo $record, array $data): void {
                            $record->update(['estado' => $data['nuevo_estado']]);
                        }),

                    Tables\Actions\DeleteAction::make()
                        ->icon('heroicon-o-trash')
                        ->color('danger'),
                ])
                ->label('Acciones')
                ->icon('heroicon-m-ellipsis-vertical')
                ->size('sm')
                ->color('gray'),

                Tables\Actions\Action::make('asignar')
                    ->label('Asignar')
                    ->icon('heroicon-o-user-plus')
                    ->color('success')
                    ->visible(fn (Dispositivo $record): bool => $record->estado === 'Disponible')
                    ->form([
                        Forms\Components\Select::make('area_id')
                            ->label('Área')
                            ->relationship('area', 'nombre')
                            ->required()
                            ->live()
                            ->preload(),

                        Forms\Components\Select::make('usuario_id')
                            ->label('Usuario')
                            ->options(function (callable $get) {
                                $areaId = $get('area_id');
                                if (!$areaId) {
                                    return [];
                                }
                                return User::where('area_id', $areaId)->pluck('name', 'id');
                            })
                            ->required()
                            ->searchable(),
                    ])
                    ->action(function (Dispositivo $record, array $data): void {
                        $record->update([
                            'area_id' => $data['area_id'],
                            'usuario_id' => $data['usuario_id'],
                            'estado' => 'Asignado'
                        ]);
                    }),

                Tables\Actions\Action::make('liberar')
                    ->label('Liberar')
                    ->icon('heroicon-o-user-minus')
                    ->color('warning')
                    ->visible(fn (Dispositivo $record): bool => $record->estado === 'Asignado')
                    ->requiresConfirmation()
                    ->modalHeading('Liberar Dispositivo')
                    ->modalDescription('¿Está seguro de que desea liberar este dispositivo?')
                    ->action(function (Dispositivo $record): void {
                        // Cerrar todas las asignaciones activas del dispositivo usando el método del modelo
                        $asignacionesActivas = \App\Models\DispositivoAsignacion::where('dispositivo_id', $record->id)
                            ->whereNull('fecha_desasignacion')
                            ->get();

                        foreach ($asignacionesActivas as $asignacion) {
                            $asignacion->desasignar('Liberado manualmente desde el sistema');
                        }

                        // Actualizar el dispositivo (el método desasignar ya actualiza el dispositivo,
                        // pero lo hacemos aquí por si acaso para asegurar consistencia)
                        $record->update([
                            'usuario_id' => null,
                            'estado' => 'Disponible'
                        ]);
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),

                    Tables\Actions\BulkAction::make('cambiar_estado_bulk')
                        ->label('Cambiar Estado')
                        ->icon('heroicon-o-arrow-path')
                        ->color('primary')
                        ->form([
                            Forms\Components\Select::make('estado_bulk')
                                ->label('Nuevo Estado para Todos')
                                ->options(Dispositivo::ESTADOS)
                                ->required()
                                ->native(false),
                        ])
                        ->action(function (Collection $records, array $data): void {
                            $records->each(function (Dispositivo $record) use ($data) {
                                $record->update(['estado' => $data['estado_bulk']]);
                            });
                        })
                        ->requiresConfirmation(),
                ])
                ->label('Acciones Masivas'),
            ])
            ->emptyStateHeading('No hay dispositivos')
            ->emptyStateDescription('Cuando agregues dispositivos al inventario, aparecerán aquí.')
            ->emptyStateIcon('heroicon-o-computer-desktop')
            ->striped()
            ->defaultSort('created_at', 'desc')
            ->persistSortInSession()
            ->persistFiltersInSession()
            ->searchOnBlur()
            ->deferLoading();
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
