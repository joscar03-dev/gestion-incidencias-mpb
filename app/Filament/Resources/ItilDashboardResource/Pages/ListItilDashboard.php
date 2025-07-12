<?php

namespace App\Filament\Resources\ItilDashboardResource\Pages;

use App\Filament\Resources\ItilDashboardResource;
use Filament\Resources\Pages\ListRecords;
use Filament\Pages\Actions\Action;

class ListItilDashboard extends ListRecords
{
    protected static string $resource = ItilDashboardResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('view_metrics')
                ->label('Ver Métricas Avanzadas')
                ->icon('heroicon-m-chart-bar')
                ->url(static::$resource::getUrl('metrics'))
                ->color('info'),

            Action::make('view_analytics')
                ->label('Analytics ITIL')
                ->icon('heroicon-m-chart-pie')
                ->url(static::$resource::getUrl('analytics'))
                ->color('success'),

            Action::make('service_catalog')
                ->label('Catálogo de Servicios')
                ->icon('heroicon-m-squares-2x2')
                ->url(static::$resource::getUrl('service-catalog'))
                ->color('warning'),
        ];
    }
}
