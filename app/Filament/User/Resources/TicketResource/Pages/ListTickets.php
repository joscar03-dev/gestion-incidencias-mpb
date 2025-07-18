<?php

namespace App\Filament\User\Resources\TicketResource\Pages;

use App\Filament\User\Resources\TicketResource;
use App\Filament\Resources\TicketResource\Widgets\MetricsOverviewSample;
use App\Filament\Resources\TicketResource\Widgets\TicketsTiempoResolucionChart;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTickets extends ListRecords
{
    protected static string $resource = TicketResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            // StatsOverview::class, //agregar widgets de encabezado
            MetricsOverviewSample::class,
            TicketsTiempoResolucionChart::class,
        ];
    }
}
