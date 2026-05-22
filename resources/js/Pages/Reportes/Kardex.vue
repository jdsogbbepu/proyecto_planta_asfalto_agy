<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import { ref, computed } from 'vue';

const props = defineProps({
    proyectos: {
        type: Array,
        required: true,
    },
    materials: {
        type: Array,
        required: true,
    },
    lotes: {
        type: Array,
        required: true,
    },
    kardex: {
        type: Array,
        required: true,
    },
    filtros: {
        type: Object,
        required: true,
    }
});

const selectedProyecto = ref(props.filtros.id_proyecto || '');
const selectedMaterial = ref(props.filtros.id_material || '');
const fechaDesde = ref(props.filtros.fecha_desde || '');
const fechaHasta = ref(props.filtros.fecha_hasta || '');
const printOrientation = ref('landscape'); // 'landscape' or 'portrait'

// Fetch updated ledger values on selection
const applyFilters = () => {
    router.get(route('kardex.index'), {
        id_proyecto: selectedProyecto.value,
        id_material: selectedMaterial.value,
        fecha_desde: fechaDesde.value || undefined,
        fecha_hasta: fechaHasta.value || undefined,
    }, { preserveState: true });
};

// Reset selectors
const clearFilters = () => {
    selectedProyecto.value = props.proyectos[0]?.id || '';
    selectedMaterial.value = props.materials[0]?.id || '';
    fechaDesde.value = '';
    fechaHasta.value = '';
    applyFilters();
};

const getSelectedMaterialUnit = () => {
    const mat = props.materials.find(m => m.id === Number(selectedMaterial.value));
    return mat && mat.medida ? mat.medida.abreviacion : '';
};

const getSelectedMaterialName = () => {
    const mat = props.materials.find(m => m.id === Number(selectedMaterial.value));
    return mat ? mat.nombre : '';
};

const getSelectedProyectoName = () => {
    const proj = props.proyectos.find(p => p.id === Number(selectedProyecto.value));
    return proj ? proj.nombre : '';
};

// Totals computation
const totals = computed(() => {
    let totalIn = 0;
    let totalOut = 0;
    props.kardex.forEach(item => {
        totalIn += Number(item.cantidad_in) || 0;
        totalOut += Number(item.cantidad_out) || 0;
    });
    const lastSaldo = props.kardex.length > 0 ? Number(props.kardex[0].saldo) : 0;
    return { totalIn, totalOut, lastSaldo };
});

// Print handler
const handlePrint = () => {
    // Dynamically set print orientation via a style tag
    const styleId = 'print-orientation-style';
    let styleEl = document.getElementById(styleId);
    if (!styleEl) {
        styleEl = document.createElement('style');
        styleEl.id = styleId;
        document.head.appendChild(styleEl);
    }
    styleEl.textContent = `@page { size: ${printOrientation.value === 'landscape' ? 'landscape' : 'portrait'}; margin: 10mm 8mm; }`;
    
    window.print();
};
</script>

