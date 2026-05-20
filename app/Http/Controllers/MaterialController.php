<?php

namespace App\Http\Controllers;

use App\Models\Material;
use App\Models\UnidadMedida;
use Illuminate\Http\Request;
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
            'nombre' => ['required', 'string', 'max:100', 'unique:materials,nombre'],
            'descripcion' => ['nullable', 'string'],
            'id_medida' => ['required', 'exists:unidad_medidas,id'],
            'stock_minimo' => ['required', 'numeric', 'min:0'],
        ]);

        Material::create($validated);

        return redirect()->route('materials.index')->with('success', 'Material registrado correctamente.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Material $material)
    {
        $validated = $request->validate([
            'nombre' => ['required', 'string', 'max:100', 'unique:materials,nombre,' . $material->id],
            'descripcion' => ['nullable', 'string'],
            'id_medida' => ['required', 'exists:unidad_medidas,id'],
            'stock_minimo' => ['required', 'numeric', 'min:0'],
        ]);

        $material->update($validated);

        return redirect()->route('materials.index')->with('success', 'Material actualizado correctamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Material $material)
    {
        try {
            $material->delete();
            return redirect()->route('materials.index')->with('success', 'Material eliminado correctamente.');
        } catch (\Exception $e) {
            return redirect()->route('materials.index')->with('error', 'No se puede eliminar el material porque tiene registros históricos de ingresos o despachos asociados.');
        }
    }
}
