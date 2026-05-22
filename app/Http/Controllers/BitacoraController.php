<?php

namespace App\Http\Controllers;

use App\Models\BitacoraActividad;
use Illuminate\Http\Request;
use Inertia\Inertia;

class BitacoraController extends Controller
{
    /**
     * Muestra el registro de actividades (Bitácora).
     */
    public function index(Request $request)
    {
        $query = BitacoraActividad::with('usuario:id,name,username')
            ->orderBy('created_at', 'desc');

        // Búsqueda opcional
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('accion', 'like', "%{$search}%")
                  ->orWhere('detalle', 'like', "%{$search}%")
                  ->orWhere('ip_address', 'like', "%{$search}%")
                  ->orWhereHas('usuario', function ($u) use ($search) {
                      $u->where('name', 'like', "%{$search}%")
                        ->orWhere('username', 'like', "%{$search}%");
                  });
            });
        }

        $bitacora = $query->paginate(20)->withQueryString();

        return Inertia::render('Bitacora/Index', [
            'bitacora' => $bitacora,
            'filters' => $request->only(['search']),
        ]);
    }
}
