<?php

namespace App\Filament\Resources\ItilDashboardResource\Pages;

use App\Filament\Resources\ItilDashboardResource;
use App\Models\ItilDashboard;
use Filament\Resources\Pages\Page;
use Filament\Pages\Actions\Action;
use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;
use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;

class ItilMetrics extends Page
{
    protected static string $resource = ItilDashboardResource::class;

    protected static string $view = 'filament.resources.itil-dashboard-resource.pages.itil-metrics';

    protected static ?string $title = 'Métricas ITIL Avanzadas';

    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';

    protected function getHeaderActions(): array
    {
        return [
            Action::make('refresh')
                ->label('Actualizar Datos')
                ->icon('heroicon-m-arrow-path')
                ->action(function () {
                    $this->redirect(request()->header('Referer'));
                }),

            Action::make('export')
                ->label('Exportar Métricas')
                ->icon('heroicon-m-arrow-down-tray')
                ->color('info')
                ->form([
                    Section::make('Configuración del Reporte')
                        ->description('Selecciona el período y configuración para el reporte PDF')
                        ->schema([
                            Grid::make(2)
                                ->schema([
                                    Select::make('period_type')
                                        ->label('Tipo de Período')
                                        ->options([
                                            'custom' => 'Período personalizado',
                                            'today' => 'Hoy',
                                            'week' => 'Esta semana',
                                            'month' => 'Este mes',
                                            'quarter' => 'Este trimestre',
                                            'all' => 'Todos los tiempos',
                                        ])
                                        ->default('all')
                                        ->live()
                                        ->afterStateUpdated(function ($state, $set) {
                                            if ($state !== 'custom') {
                                                $set('start_date', null);
                                                $set('end_date', null);
                                            }
                                        }),

                                    Select::make('format')
                                        ->label('Formato')
                                        ->options([
                                            'pdf' => 'PDF',
                                            // 'excel' => 'Excel', // Para futuro
                                        ])
                                        ->default('pdf'),

                                    Select::make('include_empty')
                                        ->label('Si no hay datos')
                                        ->options([
                                            'error' => 'Mostrar error (por defecto)',
                                            'generate' => 'Generar reporte vacío',
                                        ])
                                        ->default('error')
                                        ->helperText('Elige qué hacer cuando no hay datos en el período seleccionado'),
                                ]),

                            Grid::make(2)
                                ->schema([
                                    DatePicker::make('start_date')
                                        ->label('Fecha de Inicio')
                                        ->visible(fn($get) => $get('period_type') === 'custom')
                                        ->required(fn($get) => $get('period_type') === 'custom')
                                        ->maxDate(now()),

                                    DatePicker::make('end_date')
                                        ->label('Fecha de Fin')
                                        ->visible(fn($get) => $get('period_type') === 'custom')
                                        ->required(fn($get) => $get('period_type') === 'custom')
                                        ->maxDate(now())
                                        ->after('start_date'),
                                ])
                                ->visible(fn($get) => $get('period_type') === 'custom'),
                        ]),
                ])
                ->action(function (array $data) {
                    return $this->exportToPdf($data);
                }),
        ];
    }

    public function getViewData(): array
    {
        return [
            'incident_metrics' => ItilDashboard::getIncidentMetrics('all'),
            'resolution_metrics' => ItilDashboard::getResolutionTimeMetrics(),
            'category_distribution' => ItilDashboard::getCategoryDistribution(),
            'service_availability' => ItilDashboard::getServiceAvailabilityMetrics(),
            'user_satisfaction' => ItilDashboard::getUserSatisfactionMetrics(),
            'workload_analysis' => ItilDashboard::getWorkloadAnalysis(),
            'trend_analysis' => ItilDashboard::getTrendAnalysis(30),
        ];
    }

