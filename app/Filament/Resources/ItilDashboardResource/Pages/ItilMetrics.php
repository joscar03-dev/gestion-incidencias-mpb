<?php

namespace App\Filament\Resources\ItilDashboardResource\Pages;

use App\Filament\Resources\ItilDashboardResource;
use App\Models\ItilDashboard;
use Filament\Resources\Pages\Page;
use Filament\Pages\Actions\Action;
use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;

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
                    // Lógica de exportación
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
            \App\Filament\Widgets\ItilIncidentMetricsChart::class,
            \App\Filament\Widgets\ItilSlaComplianceChart::class,
        ];
    }
}
