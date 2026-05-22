<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;
use App\Models\RolePermission;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that is loaded on the first page visit.
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determine the current asset version.
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        $user = $request->user();
        $permissions = [];

        if ($user) {
            if ($user->role === 'administrador') {
                $permissions = [
                    'ver_dashboard',
                    'gestionar_usuarios',
                    'gestionar_materiales',
                    'gestionar_proveedores',
                    'gestionar_proyectos',
                    'gestionar_funcionarios',
                    'gestionar_ingresos',
                    'gestionar_salidas',
                    'ver_reportes',
                    'gestionar_permisos',
                    'ver_bitacora',
                ];
            } else {
                $permissions = RolePermission::where('role', $user->role)
                    ->pluck('permission')
                    ->toArray();
            }
        }

        return [
            ...parent::share($request),
            'auth' => [
                'user' => $user,
                'permissions' => $permissions,
            ],
        ];
    }
}