    public function getFilteredViewData(array $filters): array
    {
        // Determinar el período basado en los filtros
        $period = $filters['period_type'];

        // Si es período personalizado, usar métodos con fechas específicas
        if ($period === 'custom' && isset($filters['start_date']) && isset($filters['end_date'])) {
            return [
                'incident_metrics' => ItilDashboard::getIncidentMetricsCustom($filters['start_date'], $filters['end_date']),
                'resolution_metrics' => ItilDashboard::getResolutionTimeMetricsCustom($filters['start_date'], $filters['end_date']),
                'category_distribution' => ItilDashboard::getCategoryDistributionCustom($filters['start_date'], $filters['end_date']),
                'service_availability' => ItilDashboard::getServiceAvailabilityMetrics(),
                'user_satisfaction' => ItilDashboard::getUserSatisfactionMetrics(),
                'workload_analysis' => ItilDashboard::getWorkloadAnalysisCustom($filters['start_date'], $filters['end_date']),
                'trend_analysis' => ItilDashboard::getTrendAnalysis(30),
            ];
        }

        // Para períodos predefinidos, usar métodos con filtro estándar
        return [
            'incident_metrics' => ItilDashboard::getIncidentMetrics($period),
            'resolution_metrics' => ItilDashboard::getResolutionTimeMetrics(),
            'category_distribution' => $this->getCategoryDistributionFiltered($period),
            'service_availability' => ItilDashboard::getServiceAvailabilityMetrics(),
            'user_satisfaction' => ItilDashboard::getUserSatisfactionMetrics(),
            'workload_analysis' => $this->getWorkloadAnalysisFiltered($period),
            'trend_analysis' => ItilDashboard::getTrendAnalysis(30),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            \App\Filament\Widgets\WorkloadStatsWidget::class,
            \App\Filament\Widgets\ItilIncidentMetricsChart::class,
            \App\Filament\Widgets\ItilSlaComplianceChart::class,
            \App\Filament\Widgets\ItilWorkloadTableWidget::class,
        ];
    }

    public function exportToPdf(array $filters = [])
    {
        try {
            // Obtener datos filtrados o datos por defecto
            $data = !empty($filters) ? $this->getFilteredViewData($filters) : $this->getViewData();

            // Validación más inteligente de datos
            $hasIncidentData = !empty($data['incident_metrics']) &&
                ($data['incident_metrics']['total_incidents'] > 0);

            $hasWorkloadData = !empty($data['workload_analysis']) &&
                count($data['workload_analysis']) > 0;

            // Si no hay datos y el usuario no quiere generar reporte vacío
            if (
                !$hasIncidentData && !$hasWorkloadData &&
                (!isset($filters['include_empty']) || $filters['include_empty'] === 'error')
            ) {
                // Obtener descripción del período para mensaje más específico
                $periodDesc = $this->getPeriodDescription($filters);
                throw new \Exception("No hay datos suficientes para el período seleccionado: {$periodDesc}. Puedes cambiar el período o generar un reporte vacío desde las opciones del formulario.");
            }

            // Si solo hay workload pero no incidentes, mostrar advertencia pero continuar
            if (!$hasIncidentData && $hasWorkloadData) {
                Notification::make()
                    ->title('Advertencia')
                    ->body('No hay incidentes nuevos en el período seleccionado. El reporte mostrará datos históricos de carga de trabajo.')
                    ->warning()
                    ->send();
            }

            // Si no hay datos pero el usuario quiere generar reporte vacío
            if (
                !$hasIncidentData && !$hasWorkloadData &&
                isset($filters['include_empty']) && $filters['include_empty'] === 'generate'
            ) {
                Notification::make()
                    ->title('Reporte sin datos')
                    ->body('Se generará un reporte indicando que no hubo actividad en el período seleccionado.')
                    ->warning()
                    ->send();
            }

            // Determinar la descripción del período
            $periodDescription = $this->getPeriodDescription($filters);

            // Agregar información adicional para el PDF
            $data['generated_at'] = now()->format('d/m/Y H:i:s');
            $data['generated_by'] = Auth::user()->name ?? 'Sistema';
            $data['period'] = $periodDescription;
            $data['has_incident_data'] = $hasIncidentData;
            $data['has_workload_data'] = $hasWorkloadData;

            // Asegurar que todas las claves necesarias existan con valores por defecto
            $data['incident_metrics'] = array_merge([
                'total_incidents' => 0,
                'resolved_incidents' => 0,
                'open_incidents' => 0,
                'escalated_incidents' => 0,
                'cancelled_incidents' => 0,
                'sla_compliance' => 0,
                'resolution_rate' => 0,
                'escalation_rate' => 0
            ], $data['incident_metrics'] ?? []);

            $data['resolution_metrics'] = array_merge([
                'mean_time_to_resolve' => 0,
                'median_time_to_resolve' => 0,
                'min_time_to_resolve' => 0,
                'max_time_to_resolve' => 0
            ], $data['resolution_metrics'] ?? []);

            $data['service_availability'] = array_merge([
                'availability_percentage' => 0
            ], $data['service_availability'] ?? []);

            $data['user_satisfaction'] = array_merge([
                'satisfaction_score' => 0,
                'total_surveys' => 0,
                'response_rate' => 0,
                'net_promoter_score' => 0
            ], $data['user_satisfaction'] ?? []);

            $pdf = Pdf::loadView('exports.itil-metrics-pdf', $data)
                ->setPaper('a4', 'portrait')
                ->setOptions([
                    'defaultFont' => 'DejaVu Sans',
                    'isHtml5ParserEnabled' => true,
                    'isPhpEnabled' => true,
                    'chroot' => public_path(),
                ]);

            $filename = 'metricas-itil-' . now()->format('Y-m-d-H-i-s') . '.pdf';

            Notification::make()
                ->title('Exportación exitosa')
                ->body('Las métricas ITIL han sido exportadas correctamente.')
                ->success()
                ->send();

            return response()->streamDownload(
                fn() => print($pdf->output()),
                $filename,
                ['Content-Type' => 'application/pdf']
            );
        } catch (\Exception $e) {
            Notification::make()
                ->title('Error en la exportación')
                ->body('No se pudo generar el PDF: ' . $e->getMessage())
                ->danger()
                ->send();

            return null;
        }
    }

