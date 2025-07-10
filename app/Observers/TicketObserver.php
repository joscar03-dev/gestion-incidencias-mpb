<?php

namespace App\Observers;

use App\Models\Ticket;
use Filament\Notifications\Events\DatabaseNotificationsSent;
use Filament\Notifications\Notification;


class TicketObserver
{
    /**
     * Handle the Ticket "created" event.
     */
    public function created(Ticket $ticket): void
    {
        // Enviar notificación al agente asignado solo si está asignado
        $agent = $ticket->asignadoA()->first();

        if ($agent) {
            Notification::make()
                ->title('Se te ha asignado un ticket: ' . $ticket->id)
                ->sendToDatabase($agent);
            event(new DatabaseNotificationsSent($agent));
        }
    }

    /**
     * Handle the Ticket "updated" event.
     */
    public function updated(Ticket $ticket): void
    {
        // Verificar si el campo asignado_a ha cambiado
        if ($ticket->isDirty('asignado_a')) {
            $agent = $ticket->asignadoA()->first();

            // Si el ticket fue reasignado a un nuevo agente
            if ($agent) {
                Notification::make()
                    ->title('Se te ha asignado el ticket: ' . $ticket->id)
                    ->sendToDatabase($agent);

                event(new DatabaseNotificationsSent($agent));
            }
        } else {
            // Notificar al creador si el ticket fue cerrado
            if ($ticket->isDirty('estado') && $ticket->estado === Ticket::ESTADOS['Cerrado']) {
                $creador = $ticket->creadoPor()->first();

                if ($creador) {
                    Notification::make()
                        ->title('Tu ticket ha sido resuelto')
                        ->body('El ticket #' . $ticket->id . ' ha sido cerrado y marcado como resuelto.')
                        ->sendToDatabase($creador);

                    event(new DatabaseNotificationsSent($creador));
                }
            }
            // Obtener los campos que fueron modificados
            $dirtyFields = $ticket->getDirty();
            $updatedFields = array_keys($dirtyFields);

            // Opcional: traducir los nombres de los campos si es necesario
            $fieldNames = implode(', ', $updatedFields);

            $agent = $ticket->asignadoA()->first();

            Notification::make()
            ->title('El ticket ha sido actualizado: ' . $ticket->id)
            ->body('Campos actualizados: ' . $fieldNames)
            ->sendToDatabase($agent);

            event(new DatabaseNotificationsSent($agent));
        }
    }
    // public function updated(Ticket $ticket): void
    // {
    //     // Notificar al agente asignado si cambia el campo asignado_a
    //     if ($ticket->isDirty('asignado_a')) {
    //         $agent = $ticket->asignadoA()->first();

    //         Notification::make()
    //             ->title('Se te ha asignado el ticket: ' . $ticket->id)
    //             ->sendToDatabase($agent);

    //         event(new DatabaseNotificationsSent($agent));
    //     }

    //     // Notificar al creador si el ticket fue cerrado
    //     if ($ticket->isDirty('estado') && $ticket->estado === Ticket::ESTADOS['Cerrado']) {
    //         $creador = $ticket->creadoPor()->first();

    //         if ($creador) {
    //             Notification::make()
    //                 ->title('Tu ticket ha sido resuelto')
    //                 ->body('El ticket #' . $ticket->id . ' ha sido cerrado y marcado como resuelto.')
    //                 ->sendToDatabase($creador);

    //             event(new DatabaseNotificationsSent($creador));
    //         }
    //     }

    //     // Notificar al agente si hubo otros cambios
    //     if (!$ticket->isDirty('asignado_a') && !$ticket->isDirty('estado')) {
    //         $dirtyFields = $ticket->getDirty();
    //         $updatedFields = array_keys($dirtyFields);
    //         $fieldNames = implode(', ', $updatedFields);

    //         $agent = $ticket->asignadoA()->first();

    //         Notification::make()
    //             ->title('El ticket ha sido actualizado: ' . $ticket->id)
    //             ->body('Campos actualizados: ' . $fieldNames)
    //             ->sendToDatabase($agent);

    //         event(new DatabaseNotificationsSent($agent));
    //     }
    // }
    /**
     * Handle the Ticket "deleted" event.
     */
    public function deleted(Ticket $ticket): void
    {
        //
    }

    /**
     * Handle the Ticket "restored" event.
     */
    public function restored(Ticket $ticket): void
    {
        //
    }

    /**
     * Handle the Ticket "force deleted" event.
     */
    public function forceDeleted(Ticket $ticket): void
    {
        //
    }
}
