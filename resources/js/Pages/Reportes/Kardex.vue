<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

const props = defineProps({
    proyectos: { type: Array, required: true },
    materials: { type: Array, required: true },
    lotes: { type: Array, required: true },
    kardex: { type: Array, required: true },
    reporteProyecto: { type: Object, required: true },
    reporteFechas: { type: Object, required: true },
    filtros: { type: Object, required: true },
});

const activeReport = ref(props.filtros.tipo_reporte || 'material');
const selectedProyecto = ref(props.filtros.id_proyecto || '');
const selectedMaterial = ref(props.filtros.id_material || '');
const fechaDesde = ref(props.filtros.fecha_desde || '');
const fechaHasta = ref(props.filtros.fecha_hasta || '');
const tipoMovimiento = ref(props.filtros.tipo_movimiento || 'todos');
const printOrientation = ref('landscape');

const reportTabs = [
    { id: 'material', label: 'Materiales', title: 'Kardex por Material' },
    { id: 'proyecto', label: 'Proyectos', title: 'Reporte por Proyecto' },
    { id: 'fechas', label: 'Fechas', title: 'Reporte por Fechas' },
];

const setReport = (report) => {
    activeReport.value = report;
    applyFilters();
};

const applyFilters = () => {
    router.get(route('kardex.index'), {
        tipo_reporte: activeReport.value,
        id_proyecto: selectedProyecto.value || undefined,
        id_material: activeReport.value === 'material' ? selectedMaterial.value || undefined : undefined,
        fecha_desde: fechaDesde.value || undefined,
        fecha_hasta: fechaHasta.value || undefined,
        tipo_movimiento: activeReport.value === 'fechas' ? tipoMovimiento.value : undefined,
    }, {
        preserveState: true,
        preserveScroll: true,
    });
};

const clearFilters = () => {
    selectedProyecto.value = props.proyectos[0]?.id || '';
    selectedMaterial.value = props.materials[0]?.id || '';
    fechaDesde.value = '';
    fechaHasta.value = '';
    tipoMovimiento.value = 'todos';
    applyFilters();
};

const selectedProject = computed(() => props.proyectos.find(p => p.id === Number(selectedProyecto.value)));
const selectedMaterialRecord = computed(() => props.materials.find(m => m.id === Number(selectedMaterial.value)));

const reportTitle = computed(() => reportTabs.find(tab => tab.id === activeReport.value)?.title || 'Reporte');

const materialTotals = computed(() => {
    const totalIn = props.kardex.reduce((sum, item) => sum + (Number(item.cantidad_in) || 0), 0);
    const totalOut = props.kardex.reduce((sum, item) => sum + (Number(item.cantidad_out) || 0), 0);
    const lastSaldo = props.kardex.length > 0 ? Number(props.kardex[0].saldo) : 0;
    return { totalIn, totalOut, lastSaldo };
});

const canPrint = computed(() => {
    if (activeReport.value === 'material') return selectedProyecto.value && selectedMaterial.value && props.kardex.length > 0;
    if (activeReport.value === 'proyecto') return selectedProyecto.value && props.reporteProyecto?.materiales?.length > 0;
    return props.reporteFechas?.movimientos?.length > 0;
});

const printMeta = computed(() => {
    if (activeReport.value === 'material') {
        return [
            ['Proyecto / Obra', selectedProject.value?.nombre || '---'],
            ['Material / Insumo', selectedMaterialRecord.value?.nombre || '---'],
            ['Unidad', selectedMaterialRecord.value?.medida?.abreviacion || '---'],
            ['Periodo', getPeriodoLabel()],
        ];
    }

    if (activeReport.value === 'proyecto') {
        return [
            ['Proyecto / Obra', props.reporteProyecto?.proyecto?.nombre || selectedProject.value?.nombre || '---'],
            ['Encargado', props.reporteProyecto?.proyecto?.encargado || '---'],
            ['Estado', props.reporteProyecto?.proyecto?.estado || '---'],
            ['Periodo', getPeriodoLabel()],
        ];
    }

    return [
        ['Tipo de Movimiento', getTipoMovimientoLabel()],
        ['Periodo', getPeriodoLabel()],
        ['Proyectos Involucrados', props.reporteFechas?.resumen?.proyectos_count ?? 0],
        ['Movimientos', props.reporteFechas?.resumen?.movimientos_count ?? 0],
    ];
});

const getPeriodoLabel = () => {
    if (!fechaDesde.value && !fechaHasta.value) return 'Todos los registros';
    return `${fechaDesde.value || 'Inicio'} al ${fechaHasta.value || 'Actual'}`;
};

const getTipoMovimientoLabel = () => {
    const labels = {
        todos: 'Ingresos y despachos',
        ingresos: 'Solo ingresos',
        despachos: 'Solo despachos',
    };
    return labels[tipoMovimiento.value] || labels.todos;
};

