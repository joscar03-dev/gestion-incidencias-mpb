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

    protected function resolveRecord($key): \Illuminate\Database\Eloquent\Model
    {
        return static::getResource()::resolveRecordRouteBinding($key)->load('ticket');
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        // Cargar los datos del ticket asociado
        if ($this->record->ticket) {
            $data['ticket_estado'] = $this->record->ticket->estado;
        }

        return $data;
    }
}
