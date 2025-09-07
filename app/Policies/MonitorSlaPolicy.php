<?php

namespace App\Policies;

use App\Models\User;

/**
 * Política para MonitorSla
 * Nota: El modelo MonitorSla aún no está creado.
 * Cuando se cree, actualizar las signatures para usar el tipo específico.
 */
class MonitorSlaPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('ver-monitor-sla');
    }

    /**
     * Determine whether the user can view the model.
     * TODO: Cambiar $monitorSla por MonitorSla $monitorSla cuando se cree el modelo
     */
    public function view(User $user, $monitorSla): bool
    {
        return $user->can('ver-monitor-sla');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('crear-monitor-sla');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, $monitorSla): bool
    {
        return $user->can('editar-monitor-sla');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, $monitorSla): bool
    {
        return $user->can('borrar-monitor-sla');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, $monitorSla): bool
    {
        return $user->can('editar-monitor-sla');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, $monitorSla): bool
    {
        return $user->can('borrar-monitor-sla');
    }
}
