<?php

namespace App\Filament\Resources\TicketResource\Pages;

use App\Filament\Resources\TicketResource;
use App\Models\Ticket;
use App\Models\User;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateTicket extends CreateRecord
{
    protected static string $resource = TicketResource::class;
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $estadosNoDisponibles = [];
        if (empty($data['creado_por'])) {
            $data['creado_por'] = auth()->id();
        }

        // Buscar usuarios con rol 'tecnico' o 'moderador'
        $tecnicos = User::role(['Tecnico', 'Moderador'])->get();

        // Filtrar técnicos disponibles (<5 tickets no cerrados ni resueltos)
        $disponibles = $tecnicos->filter(function ($u) {
            // Usar los estados definidos en el modelo Ticket
            $estadosNoDisponibles = [
                Ticket::ESTADOS['Cerrado'],
                Ticket::ESTADOS['Archivado'],
            ];
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
        $data['asignado_por'] = null; // Asignado por el sistema

        // Asignar el ticket al técnico seleccionado
        $data['asignado_a'] = $tecnico->id;

        // Obtener el usuario creador
        $usuario = User::find($data['creado_por']);
        if ($usuario) {
            if ($usuario->area) {
            // Obtener el SLA asociado al área del usuario
            $sla = $usuario->area->slas()->first();
            if ($sla) {
                $data['tiempo_respuesta'] = $sla->tiempo_respuesta;
                $data['tiempo_solucion'] = $sla->tiempo_resolucion;
            } else {
                // Si no hay SLA, establecer tiempos por defecto
                $data['tiempo_respuesta'] = '01:00:00'; // 1 hora por defecto
                $data['tiempo_solucion'] = '02:00:00'; // 2 horas por defecto
            }
            $data['prioridad'] = $sla->nivel ?? Ticket::PRIORIDAD['Media']; // Asignar prioridad según SLA
            } else {
            // Si el usuario no tiene área, establecer tiempos y prioridad por defecto
            $data['tiempo_respuesta'] = '01:00:00'; // 1 hora por defecto en formato hh:mm:ss
            $data['tiempo_solucion'] = '02:00:00'; // 2 horas por defecto
            $data['prioridad'] = Ticket::PRIORIDAD['Media'];
            }
        }

        return $data;
    }
}
