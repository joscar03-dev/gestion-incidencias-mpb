<?php

namespace App\Filament\Resources\DispositivoResource\Pages;

use App\Filament\Resources\DispositivoResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\DB;



class EditDispositivo extends EditRecord
{
    protected static string $resource = DispositivoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
    //logica para asignar un dispositivo a un usuario
    protected function afterSave(): void
    {
        $record = $this->record;

        // Cierra cualquier asignación activa previa (sin importar usuario)
        $asignacionesActivas = \App\Models\DispositivoAsignacion::where('dispositivo_id', $record->id)
            ->whereNull('fecha_desasignacion');

        if (!empty($record->usuario_id)) {
            $asignacionesActivas = $asignacionesActivas->where(function ($query) use ($record) {
                $query->where('user_id', '!=', $record->usuario_id)
                    ->orWhereNull('user_id');
            });
        }

        foreach ($asignacionesActivas->get() as $asignacion) {
            $asignacion->fecha_desasignacion = now();
            $asignacion->save();
        }

        // Si hay usuario, crea nueva asignación si no existe activa
        if (!empty($record->usuario_id)) {
            $existeAsignacion = \App\Models\DispositivoAsignacion::where('dispositivo_id', $record->id)
                ->where('user_id', $record->usuario_id)
                ->whereNull('fecha_desasignacion')
                ->exists();

            if (!$existeAsignacion) {
                \App\Models\DispositivoAsignacion::create([
                    'dispositivo_id' => $record->id,
                    'user_id' => $record->usuario_id,
                    'fecha_asignacion' => now(),
                ]);
            }

            // Asegura que el estado sea "Asignado" si hay usuario
            if ($record->estado !== 'Asignado') {
                $record->estado = 'Asignado';
                $record->save();
            }
        } 

        $this->dispatch('refreshRelationManager', relationManager: 'asignaciones'); // Notifica al manager de asignaciones para refrescar la tabla
    }
}
