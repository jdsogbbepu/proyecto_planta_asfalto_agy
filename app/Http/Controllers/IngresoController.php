<?php

namespace App\Http\Controllers;

use App\Models\Ingreso;
use App\Models\DetalleIngreso;
use App\Models\Material;
use App\Models\Proveedor;
use App\Models\Proyecto;
use App\Models\Funcionario;
use App\Models\BitacoraActividad;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class IngresoController extends Controller
{
    public function index()
    {
        $ingresos = Ingreso::with([
            'usuario',
            'funcionario',
            'detalles.material.medida',
            'detalles.proveedor',
            'detalles.proyecto',
            'detalles.detallesSalida',
        ])
            ->latest()
            ->get()
            ->map(function ($ingreso) {
                $lotes = $ingreso->detalles->map(function ($lote) {
                    $cantidadUtilizada = (float) $lote->detallesSalida->sum('cantidad_salida');
                    $stockPlanta = (float) $lote->cantidad_adquirida - $cantidadUtilizada;
                    $tieneConsumo = $cantidadUtilizada > 0;
                    $estadoLote = !$tieneConsumo ? 'INCOMPLETO' : ($stockPlanta <= 0 ? 'AGOTADO' : ($stockPlanta < (float) $lote->cantidad_adquirida ? 'PARCIAL' : 'COMPLETO'));

                    return [
                        'id' => $lote->id,
                        'nro_registro' => $lote->nro_registro,
                        'id_proveedor' => $lote->id_proveedor,
                        'proveedor_nombre' => $lote->proveedor?->razon_social,
                        'fecha_lote' => $lote->fecha_lote?->format('Y-m-d'),
                        'odc' => $lote->ingreso?->odc,
                        'id_material' => $lote->id_material,
                        'material_nombre' => $lote->material?->nombre,
                        'unidad' => $lote->material?->medida?->abreviacion,
                        'id_proyecto' => $lote->id_proyecto,
                        'proyecto_nombre' => $lote->proyecto?->nombre,
                        'cantidad_adquirida' => (float) $lote->cantidad_adquirida,
                        'cantidad_utilizada' => $cantidadUtilizada,
                        'stock_planta' => $stockPlanta,
                        'tiene_consumo' => $tieneConsumo,
                        'estado_lote' => $estadoLote,
                        'acciones_planificadas' => $lote->acciones_planificadas,
                    ];
                });

                return [
                    'id' => $ingreso->id,
                    'odc' => $ingreso->odc,
                    'fecha_adquirida' => $ingreso->fecha_adquirida,
                    'observaciones' => $ingreso->observaciones,
                    'registrado_por' => $ingreso->usuario?->name,
                    'fecha_registro' => $ingreso->created_at?->format('Y-m-d H:i'),
                    'funcionario_nombre' => $ingreso->funcionario?->nombre,
                    'lotes' => $lotes,
                    'nro_lotes' => $lotes->count(),
                    'proyecto_nombre' => $lotes->first()['proyecto_nombre'] ?? null,
                    'proveedor_nombre' => $lotes->first()['proveedor_nombre'] ?? null,
                ];
            });

        return Inertia::render('Ingresos/Index', [
            'ingresos' => $ingresos,
        ]);
    }

    public function create()
    {
        return Inertia::render('Ingresos/Create', [
            'materials' => Material::with('medida')->orderBy('nombre')->get(),
            'proveedores' => Proveedor::orderBy('razon_social')->get(),
            'proyectos' => Proyecto::where('estado', 'activo')->orderBy('nombre')->get(),
            'funcionarios' => Funcionario::where('activo', true)->orderBy('nombre')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_funcionario' => ['nullable', 'exists:funcionarios,id'],
            'odc' => ['nullable', 'string', 'max:50'],
            'observaciones' => ['nullable', 'string'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.id_material' => ['required', 'exists:materials,id'],
            'items.*.id_proyecto' => ['required', 'exists:proyectos,id'],
            'items.*.id_proveedor' => ['required', 'exists:proveedors,id'],
            'items.*.fecha_lote' => ['required', 'date'],
            'items.*.cantidad' => ['required', 'numeric', 'gt:0'],
        ]);

        DB::beginTransaction();

        try {
            $ingreso = Ingreso::create([
                'id_funcionario' => $validated['id_funcionario'] ?? null,
                'odc' => $validated['odc'] ?? null,
                'id_usuario' => auth()->id(),
                'observaciones' => $validated['observaciones'] ?? null,
            ]);

            $resumenItems = [];
            $anio = date('Y');

            foreach ($validated['items'] as $item) {
                $nroRegistro = DetalleIngreso::generarNroRegistro($anio);

                $lote = DetalleIngreso::create([
                    'id_ingreso' => $ingreso->id,
                    'nro_registro' => $nroRegistro,
                    'id_material' => $item['id_material'],
                    'id_proyecto' => $item['id_proyecto'],
                    'id_proveedor' => $item['id_proveedor'],
                    'fecha_lote' => $item['fecha_lote'],
                    'cantidad_adquirida' => $item['cantidad'],
                    'cantidad_actual_lote' => $item['cantidad'],
                ]);

                $material = Material::find($item['id_material']);
                $proyecto = Proyecto::find($item['id_proyecto']);
                $resumenItems[] = "{$nroRegistro}: {$item['cantidad']} {$material->nombre} para {$proyecto->nombre}";
            }

            BitacoraActividad::create([
                'id_usuario' => Auth::id(),
                'accion' => 'Registro de Ingreso (Lote)',
                'detalle' => "Se registró ingreso con " . count($resumenItems) . " lote(s). ODC: " . ($validated['odc'] ?? 'N/A') . ". " . implode('; ', $resumenItems),
                'ip_address' => $request->ip(),
            ]);

            DB::commit();

            return redirect()->route('ingresos.index')->with('success', 'Lote(s) registrado(s) correctamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Error al procesar el registro: ' . $e->getMessage())->withInput();
        }
    }

    public function destroy(Request $request, Ingreso $ingreso)
    {
        DB::beginTransaction();
        try {
            foreach ($ingreso->detalles as $detalle) {
                if ($detalle->cantidad_actual_lote < $detalle->cantidad_adquirida) {
                    return redirect()->route('ingresos.index')
                        ->with('error', "No se puede eliminar el lote {$detalle->nro_registro} porque ya fue consumido.");
                }
            }

            $lotesInfo = $ingreso->detalles->pluck('nro_registro')->toArray();

            $ingreso->delete();

            BitacoraActividad::create([
                'id_usuario' => Auth::id(),
                'accion' => 'Eliminación de Ingreso',
                'detalle' => 'Se eliminó ingreso con lotes: ' . implode(', ', $lotesInfo),
                'ip_address' => $request->ip(),
            ]);

            DB::commit();
            return redirect()->route('ingresos.index')->with('success', 'Ingreso y lotes eliminados correctamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('ingresos.index')->with('error', 'Error al eliminar: ' . $e->getMessage());
        }
    }
}