const number = (value) => Number(value || 0).toLocaleString('es-BO', {
    minimumFractionDigits: 2,
    maximumFractionDigits: 2,
});

const stateLabel = (lote) => {
    if (Number(lote.stock_actual ?? lote.cantidad_actual_lote) <= 0) return 'Agotado';
    if (Number(lote.cantidad_utilizada || 0) > 0) return 'Parcial';
    return 'Completo';
};

const printBalanceRows = computed(() => {
    if (activeReport.value === 'proyecto') {
        return (props.reporteProyecto?.lotes || []).map((lote, index) => ({
            nro: index + 1,
            material: lote.material_nombre,
            unidad: lote.unidad,
            proyecto: props.reporteProyecto?.proyecto?.nombre || selectedProject.value?.nombre,
            fecha_adquisicion: lote.fecha_adquisicion || lote.fecha_lote,
            odc: lote.odc || lote.nro_registro,
            cantidad_adquirida: lote.cantidad_adquirida,
            cantidad_utilizada: lote.cantidad_utilizada,
            stock_actual: lote.stock_actual,
            acciones: lote.acciones_planificadas,
        }));
    }

    if (activeReport.value === 'fechas') {
        return (props.reporteFechas?.movimientos || []).map((mov, index) => ({
            nro: index + 1,
            material: mov.material_nombre,
            unidad: mov.unidad,
            proyecto: mov.proyecto_nombre,
            fecha_adquisicion: mov.fecha_adquisicion || String(mov.fecha || '').slice(0, 10),
            odc: mov.odc || mov.nro_registro,
            cantidad_adquirida: mov.cantidad_adquirida || mov.entrada,
            cantidad_utilizada: mov.cantidad_utilizada || mov.salida,
            stock_actual: mov.stock_actual ?? Math.max(0, Number(mov.entrada || 0) - Number(mov.salida || 0)),
            acciones: mov.acciones_planificadas || mov.auxiliar,
        }));
    }

    return [];
});

const handlePrint = () => {
    const styleId = 'print-orientation-style';
    let styleEl = document.getElementById(styleId);
    if (!styleEl) {
        styleEl = document.createElement('style');
        styleEl.id = styleId;
        document.head.appendChild(styleEl);
    }

    styleEl.textContent = `@page { size: letter ${printOrientation.value === 'landscape' ? 'landscape' : 'portrait'}; margin: 9mm 8mm; }`;
    window.print();
};
</script>

