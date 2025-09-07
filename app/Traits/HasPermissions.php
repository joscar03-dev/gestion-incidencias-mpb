<?php

namespace App\Traits;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

trait HasPermissions
{
    /**
     * Verifica si el usuario actual tiene el permiso especificado
     */
    protected function checkPermission(string $permission): bool
    {
        if (!Auth::check()) {
            return false;
        }

        return Auth::user()->can($permission);
    }

    /**
     * Verifica el permiso y aborta si no lo tiene
     */
    protected function requirePermission(string $permission): void
    {
        if (!$this->checkPermission($permission)) {
            abort(403, "No tienes permisos para realizar esta acción. Permiso requerido: {$permission}");
        }
    }

    /**
     * Verifica múltiples permisos (requiere TODOS)
     */
    protected function requireAllPermissions(array $permissions): void
    {
        foreach ($permissions as $permission) {
            $this->requirePermission($permission);
        }
    }

    /**
     * Verifica múltiples permisos (requiere AL MENOS UNO)
     */
    protected function requireAnyPermission(array $permissions): void
    {
        if (!Auth::check()) {
            abort(401, 'No autenticado');
        }

        foreach ($permissions as $permission) {
            if (Auth::user()->can($permission)) {
                return;
            }
        }

        $permissionsList = implode(', ', $permissions);
        abort(403, "No tienes permisos para realizar esta acción. Permisos requeridos (al menos uno): {$permissionsList}");
    }

    /**
     * Genera permisos estándar para un modelo
     */
    protected function getModelPermissions(string $model): array
    {
        return [
            'view' => "ver-{$model}",
            'create' => "crear-{$model}",
            'update' => "editar-{$model}",
            'delete' => "borrar-{$model}",
        ];
    }

    /**
     * Verifica permiso de visualización para un modelo
     */
    protected function requireViewPermission(string $model): void
    {
        $this->requirePermission("ver-{$model}");
    }

    /**
     * Verifica permiso de creación para un modelo
     */
    protected function requireCreatePermission(string $model): void
    {
        $this->requirePermission("crear-{$model}");
    }

    /**
     * Verifica permiso de edición para un modelo
     */
    protected function requireUpdatePermission(string $model): void
    {
        $this->requirePermission("editar-{$model}");
    }

    /**
     * Verifica permiso de eliminación para un modelo
     */
    protected function requireDeletePermission(string $model): void
    {
        $this->requirePermission("borrar-{$model}");
    }

    /**
     * Obtiene los permisos del usuario actual para un modelo específico
     */
    protected function getUserModelPermissions(string $model): array
    {
        if (!Auth::check()) {
            return [];
        }

        $user = Auth::user();
        $permissions = $this->getModelPermissions($model);
        $userPermissions = [];

        foreach ($permissions as $action => $permission) {
            $userPermissions[$action] = $user->can($permission);
        }

        return $userPermissions;
    }

    /**
     * Retorna los datos de permisos para usar en vistas
     */
    protected function getPermissionsForView(string $model): array
    {
        return [
            'permissions' => $this->getUserModelPermissions($model),
            'can_view' => $this->checkPermission("ver-{$model}"),
            'can_create' => $this->checkPermission("crear-{$model}"),
            'can_update' => $this->checkPermission("editar-{$model}"),
            'can_delete' => $this->checkPermission("borrar-{$model}"),
        ];
    }
}
