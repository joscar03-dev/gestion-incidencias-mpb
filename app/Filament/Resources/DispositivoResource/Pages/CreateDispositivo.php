<?php

namespace App\Filament\Resources\DispositivoResource\Pages;

use App\Filament\Resources\DispositivoResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateDispositivo extends CreateRecord
{
    protected static string $resource = DispositivoResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if (!empty($data['usuario_id'])) {
            $data['estado'] = 'Asignado';
        }
        
        return $data;
    }
    protected function afterCreate(): void
    {
        $record = $this->record;

        if (!empty($record->usuario_id)) {
            \App\Models\DispositivoAsignacion::create([
                'dispositivo_id' => $record->id,
                'user_id' => $record->usuario_id,
                'fecha_asignacion' => now(),
            ]);
        }
    }
}
