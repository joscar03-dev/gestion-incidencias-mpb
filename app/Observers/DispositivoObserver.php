<?php

namespace App\Observers;

use App\Models\Dispositivo;
use App\Models\User;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Log;

class DispositivoObserver
{
    /**
     * Notificar cuando se asigna un dispositivo a un usuario
     */
    public function updated(Dispositivo $dispositivo): void
    {
        try {
            // Notificar asignación
            if ($dispositivo->isDirty('usuario_id') && $dispositivo->usuario_id) {
                $user = $dispositivo->usuario;
                if ($user) {
                    $this->sendNotificationToUser(
                        $user,
                        '📱 Dispositivo asignado',
                        "Se te ha asignado el dispositivo #{$dispositivo->id}: {$dispositivo->nombre}",
                        'heroicon-o-device-phone-mobile',
                        'success'
                    );
                    Log::info('Notificación de dispositivo asignado enviada', [
                        'dispositivo_id' => $dispositivo->id,
                        'user_id' => $user->id,
                        'user_name' => $user->name
                    ]);
                }
            }
            // Notificar desasignación
            if ($dispositivo->isDirty('usuario_id') && is_null($dispositivo->usuario_id)) {
                $originalUserId = $dispositivo->getOriginal('usuario_id');
                if ($originalUserId) {
                    $user = User::find($originalUserId);
                    if ($user) {
                        $this->sendNotificationToUser(
                            $user,
                            '📱 Dispositivo desasignado',
                            "El dispositivo #{$dispositivo->id}: {$dispositivo->nombre} ha sido desasignado de tu cuenta.",
                            'heroicon-o-device-phone-mobile',
                            'warning'
                        );
                        Log::info('Notificación de dispositivo desasignado enviada', [
                            'dispositivo_id' => $dispositivo->id,
                            'user_id' => $user->id,
                            'user_name' => $user->name
                        ]);
                    }
                }
            }
        } catch (\Exception $e) {
            Log::error('Error enviando notificación de dispositivo', [
                'dispositivo_id' => $dispositivo->id,
                'error' => $e->getMessage()
            ]);
        }
    }
    /**
     * Notificar cuando se crea un dispositivo y se asigna a un usuario
     */
    public function created(Dispositivo $dispositivo): void
    {
        try {
            if ($dispositivo->usuario_id) {
                $user = $dispositivo->usuario;
                if ($user) {
                    $this->sendNotificationToUser(
                        $user,
                        '📱 Dispositivo asignado',
                        "Se te ha asignado el dispositivo #{$dispositivo->id}: {$dispositivo->nombre}",
                        'heroicon-o-device-phone-mobile',
                        'success'
                    );
                    Log::info('Notificación de dispositivo asignado (creación) enviada', [
                        'dispositivo_id' => $dispositivo->id,
                        'user_id' => $user->id,
                        'user_name' => $user->name
                    ]);
                }
            }
        } catch (\Exception $e) {
            Log::error('Error enviando notificación de dispositivo (creación)', [
                'dispositivo_id' => $dispositivo->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Helper para enviar notificaciones
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
            $notification->sendToDatabase($user)->broadcast($user);
        } catch (\Exception $e) {
            Log::error("Error enviando notificación a usuario {$user->id}: " . $e->getMessage());
        }
    }
}
