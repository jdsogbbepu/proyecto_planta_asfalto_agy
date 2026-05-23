<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref, computed } from 'vue';

const props = defineProps({
    ingresos: { type: Array, required: true },
});

const searchQuery = ref('');

const filteredIngresos = computed(() => {
    if (!searchQuery.value) return props.ingresos;
    const q = searchQuery.value.toLowerCase();
    return props.ingresos.filter(i =>
        (i.odc && i.odc.toLowerCase().includes(q)) ||
        (i.proyecto_nombre && i.proyecto_nombre.toLowerCase().includes(q)) ||
        (i.proveedor_nombre && i.proveedor_nombre.toLowerCase().includes(q)) ||
        i.lotes.some(l => l.nro_registro && l.nro_registro.toLowerCase().includes(q))
    );
});

const expandedRows = ref({});
const toggleRow = (id) => {
    expandedRows.value[id] = !expandedRows.value[id];
};

const deleteForm = useForm({});
const canDelete = (ingreso) => {
    return !ingreso.lotes.some(l => l.cantidad_utilizada > 0);
};

const deleteIngreso = (ingreso) => {
    if (!canDelete(ingreso)) {
        alert('No se puede eliminar porque uno o más lotes ya fueron consumidos en despachos.');
        return;
    }
    if (confirm(`¿Eliminar el ingreso con ${ingreso.nro_lotes} lote(s)?`)) {
        deleteForm.delete(route('ingresos.destroy', ingreso.id));
    }
};

const getLoteNro = (index) => 'LOTE-' + String(index + 1).padStart(3, '0');
</script>

