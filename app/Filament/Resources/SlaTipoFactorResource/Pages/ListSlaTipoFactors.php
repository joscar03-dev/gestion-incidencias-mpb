<?php

namespace App\Filament\Resources\SlaTipoFactorResource\Pages;

use App\Filament\Resources\SlaTipoFactorResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSlaTipoFactors extends ListRecords
{
    protected static string $resource = SlaTipoFactorResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
