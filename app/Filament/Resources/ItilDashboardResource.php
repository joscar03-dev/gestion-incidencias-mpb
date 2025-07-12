<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ItilDashboardResource\Pages;
use App\Models\ItilDashboard;
use App\Models\Ticket;
use App\Models\User;
use App\Models\Area;
use App\Models\Categoria;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ItilReportExport;
use Barryvdh\DomPDF\Facade\Pdf;

class ItilDashboardResource extends Resource
{
    protected static ?string $model = Ticket::class;

    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';

    protected static ?string $navigationLabel = 'Dashboard ITIL';

    protected static ?string $navigationGroup = '📊 ITIL Management';

    protected static ?int $navigationSort = 1;

    protected static ?string $slug = 'itil-dashboard';

    public static function getNavigationBadge(): ?string
    {
        $openIncidents = Ticket::whereIn('estado', ['Abierto', 'En Progreso'])->count();
        return $openIncidents > 0 ? (string) $openIncidents : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        $openIncidents = Ticket::whereIn('estado', ['Abierto', 'En Progreso'])->count();

        if ($openIncidents > 50) return 'danger';
        if ($openIncidents > 20) return 'warning';
        if ($openIncidents > 0) return 'info';

        return 'success';
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('ID Ticket')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('titulo')
                    ->label('Título')
                    ->searchable()
                    ->limit(30)
                    ->tooltip(function (TextColumn $column): ?string {
                        $state = $column->getState();
                        return strlen($state) > 30 ? $state : null;
                    }),

                BadgeColumn::make('tipo')
                    ->label('Tipo ITIL')
                    ->colors([
                        'danger' => 'Incidente',
                        'warning' => 'Cambio',
                        'info' => 'Requerimiento',
                        'success' => 'General',
                    ])
                    ->searchable(),

                BadgeColumn::make('prioridad')
                    ->label('Prioridad')
                    ->colors([
                        'danger' => 'Critica',
                        'warning' => 'Alta',
                        'info' => 'Media',
                        'success' => 'Baja',
                    ])
                    ->sortable(),

                BadgeColumn::make('estado')
                    ->label('Estado')
                    ->colors([
                        'info' => 'Abierto',
                        'warning' => 'En Progreso',
                        'danger' => 'Escalado',
                        'success' => 'Cerrado',
                        'secondary' => 'Cancelado',
                        'gray' => 'Archivado',
                    ])
                    ->sortable(),

                TextColumn::make('asignadoA.name')
                    ->label('Asignado a')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('area.nombre')
                    ->label('Área')
                    ->searchable()
                    ->sortable(),

                BadgeColumn::make('sla_status')
                    ->label('Estado SLA')
                    ->getStateUsing(function ($record) {
                        if ($record->sla_vencido) {
                            return 'Vencido';
                        }

                        if ($record->escalado) {
                            return 'Escalado';
                        }

                        if (in_array($record->estado, ['Cerrado', 'Archivado'])) {
                            return 'Cumplido';
                        }

                        return 'En Tiempo';
                    })
                    ->colors([
                        'danger' => 'Vencido',
                        'warning' => 'Escalado',
                        'success' => 'Cumplido',
                        'info' => 'En Tiempo',
                    ]),

                TextColumn::make('created_at')
                    ->label('Fecha Creación')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),

                TextColumn::make('tiempo_resolucion_calculado')
                    ->label('Tiempo Resolución')
                    ->getStateUsing(function ($record) {
                        if ($record->fecha_resolucion) {
                            $hours = $record->created_at->diffInHours($record->fecha_resolucion);
                            return $hours . 'h';
                        }

                        if (in_array($record->estado, ['Abierto', 'En Progreso', 'Escalado'])) {
                            $hours = $record->created_at->diffInHours(now());
                            return $hours . 'h (En curso)';
                        }

                        return 'N/A';
                    })
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('tipo')
                    ->label('Tipo ITIL')
                    ->options(Ticket::TIPOS),

                SelectFilter::make('prioridad')
                    ->options(Ticket::PRIORIDAD),

                SelectFilter::make('estado')
                    ->options(Ticket::ESTADOS),

