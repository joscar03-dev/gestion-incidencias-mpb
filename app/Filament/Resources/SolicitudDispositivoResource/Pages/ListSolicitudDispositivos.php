<?php

namespace App\Filament\Resources\SolicitudDispositivoResource\Pages;

use App\Filament\Resources\SolicitudDispositivoResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSolicitudDispositivos extends ListRecords
{
    protected static string $resource = SolicitudDispositivoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
