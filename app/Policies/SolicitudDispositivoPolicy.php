<?php

namespace App\Policies;

use App\Models\SolicitudDispositivo;
use App\Models\User;

class SolicitudDispositivoPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('ver-solicitud-dispositivo');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, SolicitudDispositivo $solicitudDispositivo): bool
    {
        return $user->can('ver-solicitud-dispositivo');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('crear-solicitud-dispositivo');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, SolicitudDispositivo $solicitudDispositivo): bool
    {
        return $user->can('editar-solicitud-dispositivo');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, SolicitudDispositivo $solicitudDispositivo): bool
    {
        return $user->can('borrar-solicitud-dispositivo');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, SolicitudDispositivo $solicitudDispositivo): bool
    {
        return $user->can('editar-solicitud-dispositivo');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, SolicitudDispositivo $solicitudDispositivo): bool
    {
        return $user->can('borrar-solicitud-dispositivo');
    }
}
