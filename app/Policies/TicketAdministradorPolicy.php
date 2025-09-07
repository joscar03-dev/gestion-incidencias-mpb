<?php

namespace App\Policies;

use App\Models\User;

class TicketAdministradorPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('ver-ticket-administrador');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, $ticketAdministrador): bool
    {
        return $user->can('ver-ticket-administrador');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('crear-ticket-administrador');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, $ticketAdministrador): bool
    {
        return $user->can('editar-ticket-administrador');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, $ticketAdministrador): bool
    {
        return $user->can('borrar-ticket-administrador');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, $ticketAdministrador): bool
    {
        return $user->can('editar-ticket-administrador');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, $ticketAdministrador): bool
    {
        return $user->can('borrar-ticket-administrador');
    }
}
