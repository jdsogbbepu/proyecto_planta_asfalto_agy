<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';
import { ref, computed, watchEffect } from 'vue';

const page = usePage();

watchEffect(() => {
    if (page.props.flash?.nueva_salida_id) {
        window.open(route('despachos.pdf', page.props.flash.nueva_salida_id), '_blank');
        page.props.flash.nueva_salida_id = null;
    }
});

const props = defineProps({
    salidas: { type: Array, required: true },
});

const searchQuery = ref('');

const filteredSalidas = computed(() => {
    if (!searchQuery.value) return props.salidas;
    const q = searchQuery.value.toLowerCase();
    return props.salidas.filter(s =>
        s.lotes.some(l => l.nro_registro && l.nro_registro.toLowerCase().includes(q)) ||
        (s.odc && s.odc.toLowerCase().includes(q)) ||
        (s.proyecto_nombre && s.proyecto_nombre.toLowerCase().includes(q)) ||
        (s.funcionario_nombre && s.funcionario_nombre.toLowerCase().includes(q))
    );
});

const expandedRows = ref({});
const toggleRow = (id) => {
    expandedRows.value[id] = !expandedRows.value[id];
};

const deleteForm = useForm({});
const deleteSalida = (salida) => {
    if (confirm(`¿Reversar este despacho? Se restaurarán los saldos en los lotes.`)) {
        deleteForm.delete(route('despachos.destroy', salida.id));
    }
};

const getLoteNro = (index) => 'LOTE-' + String(index + 1).padStart(3, '0');
</script>

