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

    protected static ?string $navigationIcon = 'heroicon-o-clock';

    protected static ?string $navigationGroup = 'Configuración';

    protected static ?int $navigationSort = 2;

    public static function canViewAny(): bool
    {
        return auth()->user()->can('ver-sla');
    }

    public static function canView($record): bool
    {
        return auth()->user()->can('ver-sla');
    }

    public static function canCreate(): bool
    {
        return auth()->user()->can('crear-sla');
    }

    public static function canEdit($record): bool
    {
        return auth()->user()->can('editar-sla');
    }

    public static function canDelete($record): bool
    {
        return auth()->user()->can('eliminar-sla');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('area_id')
                    ->relationship('area', 'nombre')
                    ->required()
                    ->preload()
                    ->searchable()
                    ->unique(
                        table: 'slas',
                        column: 'area_id',
                        ignoreRecord: true
                    )
                    ->validationMessages([
                        'unique' => 'Ya existe un SLA para esta área. Cada área puede tener únicamente un SLA.',
                    ])
                    ->columnSpan([
                        'sm' => 1,
                        'md' => 2,
                        'lg' => 2,
                    ]),

                Forms\Components\Select::make('nivel')
                    ->options([
                        'Alto' => 'Alto',
                        'Medio' => 'Medio',
                        'Bajo' => 'Bajo',
                    ])
                    ->required()
                    ->default('Medio')
                    ->columnSpan([
                        'sm' => 1,
                        'md' => 1,
                        'lg' => 1,
                    ]),

                Forms\Components\TextInput::make('tiempo_respuesta')
                    ->label('Tiempo de Respuesta (minutos)')
                    ->required()
                    ->numeric()
                    ->default(60)
                    ->suffix('minutos')
                    ->helperText('Tiempo máximo para primera respuesta')
                    ->columnSpan([
                        'sm' => 1,
                        'md' => 1,
                        'lg' => 1,
                    ]),

                Forms\Components\TextInput::make('tiempo_resolucion')
                    ->label('Tiempo de Resolución (minutos)')
                    ->required()
                    ->numeric()
                    ->default(480)
                    ->suffix('minutos')
                    ->helperText('Tiempo máximo para resolver el ticket')
                    ->columnSpan([
                        'sm' => 1,
                        'md' => 1,
                        'lg' => 1,
                    ]),

                Forms\Components\Select::make('canal')
                    ->label('Canal')
                    ->options([
                        'Sistema' => 'Sistema',
                        'Email' => 'Email',
                        'Telefono' => 'Teléfono',
                        'Chat' => 'Chat',
                        'Presencial' => 'Presencial',
                    ])
                    ->default('Sistema')
                    ->required()
                    ->columnSpan([
                        'sm' => 1,
                        'md' => 1,
                        'lg' => 1,
                    ]),

                Forms\Components\Textarea::make('descripcion')
                    ->label('Descripción')
                    ->rows(3)
                    ->columnSpan([
                        'sm' => 1,
                        'md' => 2,
                        'lg' => 2,
                    ]),

                Forms\Components\Toggle::make('activo')
                    ->label('Activo')
                    ->default(true)
                    ->helperText('SLA activo para nuevos tickets')
                    ->columnSpan([
                        'sm' => 1,
                        'md' => 1,
                        'lg' => 1,
                    ]),

                // Campos para el sistema híbrido
                Forms\Components\Toggle::make('override_area')
                    ->label('Permitir Override por Prioridad')
                    ->default(true)
                    ->helperText('Si está ACTIVADO: Los tiempos se ajustan según la prioridad del ticket. Si está DESACTIVADO: Todos los tickets usan los mismos tiempos base.')
                    ->reactive()
                    ->columnSpan([
                        'sm' => 1,
                        'md' => 1,
                        'lg' => 1,
                    ]),

                Forms\Components\Toggle::make('escalamiento_automatico')
                    ->label('Escalamiento Automático')
                    ->default(true)
                    ->reactive()
                    ->columnSpan([
                        'sm' => 1,
                        'md' => 1,
                        'lg' => 1,
                    ]),

                Forms\Components\TextInput::make('tiempo_escalamiento')
                    ->label('Tiempo para Escalamiento (minutos)')
                    ->numeric()
                    ->default(120)
                    ->suffix('minutos')
                    ->visible(fn($get) => $get('escalamiento_automatico'))
                    ->helperText('Tiempo después del cual se escala automáticamente')
                    ->columnSpan([
                        'sm' => 1,
                        'md' => 1,
                        'lg' => 1,
                    ]),

                // Información del sistema híbrido simplificado
                Forms\Components\Placeholder::make('info_hibrido')
                    ->label('Sistema SLA Híbrido: UN SLA por Área con Factores Dinámicos')
                    ->content(function ($get) {
                        $overrideActivado = $get('override_area');

                        if ($overrideActivado) {
                            return '
                                ℹ️ **IMPORTANTE:** Cada área tiene únicamente UN SLA que se ajusta dinámicamente

                                🎯 **COMO FUNCIONA:**
                                1. Configuras UN tiempo base por área
                                2. El sistema aplica factores automáticamente según:
                                   • La prioridad del ticket (crítica, alta, media, baja)
                                   • El tipo del ticket (incidente, general, requerimiento, cambio)

                                🔴 **FACTORES POR PRIORIDAD:**
                                • CRÍTICA: 20% del tiempo base (muy urgente)
                                • ALTA: 50% del tiempo base (urgente)
                                • MEDIA: 100% del tiempo base (normal)
                                • BAJA: 150% del tiempo base (menos urgente)

                                🎯 **FACTORES POR TIPO:**
                                • INCIDENTE: 60% del tiempo (respuesta rápida)
                                • GENERAL: 80% del tiempo (consulta importante)
                                • REQUERIMIENTO: 120% del tiempo (planificación)
                                • CAMBIO: 150% del tiempo (análisis requerido)

                                ⚡ **CÁLCULO FINAL:**
                                Tiempo = Base × Factor Prioridad × Factor Tipo

                                **Ejemplo:** Incidente Crítico = 60min × 0.2 × 0.6 = 7.2min
                                **Ejemplo:** Cambio Bajo = 60min × 1.5 × 1.5 = 135min

                                ✅ **Override ACTIVADO:** Cálculo dinámico por prioridad y tipo
                            ';
                        } else {
                            return '
                                ℹ️ **IMPORTANTE:** Cada área tiene únicamente UN SLA

                                ⚠️ **Override DESACTIVADO:** Todos los tickets usarán exactamente los mismos tiempos base, sin importar la prioridad ni el tipo.

                                **Tiempo fijo para TODOS los tickets:**
                                • Respuesta: Los minutos configurados arriba
                                • Resolución: Los minutos configurados arriba

                                💡 **Sugerencia:** Active el Override para tener SLAs dinámicos según prioridad y tipo
                            ';
                        }
                    })
                    ->columnSpan([
                        'sm' => 1,
                        'md' => 2,
                        'lg' => 2,
                    ]),
            ])
            ->columns([
                'sm' => 1,
                'md' => 2,
                'lg' => 2,
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('area.nombre')
                    ->label('Área')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('nivel')
                    ->label('Nivel')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'Alto' => 'danger',
                        'Medio' => 'warning',
                        'Bajo' => 'success',
                        default => 'gray',
                    })
                    ->sortable(),

                Tables\Columns\TextColumn::make('tiempo_respuesta')
                    ->label('Tiempo Respuesta')
                    ->formatStateUsing(fn($state) => $state . ' min')
                    ->alignCenter()
                    ->sortable(),

                Tables\Columns\TextColumn::make('tiempo_resolucion')
                    ->label('Tiempo Resolución')
                    ->formatStateUsing(fn($state) => $state . ' min')
                    ->alignCenter()
                    ->sortable(),

                // Columnas de ejemplo de SLA combinado (tipo + prioridad)
                Tables\Columns\TextColumn::make('sla_incidente_critico')
                    ->label('Incidente Crítico')
                    ->getStateUsing(function ($record) {
                        if (!$record->override_area) return 'Sin override';
                        $sla = $record->calcularSlaEfectivo('critico', 'incidente');
                        $respHoras = round($sla['tiempo_respuesta'] / 60, 1);
                        $resolHoras = round($sla['tiempo_resolucion'] / 60, 1);
                        return "{$respHoras}h / {$resolHoras}h";
                    })
                    ->color('danger')
                    ->alignCenter()
                    ->description('Respuesta / Resolución'),

                Tables\Columns\TextColumn::make('sla_requerimiento_medio')
                    ->label('Requerimiento Medio')
                    ->getStateUsing(function ($record) {
                        if (!$record->override_area) return 'Sin override';
                        $sla = $record->calcularSlaEfectivo('medio', 'requerimiento');
                        $respHoras = round($sla['tiempo_respuesta'] / 60, 1);
                        $resolHoras = round($sla['tiempo_resolucion'] / 60, 1);
                        return "{$respHoras}h / {$resolHoras}h";
                    })
                    ->color('warning')
                    ->alignCenter()
                    ->description('Respuesta / Resolución'),

                Tables\Columns\TextColumn::make('sla_cambio_bajo')
                    ->label('Cambio Bajo')
                    ->getStateUsing(function ($record) {
                        if (!$record->override_area) return 'Sin override';
                        $sla = $record->calcularSlaEfectivo('bajo', 'cambio');
                        $respHoras = round($sla['tiempo_respuesta'] / 60, 1);
                        $resolHoras = round($sla['tiempo_resolucion'] / 60, 1);
                        return "{$respHoras}h / {$resolHoras}h";
                    })
                    ->color('success')
                    ->alignCenter()
                    ->description('Respuesta / Resolución'),

                Tables\Columns\IconColumn::make('activo')
                    ->label('Activo')
                    ->boolean()
                    ->alignCenter(),

                Tables\Columns\IconColumn::make('override_area')
                    ->label('Override Prioridad')
                    ->boolean()
                    ->alignCenter(),

                Tables\Columns\IconColumn::make('escalamiento_automatico')
                    ->label('Auto Escalamiento')
                    ->boolean()
                    ->alignCenter(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('nivel')
                    ->options([
                        'Alto' => 'Alto',
                        'Medio' => 'Medio',
                        'Bajo' => 'Bajo',
                    ]),

                Tables\Filters\SelectFilter::make('area_id')
                    ->label('Área')
                    ->relationship('area', 'nombre'),

                Tables\Filters\TernaryFilter::make('activo')
                    ->label('Estado')
                    ->placeholder('Todos')
                    ->trueLabel('Activos')
                    ->falseLabel('Inactivos'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),

                Tables\Actions\Action::make('ver_ejemplos')
                    ->label('Ver Ejemplos SLA')
                    ->icon('heroicon-o-calculator')
                    ->color('info')
                    ->modalHeading(fn($record) => 'Ejemplos de SLA para ' . $record->area->nombre)
                    ->modalContent(function ($record) {
                        if (!$record->override_area) {
                            return view('filament.sla.ejemplos-sin-override', ['sla' => $record]);
                        }

                        $ejemplos = [];
                        $prioridades = ['critico', 'alto', 'medio', 'bajo'];
                        $tipos = ['incidente', 'general', 'requerimiento', 'cambio'];

                        foreach ($tipos as $tipo) {
                            foreach ($prioridades as $prioridad) {
                                $sla = $record->calcularSlaEfectivo($prioridad, $tipo);
                                $ejemplos[] = [
                                    'tipo' => ucfirst($tipo),
                                    'prioridad' => ucfirst($prioridad),
                                    'respuesta_min' => $sla['tiempo_respuesta'],
                                    'respuesta_horas' => round($sla['tiempo_respuesta'] / 60, 1),
                                    'resolucion_min' => $sla['tiempo_resolucion'],
                                    'resolucion_horas' => round($sla['tiempo_resolucion'] / 60, 1),
                                    'factor_combinado' => round($sla['factor_combinado'], 3),
                                ];
                            }
                        }

                        return view('filament.sla.ejemplos-completos', [
                            'sla' => $record,
                            'ejemplos' => $ejemplos
                        ]);
                    })
                    ->modalWidth('7xl'),

                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Nuevo SLA')
                    ->icon('heroicon-o-plus'),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageSlas::route('/'),
        ];
    }
}
