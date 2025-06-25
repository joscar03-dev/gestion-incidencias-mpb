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
        // Enviar notificación al agente asignado
        $agent = $ticket->asignadoA()->first();

        Notification::make()
            ->title('Se te a asignado un ticket: '. $ticket->id)
            ->sendToDatabase($agent);
        event(new DatabaseNotificationsSent($agent));
    }

    /**
     * Handle the Ticket "updated" event.
     */
    public function updated(Ticket $ticket): void
    {
        // Verificar si el campo asignadoA_id ha cambiado
        if ($ticket->isDirty('asignado_a')) {
            $agent = $ticket->asignadoA()->first();

            // Si el ticket fue reasignado a un nuevo agente
            Notification::make()
            ->title('Se te ha asignado el ticket: ' . $ticket->id)
            ->sendToDatabase($agent);

            event(new DatabaseNotificationsSent($agent));
        } else {
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
