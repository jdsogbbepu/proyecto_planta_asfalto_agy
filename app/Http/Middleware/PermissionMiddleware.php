<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\RolePermission;
use Symfony\Component\HttpFoundation\Response;

class PermissionMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  $permission
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next, string $permission): Response
    {
        $user = Auth::user();

        if (!$user) {
            return abort(401);
        }

        // Si es administrador, tiene acceso total a todo por defecto
        if ($user->role === 'administrador') {
            return $next($request);
        }

        // Si no, verificar en la tabla de permisos
        $hasPermission = RolePermission::where('role', $user->role)
            ->where('permission', $permission)
            ->exists();

        if (!$hasPermission) {
            return abort(403, 'No tienes permiso para realizar esta acción.');
        }

        return $next($request);
    }
}
