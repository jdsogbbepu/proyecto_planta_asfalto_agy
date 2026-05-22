<?php

namespace App\Http\Controllers;

use App\Models\Material;
use App\Models\UnidadMedida;
use App\Models\BitacoraActividad;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class MaterialController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Inertia::render('Materials/Index', [
            'materials' => Material::with('medida')->orderBy('nombre')->get(),
            'unidadMedidas' => UnidadMedida::orderBy('nombre')->get(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'codigo_interno' => ['nullable', 'string', 'max:50', 'unique:materials,codigo_interno'],
            'nombre' => ['required', 'string', 'max:100', 'unique:materials,nombre'],
            'descripcion' => ['nullable', 'string'],
            'id_medida' => ['required', 'exists:unidad_medidas,id'],
            'stock_minimo' => ['required', 'numeric', 'min:0'],
        ]);

        $material = Material::create($validated);

        // Registrar en la bitácora
        BitacoraActividad::create([
            'id_usuario' => Auth::id(),
            'accion' => 'Creación de Material',
            'detalle' => "Se creó el material '{$material->nombre}' con código '{$material->codigo_interno}'.",
            'ip_address' => $request->ip(),
        ]);

        return redirect()->route('materials.index')->with('success', 'Material registrado correctamente.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Material $material)
    {
        $validated = $request->validate([
            'codigo_interno' => ['nullable', 'string', 'max:50', 'unique:materials,codigo_interno,' . $material->id],
            'nombre' => ['required', 'string', 'max:100', 'unique:materials,nombre,' . $material->id],
            'descripcion' => ['nullable', 'string'],
            'id_medida' => ['required', 'exists:unidad_medidas,id'],
            'stock_minimo' => ['required', 'numeric', 'min:0'],
        ]);

        $material->update($validated);

        // Registrar en la bitácora
        BitacoraActividad::create([
            'id_usuario' => Auth::id(),
            'accion' => 'Modificación de Material',
            'detalle' => "Se modificó el material '{$material->nombre}' (ID: {$material->id}).",
            'ip_address' => $request->ip(),
        ]);

        return redirect()->route('materials.index')->with('success', 'Material actualizado correctamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Material $material)
    {
        $nombre = $material->nombre;
        $id = $material->id;

        try {
            $material->delete();

            // Registrar en la bitácora
            BitacoraActividad::create([
                'id_usuario' => Auth::id(),
                'accion' => 'Eliminación de Material',
                'detalle' => "Se eliminó el material '{$nombre}' (ID: {$id}).",
                'ip_address' => $request->ip(),
            ]);

            return redirect()->route('materials.index')->with('success', 'Material eliminado correctamente.');
        } catch (\Exception $e) {
            return redirect()->route('materials.index')->with('error', 'No se puede eliminar el material porque tiene registros históricos de ingresos o despachos asociados.');
        }
    }
}
