<?php

namespace App\Policies;

use App\Models\Role;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class RolePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user)
    {
        return $user->hasRole(['Admin', 'Moderador'])
            ? Response::allow()
            : Response::deny('You do not have permission to view roles.');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Role $role)
    {
        return $user->hasRole('Admin')
            ? Response::allow()
            : Response::deny('You do not have permission to view this role.');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user)
    {
        return $user->hasRole('Admin')
            ? Response::allow()
            : Response::deny('You do not have permission to create roles.');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Role $role)
    {
        return $user->hasRole('Admin')
            ? Response::allow()
            : Response::deny('You do not have permission to update this role.');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Role $role)
    {
        return $user->hasRole('Admin')
            ? Response::allow()
            : Response::deny('You do not have permission to delete this role.');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Role $role)
    {
        return $user->hasRole('Admin')
            ? Response::allow()
            : Response::deny('You do not have permission to restore this role.');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Role $role)
    {
        return $user->hasRole('Admin')
            ? Response::allow()
            : Response::deny('You do not have permission to permanently delete this role.');
    }
}
