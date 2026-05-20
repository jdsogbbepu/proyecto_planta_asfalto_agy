<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref, computed } from 'vue';

const props = defineProps({
    salidas: {
        type: Array,
        required: true,
    },
});

// Search filter
const searchQuery = ref('');

// Filtered dispatches list
const filteredSalidas = computed(() => {
    return props.salidas.filter(s => {
        const matchesProyecto = s.proyecto && s.proyecto.nombre.toLowerCase().includes(searchQuery.value.toLowerCase());
        const matchesFuncionario = s.funcionario && s.funcionario.nombre.toLowerCase().includes(searchQuery.value.toLowerCase());
        const matchesUso = s.uso && s.uso.toLowerCase().includes(searchQuery.value.toLowerCase());
        return matchesProyecto || matchesFuncionario || matchesUso || searchQuery.value === '';
    });
});

// Row expansion state
const expandedRows = ref({});
const toggleRow = (id) => {
    expandedRows.value[id] = !expandedRows.value[id];
};

// Deletion/Reversion logic
const deleteForm = useForm({});
const deleteSalida = (salida) => {
    if (confirm(`¿Está seguro de revertir este despacho? Se restaurarán los saldos en los lotes correspondientes.`)) {
        deleteForm.delete(route('despachos.destroy', salida.id));
    }
};
</script>

