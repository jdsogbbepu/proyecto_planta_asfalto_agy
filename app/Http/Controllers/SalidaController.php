<?php

namespace App\Http\Controllers;

use App\Models\Salida;
use App\Models\DetalleSalida;
use App\Models\DetalleIngreso;
use App\Models\Funcionario;
use App\Models\Proyecto;
use App\Models\Material;
use App\Models\BitacoraActividad;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class SalidaController extends Controller
{
    public function index()
    {
        $salidas = Salida::with([
            'funcionario',
            'proyecto',
            'usuario',
            'detalles.detalleIngreso.material.medida',
            'detalles.detalleIngreso.proveedor',
        ])
            ->latest()
            ->get()
            ->map(function ($salida) {
                $lotes = $salida->detalles->map(function ($detalleSalida) {
                    $lote = $detalleSalida->detalleIngreso;
                    $cantidadUtilizada = (float) $lote->detallesSalida->sum('cantidad_salida');
                    $stockPlanta = (float) $lote->cantidad_adquirida - $cantidadUtilizada;
                    $estadoLote = $stockPlanta <= 0 ? 'AGOTADO' : ($stockPlanta < (float) $lote->cantidad_adquirida ? 'PARCIAL' : 'COMPLETO');

                    return [
                        'id' => $lote->id,
                        'nro_registro' => $lote->nro_registro,
                        'id_material' => $lote->id_material,
                        'material_nombre' => $lote->material?->nombre,
                        'unidad' => $lote->material?->medida?->abreviacion,
                        'id_proyecto' => $lote->id_proyecto,
                        'proyecto_nombre' => $lote->proyecto?->nombre,
                        'fecha_lote' => $lote->fecha_lote?->format('Y-m-d'),
                        'odc' => $lote->ingreso?->odc,
                        'cantidad_adquirida' => (float) $lote->cantidad_adquirida,
                        'cantidad_utilizada' => $cantidadUtilizada,
                        'stock_planta' => $stockPlanta,
                        'estado_lote' => $estadoLote,
                        'acciones_planificadas' => $lote->acciones_planificadas,
                        'proveedor_nombre' => $lote->proveedor?->razon_social,
                        'cantidad_salida' => (float) $detalleSalida->cantidad_salida,
                    ];
                });

                return [
                    'id' => $salida->id,
                    'odc' => $salida->odc,
                    'fecha_salida' => $salida->fecha_salida,
                    'uso' => $salida->uso,
                    'observaciones' => $salida->observaciones,
                    'registrado_por' => $salida->usuario?->name,
                    'fecha_registro' => $salida->created_at?->format('Y-m-d H:i'),
                    'funcionario_nombre' => $salida->funcionario?->nombre,
                    'funcionario_cargo' => $salida->funcionario?->cargo,
                    'proyecto_nombre' => $salida->proyecto?->nombre,
                    'lotes' => $lotes,
                    'nro_lotes' => $lotes->count(),
                ];
            });

        return Inertia::render('Salidas/Index', [
            'salidas' => $salidas,
        ]);
    }

    public function create()
    {
        return Inertia::render('Salidas/Create', [
            'funcionarios' => Funcionario::where('activo', true)->orderBy('nombre')->get(),
            'proyectos' => Proyecto::where('estado', 'activo')->orderBy('nombre')->get(),
            'materials' => Material::with('medida')->orderBy('nombre')->get(),
        ]);
    }

    public function getLotesPorProyecto(Request $request)
    {
        $request->validate(['id_proyecto' => 'required|exists:proyectos,id']);

        $lotes = DetalleIngreso::with(['material.medida', 'proveedor', 'proyecto'])
            ->where('id_proyecto', $request->id_proyecto)
            ->where('cantidad_actual_lote', '>', 0)
            ->orderBy('created_at', 'asc')
            ->get()
            ->map(function ($lote) {
                $cantidadUtilizada = (float) $lote->detallesSalida->sum('cantidad_salida');
                $stockPlanta = (float) $lote->cantidad_actual_lote;

                return [
                    'id' => $lote->id,
                    'nro_registro' => $lote->nro_registro,
                    'id_material' => $lote->id_material,
                    'material_nombre' => $lote->material?->nombre,
                    'unidad' => $lote->material?->medida?->abreviacion,
                    'id_proveedor' => $lote->id_proveedor,
                    'proveedor_nombre' => $lote->proveedor?->razon_social,
                    'fecha_lote' => $lote->fecha_lote?->format('Y-m-d'),
                    'cantidad_adquirida' => (float) $lote->cantidad_adquirida,
                    'stock_disponible' => $stockPlanta,
                    'cantidad_utilizada' => $cantidadUtilizada,
                    'stock_planta' => $stockPlanta,
                    'estado_lote' => $stockPlanta <= 0 ? 'AGOTADO' : ($stockPlanta < (float) $lote->cantidad_adquirida ? 'PARCIAL' : 'COMPLETO'),
                    'acciones_planificadas' => $lote->acciones_planificadas,
                ];
            });

        return response()->json($lotes);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_funcionario' => ['required', 'exists:funcionarios,id'],
            'id_proyecto' => ['required', 'exists:proyectos,id'],
            'odc' => ['nullable', 'string', 'max:50', 'regex:/^[A-Z0-9\/]+$/'],
            'uso' => ['nullable', 'string', 'max:255'],
            'observaciones' => ['nullable', 'string'],
            'fecha_salida' => ['required', 'date'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.id_detalle_ingreso' => ['required', 'exists:detalle_ingresos,id'],
            'items.*.cantidad_salida' => ['required', 'numeric', 'gt:0'],
            'items.*.acciones_planificadas' => ['nullable', 'string', 'max:255'],
        ]);

        DB::beginTransaction();

        try {
            $salida = Salida::create([
                'id_funcionario' => $validated['id_funcionario'],
                'id_proyecto' => $validated['id_proyecto'],
                'id_usuario' => auth()->id(),
                'odc' => $validated['odc'] ?? null,
                'uso' => $validated['uso'] ?? null,
                'fecha_salida' => $validated['fecha_salida'],
            ]);

            $resumenItems = [];

            foreach ($validated['items'] as $item) {
                $lote = DetalleIngreso::lockForUpdate()->find($item['id_detalle_ingreso']);

                if (!$lote) {
                    throw new \Exception("Lote no encontrado.");
                }

                $cantSolicitada = (float) $item['cantidad_salida'];

                if ($cantSolicitada > (float) $lote->cantidad_actual_lote) {
                    throw new \Exception("Stock insuficiente para lote {$lote->nro_registro}. Solicitado: {$cantSolicitada}, Disponible: {$lote->cantidad_actual_lote}");
                }

                DetalleSalida::create([
                    'id_salida' => $salida->id,
                    'id_detalle_ingreso' => $lote->id,
                    'cantidad_salida' => $cantSolicitada,
                ]);

                $lote->update([
                    'cantidad_actual_lote' => (float) $lote->cantidad_actual_lote - $cantSolicitada,
                    'acciones_planificadas' => $item['acciones_planificadas'] ?? null,
                ]);

                $resumenItems[] = "{$lote->nro_registro}: {$cantSolicitada} {$lote->material?->nombre}";
            }

            BitacoraActividad::create([
                'id_usuario' => Auth::id(),
                'accion' => 'Registro de Despacho (PEPS)',
                'detalle' => "Despacho ID {$salida->id}, ODC: " . ($validated['odc'] ?? 'N/A') . ". " . implode('; ', $resumenItems),
                'ip_address' => $request->ip(),
            ]);

            DB::commit();

            return redirect()->route('despachos.index')->with('success', 'Despacho registrado correctamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Error al procesar el despacho: ' . $e->getMessage())->withInput();
        }
    }

    public function destroy(Request $request, Salida $salida)
    {
        DB::beginTransaction();

        try {
            foreach ($salida->detalles as $detalleSalida) {
                $lote = DetalleIngreso::find($detalleSalida->id_detalle_ingreso);
                if ($lote) {
                    $lote->update([
                        'cantidad_actual_lote' => (float) $lote->cantidad_actual_lote + (float) $detalleSalida->cantidad_salida,
                    ]);
                }
            }

            $lotesInfo = $salida->detalles->map(fn($d) => $d->detalleIngreso?->nro_registro)->toArray();

            $salida->delete();

            BitacoraActividad::create([
                'id_usuario' => Auth::id(),
                'accion' => 'Reversión de Despacho',
                'detalle' => 'Se eliminó despacho ID ' . $salida->id . '. Lotes afectados: ' . implode(', ', $lotesInfo),
                'ip_address' => $request->ip(),
            ]);

            DB::commit();
            return redirect()->route('despachos.index')->with('success', 'Despacho revertido. Los saldos fueron restaurados.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('despachos.index')->with('error', 'Error al revertir: ' . $e->getMessage());
        }
    }
}