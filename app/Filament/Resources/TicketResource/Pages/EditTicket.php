<?php

namespace App\Filament\Resources\TicketResource\Pages;

use App\Filament\Resources\TicketResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Kirschbaum\Commentions\Filament\Actions\CommentsAction;
use Illuminate\Support\Facades\Auth;

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
        // Solo ejecutar la lógica si el campo 'asignado_a' está presente en los datos
        // (esto significa que el usuario actual es Super Admin y tiene acceso a este campo)
        if (isset($data['asignado_a'])) {
            // Obtenemos el ticket original cargado en el formulario
            $ticket = $this->getRecord();

            // Si el valor de 'asignado_a' ha cambiado
            if ($ticket->asignado_a !== $data['asignado_a']) {
                // Asignamos el usuario autenticado a 'asignado_por'
                $data['asignado_por'] = Auth::id();
            }
        }

        return $data;
    }

}