<template>
    <Head title="Despachos y Salidas (PEPS)" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h2 class="text-2xl font-bold tracking-tight text-white">Despachos y Salidas (PEPS)</h2>
                    <p class="text-sm text-industrial-muted mt-1">Registro de dispatch de materiales con trazabilidad PEPS.</p>
                </div>
                <div v-if="$page.props.auth.permissions.includes('gestionar_salidas')">
                    <Link
                        :href="route('despachos.create')"
                        class="bg-[#f27b00] hover:bg-[#ff9426] text-[#111417] font-bold py-2.5 px-4 rounded-lg text-sm tracking-wider uppercase inline-flex items-center gap-2"
                    >
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                        </svg>
                        Registrar Salida
                    </Link>
                </div>
            </div>
        </template>

        <div class="py-6 px-4 sm:px-6 lg:px-8 mx-auto max-w-7xl font-sans">
            <div class="bg-[#1b1e22] border border-[#2d3139] rounded-xl p-4 mb-6">
                <input
                    type="text"
                    placeholder="Buscar por RGTR, ODC, proyecto o funcionario..."
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
                                <th v-if="$page.props.auth.permissions.includes('gestionar_salidas')" class="px-4 py-3.5 font-semibold text-right">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#2d3139]">
                            <template v-for="salida in filteredSalidas" :key="salida.id">
                                <tr
                                    class="hover:bg-[#1f2329]/30 transition cursor-pointer"
                                    @click="toggleRow(salida.id)"
                                >
                                    <td class="px-4 py-3.5 text-center">
                                        <svg
                                            class="h-4 w-4 text-industrial-muted transform transition-transform duration-200 mx-auto"
                                            :class="{ 'rotate-180': expandedRows[salida.id] }"
                                            fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"
                                        >
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                                        </svg>
                                    </td>
                                    <td class="px-4 py-3.5">
                                        <span class="font-mono text-xs font-bold text-[#f27b00]">
                                            {{ salida.lotes[0]?.nro_registro || '—' }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3.5 text-center font-mono font-bold">{{ salida.nro_lotes }}</td>
                                    <td class="px-4 py-3.5 font-medium text-white text-xs">{{ salida.proyecto_nombre || '—' }}</td>
                                    <td class="px-4 py-3.5 font-mono text-xs text-industrial-muted">{{ salida.fecha_salida }}</td>
                                    <td class="px-4 py-3.5 font-mono text-xs text-white">{{ salida.odc || '—' }}</td>
                                    <td class="px-4 py-3.5 text-xs text-industrial-muted">{{ salida.funcionario_nombre || '—' }}</td>
                                    <td class="px-4 py-3.5 text-xs text-industrial-muted">{{ salida.registrado_por }}</td>
                                    <td class="px-4 py-3.5 text-right" @click.stop>
                                        <a
                                            :href="route('despachos.pdf', salida.id)"
                                            target="_blank"
                                            class="text-xs bg-[#2d3139] hover:bg-[#383d47] text-white font-semibold py-1.5 px-3 rounded border border-[#2d3139] transition mr-2 inline-block"
                                            title="Imprimir Recibo PDF"
                                        >
                                            PDF
                                        </a>
                                        <button
                                            v-if="$page.props.auth.permissions.includes('gestionar_salidas')"
                                            @click="deleteSalida(salida)"
                                            class="text-xs bg-[#3b1d22] hover:bg-[#52252a] text-[#ff8c94] font-semibold py-1.5 px-3 rounded border border-[#8a252c] transition"
                                            title="Revertir despacho y re-sumar stock al lote original."
                                        >
                                            Revertir
                                        </button>
                                    </td>
                                </tr>

                                <tr v-if="expandedRows[salida.id]" class="bg-[#0e1113]">
                                    <td colspan="9" class="px-6 py-4 border-l-2 border-[#ff8c94]">
                                        <div class="mb-3 text-[10px] font-bold text-industrial-muted uppercase tracking-widest">
                                            Detalle de Lotes Consumidos (PEPS)
                                        </div>
                                        <div class="overflow-hidden border border-[#2d3139] rounded-lg">
                                            <table class="w-full text-left text-xs">
                                                <thead class="bg-[#1b1e22] text-[10px] text-industrial-muted uppercase border-b border-[#2d3139]">
                                                    <tr>
                                                        <th class="px-3 py-2 font-semibold">Nro</th>
                                                        <th class="px-3 py-2 font-semibold">Nro. Registro</th>
                                                        <th class="px-3 py-2 font-semibold">Material / Insumo</th>
                                                        <th class="px-3 py-2 font-semibold">Und</th>
                                                        <th class="px-3 py-2 font-semibold">Proyecto</th>
                                                        <th class="px-3 py-2 font-semibold">Fecha</th>
                                                        <th class="px-3 py-2 font-semibold">ODC</th>
                                                        <th class="px-3 py-2 font-semibold">Proveedor</th>
                                                        <th class="px-3 py-2 font-semibold text-right">Cant. Adquirida</th>
                                                        <th class="px-3 py-2 font-semibold text-right">Cant. Utilizada</th>
                                                        <th class="px-3 py-2 font-semibold text-right">Stock en Planta</th>
                                                        <th class="px-3 py-2 font-semibold">Acciones Planificadas</th>
                                                        <th class="px-3 py-2 font-semibold text-center">Estado</th>
                                                    </tr>
                                                </thead>
                                                <tbody class="divide-y divide-[#2d3139] font-mono">
                                                    <tr
                                                        v-for="(lote, idx) in salida.lotes"
                                                        :key="lote.id"
                                                        class="hover:bg-[#1b1e22]/50"
                                                    >
                                                        <td class="px-3 py-2.5 text-industrial-muted">{{ getLoteNro(idx) }}</td>
                                                        <td class="px-3 py-2.5 font-bold text-[#f27b00]">{{ lote.nro_registro }}</td>
                                                        <td class="px-3 py-2.5 text-white font-sans text-xs">{{ lote.material_nombre }}</td>
                                                        <td class="px-3 py-2.5 text-industrial-muted">{{ lote.unidad }}</td>
                                                        <td class="px-3 py-2.5 text-industrial-muted font-sans text-xs">{{ lote.proyecto_nombre }}</td>
                                                        <td class="px-3 py-2.5 text-industrial-muted">{{ lote.fecha_lote }}</td>
                                                        <td class="px-3 py-2.5 text-white">{{ lote.odc || '—' }}</td>
                                                        <td class="px-3 py-2.5 text-industrial-muted">{{ lote.proveedor_nombre || '—' }}</td>
                                                        <td class="px-3 py-2.5 text-industrial-muted text-right">{{ lote.cantidad_adquirida > 0 ? Number(lote.cantidad_adquirida).toLocaleString() + ' ' + lote.unidad : '—' }}</td>
                                                        <td class="px-3 py-2.5 text-[#ff8c94] text-right">{{ lote.cantidad_utilizada > 0 ? Number(lote.cantidad_utilizada).toLocaleString() + ' ' + lote.unidad : '—' }}</td>
                                                        <td class="px-3 py-2.5 text-[#f27b00] font-bold text-right">{{ Number(lote.stock_planta).toLocaleString() }} {{ lote.unidad }}</td>
                                                        <td class="px-3 py-2.5 text-industrial-muted font-sans text-[10px] max-w-[150px] truncate" :title="lote.acciones_planificadas">{{ lote.acciones_planificadas || '—' }}</td>
                                                        <td class="px-3 py-2.5 text-center">
                                                            <span
                                                                class="px-2 py-0.5 rounded text-[9px] font-bold"
                                                                :class="{
                                                                    'bg-[#0f3d2a] border border-[#1a7f4c] text-[#a6ffcc]': lote.estado_lote === 'COMPLETO',
                                                                    'bg-[#f27b00]/10 border border-[#f27b00]/30 text-[#f27b00]': lote.estado_lote === 'PARCIAL',
                                                                    'bg-[#3b1d22] border border-[#8a252c] text-[#ff8c94]': lote.estado_lote === 'AGOTADO',
                                                                }"
                                                            >{{ lote.estado_lote }}</span>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                        <div class="mt-3 grid grid-cols-2 gap-4 text-xs">
                                            <div>
                                                <span class="font-bold text-industrial-muted uppercase text-[10px]">Uso / Destino:</span>
                                                <span class="text-industrial-foreground ml-1">{{ salida.uso || '—' }}</span>
                                            </div>
                                            <div>
                                                <span class="font-bold text-industrial-muted uppercase text-[10px]">Funcionario:</span>
                                                <span class="text-industrial-foreground ml-1">{{ salida.funcionario_nombre || '—' }}</span>
                                                <span v-if="salida.funcionario_cargo" class="text-industrial-muted ml-1">({{ salida.funcionario_cargo }})</span>
                                            </div>
                                        </div>
                                        <div v-if="salida.observaciones" class="mt-2 text-xs text-industrial-muted">
                                            <span class="font-bold uppercase text-[10px]">Observaciones:</span> {{ salida.observaciones }}
                                        </div>
                                        <div class="mt-2 text-[10px] text-industrial-muted/50">
                                            Registrado: {{ salida.fecha_registro }}
                                        </div>
                                    </td>
                                </tr>
                            </template>
                            <tr v-if="filteredSalidas.length === 0">
                                <td colspan="9" class="px-6 py-12 text-center text-industrial-muted text-sm">
                                    No se encontraron despachos registrados.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>