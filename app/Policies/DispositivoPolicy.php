<?php

namespace App\Policies;

use App\Models\Dispositivo;
use App\Models\User;

class DispositivoPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('ver-dispositivo');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Dispositivo $dispositivo): bool
    {
        return $user->can('ver-dispositivo');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('crear-dispositivo');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Dispositivo $dispositivo): bool
    {
        return $user->can('editar-dispositivo');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Dispositivo $dispositivo): bool
    {
        return $user->can('borrar-dispositivo');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Dispositivo $dispositivo): bool
    {
        return $user->can('editar-dispositivo');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Dispositivo $dispositivo): bool
    {
        return $user->can('borrar-dispositivo');
    }
}
