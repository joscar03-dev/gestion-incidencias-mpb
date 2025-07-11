<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TicketResource\Pages;
use App\Filament\Resources\TicketResource\RelationManagers;
use App\Filament\Resources\TicketResource\RelationManagers\CategoriasRelationManager;
use App\Filament\Resources\TicketResource\RelationManagers\CommentsRelationManager;
use App\Models\Area;
use App\Models\Rol;
use App\Models\Ticket;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Form;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;
use Kirschbaum\Commentions\Filament\Actions\CommentsTableAction;
use Kirschbaum\Commentions\Filament\Infolists\Components\CommentsEntry;

class TicketResource extends Resource
{
    protected static ?string $model = Ticket::class;

    protected static ?string $navigationIcon = 'heroicon-o-ticket';

    protected static ?string $navigationGroup = '📊 Centro de Soporte';

    protected static ?int $navigationSort = 1;

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }
    public static function getNavigationBadgeColor(): string|array|null
    {
        return static::getModel()::count() > 4 ? 'danger' : 'success';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->columns(3)
            ->schema([
                // Campo creado por solo para Super Admin al crear ticket (inmutable una vez creado)
                Select::make('creado_por')
                    ->label('Creado por')
                    ->options(User::pluck('name', 'id')->toArray())
                    ->visible(fn($record) => Auth::user()?->hasRole('Super Admin') && $record === null)
                    ->required(fn($record) => Auth::user()?->hasRole('Super Admin') && $record === null)
                    ->default(fn() => Auth::id())
                    ->reactive()
                    ->afterStateUpdated(function ($state, $set) {
                        // Cuando Super Admin cambie el usuario, actualizar el área automáticamente
                        $user = User::find($state);
                        if ($user && $user->area_id) {
                            $set('area_id', $user->area_id);
                        } else {
                            $set('area_id', null);
                        }
                    }),

                // Mostrar quién creó el ticket (solo lectura durante edición)
                Placeholder::make('creado_por_info')
                    ->label('Creado por')
                    ->content(fn($record) => $record?->creadoPor?->name ?? 'N/A')
                    ->visible(fn($record) => $record !== null)
                    ->columnSpan(1),

                // Campo área oculto que se asigna automáticamente según el usuario
                Forms\Components\Hidden::make('area_id')
                    ->default(fn() => Auth::user()?->area_id),

                // Mostrar área del usuario que reportó el ticket (siempre solo lectura)
                Placeholder::make('area_usuario')
                    ->label('Área del Usuario que Reporta')
                    ->content(function ($record, $get) {
                        if ($record) {
                            // Si estamos editando un ticket, siempre mostrar el área del usuario que lo creó
                            return $record->creadoPor?->area?->nombre ?? 'Sin área asignada';
                        } else {
                            // Si estamos creando un ticket
                            if (Auth::user()?->hasRole('Super Admin')) {
                                // Para Super Admin, mostrar área del usuario seleccionado
                                $userId = $get('creado_por') ?? Auth::id();
                                $user = User::find($userId);
                                return $user?->area?->nombre ?? 'Sin área asignada';
                            } else {
                                // Para usuarios normales, mostrar su propia área
                                return Auth::user()?->area?->nombre ?? 'Sin área asignada';
                            }
                        }
                    })
                    ->columnSpan(1),

                TextInput::make('titulo')
                    ->label('Título')
                    ->required()
                    ->autofocus()
                    ->columnSpan(fn() => Auth::user()?->hasRole('Super Admin') ? 2 : 2),

                Textarea::make('descripcion')
                    ->label('Descripción')
                    ->rows(3)
                    ->columnSpan(3),

                // Sección de Prioridad y SLA
                Select::make('prioridad')
                    ->label('Prioridad')
                    ->options(self::$model::PRIORIDAD)
                    ->required()
                    ->in(array_keys(self::$model::PRIORIDAD))
                    ->default('Media')
                    ->reactive()
                    ->afterStateUpdated(function ($state, $set, $get) {
                        // Actualizar información de SLA cuando cambie la prioridad
                        $this->actualizarSlaAdmin($state, $get('tipo'), $get('area_id'), $set);
                    }),

                Placeholder::make('sla_info')
                    ->label('SLA Calculado')
                    ->content(function ($get) {
                        $prioridad = $get('prioridad');
                        $tipo = $get('tipo');
                        $areaId = $get('area_id');

                        if (!$areaId) {
                            $areaId = Auth::user()?->area_id;
                        }

                        if ($prioridad && $tipo && $areaId) {
                            $resultado = \App\Models\Sla::calcularParaTicket($areaId, $prioridad, $tipo);

                            if ($resultado['encontrado']) {
                                $horas_resp = floor($resultado['tiempo_respuesta'] / 60);
                                $min_resp = $resultado['tiempo_respuesta'] % 60;
                                $horas_resol = floor($resultado['tiempo_resolucion'] / 60);
                                $min_resol = $resultado['tiempo_resolucion'] % 60;

                                $overrideInfo = $resultado['override_aplicado'] ?
                                    " (Sistema híbrido - Factor: " . round($resultado['factor_combinado'] * 100) . "%)" :
                                    " (Tiempo fijo)";

                                return "⏱️ Respuesta: {$horas_resp}h {$min_resp}m | 🔧 Resolución: {$horas_resol}h {$min_resol}m{$overrideInfo}";
                            }
                            return 'No hay SLA configurado para esta área';
                        }
                        return 'Selecciona prioridad y tipo para ver SLA completo';
                    })
                    ->visible(fn($get) => $get('prioridad')),

                // Tipo de ticket (nuevo campo)
                Select::make('tipo')
                    ->label('Tipo de Ticket')
                    ->options(self::$model::TIPOS)
                    ->required()
                    ->in(array_keys(self::$model::TIPOS))
                    ->default('General')
                    ->reactive()
                    ->afterStateUpdated(function ($state, $set, $get) {
                        // Actualizar información de SLA cuando cambie el tipo
                        $this->actualizarSlaAdmin($get('prioridad'), $state, $get('area_id'), $set);
                    })
                    ->helperText('Selecciona el tipo que mejor describe la solicitud'),

                // Sección de Estado y Asignación
                Select::make('estado')
                    ->label('Estado')
                    ->options(self::$model::ESTADOS)
                    ->default(self::$model::ESTADOS['Abierto'])
                    ->required()
                    ->in(array_keys(self::$model::ESTADOS))
                    ->reactive(),

                Select::make('asignado_a')
                    ->label('Asignado a')
                    ->options(
                        User::role(['Tecnico'])->pluck('name', 'id')->toArray()
                    )
                    ->visible(fn() => Auth::user()?->hasRole(['Super Admin','Admin',])),

                // Sección de Escalamiento (solo visible en edición)
                Toggle::make('escalado')
                    ->label('¿Escalado?')
                    ->visible(fn($record) => $record !== null)
                    ->disabled(),

                DateTimePicker::make('fecha_escalamiento')
                    ->label('Fecha de Escalamiento')
                    ->visible(fn($record) => $record?->escalado)
                    ->disabled(),

                Toggle::make('sla_vencido')
                    ->label('SLA Vencido')
                    ->visible(fn($record) => $record !== null)
                    ->disabled(),

                // Archivos y Comentarios
                FileUpload::make('attachment')
                    ->label('Archivo')
                    ->preserveFilenames()
                    ->downloadable()
                    ->uploadingMessage('Subiendo archivo...')
                    ->directory('tickets')
                    ->acceptedFileTypes(['application/pdf', 'image/*'])
                    ->maxSize(1024)
                    ->columnSpan(2),

                Textarea::make('comentario')
                    ->label('Solución / Comentario')
                    ->rows(3)
                    ->visible(fn($get) => $get('estado') === Ticket::ESTADOS['Cerrado'])
                    ->required(fn($get) => $get('estado') === Ticket::ESTADOS['Cerrado'])
                    ->columnSpan(3),
            ])->statePath('data');
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(
                fn(Builder $query) =>
                Auth::user()->hasRole('Super Admin') ?
                    $query : $query->where('asignado_a', Auth::id())
            )
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('titulo')
                    ->description(fn(Ticket $record): ?string => $record?->descripcion ?? null)
                    ->label('Título')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('area.nombre')
                    ->label('Área')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('prioridad')
                    ->label('Prioridad')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Critica' => 'danger',
                        'Alta' => 'warning',
                        'Media' => 'success',
                        'Baja' => 'secondary',
                        default => 'secondary',
                    })
                    ->icon(fn (string $state): string => match ($state) {
                        'Critica' => 'heroicon-o-fire',
                        'Alta' => 'heroicon-o-exclamation-triangle',
                        'Media' => 'heroicon-o-information-circle',
                        'Baja' => 'heroicon-o-minus-circle',
                        default => 'heroicon-o-question-mark-circle',
                    }),

                TextColumn::make('tipo')
                    ->label('Tipo')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Incidente' => 'danger',
                        'General' => 'info',
                        'Requerimiento' => 'warning',
                        'Cambio' => 'success',
                        default => 'secondary',
                    })
                    ->icon(fn (string $state): string => match ($state) {
                        'Incidente' => 'heroicon-o-exclamation-triangle',
                        'General' => 'heroicon-o-chat-bubble-left-right',
                        'Requerimiento' => 'heroicon-o-document-text',
                        'Cambio' => 'heroicon-o-cog-6-tooth',
                        default => 'heroicon-o-question-mark-circle',
                    }),

                TextColumn::make('estado')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Abierto' => 'danger',
                        'En Progreso' => 'warning',
                        'Escalado' => 'danger',
                        'Cerrado' => 'success',
                        'Archivado' => 'secondary',
                        default => 'secondary',
                    })
                    ->icon(fn (string $state): string => match ($state) {
                        'Abierto' => 'heroicon-o-clock',
                        'En Progreso' => 'heroicon-o-cog',
                        'Escalado' => 'heroicon-o-arrow-trending-up',
                        'Cerrado' => 'heroicon-o-check-circle',
                        'Archivado' => 'heroicon-o-archive-box',
                        default => 'heroicon-o-question-mark-circle',
                    }),

                // Columna de Estado de SLA
                TextColumn::make('estado_sla')
                    ->label('SLA')
                    ->getStateUsing(function (Ticket $record) {
                        return $record->getEstadoSla();
                    })
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'ok' => 'success',
                        'advertencia' => 'warning',
                        'vencido' => 'danger',
                        'sin_sla' => 'secondary',
                        default => 'secondary',
                    })
                    ->formatStateUsing(function ($state) {
                        return match($state) {
                            'ok' => 'OK',
                            'advertencia' => 'Advertencia',
                            'vencido' => 'Vencido',
                            'sin_sla' => 'Sin SLA',
                            default => 'Desconocido'
                        };
                    })
                    ->icon(fn (string $state): string => match ($state) {
                        'ok' => 'heroicon-o-check-circle',
                        'advertencia' => 'heroicon-o-exclamation-triangle',
                        'vencido' => 'heroicon-o-x-circle',
                        'sin_sla' => 'heroicon-o-question-mark-circle',
                        default => 'heroicon-o-question-mark-circle',
                    }),

                // Tiempo restante
                TextColumn::make('tiempo_restante')
                    ->label('Tiempo Restante')
                    ->getStateUsing(function (Ticket $record) {
                        $tiempo = $record->getTiempoRestanteSla('respuesta');
                        if ($tiempo === null) return 'N/A';
                        if ($tiempo <= 0) return 'Vencido';

                        $horas = floor($tiempo / 60);
                        $minutos = $tiempo % 60;

                        if ($horas > 0) {
                            return "{$horas}h {$minutos}m";
                        }
                        return "{$minutos}m";
                    })
                    ->color(function (Ticket $record) {
                        $tiempo = $record->getTiempoRestanteSla('respuesta');
                        if ($tiempo === null) return 'secondary';
                        if ($tiempo <= 0) return 'danger';
                        if ($tiempo <= 30) return 'warning'; // Menos de 30 minutos
                        return 'success';
                    }),

                TextColumn::make('escalado')
                    ->label('Escalado')
                    ->formatStateUsing(fn (bool $state): string => $state ? 'Sí' : 'No')
                    ->badge()
                    ->color(fn (bool $state): string => $state ? 'danger' : 'success')
                    ->icon(fn (bool $state): string => $state ? 'heroicon-o-arrow-trending-up' : 'heroicon-o-minus'),

                TextColumn::make('asignadoA.name')
                    ->label('Asignado a')
                    ->searchable(),

                TextColumn::make('creadoPor.name')
                    ->label('Creado por')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Creado')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('fecha_escalamiento')
                    ->label('Fecha Escalamiento')
                    ->dateTime('d/m/Y H:i')
                    ->visible(fn($record) => $record?->escalado)
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('estado')
                    ->options(self::$model::ESTADOS)
                    ->multiple(),

                SelectFilter::make('prioridad')
                    ->options(self::$model::PRIORIDAD)
                    ->multiple(),

                SelectFilter::make('tipo')
                    ->label('Tipo de Ticket')
                    ->options(self::$model::TIPOS)
                    ->multiple(),

                SelectFilter::make('area_id')
                    ->label('Área')
                    ->relationship('area', 'nombre')
                    ->multiple(),

                SelectFilter::make('escalado')
                    ->label('Escalado')
                    ->options([
                        '1' => 'Escalado',
                        '0' => 'No Escalado',
                    ]),

                SelectFilter::make('sla_vencido')
                    ->label('SLA Vencido')
                    ->options([
                        '1' => 'SLA Vencido',
                        '0' => 'SLA Vigente',
                    ]),

                SelectFilter::make('asignado_a')
                    ->label('Asignado a')
                    ->relationship('asignadoA', 'name')
                    ->multiple(),
            ])
            ->actions([
                // Acción para escalar manualmente
                Action::make('escalar')
                    ->label('Escalar')
                    ->icon('heroicon-o-arrow-trending-up')
                    ->color('warning')
                    ->visible(fn(Ticket $record) => !$record->escalado && $record->estado !== 'Cerrado')
                    ->requiresConfirmation()
                    ->modalHeading('Escalar Ticket')
                    ->modalDescription('¿Estás seguro de que quieres escalar este ticket?')
                    ->modalSubmitActionLabel('Escalar')
                    ->modalCancelActionLabel('Cancelar')
                    ->action(function (Ticket $record) {
                        $record->escalar('Escalamiento manual');
                        Notification::make()
                            ->title('Ticket Escalado')
                            ->body("El ticket #{$record->id} ha sido escalado exitosamente.")
                            ->success()
                            ->send();
                    }),

                // Acción para verificar SLA
                Action::make('verificar_sla')
                    ->label('Verificar SLA')
                    ->icon('heroicon-o-clock')
                    ->color('info')
                    ->action(function (Ticket $record) {
                        $escalado = $record->verificarSlaYEscalamiento();

                        if ($escalado) {
                            Notification::make()
                                ->title('Ticket Escalado Automáticamente')
                                ->body("El ticket #{$record->id} ha sido escalado por vencimiento de SLA.")
                                ->warning()
                                ->send();
                        } else {
                            Notification::make()
                                ->title('SLA Verificado')
                                ->body("El ticket #{$record->id} está dentro del SLA.")
                                ->success()
                                ->send();
                        }
                    }),

                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                CommentsTableAction::make(),
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
            'view' => Pages\ViewTicket::route('/{record}'), // <-- agrega esto
        ];
    }

    /**
     * Método helper para actualizar la información de SLA en el formulario admin
     */
    protected function actualizarSlaAdmin($prioridad, $tipo, $areaId, $set)
    {
        if (!$areaId) {
            $areaId = Auth::user()?->area_id;
        }

        if ($prioridad && $tipo && $areaId) {
            $resultado = \App\Models\Sla::calcularParaTicket($areaId, $prioridad, $tipo);

            if ($resultado['encontrado']) {
                $horas_resp = floor($resultado['tiempo_respuesta'] / 60);
                $min_resp = $resultado['tiempo_respuesta'] % 60;
                $horas_resol = floor($resultado['tiempo_resolucion'] / 60);
                $min_resol = $resultado['tiempo_resolucion'] % 60;

                $overrideInfo = $resultado['override_aplicado'] ?
                    " (Sistema híbrido - Factor: " . round($resultado['factor_combinado'] * 100) . "%)" :
                    " (Tiempo fijo)";

                $set('sla_info', "⏱️ Respuesta: {$horas_resp}h {$min_resp}m | 🔧 Resolución: {$horas_resol}h {$min_resol}m{$overrideInfo}");
            } else {
                $set('sla_info', 'No hay SLA configurado para esta área');
            }
        } else {
            $set('sla_info', 'Selecciona prioridad y tipo para calcular SLA');
        }
    }
}
