<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import { ref } from 'vue';

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

// Fetch updated ledger values on selection
const applyFilters = () => {
    router.get(route('kardex.index'), {
        id_proyecto: selectedProyecto.value,
        id_material: selectedMaterial.value,
    }, { preserveState: true });
};

// Reset selectors
const clearFilters = () => {
    selectedProyecto.value = props.proyectos[0]?.id || '';
    selectedMaterial.value = props.materials[0]?.id || '';
    applyFilters();
};

const getSelectedMaterialUnit = () => {
    const mat = props.materials.find(m => m.id === Number(selectedMaterial.value));
    return mat && mat.medida ? mat.medida.abreviacion : '';
};
</script>

<template>
    <Head title="Kardex Físico PEPS" />

    <AuthenticatedLayout>
        <template #header>
            <div>
                <h2 class="text-2xl font-bold tracking-tight text-white">Kardex Físico y Trazabilidad (PEPS)</h2>
                <p class="text-sm text-industrial-muted mt-1 font-sans">Kardex cronológico de entradas/salidas y estado de lotes por proyecto.</p>
            </div>
        </template>

        <div class="py-6 px-4 sm:px-6 lg:px-8 mx-auto max-w-7xl font-sans">
            <!-- Filter card -->
            <div class="bg-[#1b1e22] border border-[#2d3139] rounded-xl p-5 shadow-xl mb-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 items-end">
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

                    <!-- Actions -->
                    <div class="flex gap-2">
                        <button
                            type="button"
                            @click="applyFilters"
                            class="flex-1 bg-[#f27b00] hover:bg-[#ff9426] text-[#111417] font-bold py-2 px-4 rounded-lg text-sm uppercase tracking-wider transition duration-150"
                        >
                            Generar Kardex
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

            <!-- Content Grid -->
            <div v-if="selectedProyecto && selectedMaterial" class="grid grid-cols-1 lg:grid-cols-12 gap-6">
                <!-- Left panel: Lotes cronológicos -->
                <div class="lg:col-span-4 bg-[#1b1e22] border border-[#2d3139] rounded-xl p-5 shadow-xl">
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
                <div class="lg:col-span-8 bg-[#1b1e22] border border-[#2d3139] rounded-xl p-5 shadow-xl">
                    <h3 class="text-sm font-bold text-white uppercase tracking-wider mb-4 border-b border-[#2d3139] pb-2">
                        Libro de Movimientos Auxiliar (Kardex Ledger)
                    </h3>
                    
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs text-[#e1e6eb]">
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
                                    <td class="px-4 py-3 font-bold" :class="item.tipo === 'INGRESO' ? 'text-emerald-400' : 'text-[#ff8c94]'">
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
        </div>
    </AuthenticatedLayout>
</template>
