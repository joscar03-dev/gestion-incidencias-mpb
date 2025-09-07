<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TicketSatisfactionResource\Pages;
use App\Models\TicketSatisfaction;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Filament\Forms\Components\DatePicker;
use App\Exports\TicketSatisfactionExport;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;

class TicketSatisfactionResource extends Resource
{
    protected static ?string $model = TicketSatisfaction::class;

    protected static ?string $navigationIcon = 'heroicon-o-star';

    protected static ?string $navigationLabel = 'Encuestas de Satisfacción';

    protected static ?string $modelLabel = 'Encuesta';

    protected static ?string $pluralModelLabel = 'Encuestas de Satisfacción';

    protected static ?string $navigationGroup = 'Análisis y Reportes';

    protected static ?int $navigationSort = 1;

    public static function canViewAny(): bool
    {
        return auth()->user()->can('ver-encuesta-satisfaccion');
    }

    public static function canView($record): bool
    {
        return auth()->user()->can('ver-encuesta-satisfaccion');
    }

    public static function canCreate(): bool
    {
        return auth()->user()->can('crear-encuesta-satisfaccion');
    }

    public static function canEdit($record): bool
    {
        return auth()->user()->can('editar-encuesta-satisfaccion');
    }

    public static function canDelete($record): bool
    {
        return auth()->user()->can('eliminar-encuesta-satisfaccion');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('ticket_id')
                    ->label('Ticket')
                    ->relationship('ticket', 'titulo')
                    ->searchable()
                    ->preload()
                    ->required(),

                Forms\Components\Select::make('user_id')
                    ->label('Usuario')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),

                Forms\Components\Select::make('rating')
                    ->label('Calificación')
                    ->options([
                        1 => '1 - 😡 Muy malo',
                        2 => '2 - 😞 Malo',
                        3 => '3 - 😐 Regular',
                        4 => '4 - 😊 Bueno',
                        5 => '5 - 😄 Excelente',
                    ])
                    ->required(),

                Forms\Components\Select::make('time_satisfaction')
                    ->label('Satisfacción de Tiempo')
                    ->options([
                        'muy_rapido' => '⚡ Muy rápido',
                        'adecuado' => '✅ Adecuado',
                        'regular' => '🕐 Regular',
                        'muy_lento' => '🐌 Muy lento',
                    ])
                    ->required(),

                Forms\Components\Textarea::make('comments')
                    ->label('Comentarios')
                    ->rows(3)
                    ->columnSpanFull(),

                Forms\Components\DateTimePicker::make('submitted_at')
                    ->label('Fecha de Envío')
                    ->default(now()),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('ticket.titulo')
                    ->label('📋 Ticket')
                    ->searchable()
                    ->sortable()
                    ->limit(30)
                    ->tooltip(fn($record) => $record->ticket?->titulo),

                TextColumn::make('user.name')
                    ->label('👤 Usuario')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('ticket.asignadoA.name')
                    ->label('👨‍💻 Técnico')
                    ->searchable()
                    ->sortable()
                    ->placeholder('Sin asignar'),

                BadgeColumn::make('rating')
                    ->label('⭐ Rating')
                    ->formatStateUsing(fn($state) => $state . '/5')
                    ->colors([
                        'danger' => [1, 2],
                        'warning' => 3,
                        'success' => [4, 5],
                    ])
                    ->icons([
                        'heroicon-o-face-frown' => [1, 2],
                        'heroicon-o-face-smile' => 3,
                        'heroicon-o-face-smile' => [4, 5],
                    ])
                    ->sortable(),

                BadgeColumn::make('time_satisfaction')
                    ->label('⏱️ Tiempo')
                    ->formatStateUsing(fn($state) => match ($state) {
                        'muy_rapido' => '⚡ Muy rápido',
                        'adecuado' => '✅ Adecuado',
                        'regular' => '🕐 Regular',
                        'muy_lento' => '🐌 Muy lento',
                        default => $state
                    })
                    ->colors([
                        'success' => ['muy_rapido', 'adecuado'],
                        'warning' => 'regular',
                        'danger' => 'muy_lento',
                    ])
                    ->sortable(),

                TextColumn::make('comments')
                    ->label('💬 Comentarios')
                    ->limit(50)
                    ->tooltip(fn($record) => $record->comments)
                    ->placeholder('Sin comentarios'),

                TextColumn::make('ticket.tipo')
                    ->label('🏷️ Categoría')
                    ->badge()
                    ->sortable(),

                TextColumn::make('submitted_at')
                    ->label('📅 Fecha')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->since()
                    ->tooltip(fn($record) => $record->submitted_at?->format('d/m/Y H:i:s')),

