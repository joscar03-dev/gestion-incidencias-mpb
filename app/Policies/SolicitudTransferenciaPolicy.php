<?php

namespace App\Policies;

use App\Models\SolicitudTransferencia;
use App\Models\User;

class SolicitudTransferenciaPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('ver-solicitud-transferencia');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, SolicitudTransferencia $solicitudTransferencia): bool
    {
        return $user->can('ver-solicitud-transferencia');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('crear-solicitud-transferencia');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, SolicitudTransferencia $solicitudTransferencia): bool
    {
        return $user->can('editar-solicitud-transferencia');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, SolicitudTransferencia $solicitudTransferencia): bool
    {
        return $user->can('borrar-solicitud-transferencia');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, SolicitudTransferencia $solicitudTransferencia): bool
    {
        return $user->can('editar-solicitud-transferencia');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, SolicitudTransferencia $solicitudTransferencia): bool
    {
        return $user->can('borrar-solicitud-transferencia');
    }
}
