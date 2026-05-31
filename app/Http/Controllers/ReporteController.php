<?php

namespace App\Http\Controllers;

use App\Models\Proyecto;
use App\Models\Material;
use App\Models\DetalleIngreso;
use App\Models\DetalleSalida;
use Illuminate\Http\Request;
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

        $tipoReporte = $request->input('tipo_reporte', 'material');
        $selectedProyectoId = $request->input('id_proyecto', $proyectos->first()?->id);
        $selectedMaterialId = $request->input('id_material', $materials->first()?->id);
        $fechaDesde = $request->input('fecha_desde');
        $fechaHasta = $request->input('fecha_hasta');
        $tipoMovimiento = $request->input('tipo_movimiento', 'todos');

        $materialReport = $this->buildMaterialReport($selectedProyectoId, $selectedMaterialId, $fechaDesde, $fechaHasta);
        $projectReport = $this->buildProjectReport($selectedProyectoId, $fechaDesde, $fechaHasta);
        $dateReport = $this->buildDateReport($fechaDesde, $fechaHasta, $tipoMovimiento);

        return Inertia::render('Reportes/Kardex', [
            'proyectos' => $proyectos,
            'materials' => $materials,
            'lotes' => $materialReport['lotes'],
            'kardex' => $materialReport['kardex'],
            'reporteProyecto' => $projectReport,
            'reporteFechas' => $dateReport,
            'filtros' => [
                'tipo_reporte' => $tipoReporte,
                'id_proyecto' => $selectedProyectoId ? (int) $selectedProyectoId : null,
                'id_material' => $selectedMaterialId ? (int) $selectedMaterialId : null,
                'fecha_desde' => $fechaDesde,
                'fecha_hasta' => $fechaHasta,
                'tipo_movimiento' => $tipoMovimiento,
            ]
        ]);
    }

    private function buildMaterialReport($selectedProyectoId, $selectedMaterialId, $fechaDesde, $fechaHasta): array
    {
        $lotes = collect();
        $kardex = collect();

        if ($selectedProyectoId && $selectedMaterialId) {
            $lotes = DetalleIngreso::with(['proveedor'])
                ->where('id_proyecto', $selectedProyectoId)
                ->where('id_material', $selectedMaterialId)
                ->orderBy('created_at', 'asc')
                ->get()
                ->map(function ($lote) {
                    return [
                        'id' => $lote->id,
                        'nro_registro' => $lote->nro_registro,
                        'fecha_lote' => $lote->fecha_lote?->format('Y-m-d'),
                        'cantidad_adquirida' => (float) $lote->cantidad_adquirida,
                        'cantidad_actual_lote' => (float) $lote->cantidad_actual_lote,
                        'proveedor_nombre' => $lote->proveedor?->razon_social,
                        'acciones_planificadas' => $lote->acciones_planificadas,
                    ];
                });

            $ingresos = DetalleIngreso::with(['proveedor'])
                ->where('id_proyecto', $selectedProyectoId)
                ->where('id_material', $selectedMaterialId)
                ->get()
                ->map(function ($det) {
                    return [
                        'fecha' => $det->created_at->format('Y-m-d H:i'),
                        'timestamp' => $det->created_at->timestamp,
                        'tipo' => 'INGRESO',
                        'documento' => $det->nro_registro ?? 'S/N',
                        'auxiliar' => $det->proveedor?->razon_social ?? 'S/P',
                        'cantidad_in' => (float) $det->cantidad_adquirida,
                        'cantidad_out' => 0,
                    ];
                });

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

            $kardex = $kardex->reverse()->values();
        }

        return [
            'lotes' => $lotes,
            'kardex' => $kardex,
        ];
    }

    private function buildProjectReport($selectedProyectoId, $fechaDesde, $fechaHasta): array
    {
        $proyecto = $selectedProyectoId ? Proyecto::find($selectedProyectoId) : null;

        if (!$proyecto) {
            return [
                'proyecto' => null,
                'resumen' => $this->emptySummary(),
                'materiales' => [],
                'lotes' => [],
                'despachos' => [],
            ];
        }

        $lotesQuery = DetalleIngreso::with(['ingreso', 'material.medida', 'proveedor', 'detallesSalida.salida.funcionario'])
            ->where('id_proyecto', $proyecto->id);

        if ($fechaDesde) {
            $lotesQuery->whereDate('created_at', '>=', $fechaDesde);
        }
        if ($fechaHasta) {
            $lotesQuery->whereDate('created_at', '<=', $fechaHasta);
        }

        $lotesCollection = $lotesQuery->orderBy('created_at', 'asc')->get();

        $materiales = $lotesCollection
            ->groupBy('id_material')
            ->map(function ($lotesMaterial) {
                $material = $lotesMaterial->first()->material;
                $cantidadAdquirida = (float) $lotesMaterial->sum('cantidad_adquirida');
                $stockActual = (float) $lotesMaterial->sum('cantidad_actual_lote');
                $cantidadUtilizada = $cantidadAdquirida - $stockActual;

                return [
                    'material_id' => $material?->id,
                    'material_nombre' => $material?->nombre,
                    'codigo_interno' => $material?->codigo_interno,
                    'unidad' => $material?->medida?->abreviacion,
                    'cantidad_adquirida' => $cantidadAdquirida,
                    'cantidad_utilizada' => $cantidadUtilizada,
                    'stock_actual' => $stockActual,
                    'lotes_count' => $lotesMaterial->count(),
                ];
            })
            ->sortBy('material_nombre')
            ->values();

        $lotes = $lotesCollection->map(function ($lote) {
            $cantidadUtilizada = (float) $lote->detallesSalida->sum('cantidad_salida');

            return [
                'id' => $lote->id,
                'nro_registro' => $lote->nro_registro,
                'fecha_lote' => $lote->fecha_lote?->format('Y-m-d'),
                'fecha_adquisicion' => $lote->fecha_lote?->format('Y-m-d') ?? $lote->ingreso?->fecha_adquirida,
                'odc' => $lote->ingreso?->odc,
                'material_nombre' => $lote->material?->nombre,
                'codigo_interno' => $lote->material?->codigo_interno,
                'unidad' => $lote->material?->medida?->abreviacion,
                'proveedor_nombre' => $lote->proveedor?->razon_social,
                'cantidad_adquirida' => (float) $lote->cantidad_adquirida,
                'cantidad_utilizada' => $cantidadUtilizada,
                'stock_actual' => (float) $lote->cantidad_actual_lote,
                'acciones_planificadas' => $lote->acciones_planificadas,
            ];
        });

        $loteIds = $lotesCollection->pluck('id')->toArray();
        $despachos = DetalleSalida::with(['salida.funcionario', 'detalleIngreso.material.medida'])
            ->whereIn('id_detalle_ingreso', $loteIds)
            ->when($fechaDesde, fn($query) => $query->whereDate('created_at', '>=', $fechaDesde))
            ->when($fechaHasta, fn($query) => $query->whereDate('created_at', '<=', $fechaHasta))
            ->orderBy('created_at', 'asc')
            ->get()
            ->map(function ($detalle) {
                return [
                    'fecha' => $detalle->salida?->fecha_salida ?? $detalle->created_at?->format('Y-m-d'),
                    'material_nombre' => $detalle->detalleIngreso?->material?->nombre,
                    'unidad' => $detalle->detalleIngreso?->material?->medida?->abreviacion,
                    'nro_registro' => $detalle->detalleIngreso?->nro_registro,
                    'funcionario_nombre' => $detalle->salida?->funcionario?->nombre,
                    'uso' => $detalle->salida?->uso,
                    'cantidad_salida' => (float) $detalle->cantidad_salida,
                ];
            });

        return [
            'proyecto' => [
                'id' => $proyecto->id,
                'nombre' => $proyecto->nombre,
                'ubicacion' => $proyecto->ubicacion,
                'encargado' => $proyecto->encargado,
                'fecha' => $proyecto->fecha,
                'estado' => $proyecto->estado,
            ],
            'resumen' => [
                'materiales_count' => $materiales->count(),
                'lotes_count' => $lotes->count(),
                'despachos_count' => $despachos->count(),
                'total_adquirido' => (float) $materiales->sum('cantidad_adquirida'),
                'total_utilizado' => (float) $materiales->sum('cantidad_utilizada'),
                'stock_actual' => (float) $materiales->sum('stock_actual'),
            ],
            'materiales' => $materiales,
            'lotes' => $lotes,
            'despachos' => $despachos,
        ];
    }

    private function buildDateReport($fechaDesde, $fechaHasta, string $tipoMovimiento): array
    {
        $ingresos = collect();
        $salidas = collect();

        if (in_array($tipoMovimiento, ['todos', 'ingresos'], true)) {
            $ingresos = DetalleIngreso::with(['ingreso', 'proyecto', 'material.medida', 'proveedor', 'detallesSalida'])
                ->when($fechaDesde, fn($query) => $query->whereDate('created_at', '>=', $fechaDesde))
                ->when($fechaHasta, fn($query) => $query->whereDate('created_at', '<=', $fechaHasta))
                ->get()
                ->map(function ($detalle) {
                    $cantidadUtilizada = (float) $detalle->detallesSalida->sum('cantidad_salida');

                    return [
                        'timestamp' => $detalle->created_at?->timestamp,
                        'fecha' => $detalle->created_at?->format('Y-m-d H:i'),
                        'fecha_adquisicion' => $detalle->fecha_lote?->format('Y-m-d') ?? $detalle->ingreso?->fecha_adquirida,
                        'tipo' => 'INGRESO',
                        'proyecto_id' => $detalle->id_proyecto,
                        'proyecto_nombre' => $detalle->proyecto?->nombre,
                        'material_id' => $detalle->id_material,
                        'material_nombre' => $detalle->material?->nombre,
                        'unidad' => $detalle->material?->medida?->abreviacion,
                        'nro_registro' => $detalle->nro_registro,
                        'odc' => $detalle->ingreso?->odc,
                        'auxiliar' => $detalle->proveedor?->razon_social,
                        'entrada' => (float) $detalle->cantidad_adquirida,
                        'salida' => 0,
                        'cantidad_adquirida' => (float) $detalle->cantidad_adquirida,
                        'cantidad_utilizada' => $cantidadUtilizada,
                        'stock_actual' => (float) $detalle->cantidad_actual_lote,
                        'acciones_planificadas' => $detalle->acciones_planificadas,
                    ];
                });
        }

        if (in_array($tipoMovimiento, ['todos', 'despachos'], true)) {
            $salidas = DetalleSalida::with(['salida.proyecto', 'salida.funcionario', 'detalleIngreso.ingreso', 'detalleIngreso.material.medida'])
                ->when($fechaDesde, fn($query) => $query->whereDate('created_at', '>=', $fechaDesde))
                ->when($fechaHasta, fn($query) => $query->whereDate('created_at', '<=', $fechaHasta))
                ->get()
                ->map(function ($detalle) {
                    $lote = $detalle->detalleIngreso;

                    return [
                        'timestamp' => $detalle->created_at?->timestamp,
                        'fecha' => $detalle->created_at?->format('Y-m-d H:i'),
                        'fecha_adquisicion' => $lote?->fecha_lote?->format('Y-m-d') ?? $lote?->ingreso?->fecha_adquirida,
                        'tipo' => 'DESPACHO',
                        'proyecto_id' => $detalle->salida?->id_proyecto,
                        'proyecto_nombre' => $detalle->salida?->proyecto?->nombre,
                        'material_id' => $lote?->id_material,
                        'material_nombre' => $lote?->material?->nombre,
                        'unidad' => $lote?->material?->medida?->abreviacion,
                        'nro_registro' => $lote?->nro_registro,
                        'odc' => $detalle->salida?->odc ?? $lote?->ingreso?->odc,
                        'auxiliar' => $detalle->salida?->funcionario?->nombre,
                        'entrada' => 0,
                        'salida' => (float) $detalle->cantidad_salida,
                        'cantidad_adquirida' => (float) ($lote?->cantidad_adquirida ?? 0),
                        'cantidad_utilizada' => (float) $detalle->cantidad_salida,
                        'stock_actual' => (float) ($lote?->cantidad_actual_lote ?? 0),
                        'acciones_planificadas' => $lote?->acciones_planificadas ?? $detalle->salida?->uso,
                    ];
                });
        }

        $movimientos = $ingresos->concat($salidas)
            ->sortBy('timestamp')
            ->values();

        $agrupado = $movimientos
            ->groupBy('proyecto_id')
            ->map(function ($movimientosProyecto) {
                return [
                    'proyecto_id' => $movimientosProyecto->first()['proyecto_id'],
                    'proyecto_nombre' => $movimientosProyecto->first()['proyecto_nombre'],
                    'materiales' => $movimientosProyecto
                        ->groupBy('material_id')
                        ->map(function ($movimientosMaterial) {
                            return [
                                'material_id' => $movimientosMaterial->first()['material_id'],
                                'material_nombre' => $movimientosMaterial->first()['material_nombre'],
                                'unidad' => $movimientosMaterial->first()['unidad'],
                                'entradas' => (float) $movimientosMaterial->sum('entrada'),
                                'salidas' => (float) $movimientosMaterial->sum('salida'),
                                'saldo_periodo' => (float) $movimientosMaterial->sum('entrada') - (float) $movimientosMaterial->sum('salida'),
                            ];
                        })
                        ->sortBy('material_nombre')
                        ->values(),
                ];
            })
            ->sortBy('proyecto_nombre')
            ->values();

        return [
            'resumen' => [
                'proyectos_count' => $agrupado->count(),
                'movimientos_count' => $movimientos->count(),
                'ingresos_count' => $movimientos->where('tipo', 'INGRESO')->count(),
                'despachos_count' => $movimientos->where('tipo', 'DESPACHO')->count(),
                'total_entradas' => (float) $movimientos->sum('entrada'),
                'total_salidas' => (float) $movimientos->sum('salida'),
            ],
            'agrupado' => $agrupado,
            'movimientos' => $movimientos->reverse()->values(),
        ];
    }

    private function emptySummary(): array
    {
        return [
            'materiales_count' => 0,
            'lotes_count' => 0,
            'despachos_count' => 0,
            'total_adquirido' => 0,
            'total_utilizado' => 0,
            'stock_actual' => 0,
        ];
    }
}
