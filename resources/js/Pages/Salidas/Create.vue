<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref, computed, watch } from 'vue';

const props = defineProps({
    funcionarios: { type: Array, required: true },
    proyectos: { type: Array, required: true },
    materials: { type: Array, required: true },
});

const getTodayDate = () => {
    const today = new Date();
    const yyyy = today.getFullYear();
    const mm = String(today.getMonth() + 1).padStart(2, '0');
    const dd = String(today.getDate()).padStart(2, '0');
    return `${yyyy}-${mm}-${dd}`;
};

const selectedProyectoId = ref('');
const headerFecha = ref(getTodayDate());

const form = useForm({
    id_funcionario: '',
    id_proyecto: '',
    odc: '',
    uso: '',
    observaciones: '',
    fecha_salida: getTodayDate(),
    items: [],
});

const lotesDisponibles = ref([]);

const getSelectedProyectoFecha = () => {
    const proj = props.proyectos.find(p => p.id === Number(selectedProyectoId.value));
    return proj?.fecha || getTodayDate();
};

const onProyectoChange = () => {
    headerFecha.value = getSelectedProyectoFecha();
    form.fecha_salida = headerFecha.value;
    form.id_proyecto = selectedProyectoId.value;
    form.items = [];
    fetchLotes();
};

const fetchLotes = async () => {
    if (!selectedProyectoId.value) {
        lotesDisponibles.value = [];
        return;
    }
    try {
        const response = await fetch(`/despachos/lotes/${selectedProyectoId.value}`);
        const data = await response.json();
        lotesDisponibles.value = data.map(lote => ({
            ...lote,
            selected: false,
            cantidad_salida: 0,
            acciones_planificadas: lote.acciones_planificadas || '',
        }));
    } catch (error) {
        console.error('Error fetching lotes:', error);
        lotesDisponibles.value = [];
    }
};

const selectedLotesCount = computed(() => lotesDisponibles.value.filter(l => l.selected).length);

const toggleLote = (lote) => {
    lote.selected = !lote.selected;
    if (lote.selected && lote.cantidad_salida <= 0) {
        lote.cantidad_salida = Math.min(1, lote.stock_disponible);
    }
};

const isLoteValid = (lote) => {
    if (!lote.selected) return true;
    return lote.cantidad_salida > 0 && lote.cantidad_salida <= lote.stock_disponible;
};

const isFormInvalid = computed(() => {
    const selectedItems = lotesDisponibles.value.filter(l => l.selected);
    if (selectedItems.length === 0) return true;
    return selectedItems.some(l => !isLoteValid(l));
});

const submitForm = () => {
    if (isFormInvalid.value) {
        alert('Seleccione al menos un lote e ingrese cantidades válidas.');
        return;
    }
    form.items = lotesDisponibles.value
        .filter(l => l.selected && l.cantidad_salida > 0)
        .map(l => ({
            id_detalle_ingreso: l.id,
            cantidad_salida: l.cantidad_salida,
            acciones_planificadas: l.acciones_planificadas || null,
        }));
    form.post(route('despachos.store'));
};
</script>

