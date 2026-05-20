<?php

namespace App\Http\Controllers;

use App\Models\Material;
use App\Models\Proyecto;
use App\Models\DetalleIngreso;
use App\Models\DetalleSalida;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class DashboardController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke()
    {
        // 1. Get all materials with their current physical stocks (sum of active batches balance)
        $materials = Material::with('medida')
            ->withSum('detalleIngresos as stock_actual', 'cantidad_actual_lote')
            ->orderBy('nombre')
            ->get()
            ->map(function ($material) {
                $material->stock_actual = (float) ($material->stock_actual ?? 0);
                return $material;
            });

        // 2. Bento View: General inventory grouped by active projects
        $proyectosConInventario = Proyecto::where('estado', 'activo')
            ->orderBy('nombre')
            ->get()
            ->map(function ($proyecto) {
                // Sum actual lote balances grouped by material for this project
                $inventario = DB::table('detalle_ingresos')
                    ->join('materials', 'detalle_ingresos.id_material', '=', 'materials.id')
                    ->join('unidad_medidas', 'materials.id_medida', '=', 'unidad_medidas.id')
                    ->where('detalle_ingresos.id_proyecto', $proyecto->id)
                    ->select(
                        'materials.nombre as material',
                        'unidad_medidas.abreviacion as unidad',
                        DB::raw('SUM(detalle_ingresos.cantidad_actual_lote) as stock')
                    )
                    ->groupBy('materials.id', 'materials.nombre', 'unidad_medidas.abreviacion')
                    ->having('stock', '>', 0)
                    ->get()
                    ->map(function ($item) {
                        $item->stock = (float) $item->stock;
                        return $item;
                    });
                
                $proyecto->inventario = $inventario;
                return $proyecto;
            });

        // 3. Recent Movements (Ingresos and Salidas)
        $recentIngresos = DetalleIngreso::with(['ingreso.usuario', 'material.medida', 'proyecto'])
            ->latest()
            ->limit(5)
            ->get()
            ->map(function ($det) {
                return [
                    'id' => 'ING-' . $det->id,
                    'fecha' => $det->created_at->format('Y-m-d H:i'),
                    'timestamp' => $det->created_at->timestamp,
                    'tipo' => 'INGRESO',
                    'tipo_class' => 'text-emerald-400 font-bold',
                    'material' => $det->material->nombre,
                    'proyecto' => $det->proyecto->nombre,
                    'cantidad' => (float) $det->cantidad_adquirida,
                    'unidad' => $det->material->medida->abreviacion,
                    'usuario' => $det->ingreso->usuario->name ?? 'Sistema',
                ];
            });

        $recentSalidas = DetalleSalida::with(['salida.usuario', 'salida.proyecto', 'detalleIngreso.material.medida'])
            ->latest()
            ->limit(5)
            ->get()
            ->map(function ($det) {
                return [
                    'id' => 'SAL-' . $det->id,
                    'fecha' => $det->created_at->format('Y-m-d H:i'),
                    'timestamp' => $det->created_at->timestamp,
                    'tipo' => 'DESPACHO (PEPS)',
                    'tipo_class' => 'text-[#ff8c94] font-bold',
                    'material' => $det->detalleIngreso->material->nombre,
                    'proyecto' => $det->salida->proyecto->nombre,
                    'cantidad' => (float) $det->cantidad_salida,
                    'unidad' => $det->detalleIngreso->material->medida->abreviacion,
                    'usuario' => $det->salida->usuario->name ?? 'Sistema',
                ];
            });

        // Merge and sort by timestamp desc
        $recentMovements = $recentIngresos->concat($recentSalidas)
            ->sortByDesc('timestamp')
            ->take(8)
            ->values();

        return Inertia::render('Dashboard', [
            'materials' => $materials,
            'proyectosConInventario' => $proyectosConInventario,
            'recentMovements' => $recentMovements,
        ]);
    }
}
