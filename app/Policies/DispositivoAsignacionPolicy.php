<?php

namespace App\Policies;

use App\Models\DispositivoAsignacion;
use App\Models\User;

class DispositivoAsignacionPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('ver-dispositivo-asignacion');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, DispositivoAsignacion $dispositivoAsignacion): bool
    {
        return $user->can('ver-dispositivo-asignacion');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('crear-dispositivo-asignacion');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, DispositivoAsignacion $dispositivoAsignacion): bool
    {
        return $user->can('editar-dispositivo-asignacion');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, DispositivoAsignacion $dispositivoAsignacion): bool
    {
        return $user->can('borrar-dispositivo-asignacion');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, DispositivoAsignacion $dispositivoAsignacion): bool
    {
        return $user->can('editar-dispositivo-asignacion');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, DispositivoAsignacion $dispositivoAsignacion): bool
    {
        return $user->can('borrar-dispositivo-asignacion');
    }
}
