<?php

namespace App\Filament\User\Resources\TicketResource\Pages;

use App\Filament\User\Resources\TicketResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Kirschbaum\Commentions\Filament\Actions\CommentsAction;

class EditTicket extends EditRecord
{
    protected static string $resource = TicketResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CommentsAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Obtenemos el ticket original cargado en el formulario
        $ticket = $this->getRecord();

        // Si el valor de 'asignado_a' ha cambiado
        if ($ticket->asignado_a !== $data['asignado_a']) {
            // Asignamos el usuario autenticado a 'asignado_por'
            $data['asignado_por'] = auth()->id();
        }

        return $data;
    }

}