<template>
    <Head title="Registrar Despacho de Materiales" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center gap-4">
                <Link :href="route('despachos.index')" class="text-industrial-muted hover:text-white transition">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                </Link>
                <div>
                    <h2 class="text-2xl font-bold tracking-tight text-white">Registrar Salida / Despacho (PEPS)</h2>
                    <p class="text-sm text-industrial-muted mt-1">Despacho de materiales a obras municipales con deducción PEPS.</p>
                </div>
            </div>
        </template>

        <div class="py-6 px-4 sm:px-6 lg:px-8 mx-auto max-w-7xl font-sans">
            <form @submit.prevent="submitForm" class="space-y-6">
                <div class="bg-[#1b1e22] border border-[#2d3139] rounded-xl p-6 shadow-xl">
                    <h3 class="text-base font-bold text-white tracking-tight mb-4 border-b border-[#2d3139] pb-2">
                        1. Información de la Entrega y Destino
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <label class="block text-xs font-semibold text-industrial-muted uppercase tracking-wider mb-2">Proyecto / Obra Municipal Destino</label>
                            <select
                                class="w-full bg-[#0e1113] text-[#e1e6eb] border border-[#2d3139] rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:border-[#f27b00]"
                                v-model="selectedProyectoId"
                                @change="onProyectoChange"
                                required
                            >
                                <option value="" disabled>-- Seleccionar Proyecto --</option>
                                <option v-for="proj in proyectos" :key="proj.id" :value="proj.id">
                                    {{ proj.nombre }}
                                </option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-industrial-muted uppercase tracking-wider mb-2">Fecha de Salida</label>
                            <input
                                type="date"
                                class="w-full bg-[#0e1113] text-[#e1e6eb] border border-[#2d3139] rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:border-[#f27b00] font-mono"
                                v-model="form.fecha_salida"
                            />
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-industrial-muted uppercase tracking-wider mb-2">Orden de Compra (ODC)</label>
                            <input
                                type="text"
                                class="w-full bg-[#0e1113] text-[#e1e6eb] border border-[#2d3139] rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:border-[#f27b00] font-mono uppercase"
                                v-model="form.odc"
                                placeholder="XX/XXXX/ODC/XXX/YY"
                                maxlength="50"
                            />
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-4">
                        <div>
                            <label class="block text-xs font-semibold text-industrial-muted uppercase tracking-wider mb-2">Funcionario Receptor</label>
                            <select
                                class="w-full bg-[#0e1113] text-[#e1e6eb] border border-[#2d3139] rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:border-[#f27b00]"
                                v-model="form.id_funcionario"
                            >
                                <option value="">-- Seleccionar --</option>
                                <option v-for="func in funcionarios" :key="func.id" :value="func.id">
                                    {{ func.nombre }} ({{ func.cargo }})
                                </option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-industrial-muted uppercase tracking-wider mb-2">Destino de Uso</label>
                            <input
                                type="text"
                                class="w-full bg-[#0e1113] text-[#e1e6eb] border border-[#2d3139] rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:border-[#f27b00]"
                                v-model="form.uso"
                                placeholder="Ej. Capa de rodadura tramo 0+000 a 0+500"
                            />
                        </div>
                    </div>

                    <div class="mt-4">
                        <label class="block text-xs font-semibold text-industrial-muted uppercase tracking-wider mb-2">Observaciones</label>
                        <input
                            type="text"
                            class="w-full bg-[#0e1113] text-[#e1e6eb] border border-[#2d3139] rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:border-[#f27b00]"
                            v-model="form.observaciones"
                            placeholder="Observaciones del despacho..."
                        />
                    </div>
                </div>

                <div class="bg-[#1b1e22] border border-[#2d3139] rounded-xl p-6 shadow-xl">
                    <div class="flex items-center justify-between border-b border-[#2d3139] pb-3 mb-4">
                        <h3 class="text-base font-bold text-white tracking-tight">
                            2. Lotes Disponibles para Despacho
                        </h3>
                        <span class="text-xs text-industrial-muted">
                            {{ selectedLotesCount }} lote(s) seleccionado(s)
                        </span>
                    </div>

                    <div v-if="!selectedProyectoId" class="text-center py-12 text-industrial-muted text-sm bg-[#0e1113]/50 rounded-lg border border-dashed border-[#2d3139]">
                        Seleccione primero el Proyecto para cargar los lotes disponibles.
                    </div>

                    <div v-else-if="lotesDisponibles.length === 0" class="text-center py-12 text-industrial-muted text-sm bg-[#0e1113]/50 rounded-lg border border-dashed border-[#2d3139]">
                        No hay lotes disponibles con stock para este proyecto.
                    </div>

                    <div v-else class="overflow-x-auto">
                        <table class="w-full text-left text-sm text-[#e1e6eb]">
                            <thead class="text-xs text-industrial-muted uppercase bg-[#0e1113] border-b border-[#2d3139]">
                                <tr>
                                    <th class="px-3 py-3 font-semibold text-center w-12"></th>
                                    <th class="px-3 py-3 font-semibold">Nro. Registro</th>
                                    <th class="px-3 py-3 font-semibold">Material / Insumo</th>
                                    <th class="px-3 py-3 font-semibold text-center">Und</th>
                                    <th class="px-3 py-3 font-semibold text-right">Stock Disp.</th>
                                    <th class="px-3 py-3 font-semibold text-right">Cant. a Usar</th>
                                    <th class="px-3 py-3 font-semibold">Acciones Planificadas</th>
                                    <th class="px-3 py-3 font-semibold text-center w-20">Estado</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-[#2d3139]">
                                <tr
                                    v-for="lote in lotesDisponibles"
                                    :key="lote.id"
                                    class="hover:bg-[#1f2329]/10 transition"
                                    :class="{ 'bg-[#f27b00]/5': lote.selected }"
                                >
                                    <td class="px-3 py-3 text-center">
                                        <input
                                            type="checkbox"
                                            class="w-4 h-4 rounded border-[#2d3139] bg-[#0e1113] text-[#f27b00] focus:ring-[#f27b00] focus:ring-offset-0"
                                            v-model="lote.selected"
                                            @change="toggleLote(lote)"
                                        />
                                    </td>
                                    <td class="px-3 py-3 font-mono text-xs font-bold text-[#f27b00]">{{ lote.nro_registro }}</td>
                                    <td class="px-3 py-3 text-xs font-sans">{{ lote.material_nombre }}</td>
                                    <td class="px-3 py-3 text-center text-xs text-industrial-muted">{{ lote.unidad }}</td>
                                    <td class="px-3 py-3 text-right font-mono text-xs">
                                        <span class="text-[#f27b00] font-bold">{{ Number(lote.stock_disponible).toLocaleString() }}</span>
                                        <span class="text-industrial-muted ml-1">{{ lote.unidad }}</span>
                                    </td>
                                    <td class="px-3 py-3">
                                        <div class="flex items-center rounded-lg overflow-hidden bg-[#0e1113] border border-[#2d3139]">
                                            <input
                                                type="number"
                                                step="0.01"
                                                min="0"
                                                :max="lote.stock_disponible"
                                                class="flex-1 bg-transparent text-right text-[#e1e6eb] border-none px-3 py-2 text-sm focus:outline-none focus:ring-0 font-mono disabled:opacity-50"
                                                v-model="lote.cantidad_salida"
                                                :disabled="!lote.selected"
                                                :class="{ 'text-[#ff8c94]': lote.selected && lote.cantidad_salida > lote.stock_disponible }"
                                            />
                                            <span class="bg-[#1b1e22] text-xs font-mono text-industrial-muted px-2 py-2 border-l border-[#2d3139]">
                                                {{ lote.unidad }}
                                            </span>
                                        </div>
                                        <div v-if="lote.selected && lote.cantidad_salida > lote.stock_disponible" class="text-xs text-[#ff8c94] mt-1">
                                            Supera stock (max: {{ Number(lote.stock_disponible).toLocaleString() }})
                                        </div>
                                    </td>
                                    <td class="px-3 py-3">
                                        <input
                                            type="text"
                                            class="w-full bg-[#0e1113] text-[#e1e6eb] border border-[#2d3139] rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-[#f27b00] font-sans"
                                            v-model="lote.acciones_planificadas"
                                            :disabled="!lote.selected"
                                            placeholder="Uso previsto del material..."
                                            :class="{ 'opacity-50': !lote.selected }"
                                        />
                                    </td>
                                    <td class="px-3 py-3 text-center">
                                        <span
                                            class="px-2 py-0.5 rounded text-[9px] font-bold"
                                            :class="{
                                                'bg-[#0f3d2a] border border-[#1a7f4c] text-[#a6ffcc]': lote.stock_planta === 0 || lote.estado_lote === 'AGOTADO',
                                                'bg-[#f27b00]/10 border border-[#f27b00]/30 text-[#f27b00]': lote.stock_planta > 0 && lote.stock_planta < lote.cantidad_adquirida,
                                                'bg-[#1b1e22] border border-[#2d3139] text-industrial-muted': lote.stock_planta === lote.cantidad_adquirida,
                                            }"
                                        >
                                            {{ lote.estado_lote }}
                                        </span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="flex justify-end gap-3 pt-4">
                    <Link
                        :href="route('despachos.index')"
                        class="bg-[#2d3139] hover:bg-[#383d47] text-white font-bold py-2.5 px-5 rounded-lg text-sm tracking-wider uppercase transition"
                    >
                        Cancelar
                    </Link>
                    <button
                        type="submit"
                        class="bg-[#f27b00] hover:bg-[#ff9426] text-[#111417] font-bold py-2.5 px-8 rounded-lg text-sm tracking-wider uppercase transition disabled:opacity-50"
                        :disabled="form.processing || isFormInvalid"
                    >
                        {{ form.processing ? 'Registrando...' : 'Registrar Salida' }}
                    </button>
                </div>
            </form>
        </div>
    </AuthenticatedLayout>
</template>