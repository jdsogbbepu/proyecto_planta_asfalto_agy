<?php

namespace App\Http\Controllers;

use App\Models\Proyecto;
use App\Models\BitacoraActividad;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class ProyectoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Inertia::render('Proyectos/Index', [
            'proyectos' => Proyecto::orderBy('nombre')->get()
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => ['required', 'string', 'max:150'],
            'ubicacion' => ['nullable', 'string', 'max:255'],
            'encargado' => ['nullable', 'string', 'max:100'],
            'fecha_inicio' => ['nullable', 'date'],
            'fecha_fin' => ['nullable', 'date', 'after_or_equal:fecha_inicio'],
            'estado' => ['required', 'string', Rule::in(['activo', 'finalizado', 'pausado'])],
        ]);

        $proyecto = Proyecto::create($validated);

        // Registrar en bitácora
        BitacoraActividad::create([
            'id_usuario' => Auth::id(),
            'accion' => 'Creación de Proyecto',
            'detalle' => "Se creó el proyecto '{$proyecto->nombre}' (ID: {$proyecto->id}).",
            'ip_address' => $request->ip(),
        ]);

        return redirect()->route('proyectos.index')->with('success', 'Proyecto registrado correctamente.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Proyecto $proyecto)
    {
        $validated = $request->validate([
            'nombre' => ['required', 'string', 'max:150'],
            'ubicacion' => ['nullable', 'string', 'max:255'],
            'encargado' => ['nullable', 'string', 'max:100'],
            'fecha_inicio' => ['nullable', 'date'],
            'fecha_fin' => ['nullable', 'date', 'after_or_equal:fecha_inicio'],
            'estado' => ['required', 'string', Rule::in(['activo', 'finalizado', 'pausado'])],
        ]);

        $proyecto->update($validated);

        // Registrar en bitácora
        BitacoraActividad::create([
            'id_usuario' => Auth::id(),
            'accion' => 'Modificación de Proyecto',
            'detalle' => "Se modificó el proyecto '{$proyecto->nombre}' (ID: {$proyecto->id}). Estado actual: {$proyecto->estado}.",
            'ip_address' => $request->ip(),
        ]);

        return redirect()->route('proyectos.index')->with('success', 'Proyecto actualizado correctamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Proyecto $proyecto)
    {
        $nombre = $proyecto->nombre;
        $id = $proyecto->id;

        try {
            $proyecto->delete();

            // Registrar en bitácora
            BitacoraActividad::create([
                'id_usuario' => Auth::id(),
                'accion' => 'Eliminación de Proyecto',
                'detalle' => "Se eliminó el proyecto '{$nombre}' (ID: {$id}).",
                'ip_address' => $request->ip(),
            ]);

            return redirect()->route('proyectos.index')->with('success', 'Proyecto eliminado correctamente.');
        } catch (\Exception $e) {
            return redirect()->route('proyectos.index')->with('error', 'No se puede eliminar el proyecto porque tiene registros de ingresos o despachos de materiales asociados.');
        }
    }
}