<template>
    <Head title="Ingresos y Lotes (PEPS)" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h2 class="text-2xl font-bold tracking-tight text-white">Ingresos y Lotes (PEPS)</h2>
                    <p class="text-sm text-industrial-muted mt-1">Registro de materiales adquiridos y lotes cronológicos en almacén.</p>
                </div>
                <div v-if="$page.props.auth.permissions.includes('gestionar_ingresos')">
                    <Link
                        :href="route('ingresos.create')"
                        class="bg-[#f27b00] hover:bg-[#ff9426] text-[#111417] font-bold py-2.5 px-4 rounded-lg text-sm tracking-wider uppercase inline-flex items-center gap-2"
                    >
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                        </svg>
                        Registrar Ingreso
                    </Link>
                </div>
            </div>
        </template>

        <div class="py-6 px-4 sm:px-6 lg:px-8 mx-auto max-w-7xl font-sans">
            <div class="bg-[#1b1e22] border border-[#2d3139] rounded-xl p-4 mb-6">
                <input
                    type="text"
                    placeholder="Buscar por RGTR, ODC, proyecto o proveedor..."
                    class="w-full bg-[#0e1113] text-[#e1e6eb] border border-[#2d3139] rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-[#f27b00]"
                    v-model="searchQuery"
                />
            </div>

            <div class="bg-[#1b1e22] border border-[#2d3139] rounded-xl overflow-hidden shadow-xl">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-[#e1e6eb]">
                        <thead class="text-xs text-industrial-muted uppercase bg-[#0e1113] border-b border-[#2d3139]">
                            <tr>
                                <th class="px-4 py-3.5 font-semibold text-center w-10"></th>
                                <th class="px-4 py-3.5 font-semibold">Nro. Registro</th>
                                <th class="px-4 py-3.5 font-semibold text-center">Lotes</th>
                                <th class="px-4 py-3.5 font-semibold">Proyecto Destino</th>
                                <th class="px-4 py-3.5 font-semibold">Fecha</th>
                                <th class="px-4 py-3.5 font-semibold">ODC</th>
                                <th class="px-4 py-3.5 font-semibold">Funcionario Receptor</th>
                                <th class="px-4 py-3.5 font-semibold">Registrado Por</th>
                                <th v-if="$page.props.auth.permissions.includes('gestionar_ingresos')" class="px-4 py-3.5 font-semibold text-right">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#2d3139]">
                            <template v-for="ingreso in filteredIngresos" :key="ingreso.id">
                                <tr
                                    class="hover:bg-[#1f2329]/30 transition cursor-pointer"
                                    @click="toggleRow(ingreso.id)"
                                >
                                    <td class="px-4 py-3.5 text-center">
                                        <svg
                                            class="h-4 w-4 text-industrial-muted transform transition-transform duration-200 mx-auto"
                                            :class="{ 'rotate-180': expandedRows[ingreso.id] }"
                                            fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"
                                        >
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                                        </svg>
                                    </td>
                                    <td class="px-4 py-3.5">
                                        <span class="font-mono text-xs font-bold text-[#f27b00]">
                                            {{ ingreso.lotes[0]?.nro_registro || '—' }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3.5 text-center font-mono font-bold">{{ ingreso.nro_lotes }}</td>
                                    <td class="px-4 py-3.5 font-medium text-white text-xs">{{ ingreso.proyecto_nombre || '—' }}</td>
                                    <td class="px-4 py-3.5 font-mono text-xs text-industrial-muted">{{ ingreso.fecha_adquirida }}</td>
                                    <td class="px-4 py-3.5 font-mono text-xs text-white">{{ ingreso.odc || '—' }}</td>
                                    <td class="px-4 py-3.5 text-xs text-industrial-muted">{{ ingreso.funcionario_nombre || '—' }}</td>
                                    <td class="px-4 py-3.5 text-xs text-industrial-muted">{{ ingreso.registrado_por }}</td>
                                    <td class="px-4 py-3.5 text-right" @click.stop>
                                        <button
                                            v-if="$page.props.auth.permissions.includes('gestionar_ingresos')"
                                            @click="deleteIngreso(ingreso)"
                                            :disabled="!canDelete(ingreso)"
                                            class="text-xs bg-[#3b1d22] hover:bg-[#52252a] text-[#ff8c94] font-semibold py-1.5 px-3 rounded border border-[#8a252c] transition disabled:opacity-30 disabled:cursor-not-allowed"
                                            title="Solo se eliminan lotes sin consumo"
                                        >
                                            Eliminar
                                        </button>
                                    </td>
                                </tr>

<tr v-if="expandedRows[ingreso.id]" class="bg-[#0e1113]">
                                    <td colspan="9" class="px-0 py-4 border-l-2 border-[#f27b00]">
                                        <div class="mx-0">
                                            <div class="mb-3 mx-6 text-[10px] font-bold text-industrial-muted uppercase tracking-widest">
                                                Detalle de Lotes Generados (PEPS)
                                            </div>
                                            <div class="mx-6 overflow-x-auto horizontal-scroll">
                                                <table class="w-full text-left text-xs min-w-[1200px]">
                                                    <thead class="bg-[#1b1e22] text-[10px] text-industrial-muted uppercase border-b border-[#2d3139]">
                                                        <tr>
                                                            <th class="px-3 py-2 font-semibold">Nro</th>
                                                            <th class="px-3 py-2 font-semibold">Nro. Registro</th>
                                                            <th class="px-3 py-2 font-semibold">Material / Insumo</th>
                                                            <th class="px-3 py-2 font-semibold">Unidad</th>
                                                            <th class="px-3 py-2 font-semibold">Proyecto</th>
                                                            <th class="px-3 py-2 font-semibold">Fecha</th>
                                                            <th class="px-3 py-2 font-semibold">ODC</th>
                                                            <th class="px-3 py-2 font-semibold text-right">Cant. Adquirida</th>
                                                            <th class="px-3 py-2 font-semibold text-right">Cant. Utilizada</th>
                                                            <th class="px-3 py-2 font-semibold text-right">Stock en Planta</th>
                                                            <th class="px-3 py-2 font-semibold">Acciones Planificadas</th>
                                                            <th class="px-3 py-2 font-semibold">Proveedor</th>
                                                            <th class="px-3 py-2 font-semibold text-center">Estado</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody class="divide-y divide-[#2d3139] font-mono">
                                                        <tr
                                                            v-for="(lote, idx) in ingreso.lotes"
                                                            :key="lote.id"
                                                            class="hover:bg-[#1b1e22]/50"
                                                        >
                                                            <td class="px-3 py-2.5 text-industrial-muted">{{ getLoteNro(idx) }}</td>
                                                            <td class="px-3 py-2.5 font-bold text-[#f27b00]">{{ lote.nro_registro }}</td>
                                                            <td class="px-3 py-2.5 text-white font-sans text-xs">{{ lote.material_nombre }}</td>
                                                            <td class="px-3 py-2.5 text-industrial-muted">{{ lote.unidad }}</td>
                                                            <td class="px-3 py-2.5 text-industrial-muted font-sans text-xs max-w-[200px] truncate" :title="lote.proyecto_nombre">{{ lote.proyecto_nombre }}</td>
                                                            <td class="px-3 py-2.5 text-industrial-muted">{{ lote.fecha_lote }}</td>
                                                            <td class="px-3 py-2.5 text-white font-mono text-[10px]">{{ lote.odc || '—' }}</td>
                                                            <td class="px-3 py-2.5 text-industrial-muted text-right">{{ lote.cantidad_adquirida > 0 ? Number(lote.cantidad_adquirida).toLocaleString() + ' ' + lote.unidad : '—' }}</td>
                                                            <td class="px-3 py-2.5 text-[#ff8c94] text-right">{{ lote.tiene_consumo ? Number(lote.cantidad_utilizada).toLocaleString() + ' ' + lote.unidad : '—' }}</td>
                                                            <td class="px-3 py-2.5 text-[#f27b00] font-bold text-right">{{ lote.tiene_consumo ? Number(lote.stock_planta).toLocaleString() + ' ' + lote.unidad : '—' }}</td>
                                                            <td class="px-3 py-2.5 text-industrial-muted font-sans text-[10px] max-w-[150px] truncate" :title="lote.acciones_planificadas">{{ lote.acciones_planificadas || '—' }}</td>
                                                            <td class="px-3 py-2.5 text-industrial-muted text-xs">{{ lote.proveedor_nombre || '—' }}</td>
                                                            <td class="px-3 py-2.5 text-center">
                                                                <span
                                                                    class="px-2 py-0.5 rounded text-[9px] font-bold"
                                                                    :class="{
                                                                        'bg-[#0f3d2a] border border-[#1a7f4c] text-[#a6ffcc]': lote.estado_lote === 'COMPLETO',
                                                                        'bg-[#f27b00]/10 border border-[#f27b00]/30 text-[#f27b00]': lote.estado_lote === 'PARCIAL',
                                                                        'bg-[#3b1d22] border border-[#8a252c] text-[#ff8c94]': lote.estado_lote === 'AGOTADO',
                                                                        'bg-[#2d3139] border border-[#4a5568] text-industrial-muted': lote.estado_lote === 'INCOMPLETO',
                                                                    }"
                                                                >{{ lote.estado_lote }}</span>
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
.sticky-container {
    position: sticky;
    top: 80px;
    z-index: 30;
    background: #0e1113;
    max-height: calc(100vh - 200px);
    overflow-y: auto;
}

.horizontal-scroll::-webkit-scrollbar {
    height: 8px;
}

.horizontal-scroll::-webkit-scrollbar-track {
    background: #1b1e22;
    border-radius: 4px;
}

.horizontal-scroll::-webkit-scrollbar-thumb {
    background: #f27b00;
    border-radius: 4px;
}

.horizontal-scroll::-webkit-scrollbar-thumb:hover {
    background: #ff9426;
}

.horizontal-scroll {
    scrollbar-width: thin;
    scrollbar-color: #f27b00 #1b1e22;
}
</style>