<?php

namespace App\Filament\Resources\SolicitudDispositivoResource\Pages;

use App\Filament\Resources\SolicitudDispositivoResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSolicitudDispositivo extends EditRecord
{
    protected static string $resource = SolicitudDispositivoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
