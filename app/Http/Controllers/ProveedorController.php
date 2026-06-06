<?php

namespace App\Http\Controllers;

use App\Models\Proveedor;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ProveedorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Inertia::render('Proveedores/Index', [
            'proveedores' => Proveedor::orderBy('razon_social')->get()
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'razon_social' => ['required', 'string', 'max:150'],
            'nit' => ['nullable', 'string', 'max:30', 'unique:proveedors,nit'],
            'telefono' => ['nullable', 'string', 'max:20'],
            'direccion' => ['nullable', 'string', 'max:255'],
        ]);

        Proveedor::create($validated);

        return redirect()->route('proveedores.index')->with('success', 'Proveedor registrado correctamente.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Proveedor $proveedor)
    {
        $validated = $request->validate([
            'razon_social' => ['required', 'string', 'max:150'],
            'nit' => ['nullable', 'string', 'max:30', 'unique:proveedors,nit,' . $proveedor->id],
            'telefono' => ['nullable', 'string', 'max:20'],
            'direccion' => ['nullable', 'string', 'max:255'],
        ]);

        $proveedor->update($validated);

        return redirect()->route('proveedores.index')->with('success', 'Proveedor actualizado correctamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Proveedor $proveedor)
    {
        try {
            $proveedor->delete();
            return redirect()->route('proveedores.index')->with('success', 'Proveedor eliminado correctamente.');
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('ProveedorController::destroy failed', [
                'proveedor_id' => $proveedor->id,
                'user_id' => auth()->id(),
                'exception' => $e,
            ]);
            return redirect()->route('proveedores.index')->with('error', 'No se puede eliminar el proveedor porque tiene registros de ingresos de materiales asociados.');
        }
    }
}
