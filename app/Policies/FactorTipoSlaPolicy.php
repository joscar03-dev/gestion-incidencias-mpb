<?php

namespace App\Policies;

use App\Models\SlaTipoFactor;
use App\Models\User;

class FactorTipoSlaPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('ver-factor-tipo-sla');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, SlaTipoFactor $slaTipoFactor): bool
    {
        return $user->can('ver-factor-tipo-sla');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('crear-factor-tipo-sla');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, SlaTipoFactor $slaTipoFactor): bool
    {
        return $user->can('editar-factor-tipo-sla');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, SlaTipoFactor $slaTipoFactor): bool
    {
        return $user->can('borrar-factor-tipo-sla');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, $factorTipoSla): bool
    {
        return $user->can('editar-factor-tipo-sla');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, $factorTipoSla): bool
    {
        return $user->can('borrar-factor-tipo-sla');
    }
}
