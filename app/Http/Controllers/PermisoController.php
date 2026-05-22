<?php

namespace App\Http\Controllers;

use App\Models\RolePermission;
use App\Models\BitacoraActividad;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class PermisoController extends Controller
{
    /**
     * Muestra la matriz de permisos.
     */
    public function index()
    {
        $roles = ['administrador', 'operador', 'visor'];

        $permisosDisponibles = [
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

        // Obtener permisos agrupados por rol
        $permisosActuales = RolePermission::all()->groupBy('role')->map(function ($items) {
            return $items->pluck('permission');
        });

        return Inertia::render('Permisos/Index', [
            'roles' => $roles,
            'permisosDisponibles' => $permisosDisponibles,
            'permisosActuales' => $permisosActuales,
        ]);
    }

    /**
     * Actualiza los permisos de los roles.
     */
    public function update(Request $request)
    {
        $validated = $request->validate([
            'matrix' => 'required|array', // { operador: [...], visor: [...], administrador: [...] }
        ]);

        $matrix = $validated['matrix'];

        DB::transaction(function () use ($matrix) {
            // Limpiar permisos existentes de los roles editados
            foreach ($matrix as $role => $permissions) {
                if (in_array($role, ['administrador', 'operador', 'visor'])) {
                    RolePermission::where('role', $role)->delete();

                    foreach ($permissions as $permission) {
                        RolePermission::create([
                            'role' => $role,
                            'permission' => $permission,
                        ]);
                    }
                }
            }
        });

        // Registrar acción en la bitácora
        BitacoraActividad::create([
            'id_usuario' => Auth::id(),
            'accion' => 'Actualización de Permisos',
            'detalle' => 'Se actualizó la matriz de permisos para los roles del sistema.',
            'ip_address' => $request->ip(),
        ]);

        return redirect()->route('permisos.index')->with('success', 'Permisos actualizados correctamente.');
    }
}
