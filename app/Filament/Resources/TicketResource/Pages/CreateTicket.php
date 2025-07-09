<?php

namespace App\Filament\Resources\TicketResource\Pages;

use App\Filament\Resources\TicketResource;
use App\Models\Ticket;
use App\Models\User;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreateTicket extends CreateRecord
{
    protected static string $resource = TicketResource::class;
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Establecer usuario creador
        if (empty($data['creado_por'])) {
            $data['creado_por'] = Auth::id();
        }

        // Establecer área automáticamente basada en el usuario
        $usuario = User::find($data['creado_por']);
        if ($usuario && $usuario->area_id) {
            $data['area_id'] = $usuario->area_id;
        } elseif (empty($data['area_id'])) {
            // Si el usuario no tiene área y no se especificó, usar área por defecto
            $data['area_id'] = Auth::user()?->area_id;
        }

        // Buscar usuarios con rol 'Técnico'
        $tecnicos = User::role(['Técnico'])->get();

        // Filtrar técnicos disponibles (<5 tickets no cerrados ni resueltos)
        $estadosNoDisponibles = [
            Ticket::ESTADOS['Cerrado'],
            Ticket::ESTADOS['Archivado'],
        ];

        $disponibles = $tecnicos->filter(function ($u) use ($estadosNoDisponibles) {
            return $u->ticketsAsignados()
                ->whereNotIn('estado', $estadosNoDisponibles)
                ->count() < 5;
        });

        if ($disponibles->isEmpty()) {
            // Si no hay técnicos disponibles, seleccionar aleatoriamente cualquier técnico
            $tecnico = $tecnicos->random();
        } else {
            // Seleccionar el técnico con menor número de tickets no cerrados ni archivados
            $tecnico = $disponibles->sortBy(function ($u) use ($estadosNoDisponibles) {
                return $u->ticketsAsignados()
                    ->whereNotIn('estado', $estadosNoDisponibles)
                    ->count();
            })->first();
        }

        // Solo asignar automáticamente si no es Super Admin
        if (!Auth::user()?->hasRole('Super Admin') && $tecnico) {
            $data['asignado_a'] = $tecnico->id;
        }

        return $data;
    }
}
