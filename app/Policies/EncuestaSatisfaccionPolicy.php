<?php

namespace App\Policies;

use App\Models\TicketSatisfaction;
use App\Models\User;

class EncuestaSatisfaccionPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('ver-encuesta-satisfaccion');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, TicketSatisfaction $ticketSatisfaction): bool
    {
        return $user->can('ver-encuesta-satisfaccion');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('crear-encuesta-satisfaccion');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, TicketSatisfaction $ticketSatisfaction): bool
    {
        return $user->can('editar-encuesta-satisfaccion');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, TicketSatisfaction $ticketSatisfaction): bool
    {
        return $user->can('borrar-encuesta-satisfaccion');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, $encuestaSatisfaccion): bool
    {
        return $user->can('editar-encuesta-satisfaccion');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, $encuestaSatisfaccion): bool
    {
        return $user->can('borrar-encuesta-satisfaccion');
    }
}
