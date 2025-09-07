<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PermissionMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $permission): Response
    {
        if (!auth()->check()) {
            abort(401, 'No autenticado');
        }

        if (!auth()->user()->can($permission)) {
            abort(403, "No tienes permisos para realizar esta acción. Permiso requerido: {$permission}");
        }

        return $next($request);
    }
}
