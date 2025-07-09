<?php

namespace App\Filament\User\Resources\TicketResource\Pages;

use App\Filament\User\Resources\TicketResource;
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
        // Asignar el usuario autenticado como creador
        $data['creado_por'] = Auth::id();

        // Asignar el área del usuario autenticado
        $user = Auth::user();
        if ($user && $user->area_id) {
            $data['area_id'] = $user->area_id;
        }

        // Asignar un técnico disponible automáticamente
        $estadosNoDisponibles = [
            Ticket::ESTADOS['Cerrado'],
            Ticket::ESTADOS['Archivado'],
        ];

        // Buscar usuarios con rol 'tecnico' o 'moderador'
        $tecnicos = User::role(['Tecnico'])->get();

        // Filtrar técnicos disponibles (<5 tickets no cerrados ni resueltos)
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

        // Asignar el ticket al técnico seleccionado
        if ($tecnico) {
            $data['asignado_a'] = $tecnico->id;
        }

        // Asignar por el sistema (null indica asignación automática)
        $data['asignado_por'] = null;

        return $data;
    }
}
