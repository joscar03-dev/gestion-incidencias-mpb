<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SolicitudDispositivoResource\Pages;
use App\Filament\Resources\SolicitudDispositivoResource\RelationManagers;
use App\Models\SolicitudDispositivo;
use App\Models\User;
use App\Models\CategoriaDispositivo;
use App\Models\Dispositivo;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Support\Enums\FontWeight;
use Illuminate\Support\Facades\Auth;
use Filament\Tables\Enums\FiltersLayout;
use Illuminate\Support\Collection;
use Filament\Notifications\Notification;

class SolicitudDispositivoResource extends Resource
{
    protected static ?string $model = SolicitudDispositivo::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';

    protected static ?string $navigationLabel = 'Requerimientos de Dispositivos';

    protected static ?string $modelLabel = 'Requerimiento';

    protected static ?string $pluralModelLabel = 'Requerimientos';

    protected static ?string $navigationGroup = 'Gestión de Dispositivos';

    protected static ?int $navigationSort = 2;

    // Configuración adicional
    protected static ?string $recordTitleAttribute = null;

    protected static int $globalSearchResultsLimit = 20;

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('estado', 'Pendiente')->count();
    }

    public static function getNavigationBadgeColor(): string|array|null
    {
        return static::getModel()::where('estado', 'Pendiente')->count() > 0 ? 'warning' : 'primary';
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['justificacion', 'user.name', 'categoria_dispositivo.nombre'];
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Grid::make(['default' => 1, 'lg' => 3])
                    ->schema([
                        Forms\Components\Section::make('📋 Información del Requerimiento')
                            ->description('Detalles básicos del requerimiento de dispositivo')
                            ->schema([
                                Forms\Components\Select::make('user_id')
                                    ->label('👤 Usuario Solicitante')
                                    ->relationship('user', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->placeholder('Seleccionar usuario'),

                                Forms\Components\Select::make('categoria_dispositivo_id')
                                    ->label('🏷️ Categoría de Dispositivo')
                                    ->relationship('categoria_dispositivo', 'nombre')
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->placeholder('Seleccionar categoría'),

                                Forms\Components\Grid::make(2)
                                    ->schema([
                                        Forms\Components\Select::make('prioridad')
                                            ->label('⚡ Prioridad')
                                            ->options(SolicitudDispositivo::PRIORIDADES)
                                            ->required()
                                            ->default('Media')
                                            ->native(false),

                                        Forms\Components\Select::make('estado')
                                            ->label('📊 Estado')
                                            ->options(SolicitudDispositivo::ESTADOS)
                                            ->required()
                                            ->default('Pendiente')
                                            ->native(false),
                                    ]),
                            ])
                            ->columnSpan(['default' => 1, 'lg' => 2])
                            ->collapsible(),

                        Forms\Components\Section::make('📅 Fechas y Seguimiento')
                            ->description('Control de fechas del requerimiento')
                            ->schema([
                                Forms\Components\DateTimePicker::make('fecha_solicitud')
                                    ->label('📅 Fecha de Solicitud')
                                    ->required()
                                    ->default(now())
                                    ->displayFormat('d/m/Y H:i')
                                    ->native(false),

                                Forms\Components\DateTimePicker::make('fecha_aprobacion')
                                    ->label('✅ Fecha de Aprobación')
                                    ->readonly()
                                    ->visible(fn($record) => $record && $record->estado === 'Aprobado')
                                    ->displayFormat('d/m/Y H:i')
                                    ->native(false),

                                Forms\Components\DateTimePicker::make('fecha_rechazo')
                                    ->label('❌ Fecha de Rechazo')
                                    ->readonly()
                                    ->visible(fn($record) => $record && $record->estado === 'Rechazado')
                                    ->displayFormat('d/m/Y H:i')
                                    ->native(false),
                            ])
                            ->columnSpan(['default' => 1, 'lg' => 1])
                            ->collapsible(),
                    ]),

                Forms\Components\Section::make('📝 Detalles del Requerimiento')
                    ->description('Justificación y documentación de respaldo')
                    ->schema([
                        Forms\Components\Textarea::make('justificacion')
                            ->label('📝 Justificación')
                            ->required()
                            ->rows(4)
                            ->placeholder('Describa detalladamente el motivo del requerimiento del dispositivo...')
                            ->columnSpanFull(),

                        Forms\Components\FileUpload::make('documento_requerimiento')
                            ->label('📎 Documento de Respaldo')
                            ->disk('public')
                            ->directory('requerimientos')
                            ->acceptedFileTypes(['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'image/jpeg', 'image/png'])
                            ->maxSize(2048)
                            ->downloadable()
                            ->previewable()
                            ->uploadingMessage('Subiendo documento...')
                            ->helperText('Formatos permitidos: PDF, Word, JPG, PNG. Tamaño máximo: 2MB')
                            ->columnSpanFull(),
                    ])
                    ->collapsible(),

                Forms\Components\Section::make('🔧 Respuesta Administrativa')
                    ->description('Gestión y respuesta del administrador')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\Select::make('admin_respuesta_id')
                                    ->label('👨‍💼 Administrador que Respondió')
                                    ->relationship('aprobadoPor', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->placeholder('Sin asignar')
                                    ->disabled() // Deshabilitado porque se asigna automáticamente
                                    ->helperText('Se asigna automáticamente al Jefe de Administración'),

                                Forms\Components\Select::make('dispositivo_asignado_id')
                                    ->label('💻 Dispositivo Asignado')
                                    ->options(function () {
                                        return Dispositivo::disponiblesParaAsignacion()
                                            ->orderBy('nombre')
                                            ->pluck('nombre', 'id');
                                    })
                                    ->searchable()
                                    ->preload()
                                    ->placeholder('Sin asignar')
                                    ->helperText('Solo dispositivos disponibles sin asignaciones activas'),
                            ]),

                        Forms\Components\Grid::make(1)
                            ->schema([
                                Forms\Components\Placeholder::make('ticket_info')
                                    ->label('🎫 Información del Ticket')
                                    ->visible(fn($record) => $record && $record->ticket_id)
                                    ->content(function ($record) {
                                        if (!$record || !$record->ticket) {
                                            return 'Sin ticket asociado';
                                        }

                                        $ticket = $record->ticket;
                                        $verTicketUrl = "/admin/tickets/{$ticket->id}";

                                        return new \Illuminate\Support\HtmlString(
                                            "<div class='space-y-2'>
                                                <div><strong>Ticket #{$ticket->id}</strong></div>
                                                <div>Estado: <span class='inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800'>{$ticket->estado}</span></div>
                                                <div>Título: {$ticket->titulo}</div>
                                                <div><a href='{$verTicketUrl}' target='_blank' class='text-blue-600 hover:text-blue-800 underline'>Ver ticket completo →</a></div>
                                            </div>"
                                        );
                                    })
                                    ->columnSpanFull(),
                            ]),

                        Forms\Components\Textarea::make('observaciones_admin')
                            ->label('💬 Observaciones del Administrador')
                            ->rows(3)
                            ->placeholder('Observaciones o comentarios adicionales del administrador...')
                            ->columnSpanFull(),
                    ])
                    ->collapsible()
                    ->collapsed(),
            ])
            ->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('#')
                    ->sortable()
                    ->searchable()
                    ->size('sm')
                    ->weight(FontWeight::Bold),

                Tables\Columns\TextColumn::make('user.name')
                    ->label('Solicitante')
                    ->searchable()
                    ->sortable()
                    ->weight(FontWeight::Medium)
                    ->description(fn(SolicitudDispositivo $record): string => $record->user->email ?? '')
                    ->wrap(),

                Tables\Columns\TextColumn::make('categoria_dispositivo.nombre')
                    ->label('Categoría')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('primary')
                    ->wrap(),

                Tables\Columns\TextColumn::make('justificacion')
                    ->label('Justificación')
                    ->searchable()
                    ->limit(40)
                    ->tooltip(function (Tables\Columns\TextColumn $column): ?string {
                        $state = $column->getState();
                        return strlen($state) > 40 ? $state : null;
                    })
                    ->wrap(),

                Tables\Columns\TextColumn::make('prioridad')
                    ->label('Prioridad')
                    ->badge()
                    ->color(fn(SolicitudDispositivo $record): string => $record->prioridad_badge_color)
                    ->sortable(),

                Tables\Columns\TextColumn::make('estado')
                    ->label('Estado')
                    ->badge()
                    ->color(fn(SolicitudDispositivo $record): string => $record->estado_badge_color)
                    ->sortable(),

                Tables\Columns\IconColumn::make('documento_requerimiento')
                    ->label('Doc.')
                    ->boolean()
                    ->trueIcon('heroicon-o-document-check')
                    ->falseIcon('heroicon-o-document')
                    ->trueColor('success')
                    ->falseColor('gray')
                    ->tooltip(fn(SolicitudDispositivo $record): string =>
                    $record->documento_requerimiento ? 'Documento adjunto' : 'Sin documento'),

                Tables\Columns\TextColumn::make('fecha_solicitud')
                    ->label('Fecha')
                    ->dateTime('d/m/Y')
                    ->sortable()
                    ->since()
                    ->description(fn(SolicitudDispositivo $record): string =>
                    $record->fecha_solicitud->format('H:i')),

                Tables\Columns\TextColumn::make('aprobadoPor.name')
                    ->label('Gestionado por')
                    ->placeholder('-')
                    ->description(function (SolicitudDispositivo $record): ?string {
                        if ($record->fecha_aprobacion) {
                            return 'Aprobado: ' . $record->fecha_aprobacion->format('d/m/Y H:i');
                        }
                        if ($record->fecha_rechazo) {
                            return 'Rechazado: ' . $record->fecha_rechazo->format('d/m/Y H:i');
                        }
                        return null;
                    })
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('dispositivoAsignado.nombre')
                    ->label('Dispositivo')
                    ->placeholder('-')
                    ->badge()
                    ->color('success')
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('ticket.id')
                    ->label('Ticket')
                    ->formatStateUsing(fn($state) => $state ? "#{$state}" : '-')
                    ->url(fn(SolicitudDispositivo $record): ?string =>
                    $record->ticket_id ? "/admin/tickets/{$record->ticket_id}" : null)
                    ->color('info')
                    ->tooltip(fn(SolicitudDispositivo $record): ?string =>
                    $record->ticket ? "Estado: {$record->ticket->estado}" : null)
                    ->toggleable(isToggledHiddenByDefault: false),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('estado')
                    ->label('Estado')
                    ->options(SolicitudDispositivo::ESTADOS)
                    ->multiple()
                    ->preload(),

                Tables\Filters\SelectFilter::make('prioridad')
                    ->label('Prioridad')
                    ->options(SolicitudDispositivo::PRIORIDADES)
                    ->multiple()
                    ->preload(),

                Tables\Filters\SelectFilter::make('categoria_dispositivo_id')
                    ->label('Categoría')
                    ->relationship('categoria_dispositivo', 'nombre')
                    ->searchable()
                    ->preload()
                    ->multiple(),

                Tables\Filters\SelectFilter::make('user_id')
                    ->label('Usuario')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->preload(),

                Tables\Filters\Filter::make('fecha_solicitud')
                    ->form([
                        Forms\Components\DatePicker::make('desde')
                            ->label('Desde')
                            ->placeholder('Fecha inicio'),
                        Forms\Components\DatePicker::make('hasta')
                            ->label('Hasta')
                            ->placeholder('Fecha fin'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['desde'],
                                fn(Builder $query, $date): Builder => $query->whereDate('fecha_solicitud', '>=', $date),
                            )
                            ->when(
                                $data['hasta'],
                                fn(Builder $query, $date): Builder => $query->whereDate('fecha_solicitud', '<=', $date),
                            );
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];
                        if ($data['desde'] ?? null) {
                            $indicators['desde'] = 'Desde: ' . \Carbon\Carbon::parse($data['desde'])->format('d/m/Y');
                        }
                        if ($data['hasta'] ?? null) {
                            $indicators['hasta'] = 'Hasta: ' . \Carbon\Carbon::parse($data['hasta'])->format('d/m/Y');
                        }
                        return $indicators;
                    }),

                Tables\Filters\TernaryFilter::make('con_documento')
                    ->label('Con Documento')
                    ->placeholder('Todos')
                    ->trueLabel('Con documento')
                    ->falseLabel('Sin documento')
                    ->queries(
                        true: fn(Builder $query) => $query->whereNotNull('documento_requerimiento'),
                        false: fn(Builder $query) => $query->whereNull('documento_requerimiento'),
                    ),

                Tables\Filters\Filter::make('urgentes')
                    ->label('Solo Urgentes')
                    ->query(fn(Builder $query): Builder => $query->where('prioridad', 'Alta'))
                    ->toggle(),

                Tables\Filters\Filter::make('pendientes')
                    ->label('Solo Pendientes')
                    ->query(fn(Builder $query): Builder => $query->where('estado', 'Pendiente'))
                    ->toggle(),
            ], layout: FiltersLayout::AboveContentCollapsible)
            ->filtersFormWidth('2xl')
            ->filtersFormColumns(2)
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make()
                        ->icon('heroicon-o-eye')
                        ->color('info'),

                    Tables\Actions\EditAction::make()
                        ->icon('heroicon-o-pencil-square')
                        ->color('warning'),

                    Tables\Actions\Action::make('descargar_documento')
                        ->label('Descargar')
                        ->icon('heroicon-o-arrow-down-tray')
                        ->color('success')
                        ->visible(fn(SolicitudDispositivo $record): bool => !empty($record->documento_requerimiento))
                        ->url(fn(SolicitudDispositivo $record): string => asset('storage/' . $record->documento_requerimiento))
                        ->openUrlInNewTab(),
                ])
                    ->label('Acciones')
                    ->icon('heroicon-m-ellipsis-vertical')
                    ->size('sm')
                    ->color('gray'),

                Tables\Actions\Action::make('aprobar')
                    ->label('Aprobar')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->size('sm')
                    ->visible(fn(SolicitudDispositivo $record): bool => $record->estado === 'Pendiente')
                    ->form([
                        Forms\Components\Grid::make()
                            ->schema([
                                Forms\Components\Select::make('dispositivo_id')
                                    ->label('Dispositivo a Asignar')
                                    ->options(function () {
                                        return Dispositivo::disponiblesParaAsignacion()
                                            ->orderBy('nombre')
                                            ->pluck('nombre', 'id');
                                    })
                                    ->searchable()
                                    ->preload()
                                    ->placeholder('Seleccionar dispositivo (opcional)')
                                    ->helperText('Solo se muestran dispositivos disponibles y sin asignaciones activas')
                                    ->columnSpanFull(),

                                Forms\Components\Textarea::make('observaciones')
                                    ->label('Observaciones')
                                    ->placeholder('Agregar observaciones sobre la aprobación...')
                                    ->rows(3)
                                    ->columnSpanFull(),
                            ])
                    ])
                    ->modalHeading('Aprobar Requerimiento')
                    ->modalDescription('¿Está seguro de que desea aprobar este requerimiento?')
                    ->modalSubmitActionLabel('Aprobar')
                    ->action(function (SolicitudDispositivo $record, array $data): void {
                        $record->aprobar(
                            Auth::id(),
                            $data['observaciones'] ?? null,
                            $data['dispositivo_id'] ?? null
                        );

                        Notification::make()
                            ->title('Requerimiento Aprobado')
                            ->body('El requerimiento ha sido aprobado exitosamente.' .
                                ($record->ticket ? ' El ticket asociado ha sido cerrado automáticamente.' : ''))
                            ->success()
                            ->send();
                    }),

                Tables\Actions\Action::make('rechazar')
                    ->label('Rechazar')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->size('sm')
                    ->visible(fn(SolicitudDispositivo $record): bool => $record->estado === 'Pendiente')
                    ->form([
                        Forms\Components\Textarea::make('motivo')
                            ->label('Motivo del rechazo')
                            ->placeholder('Explique el motivo del rechazo...')
                            ->required()
                            ->rows(4),
                    ])
                    ->modalHeading('Rechazar Requerimiento')
                    ->modalDescription('¿Está seguro de que desea rechazar este requerimiento?')
                    ->modalSubmitActionLabel('Rechazar')
                    ->action(function (SolicitudDispositivo $record, array $data): void {
                        $record->rechazar(Auth::id(), $data['motivo']);

                        Notification::make()
                            ->title('Requerimiento Rechazado')
                            ->body('El requerimiento ha sido rechazado.' .
                                ($record->ticket ? ' El ticket asociado ha sido cerrado automáticamente.' : ''))
                            ->warning()
                            ->send();
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->requiresConfirmation()
                        ->modalDescription('¿Está seguro de que desea eliminar los requerimientos seleccionados?'),

                    Tables\Actions\BulkAction::make('aprobar_masivo')
                        ->label('Aprobar Seleccionados')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->form([
                            Forms\Components\Grid::make()
                                ->schema([
                                    Forms\Components\Textarea::make('observaciones_masivo')
                                        ->label('Observaciones (aplicará a todos)')
                                        ->placeholder('Observaciones generales para todos los requerimientos...')
                                        ->rows(3)
                                        ->columnSpanFull(),
                                ])
                        ])
                        ->action(function (Collection $records, array $data): void {
                            $aprobados = 0;
                            foreach ($records as $record) {
                                if ($record->estado === 'Pendiente') {
                                    $record->aprobar(
                                        Auth::id(),
                                        $data['observaciones_masivo'] ?? 'Aprobación masiva'
                                    );
                                    $aprobados++;
                                }
                            }

                            Notification::make()
                                ->title('Aprobación masiva completada')
                                ->body("Se aprobaron {$aprobados} requerimientos correctamente.")
                                ->success()
                                ->send();
                        })
                        ->requiresConfirmation()
                        ->modalHeading('Aprobación Masiva')
                        ->modalDescription('Esta acción aprobará todos los requerimientos pendientes seleccionados.')
                        ->modalSubmitActionLabel('Aprobar Todo'),

                    Tables\Actions\BulkAction::make('rechazar_masivo')
                        ->label('Rechazar Seleccionados')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->form([
                            Forms\Components\Grid::make()
                                ->schema([
                                    Forms\Components\Textarea::make('motivo_masivo')
                                        ->label('Motivo del rechazo (aplicará a todos)')
                                        ->placeholder('Motivo general para el rechazo de todos los requerimientos...')
                                        ->required()
                                        ->rows(4)
                                        ->columnSpanFull(),
                                ])
                        ])
                        ->action(function (Collection $records, array $data): void {
                            $rechazados = 0;
                            foreach ($records as $record) {
                                if ($record->estado === 'Pendiente') {
                                    $record->rechazar(Auth::id(), $data['motivo_masivo']);
                                    $rechazados++;
                                }
                            }

                            Notification::make()
                                ->title('Rechazo masivo completado')
                                ->body("Se rechazaron {$rechazados} requerimientos correctamente.")
                                ->warning()
                                ->send();
                        })
                        ->requiresConfirmation()
                        ->modalHeading('Rechazo Masivo')
                        ->modalDescription('Esta acción rechazará todos los requerimientos pendientes seleccionados.')
                        ->modalSubmitActionLabel('Rechazar Todo'),
                ])
                    ->label('Acciones Masivas'),
            ])
            ->emptyStateHeading('No hay requerimientos')
            ->emptyStateDescription('Cuando los usuarios envíen requerimientos de dispositivos, aparecerán aquí.')
            ->emptyStateIcon('heroicon-o-clipboard-document-list')
            ->striped()
            ->defaultSort('created_at', 'desc')
            ->persistSortInSession()
            ->persistFiltersInSession()
            ->persistSearchInSession()
            ->searchOnBlur()
            ->deferLoading()
            ->poll('30s');
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
            'index' => Pages\ListSolicitudDispositivos::route('/'),
            'create' => Pages\CreateSolicitudDispositivo::route('/create'),
            'edit' => Pages\EditSolicitudDispositivo::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with(['ticket', 'user', 'categoria_dispositivo', 'aprobadoPor', 'dispositivoAsignado']);
    }
}
