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
                ->action(function () {
                    return $this->exportToPdf();
                }),
        ];
    }

    public function getViewData(): array
    {
        return [
            'incident_metrics' => ItilDashboard::getIncidentMetrics(),
            'resolution_metrics' => ItilDashboard::getResolutionTimeMetrics(),
            'category_distribution' => ItilDashboard::getCategoryDistribution(),
            'service_availability' => ItilDashboard::getServiceAvailabilityMetrics(),
            'user_satisfaction' => ItilDashboard::getUserSatisfactionMetrics(),
            'workload_analysis' => ItilDashboard::getWorkloadAnalysis(),
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

    public function exportToPdf()
    {
        try {
            $data = $this->getViewData();

            // Validar que los datos esenciales estén disponibles
            if (empty($data['incident_metrics']) || empty($data['workload_analysis'])) {
                throw new \Exception('Datos insuficientes para generar el reporte');
            }

            // Agregar información adicional para el PDF
            $data['generated_at'] = now()->format('d/m/Y H:i:s');
            $data['generated_by'] = Auth::user()->name ?? 'Sistema';
            $data['period'] = 'Mes actual';

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
                fn () => print($pdf->output()),
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
}
