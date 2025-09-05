<?php

namespace App\Filament\Resources\TicketSatisfactionResource\Pages;

use App\Filament\Resources\TicketSatisfactionResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageTicketSatisfactions extends ManageRecords
{
    protected static string $resource = TicketSatisfactionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
