<?php

namespace App\Filament\Resources\TicketResource\Pages;

use App\Filament\Resources\TicketResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateTicket extends CreateRecord
{
    protected static string $resource = TicketResource::class;
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['asignado_por'] = auth()->id();

        return $data;
    }
}
