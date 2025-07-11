<?php

namespace App\Filament\Resources\SolicitudTransferenciaResource\Pages;

use App\Filament\Resources\SolicitudTransferenciaResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSolicitudTransferencia extends EditRecord
{
    protected static string $resource = SolicitudTransferenciaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
