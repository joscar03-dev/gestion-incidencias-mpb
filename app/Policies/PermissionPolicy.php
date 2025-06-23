<?php

namespace App\Policies;

use App\Models\Permission;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class PermissionPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user)
    {
        return $user->hasRole(['Admin', 'Moderador'])
            ? Response::allow()
            : Response::deny('You do not have permission to view permissions.');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Permission $permission)
    {
        return $user->hasRole('Admin')
            ? Response::allow()
            : Response::deny('You do not have permission to view this permission.');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user)
    {
        return ($user->hasRole('Admin') || $user->hasPermissionTo('crear-permiso'))
            ? Response::allow()
            : Response::deny('You do not have permission to create permissions.');

    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Permission $permission)
    {
        return ($user->hasRole('Admin') || $user->hasPermissionTo('editar-permiso'))
            ? Response::allow()
            : Response::deny('You do not have permission to update this permission.');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Permission $permission)
    {
        return $user->hasRole('Admin')
            ? Response::allow()
            : Response::deny('You do not have permission to delete this permission.');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Permission $permission)
    {
        return $user->hasRole('Admin')
            ? Response::allow()
            : Response::deny('You do not have permission to restore this permission.');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Permission $permission)
    {
        return $user->hasRole('Admin')
            ? Response::allow()
            : Response::deny('You do not have permission to permanently delete this permission.');
    }
    /**
     * Determine whether the user can perform bulk delete.
     */
    public function bulkDelete(User $user)
    {
        return $user->hasRole('Admin')
            ? Response::allow()
            : Response::deny('You do not have permission to bulk delete permissions.');
    }
}
