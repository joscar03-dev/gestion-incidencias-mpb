<?php

namespace App\Filament\Resources\ItilDashboardResource\Pages;

use App\Filament\Resources\ItilDashboardResource;
use App\Models\ItilDashboard;
use Filament\Resources\Pages\Page;
use Filament\Pages\Actions\Action;

class ItilServiceCatalog extends Page
{
    protected static string $resource = ItilDashboardResource::class;

    protected static string $view = 'filament.resources.itil-dashboard-resource.pages.itil-service-catalog';

    protected static ?string $title = 'Catálogo de Servicios ITIL';

    protected static ?string $navigationIcon = 'heroicon-o-squares-2x2';

    protected function getHeaderActions(): array
    {
        return [
            Action::make('export_catalog')
                ->label('Exportar Catálogo')
                ->icon('heroicon-m-arrow-down-tray')
                ->color('info')
                ->action(function () {
                    // Lógica de exportación del catálogo
                }),
        ];
    }

    public function getViewData(): array
    {
        return [
            'service_categories' => ItilDashboard::ITIL_SERVICE_CATEGORIES,
            'incident_categories' => ItilDashboard::ITIL_INCIDENT_CATEGORIES,
            'service_request_categories' => ItilDashboard::ITIL_SERVICE_REQUEST_CATEGORIES,
            'change_types' => ItilDashboard::ITIL_CHANGE_TYPES,
            'service_levels' => ItilDashboard::ITIL_SERVICE_LEVELS,
            'priority_matrix' => ItilDashboard::ITIL_PRIORITY_MATRIX,
        ];
    }
}
