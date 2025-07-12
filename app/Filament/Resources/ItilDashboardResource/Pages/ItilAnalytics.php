<?php

namespace App\Filament\Resources\ItilDashboardResource\Pages;

use App\Filament\Resources\ItilDashboardResource;
use App\Models\ItilDashboard;
use Filament\Resources\Pages\Page;
use Filament\Pages\Actions\Action;

class ItilAnalytics extends Page
{
    protected static string $resource = ItilDashboardResource::class;

    protected static string $view = 'filament.resources.itil-dashboard-resource.pages.itil-analytics';

    protected static ?string $title = 'Analytics ITIL';

    protected static ?string $navigationIcon = 'heroicon-o-chart-pie';

    protected function getHeaderActions(): array
    {
        return [
            Action::make('refresh')
                ->label('Actualizar Analytics')
                ->icon('heroicon-m-arrow-path')
                ->action(function () {
                    $this->redirect(request()->header('Referer'));
                }),
        ];
    }

    public function getViewData(): array
    {
        return [
            'trend_analysis' => ItilDashboard::getTrendAnalysis(30),
            'category_distribution' => ItilDashboard::getCategoryDistribution(),
            'workload_analysis' => ItilDashboard::getWorkloadAnalysis(),
            'service_availability' => ItilDashboard::getServiceAvailabilityMetrics(),
            'user_satisfaction' => ItilDashboard::getUserSatisfactionMetrics(),
        ];
    }
}