                SelectFilter::make('asignado_a')
                    ->label('Asignado a')
                    ->relationship('asignadoA', 'name'),

                SelectFilter::make('area_id')
                    ->label('Área')
                    ->relationship('area', 'nombre'),

                SelectFilter::make('sla_vencido')
                    ->label('SLA Vencido')
                    ->options([
                        '1' => 'Sí',
                        '0' => 'No',
                    ]),

                SelectFilter::make('escalado')
                    ->label('Escalado')
                    ->options([
                        '1' => 'Sí',
                        '0' => 'No',
                    ]),
            ])
            ->actions([
                Action::make('export_detail')
                    ->label('Exportar')
                    ->icon('heroicon-m-arrow-down-tray')
                    ->color('info')
                    ->action(function ($record) {
                        return response()->streamDownload(function () use ($record) {
                            $pdf = PDF::loadView('exports.itil-ticket-detail', ['ticket' => $record]);
                            echo $pdf->output();
                        }, 'ticket-' . $record->id . '-detail.pdf');
                    }),
            ])
            ->headerActions([
                Action::make('sync_itil_priorities')
                    ->label('Sincronizar Prioridades ITIL')
                    ->icon('heroicon-m-arrow-path')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->modalHeading('Sincronizar Prioridades ITIL')
                    ->modalDescription('Esta acción actualizará las prioridades de todos los tickets basándose en sus categorías ITIL asignadas. ¿Desea continuar?')
                    ->modalSubmitActionLabel('Sincronizar')
                    ->action(function () {
                        $updated = static::syncItilPriorities();

                        Notification::make()
                            ->title('Sincronización Completada')
                            ->body("Se actualizaron {$updated} tickets con nuevas prioridades basadas en categorías ITIL.")
                            ->success()
                            ->send();
                    }),

                Action::make('export_excel')
                    ->label('Exportar Excel')
                    ->icon('heroicon-m-document-arrow-down')
                    ->color('success')
                    ->form([
                        DatePicker::make('fecha_desde')
                            ->label('Fecha Desde')
                            ->default(now()->subMonth()),
                        DatePicker::make('fecha_hasta')
                            ->label('Fecha Hasta')
                            ->default(now()),
                        Select::make('tipo')
                            ->label('Tipo')
                            ->options(Ticket::TIPOS)
                            ->placeholder('Todos los tipos'),
                        Select::make('estado')
                            ->label('Estado')
                            ->options(Ticket::ESTADOS)
                            ->placeholder('Todos los estados'),
                    ])
                    ->action(function (array $data) {
                        $filename = 'reporte-itil-' . now()->format('Y-m-d-H-i-s') . '.xlsx';

                        return Excel::download(
                            new ItilReportExport($data),
                            $filename
                        );
                    }),

                Action::make('export_pdf')
                    ->label('Exportar PDF')
                    ->icon('heroicon-m-document-text')
                    ->color('danger')
                    ->form([
                        DatePicker::make('fecha_desde')
                            ->label('Fecha Desde')
                            ->default(now()->subMonth()),
                        DatePicker::make('fecha_hasta')
                            ->label('Fecha Hasta')
                            ->default(now()),
                        Select::make('tipo_reporte')
                            ->label('Tipo de Reporte')
                            ->options([
                                'general' => 'Reporte General',
                                'sla' => 'Reporte SLA',
                                'metricas' => 'Reporte de Métricas',
                                'tendencias' => 'Análisis de Tendencias',
                            ])
                            ->default('general')
                            ->required(),
                    ])
                    ->action(function (array $data) {
                        $filename = 'reporte-itil-' . $data['tipo_reporte'] . '-' . now()->format('Y-m-d') . '.pdf';

                        return response()->streamDownload(function () use ($data) {
                            $reportData = static::generateReportData($data);
                            $pdf = PDF::loadView('exports.itil-comprehensive-report', $reportData);
                            echo $pdf->output();
                        }, $filename);
                    }),

                Action::make('view_metrics')
                    ->label('Ver Métricas ITIL')
                    ->icon('heroicon-m-chart-bar')
                    ->color('info')
                    ->url(fn () => static::getUrl('metrics')),
            ])
            ->bulkActions([
                Tables\Actions\BulkAction::make('export_selected')
                    ->label('Exportar Seleccionados')
                    ->icon('heroicon-m-arrow-down-tray')
                    ->color('info')
                    ->action(function ($records) {
                        $filename = 'tickets-seleccionados-' . now()->format('Y-m-d-H-i-s') . '.xlsx';

                        return Excel::download(
                            new ItilReportExport(['tickets' => $records]),
                            $filename
                        );
                    }),
            ])
            ->defaultSort('created_at', 'desc')
            ->striped()
            ->paginated([10, 25, 50, 100]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListItilDashboard::route('/'),
            'metrics' => Pages\ItilMetrics::route('/metrics'),
            'analytics' => Pages\ItilAnalytics::route('/analytics'),
            'service-catalog' => Pages\ItilServiceCatalog::route('/service-catalog'),
        ];
    }

    /**
     * Genera datos para reportes comprehensivos
     */
    protected static function generateReportData(array $filters): array
    {
        $query = Ticket::query();

        // Aplicar filtros
        if (isset($filters['fecha_desde'])) {
            $query->whereDate('created_at', '>=', $filters['fecha_desde']);
        }

        if (isset($filters['fecha_hasta'])) {
            $query->whereDate('created_at', '<=', $filters['fecha_hasta']);
        }

        $tickets = $query->with(['asignadoA', 'area', 'categorias'])->get();

        return [
            'tickets' => $tickets,
            'metrics' => ItilDashboard::getIncidentMetrics(),
            'resolution_metrics' => ItilDashboard::getResolutionTimeMetrics(),
            'category_distribution' => ItilDashboard::getCategoryDistribution(),
            'service_availability' => ItilDashboard::getServiceAvailabilityMetrics(),
            'user_satisfaction' => ItilDashboard::getUserSatisfactionMetrics(),
            'workload_analysis' => ItilDashboard::getWorkloadAnalysis(),
            'trend_analysis' => ItilDashboard::getTrendAnalysis(30),
            'tipo_reporte' => $filters['tipo_reporte'] ?? 'general',
            'fecha_desde' => $filters['fecha_desde'] ?? null,
            'fecha_hasta' => $filters['fecha_hasta'] ?? null,
            'generated_at' => now(),
        ];
    }

    /**
     * Sincroniza las prioridades de los tickets basándose en sus categorías ITIL
     */
    public static function syncItilPriorities(): int
    {
        $updatedCount = 0;

        // Obtener todos los tickets que tienen categorías ITIL
        $tickets = Ticket::whereHas('categorias', function($query) {
            $query->where('itil_category', true);
        })->with('categorias')->get();

        foreach ($tickets as $ticket) {
            $maxPriority = 'Baja';

            foreach ($ticket->categorias as $categoria) {
                if ($categoria->itil_category) {
                    $categoryPriority = static::mapItilPriorityToTicket($categoria->prioridad_default);

                    if (static::isPriorityHigher($categoryPriority, $maxPriority)) {
                        $maxPriority = $categoryPriority;
                    }
                }
            }

            // Solo actualizar si la prioridad cambió
            if ($ticket->prioridad !== $maxPriority) {
                $ticket->update(['prioridad' => $maxPriority]);
                $updatedCount++;
            }
        }

        return $updatedCount;
    }

    /**
     * Mapea prioridades ITIL a prioridades de ticket
     */
    private static function mapItilPriorityToTicket($itilPriority): string
    {
        return match(strtolower($itilPriority)) {
            'baja' => 'Baja',
            'media' => 'Media',
            'alta' => 'Alta',
            'critica' => 'Critica',
            default => 'Media'
        };
    }

    /**
     * Compara si una prioridad es mayor que otra
     */
    private static function isPriorityHigher($priority1, $priority2): bool
    {
        $hierarchy = ['Baja' => 1, 'Media' => 2, 'Alta' => 3, 'Critica' => 4];
        return ($hierarchy[$priority1] ?? 0) > ($hierarchy[$priority2] ?? 0);
    }

    public static function getNavigationGroup(): ?string
    {
        return '📊 ITIL Management';
    }
}
