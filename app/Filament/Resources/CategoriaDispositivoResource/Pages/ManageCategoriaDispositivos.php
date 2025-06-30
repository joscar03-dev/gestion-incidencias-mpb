<?php

namespace App\Filament\Resources\CategoriaDispositivoResource\Pages;

use App\Filament\Resources\CategoriaDispositivoResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageCategoriaDispositivos extends ManageRecords
{
    protected static string $resource = CategoriaDispositivoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