<template>
    <Head title="Despachos de Materiales" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h2 class="text-2xl font-bold tracking-tight text-white">Despachos y Salidas (PEPS)</h2>
                    <p class="text-sm text-industrial-muted mt-1 font-sans">Visualice y registre las salidas de insumos aplicando deducciones cronológicas PEPS automáticas.</p>
                </div>
                
                <div v-if="$page.props.auth.user.role !== 'visor'">
                    <Link
                        :href="route('despachos.create')"
                        class="bg-[#f27b00] hover:bg-[#ff9426] text-[#111417] font-bold py-2.5 px-4 rounded-lg text-sm tracking-wider uppercase transition duration-150 inline-flex items-center gap-2"
                    >
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                        </svg>
                        <span>Registrar Salida</span>
                    </Link>
                </div>
            </div>
        </template>

        <div class="py-6 px-4 sm:px-6 lg:px-8 mx-auto max-w-7xl font-sans">
            <!-- Filter Bar -->
            <div class="bg-[#1b1e22] border border-[#2d3139] rounded-xl p-4 mb-6">
                <label class="block text-xs font-semibold text-industrial-muted uppercase tracking-wider mb-1.5">Búsqueda rápida</label>
                <input
                    type="text"
                    placeholder="Buscar por proyecto/obra, funcionario receptor o destino de uso..."
                    class="w-full bg-[#0e1113] text-[#e1e6eb] border border-[#2d3139] rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-[#f27b00]"
                    v-model="searchQuery"
                />
            </div>

            <!-- Table -->
            <div class="bg-[#1b1e22] border border-[#2d3139] rounded-xl overflow-hidden shadow-xl">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-[#e1e6eb]">
                        <thead class="text-xs text-industrial-muted uppercase bg-[#0e1113] border-b border-[#2d3139]">
                            <tr>
                                <th class="px-6 py-4 font-semibold text-center w-12"></th>
                                <th class="px-6 py-4 font-semibold">Fecha Salida</th>
                                <th class="px-6 py-4 font-semibold">Proyecto / Obra Municipal</th>
                                <th class="px-6 py-4 font-semibold">Funcionario Receptor</th>
                                <th class="px-6 py-4 font-semibold">Destino / Uso</th>
                                <th class="px-6 py-4 font-semibold">Registrado Por</th>
                                <th v-if="$page.props.auth.user.role !== 'visor'" class="px-6 py-4 font-semibold text-right">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#2d3139]">
                            <template v-for="salida in filteredSalidas" :key="salida.id">
                                <!-- Core row -->
                                <tr 
                                    class="hover:bg-[#1f2329]/30 transition duration-150 cursor-pointer"
                                    @click="toggleRow(salida.id)"
                                >
                                    <td class="px-6 py-4 text-center">
                                        <button class="text-industrial-muted hover:text-white focus:outline-none">
                                            <svg 
                                                class="h-4 w-4 transform transition-transform duration-200"
                                                :class="{ 'rotate-180': expandedRows[salida.id] }"
                                                fill="none" 
                                                viewBox="0 0 24 24" 
                                                stroke="currentColor" 
                                                stroke-width="2.5"
                                            >
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                                            </svg>
                                        </button>
                                    </td>
                                    <td class="px-6 py-4 font-mono text-xs font-semibold text-white">{{ salida.fecha_salida }}</td>
                                    <td class="px-6 py-4 font-medium text-white">{{ salida.proyecto?.nombre }}</td>
                                    <td class="px-6 py-4 text-xs text-industrial-foreground font-semibold">{{ salida.funcionario?.nombre }}</td>
                                    <td class="px-6 py-4 text-xs text-industrial-foreground">{{ salida.uso }}</td>
                                    <td class="px-6 py-4 text-xs text-industrial-muted">{{ salida.usuario?.name }}</td>
                                    <td v-if="$page.props.auth.user.role !== 'visor'" class="px-6 py-4 text-right" @click.stop>
                                        <button
                                            @click="deleteSalida(salida)"
                                            class="text-xs bg-[#3b1d22] hover:bg-[#52252a] text-[#ff8c94] font-semibold py-1.5 px-3 rounded border border-[#8a252c] transition duration-150"
                                            title="Revertir despacho y re-sumar stock al lote original."
                                        >
                                            Revertir Despacho
                                        </button>
                                    </td>
                                </tr>
                                
                                <!-- Expandable details row -->
                                <tr v-if="expandedRows[salida.id]" class="bg-[#0e1113]">
                                    <td colspan="7" class="px-8 py-4 border-l-2 border-[#ff8c94]">
                                        <div class="mb-2 text-xs font-bold text-industrial-muted uppercase tracking-wider">Trazabilidad de Consumo PEPS (Lotes Consumidos)</div>
                                        <div class="overflow-hidden border border-[#2d3139] rounded-lg">
                                            <table class="w-full text-left text-xs text-industrial-foreground">
                                                <thead class="bg-[#1b1e22] text-[10px] text-industrial-muted uppercase tracking-wide border-b border-[#2d3139]">
                                                    <tr>
                                                        <th class="px-4 py-2 font-semibold">Lote Consumido</th>
                                                        <th class="px-4 py-2 font-semibold">Material / Insumo</th>
                                                        <th class="px-4 py-2 font-semibold">Ticket Origen del Lote</th>
                                                        <th class="px-4 py-2 font-semibold text-right">Cantidad Extraída (Deducida)</th>
                                                    </tr>
                                                </thead>
                                                <tbody class="divide-y divide-[#2d3139] font-mono">
                                                    <tr v-for="detalle in salida.detalles" :key="detalle.id" class="hover:bg-[#1b1e22]/50">
                                                        <td class="px-4 py-2.5 text-industrial-muted">LOTE-{{ String(detalle.detalle_ingreso?.id).padStart(5, '0') }}</td>
                                                        <td class="px-4 py-2.5 text-white font-sans text-xs font-semibold">
                                                            {{ detalle.detalle_ingreso?.material?.nombre }}
                                                        </td>
                                                        <td class="px-4 py-2.5 font-sans">
                                                            <span class="text-industrial-foreground font-bold">Ticket:</span> 
                                                            {{ detalle.detalle_ingreso?.ingreso?.nro_ticket || 'S/N' }}
                                                            <span class="text-industrial-muted font-normal">
                                                                ({{ detalle.detalle_ingreso?.ingreso?.fecha_adquirida }})
                                                            </span>
                                                        </td>
                                                        <td class="px-4 py-2.5 text-right font-bold text-[#ff8c94]">
                                                            {{ Number(detalle.cantidad_salida).toLocaleString() }} {{ detalle.detalle_ingreso?.material?.medida?.abreviacion }}
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </td>
                                </tr>
                            </template>
                            <tr v-if="filteredSalidas.length === 0">
                                <td colspan="7" class="px-6 py-12 text-center text-industrial-muted font-sans text-sm">
                                    No se encontraron despachos registrados en la planta.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