<template>
    <Head title="Kardex Físico PEPS" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h2 class="text-2xl font-bold tracking-tight text-white">Kardex Físico y Trazabilidad (PEPS)</h2>
                    <p class="text-sm text-industrial-muted mt-1 font-sans">Kardex cronológico de entradas/salidas y estado de lotes por proyecto.</p>
                </div>
                
                <!-- Print Controls (only show when data is loaded) -->
                <div v-if="selectedProyecto && selectedMaterial && kardex.length > 0" class="flex items-center gap-3 no-print">
                    <!-- Orientation Toggle -->
                    <div class="flex items-center gap-2 bg-[#0e1113] border border-[#2d3139] rounded-lg px-3 py-1.5">
                        <span class="text-[9px] font-bold text-industrial-muted uppercase tracking-wider">Orientación:</span>
                        <button
                            @click="printOrientation = 'landscape'"
                            class="text-xs font-bold px-2 py-1 rounded transition-all duration-150"
                            :class="printOrientation === 'landscape' ? 'bg-[#f27b00] text-[#111417]' : 'text-industrial-muted hover:text-white'"
                        >
                            Horizontal
                        </button>
                        <button
                            @click="printOrientation = 'portrait'"
                            class="text-xs font-bold px-2 py-1 rounded transition-all duration-150"
                            :class="printOrientation === 'portrait' ? 'bg-[#f27b00] text-[#111417]' : 'text-industrial-muted hover:text-white'"
                        >
                            Vertical
                        </button>
                    </div>

                    <!-- Print Button -->
                    <button
                        @click="handlePrint"
                        class="bg-[#0a5c8f] hover:bg-[#0d6faa] text-white font-bold py-2 px-4 rounded-lg text-xs uppercase tracking-wider transition duration-150 flex items-center gap-2"
                    >
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                        </svg>
                        Imprimir / PDF
                    </button>
                </div>
            </div>
        </template>

        <div class="py-6 px-4 sm:px-6 lg:px-8 mx-auto max-w-7xl font-sans">
            <!-- Filter card -->
            <div class="bg-[#1b1e22] border border-[#2d3139] rounded-xl p-5 shadow-xl mb-6 no-print">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4 items-end">
                    <!-- Project Selector -->
                    <div>
                        <label class="block text-xs font-semibold text-industrial-muted uppercase tracking-wider mb-2">Proyecto / Obra Municipal</label>
                        <select
                            class="w-full bg-[#0e1113] text-[#e1e6eb] border border-[#2d3139] rounded-lg px-4 py-2 text-sm focus:outline-none focus:border-[#f27b00]"
                            v-model="selectedProyecto"
                            @change="applyFilters"
                        >
                            <option value="" disabled>-- Seleccione Proyecto --</option>
                            <option v-for="proj in proyectos" :key="proj.id" :value="proj.id">
                                {{ proj.nombre }}
                            </option>
                        </select>
                    </div>

                    <!-- Material Selector -->
                    <div>
                        <label class="block text-xs font-semibold text-industrial-muted uppercase tracking-wider mb-2">Material / Insumo</label>
                        <select
                            class="w-full bg-[#0e1113] text-[#e1e6eb] border border-[#2d3139] rounded-lg px-4 py-2 text-sm focus:outline-none focus:border-[#f27b00]"
                            v-model="selectedMaterial"
                            @change="applyFilters"
                        >
                            <option value="" disabled>-- Seleccione Material --</option>
                            <option v-for="mat in materials" :key="mat.id" :value="mat.id">
                                {{ mat.nombre }}
                            </option>
                        </select>
                    </div>

                    <!-- Date From -->
                    <div>
                        <label class="block text-xs font-semibold text-industrial-muted uppercase tracking-wider mb-2">Fecha Desde</label>
                        <input
                            type="date"
                            class="w-full bg-[#0e1113] text-[#e1e6eb] border border-[#2d3139] rounded-lg px-4 py-2 text-sm focus:outline-none focus:border-[#f27b00] font-mono"
                            v-model="fechaDesde"
                        />
                    </div>

                    <!-- Date To -->
                    <div>
                        <label class="block text-xs font-semibold text-industrial-muted uppercase tracking-wider mb-2">Fecha Hasta</label>
                        <input
                            type="date"
                            class="w-full bg-[#0e1113] text-[#e1e6eb] border border-[#2d3139] rounded-lg px-4 py-2 text-sm focus:outline-none focus:border-[#f27b00] font-mono"
                            v-model="fechaHasta"
                        />
                    </div>

                    <!-- Actions -->
                    <div class="flex gap-2">
                        <button
                            type="button"
                            @click="applyFilters"
                            class="flex-1 bg-[#f27b00] hover:bg-[#ff9426] text-[#111417] font-bold py-2 px-4 rounded-lg text-sm uppercase tracking-wider transition duration-150"
                        >
                            Generar
                        </button>
                        <button
                            type="button"
                            @click="clearFilters"
                            class="bg-[#2d3139] hover:bg-[#383d47] text-white font-semibold py-2 px-4 rounded-lg text-sm transition duration-150"
                        >
                            Limpiar
                        </button>
                    </div>
                </div>
            </div>

            <!-- ======================== PRINTABLE HEADER (only visible in print) ======================== -->
            <div class="print-header">
                <div class="print-header-top">
                    <div>
                        <h1 class="print-title">GOBIERNO AUTÓNOMO MUNICIPAL DE EL ALTO</h1>
                        <h2 class="print-subtitle">PLANTA DE ASFALTO — CONTROL DE ALMACÉN</h2>
                    </div>
                    <div class="print-meta">
                        <div><strong>KARDEX FÍSICO PEPS</strong></div>
                        <div>Fecha de Impresión: {{ new Date().toLocaleDateString('es-BO') }}</div>
                    </div>
                </div>
                <div class="print-info-grid">
                    <div><strong>Proyecto / Obra:</strong> {{ getSelectedProyectoName() }}</div>
                    <div><strong>Material / Insumo:</strong> {{ getSelectedMaterialName() }}</div>
                    <div><strong>Unidad:</strong> {{ getSelectedMaterialUnit() }}</div>
                    <div v-if="fechaDesde || fechaHasta">
                        <strong>Período:</strong> {{ fechaDesde || '---' }} al {{ fechaHasta || '---' }}
                    </div>
                </div>
            </div>

            <!-- Content Grid -->
            <div v-if="selectedProyecto && selectedMaterial" class="grid grid-cols-1 lg:grid-cols-12 gap-6">
                <!-- Left panel: Lotes cronológicos -->
                <div class="lg:col-span-4 bg-[#1b1e22] border border-[#2d3139] rounded-xl p-5 shadow-xl no-print">
                    <h3 class="text-sm font-bold text-white uppercase tracking-wider mb-4 border-b border-[#2d3139] pb-2">
                        Pila de Lotes FIFO / PEPS
                    </h3>
                    <p class="text-xs text-industrial-muted mb-4 font-sans">Visualice el saldo actual e inicial de cada lote cronológico en el almacén.</p>

                    <div class="space-y-3 max-h-[500px] overflow-y-auto pr-1">
                        <div 
                            v-for="lote in lotes" 
                            :key="lote.id"
                            class="bg-[#0e1113] border border-[#2d3139] rounded-lg p-3"
                        >
                            <div class="flex justify-between items-center mb-2">
                                <span class="font-mono text-xs font-bold text-[#f27b00]">LOTE-{{ String(lote.id).padStart(5, '0') }}</span>
                                <span 
                                    class="text-[9px] font-bold px-1.5 py-0.2 rounded"
                                    :class="{
                                        'bg-[#0f3d2a] border border-[#1a7f4c] text-[#a6ffcc]': Number(lote.cantidad_actual_lote) === Number(lote.cantidad_adquirida),
                                        'bg-[#f27b00]/10 border border-[#f27b00]/30 text-[#f27b00]': Number(lote.cantidad_actual_lote) > 0 && Number(lote.cantidad_actual_lote) < Number(lote.cantidad_adquirida),
                                        'bg-[#3b1d22] border border-[#8a252c] text-[#ff8c94]': Number(lote.cantidad_actual_lote) == 0
                                    }"
                                >
                                    {{ Number(lote.cantidad_actual_lote) == 0 ? 'AGOTADO' : (Number(lote.cantidad_actual_lote) < Number(lote.cantidad_adquirida) ? 'PARCIAL' : 'COMPLETO') }}
                                </span>
                            </div>

                            <div class="grid grid-cols-2 gap-2 text-xs font-mono mb-2">
                                <div class="text-industrial-muted">Ticket: <span class="text-white">{{ lote.ingreso?.nro_ticket || 'S/N' }}</span></div>
                                <div class="text-industrial-muted text-right">Fecha: <span class="text-white">{{ lote.ingreso?.fecha_adquirida }}</span></div>
                            </div>

                            <div class="w-full bg-[#1b1e22] rounded-full h-1.5 border border-[#2d3139] mb-2 overflow-hidden">
                                <div 
                                    class="h-1 rounded-full"
                                    :class="Number(lote.cantidad_actual_lote) === 0 ? 'bg-[#ff8c94]' : 'bg-[#f27b00]'"
                                    :style="{ width: (Number(lote.cantidad_actual_lote) / Number(lote.cantidad_adquirida)) * 100 + '%' }"
                                ></div>
                            </div>

                            <div class="flex justify-between text-[11px] font-mono">
                                <span class="text-industrial-muted">Inicial: {{ Number(lote.cantidad_adquirida).toLocaleString() }} {{ getSelectedMaterialUnit() }}</span>
                                <span class="text-white font-bold">Saldo: {{ Number(lote.cantidad_actual_lote).toLocaleString() }} {{ getSelectedMaterialUnit() }}</span>
                            </div>
                        </div>

                        <div v-if="lotes.length === 0" class="text-center py-8 text-industrial-muted text-xs font-sans">
                            No existen lotes registrados para esta selección.
                        </div>
                    </div>
                </div>

                <!-- Right panel: Chronological Ledger (Kardex) -->
                <div class="lg:col-span-8 bg-[#1b1e22] border border-[#2d3139] rounded-xl p-5 shadow-xl print-main-table">
                    <h3 class="text-sm font-bold text-white uppercase tracking-wider mb-4 border-b border-[#2d3139] pb-2">
                        Libro de Movimientos Auxiliar (Kardex Ledger)
                    </h3>
                    
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs text-[#e1e6eb] kardex-table">
                            <thead class="text-industrial-muted uppercase bg-[#0e1113] border-b border-[#2d3139] text-[10px]">
                                <tr>
                                    <th class="px-4 py-3 font-semibold">Fecha</th>
                                    <th class="px-4 py-3 font-semibold">Operación</th>
                                    <th class="px-4 py-3 font-semibold">Detalle / Documento</th>
                                    <th class="px-4 py-3 font-semibold">Auxiliar</th>
                                    <th class="px-4 py-3 font-semibold text-right">Entradas</th>
                                    <th class="px-4 py-3 font-semibold text-right">Salidas</th>
                                    <th class="px-4 py-3 font-semibold text-right">Saldo Físico</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-[#2d3139] font-mono">
                                <tr v-for="item in kardex" :key="item.timestamp" class="hover:bg-[#1f2329]/30">
                                    <td class="px-4 py-3 text-industrial-muted">{{ item.fecha }}</td>
                                    <td class="px-4 py-3 font-bold" :class="item.tipo === 'INGRESO' ? 'text-emerald-400 print-ingreso' : 'text-[#ff8c94] print-despacho'">
                                        {{ item.tipo }}
                                    </td>
                                    <td class="px-4 py-3 text-white font-sans">{{ item.documento }}</td>
                                    <td class="px-4 py-3 font-sans truncate max-w-[120px]" :title="item.auxiliar">{{ item.auxiliar }}</td>
                                    <td class="px-4 py-3 text-right text-emerald-400 font-bold">
                                        {{ item.cantidad_in > 0 ? Number(item.cantidad_in).toLocaleString() + ' ' + getSelectedMaterialUnit() : '-' }}
                                    </td>
                                    <td class="px-4 py-3 text-right text-[#ff8c94] font-bold">
                                        {{ item.cantidad_out > 0 ? Number(item.cantidad_out).toLocaleString() + ' ' + getSelectedMaterialUnit() : '-' }}
                                    </td>
                                    <td class="px-4 py-3 text-right text-[#f27b00] font-bold">
                                        {{ Number(item.saldo).toLocaleString() }} {{ getSelectedMaterialUnit() }}
                                    </td>
                                </tr>

                                <!-- Totals Row -->
                                <tr v-if="kardex.length > 0" class="bg-[#0e1113] border-t-2 border-[#f27b00]/30 font-bold print-totals-row">
                                    <td colspan="4" class="px-4 py-3 text-right text-industrial-muted uppercase text-[10px] tracking-wider">
                                        Total del Período
                                    </td>
                                    <td class="px-4 py-3 text-right text-emerald-400">
                                        {{ Number(totals.totalIn).toLocaleString() }} {{ getSelectedMaterialUnit() }}
                                    </td>
                                    <td class="px-4 py-3 text-right text-[#ff8c94]">
                                        {{ Number(totals.totalOut).toLocaleString() }} {{ getSelectedMaterialUnit() }}
                                    </td>
                                    <td class="px-4 py-3 text-right text-[#f27b00]">
                                        {{ Number(totals.lastSaldo).toLocaleString() }} {{ getSelectedMaterialUnit() }}
                                    </td>
                                </tr>

                                <tr v-if="kardex.length === 0">
                                    <td colspan="7" class="px-6 py-12 text-center text-industrial-muted font-sans text-xs">
                                        Seleccione un proyecto y material para computar el balance del Kardex.
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div v-else class="text-center py-12 text-industrial-muted text-sm bg-[#1b1e22] border border-dashed border-[#2d3139] rounded-xl">
                Seleccione un Proyecto y Material en los filtros de arriba para generar el Kardex Físico.
            </div>

            <!-- Print Footer (only visible in print) -->
            <div class="print-footer">
                <div class="print-signatures">
                    <div class="print-sig-block">
                        <div class="print-sig-line"></div>
                        <span>Elaborado por</span>
                    </div>
                    <div class="print-sig-block">
                        <div class="print-sig-line"></div>
                        <span>Revisado por</span>
                    </div>
                    <div class="print-sig-block">
                        <div class="print-sig-line"></div>
                        <span>Aprobado por</span>
                    </div>
                </div>
                <div class="print-footer-note">
                    Sistema ASPHALT-AGY — Control de Almacén e Inventario de la Planta de Asfalto, El Alto
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style>
/* ======================== PRINT STYLES ======================== */

/* Elements hidden on screen, shown on print */
.print-header,
.print-footer {
    display: none;
}

@media print {
    /* Hide non-printable elements */
    .no-print,
    header,
    aside,
    nav {
        display: none !important;
    }

    /* Reset all dark backgrounds to white for print */
    * {
        background: transparent !important;
        box-shadow: none !important;
    }

    body {
        background: white !important;
        color: #000 !important;
    }

    /* Show the print header & footer */
    .print-header,
    .print-footer {
        display: block !important;
    }

    /* Print Header Styles */
    .print-header {
        border-bottom: 2px solid #000;
        padding-bottom: 10px;
        margin-bottom: 16px;
    }

    .print-header-top {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 8px;
    }

    .print-title {
        font-size: 14px;
        font-weight: 800;
        color: #000;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        margin: 0;
    }

    .print-subtitle {
        font-size: 11px;
        font-weight: 600;
        color: #333;
        text-transform: uppercase;
        margin: 2px 0 0 0;
    }

    .print-meta {
        text-align: right;
        font-size: 9px;
        color: #555;
    }

    .print-meta strong {
        font-size: 11px;
        color: #000;
    }

    .print-info-grid {
        display: flex;
        gap: 24px;
        font-size: 10px;
        color: #333;
        flex-wrap: wrap;
    }

    .print-info-grid strong {
        color: #000;
    }

    /* Print Main Table */
    .print-main-table {
        width: 100% !important;
        max-width: 100% !important;
        border: 1px solid #999 !important;
        border-radius: 0 !important;
        padding: 0 !important;
    }

    .print-main-table h3 {
        display: none !important;
    }

    .kardex-table {
        width: 100% !important;
        border-collapse: collapse !important;
        color: #000 !important;
        font-size: 9px !important;
    }

    .kardex-table thead {
        background: #f0f0f0 !important;
        color: #000 !important;
    }

    .kardex-table th {
        border: 1px solid #999 !important;
        padding: 4px 6px !important;
        font-weight: 700 !important;
        color: #000 !important;
        text-transform: uppercase !important;
        font-size: 8px !important;
    }

    .kardex-table td {
        border: 1px solid #ccc !important;
        padding: 3px 6px !important;
        color: #000 !important;
    }

    .kardex-table .print-ingreso {
        color: #006600 !important;
    }

    .kardex-table .print-despacho {
        color: #cc0000 !important;
    }

    .print-totals-row {
        background: #e8e8e8 !important;
        border-top: 2px solid #000 !important;
    }

    .print-totals-row td {
        border: 1px solid #999 !important;
        font-weight: 700 !important;
        color: #000 !important;
    }

    /* Grid override for print: full width table */
    .grid {
        display: block !important;
    }

    /* Print Footer */
    .print-footer {
        margin-top: 40px;
        page-break-inside: avoid;
    }

    .print-signatures {
        display: flex;
        justify-content: space-around;
        margin-top: 60px;
    }

    .print-sig-block {
        text-align: center;
        font-size: 9px;
        color: #333;
    }

    .print-sig-line {
        width: 160px;
        border-bottom: 1px solid #000;
        margin-bottom: 4px;
    }

    .print-footer-note {
        text-align: center;
        margin-top: 24px;
        font-size: 8px;
        color: #888;
        border-top: 1px solid #ccc;
        padding-top: 6px;
    }
}
</style>
