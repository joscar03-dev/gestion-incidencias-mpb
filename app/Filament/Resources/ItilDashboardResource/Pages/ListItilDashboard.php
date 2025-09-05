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
        return [];
    }
}
