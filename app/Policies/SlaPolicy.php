<?php

namespace App\Policies;

use App\Models\Sla;
use App\Models\User;

class SlaPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('ver-sla');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Sla $sla): bool
    {
        return $user->can('ver-sla');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('crear-sla');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Sla $sla): bool
    {
        return $user->can('editar-sla');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Sla $sla): bool
    {
        return $user->can('borrar-sla');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Sla $sla): bool
    {
        return $user->can('editar-sla');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Sla $sla): bool
    {
        return $user->can('borrar-sla');
    }
}
