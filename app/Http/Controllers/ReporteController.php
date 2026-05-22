<?php

namespace App\Http\Controllers;

use App\Models\Proyecto;
use App\Models\Material;
use App\Models\DetalleIngreso;
use App\Models\DetalleSalida;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class ReporteController extends Controller
{
    /**
     * Display the Kardex ledger and active batches report.
     */
    public function index(Request $request)
    {
        $proyectos = Proyecto::orderBy('nombre')->get();
        $materials = Material::with('medida')->orderBy('nombre')->get();

        $selectedProyectoId = $request->input('id_proyecto', $proyectos->first()?->id);
        $selectedMaterialId = $request->input('id_material', $materials->first()?->id);
        
        $fechaDesde = $request->input('fecha_desde');
        $fechaHasta = $request->input('fecha_hasta');

        $lotes = [];
        $kardex = [];

        if ($selectedProyectoId && $selectedMaterialId) {
            // 1. Fetch all batches for the filtered combination
            $lotes = DetalleIngreso::with(['ingreso.usuario', 'ingreso.proveedor'])
                ->where('id_proyecto', $selectedProyectoId)
                ->where('id_material', $selectedMaterialId)
                ->orderBy('created_at', 'asc')
                ->get()
                ->map(function ($lote) {
                    $lote->cantidad_adquirida = (float) $lote->cantidad_adquirida;
                    $lote->cantidad_actual_lote = (float) $lote->cantidad_actual_lote;
                    return $lote;
                });

            // 2. Fetch all ingress events
            $ingresos = DetalleIngreso::with(['ingreso.usuario', 'ingreso.proveedor'])
                ->where('id_proyecto', $selectedProyectoId)
                ->where('id_material', $selectedMaterialId)
                ->get()
                ->map(function ($det) {
                    return [
                        'fecha' => $det->created_at->format('Y-m-d H:i'),
                        'timestamp' => $det->created_at->timestamp,
                        'tipo' => 'INGRESO',
                        'documento' => 'Ticket ' . ($det->ingreso->nro_ticket ?? 'S/N'),
                        'auxiliar' => $det->ingreso->proveedor->razon_social ?? 'S/P',
                        'cantidad_in' => (float) $det->cantidad_adquirida,
                        'cantidad_out' => 0,
                    ];
                });

            // 3. Fetch all egress events linked to these batches
            $batchIds = $lotes->pluck('id')->toArray();
            $salidas = DetalleSalida::with(['salida.usuario', 'salida.funcionario'])
                ->whereIn('id_detalle_ingreso', $batchIds)
                ->get()
                ->map(function ($det) {
                    return [
                        'fecha' => $det->created_at->format('Y-m-d H:i'),
                        'timestamp' => $det->created_at->timestamp,
                        'tipo' => 'DESPACHO',
                        'documento' => 'Uso: ' . ($det->salida->uso ?? 'S/D'),
                        'auxiliar' => $det->salida->funcionario->nombre ?? 'S/F',
                        'cantidad_in' => 0,
                        'cantidad_out' => (float) $det->cantidad_salida,
                    ];
                });

            // 4. Merge events chronologically to compute running balance
            $kardex = $ingresos->concat($salidas)
                ->sortBy('timestamp')
                ->values();

            $runningBalance = 0;
            $kardex = $kardex->map(function ($item) use (&$runningBalance) {
                if ($item['tipo'] === 'INGRESO') {
                    $runningBalance += $item['cantidad_in'];
                } else {
                    $runningBalance -= $item['cantidad_out'];
                }
                $item['saldo'] = $runningBalance;
                return $item;
            });
            
            // 5. Apply date range filters on the computed ledger so the running balance remains historically correct
            if ($fechaDesde) {
                $kardex = $kardex->filter(function ($item) use ($fechaDesde) {
                    return substr($item['fecha'], 0, 10) >= $fechaDesde;
                });
            }
            if ($fechaHasta) {
                $kardex = $kardex->filter(function ($item) use ($fechaHasta) {
                    return substr($item['fecha'], 0, 10) <= $fechaHasta;
                });
            }

            // Reverse to display newest first in the ledger table
            $kardex = $kardex->reverse()->values();
        }

        return Inertia::render('Reportes/Kardex', [
            'proyectos' => $proyectos,
            'materials' => $materials,
            'lotes' => $lotes,
            'kardex' => $kardex,
            'filtros' => [
                'id_proyecto' => $selectedProyectoId ? (int) $selectedProyectoId : null,
                'id_material' => $selectedMaterialId ? (int) $selectedMaterialId : null,
                'fecha_desde' => $fechaDesde,
                'fecha_hasta' => $fechaHasta,
            ]
        ]);
    }
}
