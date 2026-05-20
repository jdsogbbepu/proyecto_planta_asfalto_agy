<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref, computed } from 'vue';

const props = defineProps({
    ingresos: {
        type: Array,
        required: true,
    },
});

// Search filter
const searchQuery = ref('');

// Filtered ingress list
const filteredIngresos = computed(() => {
    return props.ingresos.filter(i => {
        const matchesTicket = i.nro_ticket && i.nro_ticket.toLowerCase().includes(searchQuery.value.toLowerCase());
        const matchesOdc = i.odc && i.odc.toLowerCase().includes(searchQuery.value.toLowerCase());
        const matchesProveedor = i.proveedor && i.proveedor.razon_social.toLowerCase().includes(searchQuery.value.toLowerCase());
        return matchesTicket || matchesOdc || matchesProveedor || searchQuery.value === '';
    });
});

// Row expansion state
const expandedRows = ref({});
const toggleRow = (id) => {
    expandedRows.value[id] = !expandedRows.value[id];
};

// Deletion logic
const deleteForm = useForm({});
const deleteIngreso = (ingreso) => {
    // Check if any batch in the entry has been consumed (current balance < acquired)
    const isConsumed = ingreso.detalles.some(d => Number(d.cantidad_actual_lote) < Number(d.cantidad_adquirida));
    
    if (isConsumed) {
        alert('Este ingreso no puede ser eliminado porque uno o más lotes ya han sido consumidos en despachos.');
        return;
    }

    if (confirm(`¿Está seguro de eliminar el ingreso del Ticket ${ingreso.nro_ticket || 'S/N'} y revertir sus lotes?`)) {
        deleteForm.delete(route('ingresos.destroy', ingreso.id));
    }
};

// Check if an entire entry is safe to delete
const canDelete = (ingreso) => {
    return !ingreso.detalles.some(d => Number(d.cantidad_actual_lote) < Number(d.cantidad_adquirida));
};
</script>