                TextColumn::make('nps_category')
                    ->label('🎯 Categoría NPS')
                    ->getStateUsing(function ($record) {
                        return match ($record->rating) {
                            4, 5 => '🟢 Promotor',
                            3 => '🟡 Neutro',
                            1, 2 => '🔴 Detractor',
                            default => 'Sin categorizar'
                        };
                    })
                    ->badge()
                    ->colors([
                        'success' => '🟢 Promotor',
                        'warning' => '🟡 Neutro',
                        'danger' => '🔴 Detractor',
                    ]),
            ])
            ->filters([
                SelectFilter::make('rating')
                    ->label('Calificación')
                    ->options([
                        1 => '1 - Muy malo',
                        2 => '2 - Malo',
                        3 => '3 - Regular',
                        4 => '4 - Bueno',
                        5 => '5 - Excelente',
                    ])
                    ->multiple(),

                SelectFilter::make('time_satisfaction')
                    ->label('Satisfacción de Tiempo')
                    ->options([
                        'muy_rapido' => 'Muy rápido',
                        'adecuado' => 'Adecuado',
                        'regular' => 'Regular',
                        'muy_lento' => 'Muy lento',
                    ])
                    ->multiple(),

                SelectFilter::make('nps_category')
                    ->label('Categoría NPS')
                    ->options([
                        'promoter' => 'Promotor (4-5)',
                        'neutral' => 'Neutro (3)',
                        'detractor' => 'Detractor (1-2)',
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->when(
                            $data['value'] === 'promoter',
                            fn(Builder $query): Builder => $query->whereIn('rating', [4, 5]),
                        )->when(
                            $data['value'] === 'neutral',
                            fn(Builder $query): Builder => $query->where('rating', 3),
                        )->when(
                            $data['value'] === 'detractor',
                            fn(Builder $query): Builder => $query->whereIn('rating', [1, 2]),
                        );
                    }),

                SelectFilter::make('ticket.asignado_a')
                    ->label('Técnico')
                    ->relationship('ticket.asignadoA', 'name')
                    ->searchable()
                    ->preload(),

                SelectFilter::make('ticket.tipo')
                    ->label('Tipo de Ticket')
                    ->options([
                        'Hardware' => 'Hardware',
                        'Software' => 'Software',
                        'Red' => 'Red',
                        'Accesos' => 'Accesos',
                        'Otros' => 'Otros',
                    ])
                    ->multiple(),

                Filter::make('fecha_rango')
                    ->form([
                        DatePicker::make('desde')
                            ->label('Desde'),
                        DatePicker::make('hasta')
                            ->label('Hasta'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['desde'],
                                fn(Builder $query, $date): Builder => $query->whereDate('submitted_at', '>=', $date),
                            )
                            ->when(
                                $data['hasta'],
                                fn(Builder $query, $date): Builder => $query->whereDate('submitted_at', '<=', $date),
                            );
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];
                        if ($data['desde'] ?? null) {
                            $indicators[] = 'Desde: ' . Carbon::parse($data['desde'])->format('d/m/Y');
                        }
                        if ($data['hasta'] ?? null) {
                            $indicators[] = 'Hasta: ' . Carbon::parse($data['hasta'])->format('d/m/Y');
                        }
                        return $indicators;
                    }),
            ])
            ->headerActions([
                Tables\Actions\Action::make('export')
                    ->label('📊 Exportar Excel')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('success')
                    ->action(function ($livewire) {
                        // Obtener los datos filtrados
                        $query = $livewire->getFilteredTableQuery();
                        $satisfactions = $query->get();

                        // Generar nombre del archivo
                        $fileName = 'encuestas-satisfaccion-' . now()->format('Y-m-d-H-i-s') . '.xlsx';

                        // Exportar usando Laravel Excel
                        return Excel::download(new TicketSatisfactionExport($satisfactions), $fileName);
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\Action::make('export_selected')
                        ->label('📊 Exportar Seleccionados')
                        ->icon('heroicon-o-arrow-down-tray')
                        ->color('success')
                        ->action(function ($records) {
                            // Generar nombre del archivo
                            $fileName = 'encuestas-seleccionadas-' . now()->format('Y-m-d-H-i-s') . '.xlsx';

                            // Exportar usando Laravel Excel
                            return Excel::download(new TicketSatisfactionExport($records), $fileName);
                        }),
                ]),
            ])
            ->defaultSort('submitted_at', 'desc')
            ->striped()
            ->paginated([10, 25, 50, 100]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageTicketSatisfactions::route('/'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('submitted_at', '>=', now()->subDays(7))->count();
    }

    public static function getNavigationBadgeColor(): string|array|null
    {
        return 'info';
    }
}
