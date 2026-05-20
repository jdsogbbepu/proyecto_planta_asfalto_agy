<?php

namespace App\Http\Controllers;

use App\Models\UnidadMedida;
use Illuminate\Http\Request;

class UnidadMedidaController extends Controller
{
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => ['required', 'string', 'max:50', 'unique:unidad_medidas,nombre'],
            'abreviacion' => ['required', 'string', 'max:10', 'unique:unidad_medidas,abreviacion'],
        ]);

        UnidadMedida::create($validated);

        return redirect()->back()->with('success', 'Unidad de medida creada correctamente.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, UnidadMedida $unidadMedida)
    {
        $validated = $request->validate([
            'nombre' => ['required', 'string', 'max:50', 'unique:unidad_medidas,nombre,' . $unidadMedida->id],
            'abreviacion' => ['required', 'string', 'max:10', 'unique:unidad_medidas,abreviacion,' . $unidadMedida->id],
        ]);

        $unidadMedida->update($validated);

        return redirect()->back()->with('success', 'Unidad de medida actualizada correctamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(UnidadMedida $unidadMedida)
    {
        try {
            $unidadMedida->delete();
            return redirect()->back()->with('success', 'Unidad de medida eliminada correctamente.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'No se puede eliminar la unidad de medida porque está siendo utilizada por uno o más materiales.');
        }
    }
}
