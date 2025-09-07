<?php

namespace App\Policies;

use App\Models\SlaPrioridadFactor;
use App\Models\User;

class FactorPrioridadSlaPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('ver-factor-prioridad-sla');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, SlaPrioridadFactor $slaPrioridadFactor): bool
    {
        return $user->can('ver-factor-prioridad-sla');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('crear-factor-prioridad-sla');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, SlaPrioridadFactor $slaPrioridadFactor): bool
    {
        return $user->can('editar-factor-prioridad-sla');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, SlaPrioridadFactor $slaPrioridadFactor): bool
    {
        return $user->can('borrar-factor-prioridad-sla');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, $factorPrioridadSla): bool
    {
        return $user->can('editar-factor-prioridad-sla');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, $factorPrioridadSla): bool
    {
        return $user->can('borrar-factor-prioridad-sla');
    }
}
