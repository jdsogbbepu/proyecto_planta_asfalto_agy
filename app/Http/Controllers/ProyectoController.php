<?php

namespace App\Http\Controllers;

use App\Models\Proyecto;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
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

        Proyecto::create($validated);

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

        return redirect()->route('proyectos.index')->with('success', 'Proyecto actualizado correctamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Proyecto $proyecto)
    {
        try {
            $proyecto->delete();
            return redirect()->route('proyectos.index')->with('success', 'Proyecto eliminado correctamente.');
        } catch (\Exception $e) {
            return redirect()->route('proyectos.index')->with('error', 'No se puede eliminar el proyecto porque tiene registros de ingresos o despachos de materiales asociados.');
        }
    }
}