<template>
    <Head title="Ingresos de Materiales" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h2 class="text-2xl font-bold tracking-tight text-white">Ingresos y Lotes (PEPS)</h2>
                    <p class="text-sm text-industrial-muted mt-1 font-sans">Visualice y registre los ingresos de materiales y la generación de lotes cronológicos de almacén.</p>
                </div>
                
                <div v-if="$page.props.auth.user.role !== 'visor'">
                    <Link
                        :href="route('ingresos.create')"
                        class="bg-[#f27b00] hover:bg-[#ff9426] text-[#111417] font-bold py-2.5 px-4 rounded-lg text-sm tracking-wider uppercase transition duration-150 inline-flex items-center gap-2"
                    >
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                        </svg>
                        <span>Registrar Ingreso</span>
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
                    placeholder="Buscar por nro de ticket, orden de compra (ODC) o proveedor..."
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
                                <th class="px-6 py-4 font-semibold">Fecha Adquirida</th>
                                <th class="px-6 py-4 font-semibold">Nro. Ticket</th>
                                <th class="px-6 py-4 font-semibold">Orden de Compra</th>
                                <th class="px-6 py-4 font-semibold">Proveedor</th>
                                <th class="px-6 py-4 font-semibold">Registrado Por</th>
                                <th class="px-6 py-4 font-semibold text-center">Nro Lotes</th>
                                <th v-if="$page.props.auth.user.role !== 'visor'" class="px-6 py-4 font-semibold text-right">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#2d3139]">
                            <template v-for="ingreso in filteredIngresos" :key="ingreso.id">
                                <!-- Core row -->
                                <tr 
                                    class="hover:bg-[#1f2329]/30 transition duration-150 cursor-pointer"
                                    @click="toggleRow(ingreso.id)"
                                >
                                    <td class="px-6 py-4 text-center">
                                        <button class="text-industrial-muted hover:text-white focus:outline-none">
                                            <svg 
                                                class="h-4 w-4 transform transition-transform duration-200"
                                                :class="{ 'rotate-180': expandedRows[ingreso.id] }"
                                                fill="none" 
                                                viewBox="0 0 24 24" 
                                                stroke="currentColor" 
                                                stroke-width="2.5"
                                            >
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                                            </svg>
                                        </button>
                                    </td>
                                    <td class="px-6 py-4 font-mono text-xs font-semibold text-white">{{ ingreso.fecha_adquirida }}</td>
                                    <td class="px-6 py-4 font-mono text-xs font-bold text-[#f27b00]">{{ ingreso.nro_ticket || 'S/N' }}</td>
                                    <td class="px-6 py-4 font-mono text-xs text-industrial-foreground">{{ ingreso.odc || 'S/O' }}</td>
                                    <td class="px-6 py-4 font-medium text-white">{{ ingreso.proveedor?.razon_social || 'S/P' }}</td>
                                    <td class="px-6 py-4 text-xs text-industrial-muted">{{ ingreso.usuario?.name }}</td>
                                    <td class="px-6 py-4 text-center font-mono text-xs font-bold">{{ ingreso.detalles.length }}</td>
                                    <td v-if="$page.props.auth.user.role !== 'visor'" class="px-6 py-4 text-right" @click.stop>
                                        <button
                                            @click="deleteIngreso(ingreso)"
                                            :disabled="!canDelete(ingreso)"
                                            class="text-xs bg-[#3b1d22] hover:bg-[#52252a] text-[#ff8c94] font-semibold py-1.5 px-3 rounded border border-[#8a252c] transition duration-150 disabled:opacity-20 disabled:cursor-not-allowed"
                                            title="Sólo se pueden eliminar ingresos cuyos lotes no se hayan consumido."
                                        >
                                            Eliminar
                                        </button>
                                    </td>
                                </tr>
                                
                                <!-- Expandable details row -->
                                <tr v-if="expandedRows[ingreso.id]" class="bg-[#0e1113]">
                                    <td colspan="8" class="px-8 py-4 border-l-2 border-[#f27b00]">
                                        <div class="mb-2 text-xs font-bold text-industrial-muted uppercase tracking-wider">Detalle de Lotes Generados (PEPS)</div>
                                        <div class="overflow-hidden border border-[#2d3139] rounded-lg">
                                            <table class="w-full text-left text-xs text-industrial-foreground">
                                                <thead class="bg-[#1b1e22] text-[10px] text-industrial-muted uppercase tracking-wide border-b border-[#2d3139]">
                                                    <tr>
                                                        <th class="px-4 py-2 font-semibold">Lote ID</th>
                                                        <th class="px-4 py-2 font-semibold">Material / Insumo</th>
                                                        <th class="px-4 py-2 font-semibold">Proyecto Destino</th>
                                                        <th class="px-4 py-2 font-semibold text-right">Cant. Inicial</th>
                                                        <th class="px-4 py-2 font-semibold text-right">Saldo Actual (Lote)</th>
                                                        <th class="px-4 py-2 font-semibold text-center">Estado del Lote</th>
                                                    </tr>
                                                </thead>
                                                <tbody class="divide-y divide-[#2d3139] font-mono">
                                                    <tr v-for="detalle in ingreso.detalles" :key="detalle.id" class="hover:bg-[#1b1e22]/50">
                                                        <td class="px-4 py-2.5 text-industrial-muted">LOTE-{{ String(detalle.id).padStart(5, '0') }}</td>
                                                        <td class="px-4 py-2.5 text-white font-sans text-xs font-semibold">{{ detalle.material?.nombre }}</td>
                                                        <td class="px-4 py-2.5 text-industrial-muted font-sans text-xs">{{ detalle.proyecto?.nombre }}</td>
                                                        <td class="px-4 py-2.5 text-right font-bold text-[#e1e6eb]">
                                                            {{ Number(detalle.cantidad_adquirida).toLocaleString() }} {{ detalle.material?.medida?.abreviacion }}
                                                        </td>
                                                        <td class="px-4 py-2.5 text-right font-bold text-[#f27b00]">
                                                            {{ Number(detalle.cantidad_actual_lote).toLocaleString() }} {{ detalle.material?.medida?.abreviacion }}
                                                        </td>
                                                        <td class="px-4 py-2.5 text-center">
                                                            <span 
                                                                class="px-2 py-0.5 rounded text-[9px] font-bold"
                                                                :class="{
                                                                    'bg-[#0f3d2a] border border-[#1a7f4c] text-[#a6ffcc]': Number(detalle.cantidad_actual_lote) === Number(detalle.cantidad_adquirida),
                                                                    'bg-[#f27b00]/10 border border-[#f27b00]/30 text-[#f27b00]': Number(detalle.cantidad_actual_lote) > 0 && Number(detalle.cantidad_actual_lote) < Number(detalle.cantidad_adquirida),
                                                                    'bg-[#3b1d22] border border-[#8a252c] text-[#ff8c94]': Number(detalle.cantidad_actual_lote) == 0
                                                                }"
                                                            >
                                                                {{ Number(detalle.cantidad_actual_lote) == 0 ? 'AGOTADO' : (Number(detalle.cantidad_actual_lote) < Number(detalle.cantidad_adquirida) ? 'PARCIAL' : 'COMPLETO') }}
                                                            </span>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                        <div v-if="ingreso.observaciones" class="mt-3 text-xs text-industrial-muted">
                                            <span class="font-bold uppercase text-[10px]">Observaciones:</span> {{ ingreso.observaciones }}
                                        </div>
                                    </td>
                                </tr>
                            </template>
                            <tr v-if="filteredIngresos.length === 0">
                                <td colspan="8" class="px-6 py-12 text-center text-industrial-muted font-sans text-sm">
                                    No se encontraron ingresos registrados en el almacén.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