    private function getPeriodDescription(array $filters): string
    {
        if (empty($filters['period_type'])) {
            return 'Todos los tiempos';
        }

        switch ($filters['period_type']) {
            case 'today':
                return 'Hoy (' . now()->format('d/m/Y') . ')';
            case 'week':
                return 'Esta semana (' . now()->startOfWeek()->format('d/m/Y') . ' - ' . now()->endOfWeek()->format('d/m/Y') . ')';
            case 'month':
                return 'Este mes (' . now()->format('M Y') . ')';
            case 'quarter':
                return 'Este trimestre (' . now()->startOfQuarter()->format('M Y') . ' - ' . now()->endOfQuarter()->format('M Y') . ')';
            case 'custom':
                if (isset($filters['start_date']) && isset($filters['end_date'])) {
                    return 'Período personalizado (' . $filters['start_date'] . ' - ' . $filters['end_date'] . ')';
                }
                return 'Período personalizado';
            case 'all':
            default:
                return 'Todos los tiempos';
        }
    }

    private function getCategoryDistributionFiltered(string $period): array
    {
        if ($period === 'all') {
            return ItilDashboard::getCategoryDistribution();
        }

        // Para períodos específicos, calcular las fechas y usar el método custom
        $dates = $this->getPeriodDates($period);
        return ItilDashboard::getCategoryDistributionCustom($dates['start'], $dates['end']);
    }

    private function getWorkloadAnalysisFiltered(string $period): array
    {
        if ($period === 'all') {
            return ItilDashboard::getWorkloadAnalysis();
        }

        // Para períodos específicos, calcular las fechas y usar el método custom
        $dates = $this->getPeriodDates($period);
        return ItilDashboard::getWorkloadAnalysisCustom($dates['start'], $dates['end']);
    }

    private function getPeriodDates(string $period): array
    {
        switch ($period) {
            case 'today':
                return [
                    'start' => now()->startOfDay(),
                    'end' => now()->endOfDay()
                ];
            case 'week':
                return [
                    'start' => now()->startOfWeek(),
                    'end' => now()->endOfWeek()
                ];
            case 'month':
                return [
                    'start' => now()->startOfMonth(),
                    'end' => now()->endOfMonth()
                ];
            case 'quarter':
                return [
                    'start' => now()->startOfQuarter(),
                    'end' => now()->endOfQuarter()
                ];
            default:
                return [
                    'start' => now()->subYears(10),
                    'end' => now()
                ];
        }
    }
}
