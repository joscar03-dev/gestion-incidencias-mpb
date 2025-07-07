<?php

namespace App\Filament\User\Resources\DispositivoResource\Pages;

use App\Filament\User\Resources\DispositivoResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageDispositivos extends ManageRecords
{
    protected static string $resource = DispositivoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
