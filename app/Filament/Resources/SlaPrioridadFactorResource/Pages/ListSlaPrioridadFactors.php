<?php

namespace App\Filament\Resources\SlaPrioridadFactorResource\Pages;

use App\Filament\Resources\SlaPrioridadFactorResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSlaPrioridadFactors extends ListRecords
{
    protected static string $resource = SlaPrioridadFactorResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
