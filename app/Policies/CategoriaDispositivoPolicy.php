<?php

namespace App\Policies;

use App\Models\CategoriaDispositivo;
use App\Models\User;

class CategoriaDispositivoPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('ver-categoria-dispositivo');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, CategoriaDispositivo $categoriaDispositivo): bool
    {
        return $user->can('ver-categoria-dispositivo');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('crear-categoria-dispositivo');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, CategoriaDispositivo $categoriaDispositivo): bool
    {
        return $user->can('editar-categoria-dispositivo');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, CategoriaDispositivo $categoriaDispositivo): bool
    {
        return $user->can('borrar-categoria-dispositivo');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, CategoriaDispositivo $categoriaDispositivo): bool
    {
        return $user->can('editar-categoria-dispositivo');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, CategoriaDispositivo $categoriaDispositivo): bool
    {
        return $user->can('borrar-categoria-dispositivo');
    }
}
