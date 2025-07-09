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

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

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
                        ignoreRecord: true,
                        modifyRuleUsing: function ($rule, $component, $get, $livewire) {
                            // En edición, ignorar el registro actual
                            if ($livewire instanceof \Filament\Resources\Pages\EditRecord) {
                                return $rule->ignore($livewire->getRecord()->id);
                            }
                            return $rule;
                        }
                    )
                    ->validationMessages([
                        'unique' => 'Ya existe un SLA para esta área. Cada área puede tener únicamente un SLA.',
                    ])
                    ->columnSpan(2),

                Forms\Components\Select::make('nivel')
                    ->options([
                        'Alto' => 'Alto',
                        'Medio' => 'Medio',
                        'Bajo' => 'Bajo',
                    ])
                    ->required()
                    ->default('Medio'),

                Forms\Components\TextInput::make('tiempo_respuesta')
                    ->label('Tiempo de Respuesta (minutos)')
                    ->required()
                    ->numeric()
                    ->default(60)
                    ->suffix('minutos')
                    ->helperText('Tiempo máximo para primera respuesta'),

                Forms\Components\TextInput::make('tiempo_resolucion')
                    ->label('Tiempo de Resolución (minutos)')
                    ->required()
                    ->numeric()
                    ->default(480)
                    ->suffix('minutos')
                    ->helperText('Tiempo máximo para resolver el ticket'),

                Forms\Components\Select::make('tipo_ticket')
                    ->label('Tipo de Ticket')
                    ->options([
                        'General' => 'General',
                        'Incidente' => 'Incidente',
                        'Requerimiento' => 'Requerimiento',
                        'Cambio' => 'Cambio',
                    ])
                    ->default('General')
                    ->required(),

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
                    ->required(),

                Forms\Components\Textarea::make('descripcion')
                    ->label('Descripción')
                    ->rows(3)
                    ->columnSpan(2),

                Forms\Components\Toggle::make('activo')
                    ->label('Activo')
                    ->default(true)
                    ->helperText('SLA activo para nuevos tickets'),

                // Campos para el sistema híbrido
                Forms\Components\Toggle::make('override_area')
                    ->label('Permitir Override por Prioridad')
                    ->default(true)
                    ->helperText('Si está ACTIVADO: Los tiempos se ajustan según la prioridad del ticket. Si está DESACTIVADO: Todos los tickets usan los mismos tiempos base.')
                    ->reactive(),

                Forms\Components\Toggle::make('escalamiento_automatico')
                    ->label('Escalamiento Automático')
                    ->default(true)
                    ->reactive(),

                Forms\Components\TextInput::make('tiempo_escalamiento')
                    ->label('Tiempo para Escalamiento (minutos)')
                    ->numeric()
                    ->default(120)
                    ->suffix('minutos')
                    ->visible(fn($get) => $get('escalamiento_automatico'))
                    ->helperText('Tiempo después del cual se escala automáticamente'),

                // Información del sistema híbrido
                Forms\Components\Placeholder::make('info_hibrido')
                    ->label('Sistema de Prioridades')
                    ->content(function ($get) {
                        $overrideActivado = $get('override_area');

                        if ($overrideActivado) {
                            return '
                                ℹ️ IMPORTANTE: Cada área puede tener únicamente UN SLA

                                🔴 CRÍTICA: 20% del tiempo base
                                🟠 ALTA: 50% del tiempo base
                                🟡 MEDIA: 100% del tiempo base
                                🟢 BAJA: 150% del tiempo base

                                ✅ Override ACTIVADO: Los tiempos se ajustan según la prioridad del ticket.
                            ';
                        } else {
                            return '
                                ℹ️ IMPORTANTE: Cada área puede tener únicamente UN SLA

                                ⚠️ Override DESACTIVADO: Todos los tickets usarán los mismos tiempos base, sin importar la prioridad.

                                Tiempo fijo para todos los tickets:
                                • Respuesta: Los minutos configurados arriba
                                • Resolución: Los minutos configurados arriba
                            ';
                        }
                    })
                    ->columnSpan(2),
            ])
            ->columns(2);
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

                Tables\Columns\BadgeColumn::make('nivel')
                    ->label('Nivel')
                    ->colors([
                        'danger' => 'Alto',
                        'warning' => 'Medio',
                        'success' => 'Bajo',
                    ])
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

                // Columnas de ejemplo de SLA por prioridad
                Tables\Columns\TextColumn::make('sla_critica')
                    ->label('SLA Crítica')
                    ->getStateUsing(function ($record) {
                        $respuesta = (int)($record->tiempo_respuesta * 0.2);
                        $resolucion = (int)($record->tiempo_resolucion * 0.2);
                        return "{$respuesta}m / {$resolucion}m";
                    })
                    ->color('danger')
                    ->alignCenter(),

                Tables\Columns\TextColumn::make('sla_alta')
                    ->label('SLA Alta')
                    ->getStateUsing(function ($record) {
                        $respuesta = (int)($record->tiempo_respuesta * 0.5);
                        $resolucion = (int)($record->tiempo_resolucion * 0.5);
                        return "{$respuesta}m / {$resolucion}m";
                    })
                    ->color('warning')
                    ->alignCenter(),

                Tables\Columns\TextColumn::make('tipo_ticket')
                    ->label('Tipo')
                    ->badge(),

                Tables\Columns\ToggleColumn::make('activo')
                    ->label('Activo')
                    ->alignCenter(),

                Tables\Columns\ToggleColumn::make('override_area')
                    ->label('Override Prioridad')
                    ->alignCenter(),

                Tables\Columns\ToggleColumn::make('escalamiento_automatico')
                    ->label('Auto Escalamiento')
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
