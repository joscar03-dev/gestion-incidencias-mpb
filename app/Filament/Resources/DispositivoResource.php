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

    protected static ?string $recordTitleAttribute = null;

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
                Forms\Components\Section::make('Información Básica del Dispositivo')
                    ->description('Datos principales del dispositivo')
                    ->icon('heroicon-o-computer-desktop')
                    ->columns(2)
                    ->schema([
                        Forms\Components\TextInput::make('nombre')
                            ->label('📱 Nombre del Dispositivo')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Ej: Laptop Dell Inspiron 15')
                            ->columnSpanFull(),

                        Forms\Components\Textarea::make('descripcion')
                            ->label('📝 Descripción')
                            ->rows(3)
                            ->placeholder('Descripción detallada del dispositivo...')
                            ->columnSpanFull(),

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
                            ->helperText('Formatos: JPG, PNG. Tamaño máximo: 2MB')
                            ->columnSpanFull(),
                    ]),

                Forms\Components\Section::make('Información del Fabricante')
                    ->description('Datos del fabricante y modelo')
                    ->icon('heroicon-o-building-office')
                    ->columns(2)
                    ->schema([
                        Forms\Components\TextInput::make('marca')
                            ->label('🏭 Marca')
                            ->maxLength(255)
                            ->placeholder('Ej: Dell, HP, Logitech'),

                        Forms\Components\TextInput::make('modelo')
                            ->label('📦 Modelo')
                            ->maxLength(255)
                            ->placeholder('Ej: Inspiron 15 3000'),

                        Forms\Components\TextInput::make('codigo_activo')
                            ->label('🏷️ Código de Activo')
                            ->maxLength(255)
                            ->unique(ignoreRecord: true)
                            ->placeholder('Ej: ACT-2024-001'),

                        Forms\Components\TextInput::make('etiqueta_inventario')
                            ->label('🏷️ Etiqueta de Inventario')
                            ->maxLength(255)
                            ->placeholder('Ej: INV-001'),
                    ]),

                Forms\Components\Section::make('Información Financiera')
                    ->description('Datos de adquisición y costo')
                    ->icon('heroicon-o-currency-dollar')
                    ->columns(2)
                    ->schema([
                        Forms\Components\DatePicker::make('fecha_compra')
                            ->label('📅 Fecha de Compra')
                            ->native(false)
                            ->displayFormat('d/m/Y')
                            ->placeholder('Seleccionar fecha'),

                        Forms\Components\TextInput::make('costo_adquisicion')
                            ->label('💰 Costo de Adquisición')
                            ->numeric()
                            ->prefix('S/.')
                            ->placeholder('0.00'),

                        Forms\Components\Select::make('moneda')
                            ->label('💱 Moneda')
                            ->options([
                                'PEN' => 'Soles (PEN)',
                                'USD' => 'Dólares (USD)',
                                'EUR' => 'Euros (EUR)',
                            ])
                            ->default('PEN')
                            ->native(false),

                        Forms\Components\TextInput::make('proveedor')
                            ->label('🏪 Proveedor')
                            ->maxLength(255)
                            ->placeholder('Ej: Oechsle, Ripley, Distribuidora XYZ'),
                    ]),

                Forms\Components\Section::make('Información de Garantía')
                    ->description('Datos de garantía y soporte')
                    ->icon('heroicon-o-shield-check')
                    ->columns(2)
                    ->schema([
                        Forms\Components\DatePicker::make('fecha_garantia')
                            ->label('🛡️ Fecha de Vencimiento de Garantía')
                            ->native(false)
                            ->displayFormat('d/m/Y')
                            ->placeholder('Seleccionar fecha'),

                        Forms\Components\TextInput::make('tipo_garantia')
                            ->label('🔧 Tipo de Garantía')
                            ->maxLength(255)
                            ->placeholder('Ej: Fabricante, Extendida, Comercial'),
                    ]),

                Forms\Components\Section::make('Ciclo de Vida del Dispositivo')
                    ->description('Información sobre instalación y vida útil')
                    ->icon('heroicon-o-calendar-days')
                    ->columns(2)
                    ->schema([
                        Forms\Components\DatePicker::make('fecha_instalacion')
                            ->label('📅 Fecha de Instalación')
                            ->native(false)
                            ->displayFormat('d/m/Y')
                            ->placeholder('Seleccionar fecha'),

                        Forms\Components\TextInput::make('vida_util_anos')
                            ->label('⏳ Vida Útil (años)')
                            ->numeric()
                            ->suffix('años')
                            ->placeholder('Ej: 5'),
                    ]),

                Forms\Components\Section::make('Especificaciones Técnicas')
                    ->description('Detalles técnicos y características')
                    ->icon('heroicon-o-cog-6-tooth')
                    ->columns(2)
                    ->schema([
                        Forms\Components\KeyValue::make('especificaciones_tecnicas')
                            ->label('🔧 Especificaciones Técnicas')
                            ->keyLabel('Característica')
                            ->valueLabel('Valor')
                            ->addActionLabel('Agregar Especificación')
                            ->columnSpanFull(),

                        Forms\Components\TextInput::make('color')
                            ->label('🎨 Color')
                            ->maxLength(255)
                            ->placeholder('Ej: Negro, Blanco, Gris'),

                        Forms\Components\Select::make('tipo_conexion')
                            ->label('🔌 Tipo de Conexión')
                            ->options([
                                'USB' => 'USB',
                                'USB-C' => 'USB-C',
                                'Bluetooth' => 'Bluetooth',
                                'WiFi' => 'WiFi',
                                'Ethernet' => 'Ethernet',
                                'HDMI' => 'HDMI',
                                'VGA' => 'VGA',
                                'Inalámbrico' => 'Inalámbrico',
                                'Cableado' => 'Cableado',
                            ])
                            ->searchable()
                            ->native(false),
                    ]),

                Forms\Components\Section::make('Ubicación y Asignación')
                    ->description('Área y usuario responsable')
                    ->icon('heroicon-o-map-pin')
                    ->columns(2)
                    ->schema([
                        Forms\Components\Select::make('area_id')
                            ->label('🏢 Área')
                            ->relationship('area', 'nombre')
                            ->live()
                            ->preload()
                            ->searchable()
                            ->placeholder('Seleccionar área'),

                        Forms\Components\Select::make('usuario_id')
                            ->label('� Usuario Asignado')
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
                            ->visible(fn(callable $get) => !empty($get('area_id')))
                            ->live(),

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
                            ->live()
                            ->afterStateUpdated(function ($state, $set, callable $get, $record) {
                                // Si cambia a Disponible y hay un registro existente
                                if ($state === 'Disponible' && $record && isset($record->id) && $record->estado === 'Asignado') {
                                    // Al cambiar a disponible, limpiar el usuario asignado
                                    $set('usuario_id', null);
                                }
                            }),
                    ]),

                Forms\Components\Section::make('Observaciones y Accesorios')
                    ->description('Notas adicionales y accesorios incluidos')
                    ->icon('heroicon-o-document-text')
                    ->columns(1)
                    ->schema([
                        Forms\Components\Textarea::make('observaciones')
                            ->label('📝 Observaciones')
                            ->rows(3)
                            ->placeholder('Observaciones generales sobre el dispositivo...'),

                        Forms\Components\Textarea::make('accesorios_incluidos')
                            ->label('📦 Accesorios Incluidos')
                            ->rows(3)
                            ->placeholder('Ej: Cable USB, Cargador, Manual, Estuche...'),
                    ]),
            ]);
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
                    ->description(function (Dispositivo $record): string {
                        $info = [];
                        if ($record->numero_serie) $info[] = "S/N: {$record->numero_serie}";
                        if ($record->marca) $info[] = "Marca: {$record->marca}";
                        if ($record->modelo) $info[] = "Modelo: {$record->modelo}";
                        return implode(' | ', $info);
                    })
                    ->wrap(),

                Tables\Columns\TextColumn::make('codigo_activo')
                    ->label('Código Activo')
                    ->searchable()
                    ->sortable()
                    ->placeholder('-')
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('categoria_dispositivo.nombre')
                    ->label('Categoría')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('primary'),

                Tables\Columns\TextColumn::make('estado')
                    ->label('Estado')
                    ->badge()
                    ->color(fn(Dispositivo $record): string => $record->estado_badge_color)
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
                    ->description(fn(Dispositivo $record): ?string =>
                    $record->usuario ? $record->usuario->email : null)
                    ->wrap(),

                Tables\Columns\TextColumn::make('fecha_compra')
                    ->label('Fecha Compra')
                    ->date('d/m/Y')
                    ->sortable()
                    ->placeholder('-')
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('costo_adquisicion')
                    ->label('Costo')
                    ->money('PEN')
                    ->sortable()
                    ->placeholder('-')
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('proveedor')
                    ->label('Proveedor')
                    ->searchable()
                    ->sortable()
                    ->placeholder('-')
                    ->limit(20)
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('fecha_garantia')
                    ->label('Garantía')
                    ->date('d/m/Y')
                    ->sortable()
                    ->placeholder('-')
                    ->color(function (Dispositivo $record): string {
                        if (!$record->fecha_garantia) return 'gray';
                        return $record->fecha_garantia->isFuture() ? 'success' : 'danger';
                    })
                    ->description(function (Dispositivo $record): ?string {
                        if (!$record->fecha_garantia) return null;
                        return $record->fecha_garantia->isFuture() ? 'Vigente' : 'Vencida';
                    })
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
                        true: fn(Builder $query) => $query->whereNotNull('usuario_id'),
                        false: fn(Builder $query) => $query->whereNull('usuario_id'),
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
                                fn(Builder $query, $date): Builder => $query->whereDate('fecha_compra', '>=', $date),
                            )
                            ->when(
                                $data['hasta'],
                                fn(Builder $query, $date): Builder => $query->whereDate('fecha_compra', '<=', $date),
                            );
                    }),

                Tables\Filters\Filter::make('disponibles')
                    ->label('Solo Disponibles')
                    ->query(fn(Builder $query): Builder => $query->where('estado', 'Disponible'))
                    ->toggle(),

                Tables\Filters\Filter::make('reparacion')
                    ->label('En Reparación')
                    ->query(fn(Builder $query): Builder => $query->where('estado', 'Reparación'))
                    ->toggle(),

                Tables\Filters\SelectFilter::make('marca')
                    ->label('Marca')
                    ->options(fn() => Dispositivo::whereNotNull('marca')->distinct()->pluck('marca', 'marca'))
                    ->searchable()
                    ->multiple(),

                Tables\Filters\SelectFilter::make('proveedor')
                    ->label('Proveedor')
                    ->options(fn() => Dispositivo::whereNotNull('proveedor')->distinct()->pluck('proveedor', 'proveedor'))
                    ->searchable()
                    ->multiple(),

                Tables\Filters\Filter::make('garantia_vigente')
                    ->label('Garantía Vigente')
                    ->query(fn(Builder $query): Builder => $query->whereNotNull('fecha_garantia')
                        ->where('fecha_garantia', '>', now()))
                    ->toggle(),

                Tables\Filters\Filter::make('garantia_vencida')
                    ->label('Garantía Vencida')
                    ->query(fn(Builder $query): Builder => $query->whereNotNull('fecha_garantia')
                        ->where('fecha_garantia', '<=', now()))
                    ->toggle(),
            ], layout: FiltersLayout::AboveContentCollapsible)
            ->filtersFormColumns(4)
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
                            // Si cambia a disponible, primero desasignar usando el nuevo método del modelo
                            if ($data['nuevo_estado'] === 'Disponible' && $record->estado === 'Asignado') {
                                // Usar el nuevo método desasignar que maneja todo correctamente
                                $record->desasignar('Liberado por cambio de estado');
                            } else {
                                // Para otros cambios de estado, actualizar normalmente
                                $record->update(['estado' => $data['nuevo_estado']]);
                            }
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
                    ->visible(fn(Dispositivo $record): bool => $record->estado === 'Disponible')
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

                        Forms\Components\Textarea::make('motivo_asignacion')
                            ->label('Motivo de Asignación')
                            ->placeholder('Indicar el motivo de la asignación')
                            ->maxLength(255)
                            ->default('Asignación desde panel administrativo'),
                    ])
                    ->action(function (Dispositivo $record, array $data): void {
                        // Actualizar el dispositivo
                        $record->update([
                            'area_id' => $data['area_id'],
                            'usuario_id' => $data['usuario_id'],
                            'estado' => 'Asignado'
                        ]);

                        // Crear registro en dispositivo_asignacions
                        \App\Models\DispositivoAsignacion::create([
                            'dispositivo_id' => $record->id,
                            'user_id' => $data['usuario_id'],
                            'fecha_asignacion' => now(),
                            'asignado_por' => auth()->id(), // ID del administrador que hace la asignación
                            'motivo_asignacion' => $data['motivo_asignacion'] ?? 'Asignación desde panel administrativo',
                        ]);

                        // Notificar al usuario asignado
                        $user = \App\Models\User::find($data['usuario_id']);
                        if ($user) {
                            \Filament\Notifications\Notification::make()
                                ->title('📱 Dispositivo asignado')
                                ->body("Se te ha asignado el dispositivo #{$record->id}: {$record->nombre}")
                                ->icon('heroicon-o-device-phone-mobile')
                                ->iconColor('success')
                                ->sendToDatabase($user)
                                ->broadcast($user);
                        }

                        // Notificar éxito al admin
                        \Filament\Notifications\Notification::make()
                            ->title('Dispositivo asignado correctamente')
                            ->body('El dispositivo ha sido asignado al usuario y se ha registrado en el historial.')
                            ->success()
                            ->send();
                    }),

                Tables\Actions\Action::make('liberar')
                    ->label('Liberar')
                    ->icon('heroicon-o-user-minus')
                    ->color('warning')
                    ->visible(fn(Dispositivo $record): bool => $record->estado === 'Asignado')
                    ->requiresConfirmation()
                    ->modalHeading('Liberar Dispositivo')
                    ->modalDescription('¿Está seguro de que desea liberar este dispositivo?')
                    ->action(function (Dispositivo $record): void {
                        // Usar el nuevo método desasignar del modelo Dispositivo que maneja todo correctamente
                        $record->desasignar('Liberado manualmente desde el sistema');
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
                                // Si cambia a disponible, primero desasignar usando el nuevo método del modelo
                                if ($data['estado_bulk'] === 'Disponible' && $record->estado === 'Asignado') {
                                    // Usar el nuevo método desasignar que maneja todo correctamente
                                    $record->desasignar('Liberado por cambio de estado masivo');
                                } else {
                                    // Para otros cambios de estado, actualizar normalmente
                                    $record->update(['estado' => $data['estado_bulk']]);
                                }
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
