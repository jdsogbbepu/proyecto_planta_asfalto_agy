<?php

namespace App\Http\Controllers;

use App\Models\Salida;
use App\Models\DetalleSalida;
use App\Models\DetalleIngreso;
use App\Models\Funcionario;
use App\Models\Proyecto;
use App\Models\Material;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class SalidaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Inertia::render('Salidas/Index', [
            'salidas' => Salida::with(['funcionario', 'proyecto', 'usuario', 'detalles.detalleIngreso.material.medida'])
                ->latest()
                ->get()
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Calculate available stock for each material in each project
        $stocksPorProyecto = DB::table('detalle_ingresos')
            ->join('materials', 'detalle_ingresos.id_material', '=', 'materials.id')
            ->select(
                'detalle_ingresos.id_proyecto',
                'detalle_ingresos.id_material',
                DB::raw('SUM(detalle_ingresos.cantidad_actual_lote) as stock_disponible')
            )
            ->groupBy('detalle_ingresos.id_proyecto', 'detalle_ingresos.id_material')
            ->having('stock_disponible', '>', 0)
            ->get()
            ->map(function ($stock) {
                $stock->stock_disponible = (float) $stock->stock_disponible;
                return $stock;
            });

        return Inertia::render('Salidas/Create', [
            'funcionarios' => Funcionario::where('activo', true)->orderBy('nombre')->get(),
            'proyectos' => Proyecto::where('estado', 'activo')->orderBy('nombre')->get(),
            'materials' => Material::with('medida')->orderBy('nombre')->get(),
            'stocksPorProyecto' => $stocksPorProyecto,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_funcionario' => ['required', 'exists:funcionarios,id'],
            'id_proyecto' => ['required', 'exists:proyectos,id'],
            'uso' => ['required', 'string', 'max:255'],
            'fecha_salida' => ['required', 'date'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.id_material' => ['required', 'exists:materials,id'],
            'items.*.cantidad' => ['required', 'numeric', 'gt:0'],
        ]);

        DB::beginTransaction();

        try {
            // Create core dispatch record
            $salida = Salida::create([
                'id_funcionario' => $validated['id_funcionario'],
                'id_proyecto' => $validated['id_proyecto'],
                'id_usuario' => auth()->id(),
                'uso' => $validated['uso'],
                'fecha_salida' => $validated['fecha_salida'],
            ]);

            foreach ($validated['items'] as $item) {
                $materialId = $item['id_material'];
                $cantSolicitada = (float) $item['cantidad'];
                $proyectoId = $validated['id_proyecto'];

                // 1. Verify total available stock for this material and project
                $totalDisponible = (float) DetalleIngreso::where('id_material', $materialId)
                    ->where('id_proyecto', $proyectoId)
                    ->sum('cantidad_actual_lote');

                if ($cantSolicitada > $totalDisponible) {
                    throw new \Exception("Stock insuficiente. El material solicitado no cuenta con saldo disponible suficiente en este proyecto. Solicitado: {$cantSolicitada}, Disponible: {$totalDisponible}");
                }

                // 2. Query active batches/lotes ordered chronologically (FIFO/PEPS)
                $lotes = DetalleIngreso::where('id_material', $materialId)
                    ->where('id_proyecto', $proyectoId)
                    ->where('cantidad_actual_lote', '>', 0)
                    ->orderBy('created_at', 'asc')
                    ->orderBy('id', 'asc')
                    ->lockForUpdate() // Lock rows to prevent race conditions
                    ->get();

                $restante = $cantSolicitada;

                foreach ($lotes as $lote) {
                    if ($restante <= 0) {
                        break;
                    }

                    $saldoLote = (float) $lote->cantidad_actual_lote;

                    if ($restante >= $saldoLote) {
                        // Consume entire batch
                        DetalleSalida::create([
                            'id_salida' => $salida->id,
                            'id_detalle_ingreso' => $lote->id,
                            'cantidad_salida' => $saldoLote,
                        ]);

                        $lote->update(['cantidad_actual_lote' => 0]);
                        $restante -= $saldoLote;
                    } else {
                        // Consume partial batch
                        DetalleSalida::create([
                            'id_salida' => $salida->id,
                            'id_detalle_ingreso' => $lote->id,
                            'cantidad_salida' => $restante,
                        ]);

                        $lote->update(['cantidad_actual_lote' => $saldoLote - $restante]);
                        $restante = 0;
                    }
                }

                if ($restante > 0) {
                    throw new \Exception("Error crítico al procesar PEPS. No se pudieron consumir todas las unidades solicitadas.");
                }
            }

            DB::commit();

            return redirect()->route('despachos.index')->with('success', 'Despacho registrado correctamente aplicando lógica PEPS.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Error al procesar el despacho: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Salida $salida)
    {
        // Deleting a dispatch reverts the consumed stocks back to their corresponding batches!
        DB::beginTransaction();

        try {
            foreach ($salida->detalles as $detalleSalida) {
                $lote = DetalleIngreso::find($detalleSalida->id_detalle_ingreso);
                if ($lote) {
                    // Revert stock to the batch
                    $lote->update([
                        'cantidad_actual_lote' => (float) $lote->cantidad_actual_lote + (float) $detalleSalida->cantidad_salida
                    ]);
                }
            }

            $salida->delete(); // Cascades on database for details

            DB::commit();
            return redirect()->route('despachos.index')->with('success', 'Despacho revertido correctamente. Los saldos de los lotes fueron restaurados.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('despachos.index')->with('error', 'Error al revertir el despacho: ' . $e->getMessage());
        }
    }
}
