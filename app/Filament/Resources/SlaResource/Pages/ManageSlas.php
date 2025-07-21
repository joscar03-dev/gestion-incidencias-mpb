<?php

namespace App\Filament\Resources\SlaResource\Pages;

use App\Filament\Resources\SlaResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageSlas extends ManageRecords
{
    protected static string $resource = SlaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\CreateAction::make(),
        ];
    }
}
