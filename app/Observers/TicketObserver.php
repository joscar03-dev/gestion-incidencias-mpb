<?php

namespace App\Observers;

use App\Models\Ticket;
use App\Models\User;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Log;

class TicketObserver
{
    /**
     * Handle the Ticket "created" event.
     */
    public function created(Ticket $ticket): void
    {
        try {
            // 1. Notificar al técnico asignado si está asignado
            if ($ticket->asignado_a) {
                $agent = $ticket->asignadoA;
                
                if ($agent) {
                    $this->sendNotificationToUser(
                        $agent,
                        '🎫 Nuevo ticket asignado',
                        "Se te ha asignado el ticket #{$ticket->id}: {$ticket->titulo}",
                        'heroicon-o-ticket',
                        'success'
                    );
                    
                    Log::info("Notificación de ticket creado enviada", [
                        'ticket_id' => $ticket->id,
                        'agent_id' => $agent->id,
                        'agent_name' => $agent->name
                    ]);
                }
            }

            // 2. Notificar a todos los administradores
            $this->notificarAdministradores($ticket, 'creado');

        } catch (\Exception $e) {
            Log::error("Error enviando notificación en ticket creado", [
                'ticket_id' => $ticket->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Handle the Ticket "updated" event.
     */
    public function updated(Ticket $ticket): void
    {
        try {
            // 1. Verificar reasignación de técnico
            if ($ticket->isDirty('asignado_a') && $ticket->asignado_a) {
                $agent = $ticket->asignadoA;

                if ($agent) {
                    $this->sendNotificationToUser(
                        $agent,
                        '🔄 Ticket reasignado',
                        "Se te ha reasignado el ticket #{$ticket->id}: {$ticket->titulo}",
                        'heroicon-o-arrow-path',
                        'warning'
                    );
                    
                    Log::info("Notificación de reasignación enviada", [
                        'ticket_id' => $ticket->id,
                        'agent_id' => $agent->id,
                        'agent_name' => $agent->name
                    ]);

                    // Notificar a administradores sobre la reasignación
                    $this->notificarAdministradores($ticket, 'reasignado', $agent->name);
                }
            }

            // 2. Verificar cierre del ticket
            if ($ticket->isDirty('estado') && $ticket->estado === Ticket::ESTADOS['Cerrado']) {
                $creador = $ticket->creadoPor;

                if ($creador) {
                    $this->sendNotificationToUser(
                        $creador,
                        '✅ Ticket resuelto',
                        "Tu ticket #{$ticket->id} ha sido cerrado y marcado como resuelto.",
                        'heroicon-o-check-circle',
                        'success'
                    );
                    
                    Log::info("Notificación de cierre enviada", [
                        'ticket_id' => $ticket->id,
                        'creator_id' => $creador->id,
                        'creator_name' => $creador->name
                    ]);
                }
            }

            // 3. Verificar escalado del ticket
            if ($ticket->isDirty('escalado') && $ticket->escalado) {
                $this->notificarEscalado($ticket);
            }

            // 4. Verificar cambios de prioridad importantes
            if ($ticket->isDirty('prioridad')) {
                $prioridadAnterior = $ticket->getOriginal('prioridad');
                $nuevaPrioridad = $ticket->prioridad;
                
                // Solo notificar si aumentó a crítica
                if ($nuevaPrioridad === 'Critica' && $prioridadAnterior !== 'Critica') {
                    $this->notificarCambioPrioridad($ticket, $prioridadAnterior, $nuevaPrioridad);
                }
            }

            // 5. Verificar cambios de estado importantes (excepto cerrado que ya se maneja)
            if ($ticket->isDirty('estado')) {
                $estadoAnterior = $ticket->getOriginal('estado');
                $nuevoEstado = $ticket->estado;

                if (in_array($nuevoEstado, ['En Progreso', 'Escalado', 'Cancelado']) && $estadoAnterior !== $nuevoEstado) {
                    $this->notificarCambioEstado($ticket, $estadoAnterior, $nuevoEstado);
                }
            }

        } catch (\Exception $e) {
            Log::error("Error enviando notificación en ticket actualizado", [
                'ticket_id' => $ticket->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Notificar escalado de ticket
     */
    private function notificarEscalado(Ticket $ticket): void
    {
        try {
            // Notificar al técnico asignado
            if ($ticket->asignadoA) {
                $this->sendNotificationToUser(
                    $ticket->asignadoA,
                    '🚨 Tu ticket fue escalado',
                    "El ticket #{$ticket->id} ha sido escalado automáticamente por SLA vencido. Revisa inmediatamente.",
                    'heroicon-o-exclamation-triangle',
                    'danger',
                    true // persistente
                );
            }

            // Notificar a administradores
            $this->notificarAdministradores($ticket, 'escalado');

            Log::info("Notificaciones de escalado enviadas", [
                'ticket_id' => $ticket->id,
                'tecnico_asignado' => $ticket->asignadoA->name ?? 'Sin asignar'
            ]);

        } catch (\Exception $e) {
            Log::error("Error notificando escalado", [
                'ticket_id' => $ticket->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Notificar cambio de prioridad a crítica
     */
    private function notificarCambioPrioridad(Ticket $ticket, string $prioridadAnterior, string $nuevaPrioridad): void
    {
        try {
            // Notificar al técnico asignado
            if ($ticket->asignadoA) {
                $this->sendNotificationToUser(
                    $ticket->asignadoA,
                    '🔴 Prioridad CRÍTICA',
                    "El ticket #{$ticket->id} ahora tiene prioridad CRÍTICA. Atención inmediata requerida.",
                    'heroicon-o-fire',
                    'danger',
                    true // persistente
                );
            }

            // Notificar a administradores
            $this->notificarAdministradores($ticket, 'prioridad_critica');

            Log::info("Notificaciones de prioridad crítica enviadas", [
                'ticket_id' => $ticket->id,
                'prioridad_anterior' => $prioridadAnterior,
                'nueva_prioridad' => $nuevaPrioridad
            ]);

        } catch (\Exception $e) {
            Log::error("Error notificando cambio de prioridad", [
                'ticket_id' => $ticket->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Notificar cambio de estado
     */
    private function notificarCambioEstado(Ticket $ticket, string $estadoAnterior, string $nuevoEstado): void
    {
        try {
            $iconos = [
                'En Progreso' => 'heroicon-o-play',
                'Escalado' => 'heroicon-o-arrow-trending-up',
                'Cancelado' => 'heroicon-o-x-circle'
            ];

            $colores = [
                'En Progreso' => 'info',
                'Escalado' => 'warning',
                'Cancelado' => 'danger'
            ];

            $titulo = "📋 Estado del ticket cambió";
            $mensaje = "El ticket #{$ticket->id} cambió de '{$estadoAnterior}' a '{$nuevoEstado}'";

            // Notificar al creador
            if ($ticket->creadoPor) {
                $this->sendNotificationToUser(
                    $ticket->creadoPor,
                    $titulo,
                    $mensaje,
                    $iconos[$nuevoEstado] ?? 'heroicon-o-information-circle',
                    $colores[$nuevoEstado] ?? 'info'
                );
            }

            // Notificar al técnico asignado si es diferente al creador
            if ($ticket->asignadoA && $ticket->asignadoA->id !== $ticket->creadoPor->id) {
                $this->sendNotificationToUser(
                    $ticket->asignadoA,
                    $titulo,
                    $mensaje,
                    $iconos[$nuevoEstado] ?? 'heroicon-o-information-circle',
                    $colores[$nuevoEstado] ?? 'info'
                );
            }

            Log::info("Notificaciones de cambio de estado enviadas", [
                'ticket_id' => $ticket->id,
                'estado_anterior' => $estadoAnterior,
                'nuevo_estado' => $nuevoEstado
            ]);

        } catch (\Exception $e) {
            Log::error("Error notificando cambio de estado", [
                'ticket_id' => $ticket->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Notificar a todos los administradores
     */
    private function notificarAdministradores(Ticket $ticket, string $accion, ?string $agenteNombre = null): void
    {
        try {
            $administradores = User::whereHas('roles', function ($query) {
                $query->whereIn('name', ['Admin', 'Super Admin']);
            })->get();

            if ($administradores->isEmpty()) {
                return;
            }

            $titulos = [
                'creado' => '📝 Nuevo ticket creado',
                'reasignado' => '🔄 Ticket reasignado',
                'escalado' => '🚨 Ticket escalado automáticamente',
                'prioridad_critica' => '🔴 Ticket con prioridad CRÍTICA'
            ];

            $mensajes = [
                'creado' => "Nuevo ticket #{$ticket->id} creado por {$ticket->creadoPor->name}: {$ticket->titulo}",
                'reasignado' => "Ticket #{$ticket->id} reasignado a {$agenteNombre}: {$ticket->titulo}",
                'escalado' => "Ticket #{$ticket->id} escalado automáticamente por SLA vencido: {$ticket->titulo}",
                'prioridad_critica' => "Ticket #{$ticket->id} tiene ahora prioridad CRÍTICA: {$ticket->titulo}"
            ];

            $iconos = [
                'creado' => 'heroicon-o-document-plus',
                'reasignado' => 'heroicon-o-arrow-path',
                'escalado' => 'heroicon-o-exclamation-triangle',
                'prioridad_critica' => 'heroicon-o-fire'
            ];

            $colores = [
                'creado' => 'info',
                'reasignado' => 'warning',
                'escalado' => 'danger',
                'prioridad_critica' => 'danger'
            ];

            foreach ($administradores as $admin) {
                $this->sendNotificationToUser(
                    $admin,
                    $titulos[$accion] ?? 'Actualización de ticket',
                    $mensajes[$accion] ?? "Ticket #{$ticket->id} actualizado",
                    $iconos[$accion] ?? 'heroicon-o-information-circle',
                    $colores[$accion] ?? 'info',
                    in_array($accion, ['escalado', 'prioridad_critica']) // persistente para acciones críticas
                );
            }

            Log::info("Notificaciones enviadas a administradores", [
                'ticket_id' => $ticket->id,
                'accion' => $accion,
                'admins_notificados' => $administradores->count()
            ]);

        } catch (\Exception $e) {
            Log::error("Error notificando a administradores", [
                'ticket_id' => $ticket->id,
                'accion' => $accion,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Helper method to send notifications to users
     */
    private function sendNotificationToUser(
        User $user, 
        string $title, 
        string $body, 
        string $icon = 'heroicon-o-information-circle',
        string $color = 'info',
        bool $persistent = false
    ): void {
        try {
            $notification = Notification::make()
                ->title($title)
                ->body($body)
                ->icon($icon)
                ->iconColor($color);

            if ($persistent) {
                $notification->persistent();
            }

            // Enviar a la base de datos y hacer broadcast en tiempo real
            $notification->sendToDatabase($user)->broadcast($user);

        } catch (\Exception $e) {
            Log::error("Error enviando notificación a usuario {$user->id}: " . $e->getMessage());
        }
    }

    /**
     * Handle the Ticket "deleted" event.
     */
    public function deleted(Ticket $ticket): void
    {
        // Opcional: notificar cuando se elimina un ticket
    }

    /**
     * Handle the Ticket "restored" event.
     */
    public function restored(Ticket $ticket): void
    {
        // Opcional: notificar cuando se restaura un ticket
    }

    /**
     * Handle the Ticket "force deleted" event.
     */
    public function forceDeleted(Ticket $ticket): void
    {
        // Opcional: notificar cuando se elimina permanentemente un ticket
    }
}
