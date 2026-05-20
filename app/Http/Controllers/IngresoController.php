<?php

namespace App\Http\Controllers;

use App\Models\Ingreso;
use App\Models\DetalleIngreso;
use App\Models\Material;
use App\Models\Proveedor;
use App\Models\Proyecto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class IngresoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Inertia::render('Ingresos/Index', [
            'ingresos' => Ingreso::with(['proveedor', 'usuario', 'detalles.material.medida', 'detalles.proyecto'])
                ->latest()
                ->get()
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Ingresos/Create', [
            'materials' => Material::with('medida')->orderBy('nombre')->get(),
            'proveedores' => Proveedor::orderBy('razon_social')->get(),
            'proyectos' => Proyecto::where('estado', 'activo')->orderBy('nombre')->get(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nro_ticket' => ['nullable', 'string', 'max:50'],
            'odc' => ['nullable', 'string', 'max:50'],
            'id_proveedor' => ['nullable', 'exists:proveedors,id'],
            'fecha_adquirida' => ['required', 'date'],
            'observaciones' => ['nullable', 'string'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.id_material' => ['required', 'exists:materials,id'],
            'items.*.id_proyecto' => ['required', 'exists:proyectos,id'],
            'items.*.cantidad' => ['required', 'numeric', 'gt:0'],
        ]);

        DB::beginTransaction();

        try {
            // Create core entry record
            $ingreso = Ingreso::create([
                'nro_ticket' => $validated['nro_ticket'],
                'odc' => $validated['odc'],
                'id_proveedor' => $validated['id_proveedor'],
                'id_usuario' => auth()->id(),
                'fecha_adquirida' => $validated['fecha_adquirida'],
                'observaciones' => $validated['observaciones'],
            ]);

            // Save details (lotes)
            foreach ($validated['items'] as $item) {
                DetalleIngreso::create([
                    'id_ingreso' => $ingreso->id,
                    'id_material' => $item['id_material'],
                    'id_proyecto' => $item['id_proyecto'],
                    'cantidad_adquirida' => $item['cantidad'],
                    'cantidad_actual_lote' => $item['cantidad'], // Initial balance equals acquired quantity
                ]);
            }

            DB::commit();

            return redirect()->route('ingresos.index')->with('success', 'Ingreso de materiales y creación de lotes registrado correctamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Error al procesar el registro de ingreso: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Ingreso $ingreso)
    {
        // Deleting historical records can corrupt the FIFO traceability.
        // We will restrict deletion of entries if any of their batches/lotes have already been consumed.
        DB::beginTransaction();
        try {
            foreach ($ingreso->detalles as $detalle) {
                // If the batch has been consumed (even partially), we block deletion
                if ($detalle->cantidad_actual_lote < $detalle->cantidad_adquirida) {
                    return redirect()->route('ingresos.index')->with('error', 'No se puede eliminar el ingreso porque uno de los lotes ya ha sido consumido parcial o totalmente en despachos.');
                }
            }

            // If clean, we can cascade delete details and core record
            $ingreso->delete(); // Cascades on database foreign key for details
            DB::commit();
            return redirect()->route('ingresos.index')->with('success', 'Ingreso y lotes eliminados correctamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('ingresos.index')->with('error', 'Error al intentar eliminar el registro: ' . $e->getMessage());
        }
    }
}
