<?php

namespace App\Filament\User\Resources;

use App\Filament\Resources\TicketResource\RelationManagers\CategoriasRelationManager;
use App\Filament\User\Resources\TicketResource\Pages;
use App\Filament\User\Resources\TicketResource\RelationManagers;
use App\Models\Area;
use App\Models\Ticket;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\SelectColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;
use Kirschbaum\Commentions\Filament\Actions\CommentsTableAction;

class TicketResource extends Resource
{
    protected static ?string $model = Ticket::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

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
                // Mostrar área del usuario (solo lectura)
                Placeholder::make('area_usuario')
                    ->label('Área')
                    ->content(fn() => Auth::user()?->area?->nombre ?? 'Sin área asignada')
                    ->columnSpan(1),

                TextInput::make('titulo')
                    ->label('Título')
                    ->required()
                    ->autofocus()
                    ->columnSpan(2),

                Textarea::make('descripcion')
                    ->label('Descripción')
                    ->rows(3)
                    ->columnSpan(3),

                // Prioridad (habilitada para usuarios)
                Select::make('prioridad')
                    ->label('Prioridad')
                    ->options(self::$model::PRIORIDAD)
                    ->required()
                    ->in(array_keys(self::$model::PRIORIDAD))
                    ->default('Media')
                    ->reactive()
                    ->afterStateUpdated(function ($state, $set, $get) {
                        $this->actualizarSlaInfo($state, $get('tipo'), $set);
                    }),

                // Tipo de ticket (nuevo campo)
                Select::make('tipo')
                    ->label('Tipo de Ticket')
                    ->options(self::$model::TIPOS)
                    ->required()
                    ->in(array_keys(self::$model::TIPOS))
                    ->default('General')
                    ->reactive()
                    ->afterStateUpdated(function ($state, $set, $get) {
                        $this->actualizarSlaInfo($get('prioridad'), $state, $set);
                    })
                    ->helperText('Selecciona el tipo que mejor describe tu solicitud'),

                Placeholder::make('sla_info')
                    ->label('SLA Calculado')
                    ->content(function ($get) {
                        $prioridad = $get('prioridad');
                        $tipo = $get('tipo');
                        $areaId = Auth::user()?->area_id;

                        if ($prioridad && $tipo && $areaId) {
                            $resultado = \App\Models\Sla::calcularParaTicket($areaId, $prioridad, $tipo);

                            if ($resultado['encontrado']) {
                                $horas_resp = floor($resultado['tiempo_respuesta'] / 60);
                                $min_resp = $resultado['tiempo_respuesta'] % 60;
                                $horas_resol = floor($resultado['tiempo_resolucion'] / 60);
                                $min_resol = $resultado['tiempo_resolucion'] % 60;

                                return "⏱️ Respuesta: {$horas_resp}h {$min_resp}m | 🔧 Resolución: {$horas_resol}h {$min_resol}m";
                            }
                        }

                        return 'Selecciona prioridad y tipo para ver el SLA';
                    })
                    ->columnSpan(2),

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
                fn (Builder $query) => $query->where('creado_por', Auth::id())
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

                SelectColumn::make('estado')
                    ->options(self::$model::ESTADOS)
                    ->label('Estado')
                    ->disabled(fn(Ticket $record) => $record->estado === 'Cerrado'),

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

                TextColumn::make('asignadoA.name')
                    ->label('Asignado a')
                    ->searchable()
                    ->placeholder('Sin asignar'),

                TextColumn::make('created_at')
                    ->label('Creado')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('escalado')
                    ->label('Escalado')
                    ->formatStateUsing(fn (bool $state): string => $state ? 'Sí' : 'No')
                    ->badge()
                    ->color(fn (bool $state): string => $state ? 'danger' : 'success')
                    ->icon(fn (bool $state): string => $state ? 'heroicon-o-arrow-trending-up' : 'heroicon-o-minus')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('estado')
                    ->options(self::$model::ESTADOS)
                    ->label('Estado')
                    ->placeholder('Filtro por estado')
                    ->multiple(),

                SelectFilter::make('prioridad')
                    ->options(self::$model::PRIORIDAD)
                    ->label('Prioridad')
                    ->placeholder('Filtro por prioridad')
                    ->multiple(),

                SelectFilter::make('tipo')
                    ->options(self::$model::TIPOS)
                    ->label('Tipo de Ticket')
                    ->placeholder('Filtro por tipo')
                    ->multiple(),

                SelectFilter::make('area_id')
                    ->label('Área')
                    ->relationship('area', 'nombre')
                    ->placeholder('Filtro por área')
                    ->multiple(),

                SelectFilter::make('escalado')
                    ->label('Escalado')
                    ->options([
                        '1' => 'Escalado',
                        '0' => 'No Escalado',
                    ])
                    ->placeholder('Filtro por escalamiento'),

                SelectFilter::make('sla_vencido')
                    ->label('SLA Vencido')
                    ->options([
                        '1' => 'SLA Vencido',
                        '0' => 'SLA Vigente',
                    ])
                    ->placeholder('Filtro por SLA'),

                SelectFilter::make('asignado_a')
                    ->label('Asignado a')
                    ->relationship('asignadoA', 'name')
                    ->placeholder('Filtro por asignado')
                    ->multiple(),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),
                ]),
                CommentsTableAction::make()
                    ->mentionables(User::all())
                    ->label('Comentarios')
                    ->icon('heroicon-o-chat-bubble-left-right'),
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

    /**
     * Método helper para actualizar la información de SLA en el formulario usuario
     */
    protected function actualizarSlaInfo($prioridad, $tipo, $set)
    {
        $areaId = Auth::user()?->area_id;

        if ($prioridad && $tipo && $areaId) {
            $resultado = \App\Models\Sla::calcularParaTicket($areaId, $prioridad, $tipo);

            if ($resultado['encontrado']) {
                $horas_resp = floor($resultado['tiempo_respuesta'] / 60);
                $min_resp = $resultado['tiempo_respuesta'] % 60;
                $horas_resol = floor($resultado['tiempo_resolucion'] / 60);
                $min_resol = $resultado['tiempo_resolucion'] % 60;

                $set('sla_info', "⏱️ Respuesta: {$horas_resp}h {$min_resp}m | 🔧 Resolución: {$horas_resol}h {$min_resol}m");
            } else {
                $set('sla_info', 'No hay SLA configurado para su área');
            }
        } else {
            $set('sla_info', 'Selecciona prioridad y tipo para ver el SLA');
        }
    }
}