<template>
    <Head title="Reportes e Impresiones Kardex" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex flex-col gap-4 xl:flex-row xl:items-center xl:justify-between">
                <div>
                    <h2 class="text-2xl font-bold tracking-tight text-white">Reportes e Impresiones Kardex</h2>
                    <p class="mt-1 text-sm text-industrial-muted">
                        Vistas previas imprimibles para materiales, proyectos y movimientos por periodo.
                    </p>
                </div>

                <div v-if="canPrint" class="no-print flex flex-wrap items-center gap-3">
                    <div class="flex items-center gap-2 rounded-lg border border-[#2d3139] bg-[#0e1113] px-3 py-1.5">
                        <span class="text-[9px] font-bold uppercase tracking-wider text-industrial-muted">Orientacion</span>
                        <button
                            type="button"
                            @click="printOrientation = 'landscape'"
                            class="rounded px-2 py-1 text-xs font-bold transition"
                            :class="printOrientation === 'landscape' ? 'bg-[#f27b00] text-[#111417]' : 'text-industrial-muted hover:text-white'"
                        >
                            Horizontal
                        </button>
                        <button
                            type="button"
                            @click="printOrientation = 'portrait'"
                            class="rounded px-2 py-1 text-xs font-bold transition"
                            :class="printOrientation === 'portrait' ? 'bg-[#f27b00] text-[#111417]' : 'text-industrial-muted hover:text-white'"
                        >
                            Vertical
                        </button>
                    </div>

                    <button
                        type="button"
                        @click="handlePrint"
                        class="inline-flex items-center gap-2 rounded-lg bg-[#0a5c8f] px-4 py-2 text-xs font-bold uppercase tracking-wider text-white transition hover:bg-[#0d6faa]"
                    >
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                        </svg>
                        Imprimir / PDF
                    </button>
                </div>
            </div>
        </template>

        <div class="mx-auto max-w-screen-2xl px-4 py-6 font-sans sm:px-6 lg:px-8">
            <section class="no-print mb-6 rounded-xl border border-[#2d3139] bg-[#1b1e22] p-2 shadow-xl">
                <div class="grid gap-2 md:grid-cols-3">
                    <button
                        v-for="tab in reportTabs"
                        :key="tab.id"
                        type="button"
                        @click="setReport(tab.id)"
                        class="rounded-lg px-4 py-3 text-left transition"
                        :class="activeReport === tab.id ? 'bg-[#f27b00] text-[#111417]' : 'bg-[#0e1113] text-industrial-muted hover:text-white'"
                    >
                        <span class="block text-xs font-black uppercase tracking-wider">{{ tab.label }}</span>
                        <span class="mt-1 block text-sm font-semibold">{{ tab.title }}</span>
                    </button>
                </div>
            </section>

            <section class="no-print mb-6 rounded-xl border border-[#2d3139] bg-[#1b1e22] p-5 shadow-xl">
                <div class="grid grid-cols-1 items-end gap-4 lg:grid-cols-6">
                    <div v-if="activeReport !== 'fechas'" class="lg:col-span-2">
                        <label class="mb-2 block text-xs font-semibold uppercase tracking-wider text-industrial-muted">Proyecto / Obra</label>
                        <select
                            v-model="selectedProyecto"
                            @change="applyFilters"
                            class="w-full rounded-lg border border-[#2d3139] bg-[#0e1113] px-4 py-2 text-sm text-[#e1e6eb] focus:border-[#f27b00] focus:outline-none"
                        >
                            <option value="" disabled>-- Seleccione Proyecto --</option>
                            <option v-for="proj in proyectos" :key="proj.id" :value="proj.id">{{ proj.nombre }}</option>
                        </select>
                    </div>

                    <div v-if="activeReport === 'material'" class="lg:col-span-2">
                        <label class="mb-2 block text-xs font-semibold uppercase tracking-wider text-industrial-muted">Material / Insumo</label>
                        <select
                            v-model="selectedMaterial"
                            @change="applyFilters"
                            class="w-full rounded-lg border border-[#2d3139] bg-[#0e1113] px-4 py-2 text-sm text-[#e1e6eb] focus:border-[#f27b00] focus:outline-none"
                        >
                            <option value="" disabled>-- Seleccione Material --</option>
                            <option v-for="mat in materials" :key="mat.id" :value="mat.id">{{ mat.nombre }}</option>
                        </select>
                    </div>

                    <div v-if="activeReport === 'fechas'" class="lg:col-span-2">
                        <label class="mb-2 block text-xs font-semibold uppercase tracking-wider text-industrial-muted">Tipo de Movimiento</label>
                        <select
                            v-model="tipoMovimiento"
                            @change="applyFilters"
                            class="w-full rounded-lg border border-[#2d3139] bg-[#0e1113] px-4 py-2 text-sm text-[#e1e6eb] focus:border-[#f27b00] focus:outline-none"
                        >
                            <option value="todos">Ingresos y despachos</option>
                            <option value="ingresos">Solo ingresos</option>
                            <option value="despachos">Solo despachos</option>
                        </select>
                    </div>

                    <div>
                        <label class="mb-2 block text-xs font-semibold uppercase tracking-wider text-industrial-muted">Fecha Desde</label>
                        <input
                            v-model="fechaDesde"
                            type="date"
                            class="w-full rounded-lg border border-[#2d3139] bg-[#0e1113] px-4 py-2 font-mono text-sm text-[#e1e6eb] focus:border-[#f27b00] focus:outline-none"
                        />
                    </div>

                    <div>
                        <label class="mb-2 block text-xs font-semibold uppercase tracking-wider text-industrial-muted">Fecha Hasta</label>
                        <input
                            v-model="fechaHasta"
                            type="date"
                            class="w-full rounded-lg border border-[#2d3139] bg-[#0e1113] px-4 py-2 font-mono text-sm text-[#e1e6eb] focus:border-[#f27b00] focus:outline-none"
                        />
                    </div>

                    <div class="flex gap-2">
                        <button
                            type="button"
                            @click="applyFilters"
                            class="flex-1 rounded-lg bg-[#f27b00] px-4 py-2 text-sm font-bold uppercase tracking-wider text-[#111417] transition hover:bg-[#ff9426]"
                        >
                            Generar
                        </button>
                        <button
                            type="button"
                            @click="clearFilters"
                            class="rounded-lg bg-[#2d3139] px-4 py-2 text-sm font-semibold text-white transition hover:bg-[#383d47]"
                        >
                            Limpiar
                        </button>
                    </div>
                </div>
            </section>

            <section class="print-sheet">
                <div class="print-header">
                    <div class="print-header-row">
                        <div>
                            <h1>GOBIERNO AUTONOMO MUNICIPAL DE EL ALTO</h1>
                            <h2>PLANTA DE ASFALTO - CONTROL DE ALMACEN E INVENTARIO</h2>
                        </div>
                        <div class="print-doc-box">
                            <strong>{{ reportTitle }}</strong>
                            <span>Fecha de impresion: {{ new Date().toLocaleDateString('es-BO') }}</span>
                        </div>
                    </div>
                    <div class="print-meta-grid">
                        <div v-for="meta in printMeta" :key="meta[0]">
                            <strong>{{ meta[0] }}:</strong> {{ meta[1] }}
                        </div>
                    </div>
                </div>

                <template v-if="activeReport === 'material'">
                    <div v-if="selectedProyecto && selectedMaterial" class="grid grid-cols-1 gap-6 xl:grid-cols-12">
                        <aside class="no-print rounded-xl border border-[#2d3139] bg-[#1b1e22] p-5 shadow-xl xl:col-span-4">
                            <h3 class="mb-4 border-b border-[#2d3139] pb-2 text-sm font-bold uppercase tracking-wider text-white">Pila de Lotes PEPS</h3>
                            <div class="max-h-[520px] space-y-3 overflow-y-auto pr-1">
                                <article v-for="lote in lotes" :key="lote.id" class="rounded-lg border border-[#2d3139] bg-[#0e1113] p-3">
                                    <div class="mb-2 flex items-center justify-between gap-2">
                                        <span class="font-mono text-xs font-bold text-[#f27b00]">{{ lote.nro_registro || 'LOTE-' + lote.id }}</span>
                                        <span class="rounded border border-[#2d3139] px-2 py-0.5 text-[9px] font-bold uppercase text-industrial-muted">
                                            {{ Number(lote.cantidad_actual_lote) <= 0 ? 'Agotado' : (Number(lote.cantidad_actual_lote) < Number(lote.cantidad_adquirida) ? 'Parcial' : 'Completo') }}
                                        </span>
                                    </div>
                                    <div class="grid grid-cols-2 gap-2 text-[11px] text-industrial-muted">
                                        <span>Fecha: <b class="text-white">{{ lote.fecha_lote || '---' }}</b></span>
                                        <span class="truncate text-right" :title="lote.proveedor_nombre">Proveedor: <b class="text-white">{{ lote.proveedor_nombre || 'N/A' }}</b></span>
                                    </div>
                                    <div class="mt-3 flex justify-between font-mono text-[11px]">
                                        <span class="text-industrial-muted">Inicial: {{ number(lote.cantidad_adquirida) }}</span>
                                        <span class="font-bold text-white">Saldo: {{ number(lote.cantidad_actual_lote) }}</span>
                                    </div>
                                </article>
                                <div v-if="lotes.length === 0" class="rounded-lg border border-dashed border-[#2d3139] py-10 text-center text-xs text-industrial-muted">
                                    No existen lotes para esta seleccion.
                                </div>
                            </div>
                        </aside>

                        <div class="rounded-xl border border-[#2d3139] bg-[#1b1e22] p-5 shadow-xl print-main-table xl:col-span-8">
                            <h3 class="report-title">Libro de Movimientos Auxiliar</h3>
                            <div class="overflow-x-auto">
                                <table class="report-table">
                                    <thead>
                                        <tr>
                                            <th>Fecha</th>
                                            <th>Operacion</th>
                                            <th>Documento / Detalle</th>
                                            <th>Auxiliar</th>
                                            <th class="text-right">Entradas</th>
                                            <th class="text-right">Salidas</th>
                                            <th class="text-right">Saldo Fisico</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="(item, index) in kardex" :key="`${item.timestamp}-${index}`">
                                            <td class="font-mono muted">{{ item.fecha }}</td>
                                            <td class="font-bold" :class="item.tipo === 'INGRESO' ? 'screen-green' : 'screen-red'">{{ item.tipo }}</td>
                                            <td>{{ item.documento }}</td>
                                            <td>{{ item.auxiliar }}</td>
                                            <td class="text-right font-mono">{{ item.cantidad_in > 0 ? number(item.cantidad_in) : '-' }}</td>
                                            <td class="text-right font-mono">{{ item.cantidad_out > 0 ? number(item.cantidad_out) : '-' }}</td>
                                            <td class="text-right font-mono font-bold">{{ number(item.saldo) }}</td>
                                        </tr>
                                        <tr v-if="kardex.length > 0" class="totals-row">
                                            <td colspan="4" class="text-right">TOTAL</td>
                                            <td class="text-right font-mono">{{ number(materialTotals.totalIn) }}</td>
                                            <td class="text-right font-mono">{{ number(materialTotals.totalOut) }}</td>
                                            <td class="text-right font-mono">{{ number(materialTotals.lastSaldo) }}</td>
                                        </tr>
                                        <tr v-if="kardex.length === 0">
                                            <td colspan="7" class="empty-cell">No existen movimientos para esta seleccion.</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div v-else class="empty-panel">Seleccione proyecto y material para generar el Kardex.</div>
                </template>

                <template v-else-if="activeReport === 'proyecto'">
                    <div v-if="selectedProyecto" class="space-y-6">
                        <div class="balance-print-only">
                            <h3>ANEXO 1. BALANCE ACTUAL DE INSUMOS EN PLANTA</h3>
                            <table class="balance-print-table">
                                <thead>
                                    <tr>
                                        <th>Nro</th>
                                        <th>Identificacion del Material y/o Insumo</th>
                                        <th>Unidad</th>
                                        <th>Proyecto</th>
                                        <th>Fecha de Adquisicion</th>
                                        <th>ODC</th>
                                        <th>Cantidad Adquirida</th>
                                        <th>Cantidad Utilizada</th>
                                        <th>Stock en Planta</th>
                                        <th>Acciones Planificadas para su Uso</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="row in printBalanceRows" :key="`project-print-${row.nro}`">
                                        <td>{{ row.nro }}</td>
                                        <td>{{ row.material || '-' }}</td>
                                        <td>{{ row.unidad || '-' }}</td>
                                        <td>{{ row.proyecto || '-' }}</td>
                                        <td>{{ row.fecha_adquisicion || '-' }}</td>
                                        <td>{{ row.odc || '-' }}</td>
                                        <td>{{ number(row.cantidad_adquirida) }}</td>
                                        <td>{{ number(row.cantidad_utilizada) }}</td>
                                        <td>{{ number(row.stock_actual) }}</td>
                                        <td>{{ row.acciones || '-' }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div class="grid grid-cols-2 gap-3 md:grid-cols-6 no-print">
                            <div class="metric-box"><span>Materiales</span><b>{{ reporteProyecto.resumen.materiales_count }}</b></div>
                            <div class="metric-box"><span>Lotes</span><b>{{ reporteProyecto.resumen.lotes_count }}</b></div>
                            <div class="metric-box"><span>Despachos</span><b>{{ reporteProyecto.resumen.despachos_count }}</b></div>
                            <div class="metric-box"><span>Adquirido</span><b>{{ number(reporteProyecto.resumen.total_adquirido) }}</b></div>
                            <div class="metric-box"><span>Utilizado</span><b>{{ number(reporteProyecto.resumen.total_utilizado) }}</b></div>
                            <div class="metric-box"><span>Stock</span><b>{{ number(reporteProyecto.resumen.stock_actual) }}</b></div>
                        </div>

                        <div class="rounded-xl border border-[#2d3139] bg-[#1b1e22] p-5 shadow-xl print-main-table">
                            <h3 class="report-title">Resumen Consolidado por Material</h3>
                            <div class="overflow-x-auto">
                                <table class="report-table">
                                    <thead>
                                        <tr>
                                            <th>Codigo</th>
                                            <th>Material</th>
                                            <th>Unidad</th>
                                            <th class="text-right">Lotes</th>
                                            <th class="text-right">Cant. Adquirida</th>
                                            <th class="text-right">Cant. Utilizada</th>
                                            <th class="text-right">Stock Actual</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="mat in reporteProyecto.materiales" :key="mat.material_id">
                                            <td class="font-mono">{{ mat.codigo_interno || '-' }}</td>
                                            <td>{{ mat.material_nombre }}</td>
                                            <td>{{ mat.unidad }}</td>
                                            <td class="text-right font-mono">{{ mat.lotes_count }}</td>
                                            <td class="text-right font-mono">{{ number(mat.cantidad_adquirida) }}</td>
                                            <td class="text-right font-mono">{{ number(mat.cantidad_utilizada) }}</td>
                                            <td class="text-right font-mono font-bold">{{ number(mat.stock_actual) }}</td>
                                        </tr>
                                        <tr v-if="reporteProyecto.materiales.length === 0">
                                            <td colspan="7" class="empty-cell">No existen materiales para este proyecto.</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="rounded-xl border border-[#2d3139] bg-[#1b1e22] p-5 shadow-xl print-main-table">
                            <h3 class="report-title">Detalle de Lotes del Proyecto</h3>
                            <div class="overflow-x-auto">
                                <table class="report-table">
                                    <thead>
                                        <tr>
                                            <th>RGTR</th>
                                            <th>Fecha</th>
                                            <th>Material</th>
                                            <th>Proveedor</th>
                                            <th class="text-right">Adquirida</th>
                                            <th class="text-right">Utilizada</th>
                                            <th class="text-right">Stock</th>
                                            <th>Estado</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="lote in reporteProyecto.lotes" :key="lote.id">
                                            <td class="font-mono">{{ lote.nro_registro || '-' }}</td>
                                            <td class="font-mono muted">{{ lote.fecha_lote || '-' }}</td>
                                            <td>{{ lote.material_nombre }}</td>
                                            <td>{{ lote.proveedor_nombre || '-' }}</td>
                                            <td class="text-right font-mono">{{ number(lote.cantidad_adquirida) }}</td>
                                            <td class="text-right font-mono">{{ number(lote.cantidad_utilizada) }}</td>
                                            <td class="text-right font-mono font-bold">{{ number(lote.stock_actual) }}</td>
                                            <td>{{ stateLabel(lote) }}</td>
                                            <td>{{ lote.acciones_planificadas || '-' }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="rounded-xl border border-[#2d3139] bg-[#1b1e22] p-5 shadow-xl print-main-table">
                            <h3 class="report-title">Despachos Relacionados</h3>
                            <div class="overflow-x-auto">
                                <table class="report-table">
                                    <thead>
                                        <tr>
                                            <th>Fecha</th>
                                            <th>Material</th>
                                            <th>RGTR</th>
                                            <th>Funcionario</th>
                                            <th>Uso</th>
                                            <th class="text-right">Cantidad</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="(despacho, index) in reporteProyecto.despachos" :key="`${despacho.nro_registro}-${index}`">
                                            <td class="font-mono muted">{{ despacho.fecha }}</td>
                                            <td>{{ despacho.material_nombre }}</td>
                                            <td class="font-mono">{{ despacho.nro_registro || '-' }}</td>
                                            <td>{{ despacho.funcionario_nombre || '-' }}</td>
                                            <td>{{ despacho.uso || '-' }}</td>
                                            <td class="text-right font-mono">{{ number(despacho.cantidad_salida) }} {{ despacho.unidad }}</td>
                                        </tr>
                                        <tr v-if="reporteProyecto.despachos.length === 0">
                                            <td colspan="6" class="empty-cell">No existen despachos para este proyecto.</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div v-else class="empty-panel">Seleccione un proyecto para generar el reporte.</div>
                </template>

                <template v-else>
                    <div class="space-y-6">
                        <div class="balance-print-only">
                            <h3>ANEXO 1. BALANCE ACTUAL DE INSUMOS EN PLANTA</h3>
                            <table class="balance-print-table">
                                <thead>
                                    <tr>
                                        <th>Nro</th>
                                        <th>Identificacion del Material y/o Insumo</th>
                                        <th>Unidad</th>
                                        <th>Proyecto</th>
                                        <th>Fecha de Adquisicion</th>
                                        <th>ODC</th>
                                        <th>Cantidad Adquirida</th>
                                        <th>Cantidad Utilizada</th>
                                        <th>Stock en Planta</th>
                                        <th>Acciones Planificadas para su Uso</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="row in printBalanceRows" :key="`date-print-${row.nro}`">
                                        <td>{{ row.nro }}</td>
                                        <td>{{ row.material || '-' }}</td>
                                        <td>{{ row.unidad || '-' }}</td>
                                        <td>{{ row.proyecto || '-' }}</td>
                                        <td>{{ row.fecha_adquisicion || '-' }}</td>
                                        <td>{{ row.odc || '-' }}</td>
                                        <td>{{ number(row.cantidad_adquirida) }}</td>
                                        <td>{{ number(row.cantidad_utilizada) }}</td>
                                        <td>{{ number(row.stock_actual) }}</td>
                                        <td>{{ row.acciones || '-' }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div class="grid grid-cols-2 gap-3 md:grid-cols-6 no-print">
                            <div class="metric-box"><span>Proyectos</span><b>{{ reporteFechas.resumen.proyectos_count }}</b></div>
                            <div class="metric-box"><span>Movimientos</span><b>{{ reporteFechas.resumen.movimientos_count }}</b></div>
                            <div class="metric-box"><span>Ingresos</span><b>{{ reporteFechas.resumen.ingresos_count }}</b></div>
                            <div class="metric-box"><span>Despachos</span><b>{{ reporteFechas.resumen.despachos_count }}</b></div>
                            <div class="metric-box"><span>Entradas</span><b>{{ number(reporteFechas.resumen.total_entradas) }}</b></div>
                            <div class="metric-box"><span>Salidas</span><b>{{ number(reporteFechas.resumen.total_salidas) }}</b></div>
                        </div>

                        <div class="rounded-xl border border-[#2d3139] bg-[#1b1e22] p-5 shadow-xl print-main-table">
                            <h3 class="report-title">Resumen por Proyecto y Material</h3>
                            <div class="space-y-5">
                                <div v-for="grupo in reporteFechas.agrupado" :key="grupo.proyecto_id">
                                    <h4 class="group-title">{{ grupo.proyecto_nombre || 'Proyecto no identificado' }}</h4>
                                    <div class="overflow-x-auto">
                                        <table class="report-table compact">
                                            <thead>
                                                <tr>
                                                    <th>Material</th>
                                                    <th>Unidad</th>
                                                    <th class="text-right">Entradas</th>
                                                    <th class="text-right">Salidas</th>
                                                    <th class="text-right">Saldo Periodo</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr v-for="mat in grupo.materiales" :key="`${grupo.proyecto_id}-${mat.material_id}`">
                                                    <td>{{ mat.material_nombre }}</td>
                                                    <td>{{ mat.unidad }}</td>
                                                    <td class="text-right font-mono">{{ number(mat.entradas) }}</td>
                                                    <td class="text-right font-mono">{{ number(mat.salidas) }}</td>
                                                    <td class="text-right font-mono font-bold">{{ number(mat.saldo_periodo) }}</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div v-if="reporteFechas.agrupado.length === 0" class="empty-cell">No existen movimientos para el periodo seleccionado.</div>
                            </div>
                        </div>

                        <div class="rounded-xl border border-[#2d3139] bg-[#1b1e22] p-5 shadow-xl print-main-table">
                            <h3 class="report-title">Movimientos Cronologicos del Periodo</h3>
                            <div class="overflow-x-auto">
                                <table class="report-table">
                                    <thead>
                                        <tr>
                                            <th>Fecha</th>
                                            <th>Proyecto</th>
                                            <th>Tipo</th>
                                            <th>Material</th>
                                            <th>RGTR</th>
                                            <th>Proveedor / Funcionario</th>
                                            <th class="text-right">Entrada</th>
                                            <th class="text-right">Salida</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="(mov, index) in reporteFechas.movimientos" :key="`${mov.timestamp}-${index}`">
                                            <td class="font-mono muted">{{ mov.fecha }}</td>
                                            <td>{{ mov.proyecto_nombre || '-' }}</td>
                                            <td class="font-bold" :class="mov.tipo === 'INGRESO' ? 'screen-green' : 'screen-red'">{{ mov.tipo }}</td>
                                            <td>{{ mov.material_nombre || '-' }}</td>
                                            <td class="font-mono">{{ mov.nro_registro || '-' }}</td>
                                            <td>{{ mov.auxiliar || '-' }}</td>
                                            <td class="text-right font-mono">{{ mov.entrada > 0 ? number(mov.entrada) + ' ' + mov.unidad : '-' }}</td>
                                            <td class="text-right font-mono">{{ mov.salida > 0 ? number(mov.salida) + ' ' + mov.unidad : '-' }}</td>
                                        </tr>
                                        <tr v-if="reporteFechas.movimientos.length === 0">
                                            <td colspan="8" class="empty-cell">No existen movimientos para el periodo seleccionado.</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </template>

                <div class="print-footer">
                    <div class="print-signatures">
                        <div><span></span><strong>Elaborado por</strong></div>
                        <div><span></span><strong>Revisado por</strong></div>
                        <div><span></span><strong>Aprobado por</strong></div>
                    </div>
                    <p>Sistema ASPHALT-AGY - Control de Almacen e Inventario de la Planta de Asfalto, El Alto</p>
                </div>
            </section>
        </div>
    </AuthenticatedLayout>
</template>

<style>
.report-title {
    margin-bottom: 12px;
    border-bottom: 1px solid #2d3139;
    padding-bottom: 8px;
    color: #fff;
    font-size: 12px;
    font-weight: 800;
    letter-spacing: 0.08em;
    text-transform: uppercase;
}

.report-table {
    width: 100%;
    border-collapse: collapse;
    color: #e1e6eb;
    font-size: 11px;
}

.report-table th {
    background: #0e1113;
    border-bottom: 1px solid #2d3139;
    color: #8b949e;
    font-size: 10px;
    font-weight: 700;
    letter-spacing: 0.05em;
    padding: 10px 12px;
    text-align: left;
    text-transform: uppercase;
}

.report-table td {
    border-bottom: 1px solid #2d3139;
    padding: 9px 12px;
    vertical-align: top;
}

.report-table.compact td,
.report-table.compact th {
    padding: 7px 10px;
}

.screen-green {
    color: #34d399;
}

.screen-red {
    color: #ff8c94;
}

.muted {
    color: #8b949e;
}

.totals-row td {
    background: #0e1113;
    color: #fff;
    font-weight: 800;
}

.empty-cell,
.empty-panel {
    color: #8b949e;
    font-size: 12px;
    text-align: center;
}

.empty-panel {
    border: 1px dashed #2d3139;
    border-radius: 12px;
    background: #1b1e22;
    padding: 48px 16px;
}

.metric-box {
    border: 1px solid #2d3139;
    border-radius: 10px;
    background: #1b1e22;
    padding: 12px;
}

.metric-box span {
    display: block;
    color: #8b949e;
    font-size: 9px;
    font-weight: 800;
    letter-spacing: 0.08em;
    text-transform: uppercase;
}

.metric-box b {
    display: block;
    margin-top: 6px;
    color: #fff;
    font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", monospace;
    font-size: 16px;
}

.group-title {
    margin-bottom: 8px;
    color: #f27b00;
    font-size: 12px;
    font-weight: 800;
    text-transform: uppercase;
}

.print-header,
.print-footer,
.balance-print-only {
    display: none;
}

@media print {
    .no-print,
    header,
    aside,
    nav {
        display: none !important;
    }

    * {
        background: transparent !important;
        box-shadow: none !important;
    }

    body {
        background: #fff !important;
        color: #000 !important;
    }

    .print-sheet {
        color: #000 !important;
        width: 100% !important;
    }

    .print-header,
    .print-footer,
    .balance-print-only {
        display: block !important;
    }

    .balance-print-only h3 {
        border: 1px solid #000;
        border-bottom: 0;
        color: #000;
        font-size: 9px;
        font-weight: 900;
        margin: 8px 0 0;
        padding: 3px 4px;
        text-align: center;
        text-transform: uppercase;
    }

    .balance-print-table {
        border-collapse: collapse;
        color: #000;
        font-size: 6.7px;
        table-layout: fixed;
        text-transform: uppercase;
        width: 100%;
    }

    .balance-print-table th,
    .balance-print-table td {
        border: 1px solid #000;
        color: #000;
        line-height: 1.12;
        padding: 2px 3px;
        text-align: center;
        vertical-align: middle;
        word-break: break-word;
    }

    .balance-print-table th {
        background: #f0f0f0 !important;
        font-size: 6.4px;
        font-weight: 900;
    }

    .balance-print-table th:nth-child(1),
    .balance-print-table td:nth-child(1) {
        width: 3%;
    }

    .balance-print-table th:nth-child(2),
    .balance-print-table td:nth-child(2) {
        width: 12%;
        font-weight: 700;
    }

    .balance-print-table th:nth-child(3),
    .balance-print-table td:nth-child(3) {
        width: 5%;
        font-weight: 700;
    }

    .balance-print-table th:nth-child(4),
    .balance-print-table td:nth-child(4) {
        width: 28%;
        font-weight: 700;
    }

    .balance-print-table th:nth-child(5),
    .balance-print-table td:nth-child(5) {
        width: 8%;
    }

    .balance-print-table th:nth-child(6),
    .balance-print-table td:nth-child(6) {
        width: 11%;
        font-weight: 700;
    }

    .balance-print-table th:nth-child(7),
    .balance-print-table td:nth-child(7),
    .balance-print-table th:nth-child(8),
    .balance-print-table td:nth-child(8),
    .balance-print-table th:nth-child(9),
    .balance-print-table td:nth-child(9) {
        width: 6%;
        font-weight: 700;
    }

    .balance-print-table th:nth-child(10),
    .balance-print-table td:nth-child(10) {
        width: 15%;
        font-weight: 700;
    }

    .balance-print-table tbody tr:nth-child(odd) td {
        background: #f7f7f7 !important;
    }

    .print-header {
        border-bottom: 2px solid #000;
        margin-bottom: 10px;
        padding-bottom: 7px;
    }

    .print-header-row {
        display: flex;
        justify-content: space-between;
        gap: 18px;
    }

    .print-header h1 {
        color: #000;
        font-size: 13px;
        font-weight: 900;
        letter-spacing: 0.04em;
        margin: 0;
        text-transform: uppercase;
    }

    .print-header h2 {
        color: #222;
        font-size: 9px;
        font-weight: 700;
        margin: 2px 0 0;
        text-transform: uppercase;
    }

    .print-doc-box {
        border: 1px solid #000;
        min-width: 210px;
        padding: 5px 7px;
        text-align: right;
    }

    .print-doc-box strong {
        color: #000;
        display: block;
        font-size: 10px;
        text-transform: uppercase;
    }

    .print-doc-box span {
        color: #222;
        display: block;
        font-size: 8px;
        margin-top: 2px;
    }

    .print-meta-grid {
        display: grid;
        gap: 3px 16px;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        margin-top: 7px;
    }

    .print-meta-grid div {
        color: #000;
        font-size: 8px;
    }

    .print-main-table {
        border: 0 !important;
        border-radius: 0 !important;
        padding: 0 !important;
        page-break-inside: auto;
    }

    .balance-print-only ~ .print-main-table,
    .balance-print-only ~ div .print-main-table {
        display: none !important;
    }

    .report-title {
        border: 1px solid #000 !important;
        border-bottom: 0 !important;
        color: #000 !important;
        font-size: 8px !important;
        margin: 8px 0 0 !important;
        padding: 4px 6px !important;
    }

    .report-table {
        border-collapse: collapse !important;
        color: #000 !important;
        font-size: 7.2px !important;
        width: 100% !important;
    }

    .report-table th {
        background: #e8e8e8 !important;
        border: 1px solid #000 !important;
        color: #000 !important;
        font-size: 6.8px !important;
        padding: 3px 4px !important;
    }

    .report-table td {
        border: 1px solid #777 !important;
        color: #000 !important;
        padding: 2.5px 4px !important;
    }

    .screen-green,
    .screen-red,
    .muted {
        color: #000 !important;
    }

    .totals-row td {
        background: #efefef !important;
        color: #000 !important;
        font-weight: 900 !important;
    }

    .group-title {
        color: #000 !important;
        font-size: 8px !important;
        margin: 5px 0 2px !important;
    }

    .grid {
        display: block !important;
    }

    .print-footer {
        margin-top: 24px;
        page-break-inside: avoid;
    }

    .print-signatures {
        display: flex;
        justify-content: space-around;
        margin-top: 36px;
    }

    .print-signatures div {
        color: #000;
        font-size: 8px;
        text-align: center;
    }

    .print-signatures span {
        border-bottom: 1px solid #000;
        display: block;
        margin-bottom: 4px;
        width: 150px;
    }

    .print-footer p {
        border-top: 1px solid #999;
        color: #555;
        font-size: 7px;
        margin-top: 16px;
        padding-top: 4px;
        text-align: center;
    }
}
</style>
