<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import axios from 'axios';

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
    observaciones: '', // Observaciones de salida (el nuevo campo)
    fecha_salida: getTodayDate(),
    items: [],
});

const lotesDisponibles = ref([]);
const obsIngreso = ref(''); // Observaciones traídas del ingreso

// Determina si un campo Cant. Utilizada es válido (cantidad_salida > 0 y <= stock_planta)
const isCantidadValid = (lote) => {
    const num = parseFloat(lote.cantidad_salida);
    return num > 0 && num <= lote.stock_planta;
};

const getSelectedProyectoFecha = () => {
    const proj = props.proyectos.find(p => p.id === Number(selectedProyectoId.value));
    return proj?.fecha || getTodayDate();
};

const onProyectoChange = () => {
    headerFecha.value = getSelectedProyectoFecha();
    form.fecha_salida = headerFecha.value;
    form.id_proyecto = selectedProyectoId.value;
    form.items = [];
    form.odc = '';
    obsIngreso.value = '';
    fetchLotes();
};

const fetchLotes = async () => {
    if (!selectedProyectoId.value) {
        lotesDisponibles.value = [];
        return;
    }
    try {
        // Usamos una URL directa en lugar de route() en el script setup para evitar ReferenceErrors
        const response = await axios.get('/despachos/lotes/' + selectedProyectoId.value);
        const data = response.data;
        
        // Autocompletar cabecera con el último ingreso
        if (data.odc_ingreso) form.odc = data.odc_ingreso;
        if (data.observaciones_ingreso) obsIngreso.value = data.observaciones_ingreso;

        lotesDisponibles.value = data.lotes.map(lote => ({
            ...lote,
            selected: false,
            // Esta cantidad_salida representa lo que se va a sumar a la cantidad_utilizada en este despacho
            cantidad_salida: 0, 
            acciones_planificadas: lote.acciones_planificadas || '',
        }));
    } catch (error) {
        console.error('Error fetching lotes:', error);
        lotesDisponibles.value = [];
    }
};

const selectedLotesCount = computed(() => lotesDisponibles.value.filter(l => l.selected).length);

// Sincroniza disabled cuando cambia la propiedad 'selected'
const toggleLote = (lote) => {
    if (!lote.selected) {
        lote.cantidad_salida = 0;
        lote.acciones_planificadas = '';
    } else if (lote.cantidad_salida <= 0) {
        // Por defecto, si seleccionan, asumen que usarán todo el stock restante
        lote.cantidad_salida = Math.max(0, lote.stock_planta);
    }
};

const isLoteValid = (lote) => {
    return lote.cantidad_salida > 0 && lote.cantidad_salida <= lote.stock_planta;
};

const isFormInvalid = computed(() => {
    const selectedItems = lotesDisponibles.value.filter(l => l.selected);
    if (selectedItems.length === 0) return true;
    return selectedItems.some(l => !isLoteValid(l));
});

const getTotalStock = (lote) => {
    return Number(lote.stock_planta - (lote.selected ? lote.cantidad_salida : 0)).toLocaleString();
};

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
    form.post('/despachos');
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
                    <p class="text-sm text-industrial-muted mt-1">Conciliación de consumos y saldos de materiales por proyecto.</p>
                </div>
            </div>
        </template>

        <div class="py-6 px-4 sm:px-6 lg:px-8 mx-auto max-w-full 2xl:max-w-screen-2xl font-sans">
            <form @submit.prevent="submitForm" class="space-y-6">
                <div class="bg-[#1b1e22] border border-[#2d3139] rounded-xl p-6 shadow-xl">
                    <h3 class="text-base font-bold text-white tracking-tight mb-4 border-b border-[#2d3139] pb-2">
                        1. Información del Proyecto Relacionado
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
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
                            <label class="block text-xs font-semibold text-industrial-muted uppercase tracking-wider mb-2">Fecha (Proyecto)</label>
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
                            />
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-industrial-muted uppercase tracking-wider mb-2">Funcionario Receptor</label>
                            <select
                                class="w-full bg-[#0e1113] text-[#e1e6eb] border border-[#2d3139] rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:border-[#f27b00]"
                                v-model="form.id_funcionario"
                                required
                            >
                                <option value="">-- Seleccionar --</option>
                                <option v-for="func in funcionarios" :key="func.id" :value="func.id">
                                    {{ func.nombre }} ({{ func.cargo }})
                                </option>
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-4">
                        <div>
                            <label class="block text-xs font-semibold text-industrial-muted uppercase tracking-wider mb-2">Observaciones de Ingresos</label>
                            <input
                                type="text"
                                class="w-full bg-[#0e1113]/50 text-[#8b949e] border border-[#2d3139] rounded-lg px-4 py-2.5 text-sm focus:outline-none cursor-not-allowed"
                                :value="obsIngreso"
                                readonly
                                placeholder="Se autocompletará con las observaciones del ingreso..."
                            />
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-industrial-muted uppercase tracking-wider mb-2">Observaciones de Salida</label>
                            <input
                                type="text"
                                class="w-full bg-[#0e1113] text-[#e1e6eb] border border-[#2d3139] rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:border-[#f27b00]"
                                v-model="form.observaciones"
                                placeholder="Novedades en la etapa de salidas..."
                            />
                        </div>
                    </div>
                </div>

                <div class="bg-[#1b1e22] border border-[#2d3139] rounded-xl p-6 shadow-xl">
                    <div class="flex items-center justify-between border-b border-[#2d3139] pb-3 mb-4">
                        <h3 class="text-base font-bold text-white tracking-tight">
                            2. Registro de Salidas del Proyecto
                        </h3>
                        <span class="text-xs text-industrial-muted font-bold px-3 py-1 bg-[#2d3139] rounded-full">
                            {{ selectedLotesCount }} ítem(s) seleccionado(s) para editar
                        </span>
                    </div>

                    <div v-if="!selectedProyectoId" class="text-center py-12 text-industrial-muted text-sm bg-[#0e1113]/50 rounded-lg border border-dashed border-[#2d3139]">
                        Seleccione primero el Proyecto para cargar los materiales.
                    </div>

                    <div v-else-if="lotesDisponibles.length === 0" class="text-center py-12 text-industrial-muted text-sm bg-[#0e1113]/50 rounded-lg border border-dashed border-[#2d3139]">
                        No se encontraron registros de ingreso para este proyecto.
                    </div>

                    <div v-else class="overflow-x-auto pb-4">
                        <table class="w-full text-left text-sm text-[#e1e6eb] whitespace-nowrap">
                            <thead class="text-xs text-industrial-muted uppercase bg-[#0e1113] border-b border-[#2d3139]">
                                <tr>
                                    <th class="px-3 py-3 font-semibold">Nro RGTR</th>
                                    <th class="px-3 py-3 font-semibold">Nro Lote</th>
                                    <th class="px-3 py-3 font-semibold">Material</th>
                                    <th class="px-3 py-3 font-semibold">Fecha (Lote)</th>
                                    <th class="px-2 py-3 font-semibold text-center">Unidad</th>
                                    <th class="px-3 py-3 font-semibold text-right">Cant. Adquirida</th>
                                    <th class="px-3 py-3 font-semibold text-right w-32 bg-[#2d3139]/30 border-l border-[#2d3139]">Cant. Utilizada</th>
                                    <th class="px-3 py-3 font-semibold text-right">Stock en Planta</th>
                                    <th class="px-3 py-3 font-semibold min-w-[200px]">Acciones Planificadas</th>
                                    <th class="px-2 py-3 font-semibold text-center w-20">Concluir</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-[#2d3139]">
                                <tr
                                    v-for="lote in lotesDisponibles"
                                    :key="lote.id"
                                    class="hover:bg-[#1f2329]/10 transition"
                                    :class="{ 'bg-[#f27b00]/5': lote.selected }"
                                >
                                    <td class="px-3 py-3 font-mono text-xs font-bold text-[#f27b00]">{{ lote.nro_registro }}</td>
                                    <td class="px-3 py-3 font-mono text-xs text-industrial-muted">LOTE-{{ String(lote.nro_lote).padStart(4, '0') }}</td>
                                    <td class="px-3 py-3 text-xs font-sans truncate max-w-[150px]" :title="lote.material_nombre">{{ lote.material_nombre }}</td>
                                    <td class="px-3 py-3 text-xs text-industrial-muted">{{ lote.fecha_lote }}</td>
                                    <td class="px-2 py-3 text-center text-xs font-bold text-industrial-muted bg-[#0e1113]">{{ lote.unidad }}</td>
                                    <td class="px-3 py-3 text-right font-mono text-xs">
                                        <span class="text-emerald-400 font-bold">{{ Number(lote.cantidad_adquirida).toLocaleString() }}</span>
                                    </td>
                                    <!-- Celda editable: Cantidad Utilizada -->
                                    <td class="px-2 py-2 bg-[#2d3139]/30 border-l border-[#2d3139]">
                                        <div class="flex flex-col gap-1">
                                            <!-- Muestra la acumulada previa + la nueva a usar -->
                                            <div class="flex items-center rounded-lg overflow-hidden bg-[#0e1113] border" :class="!lote.selected ? 'border-[#f27b00]' : 'border-[#2d3139]'">
                                                <input
                                                    type="number"
                                                    step="0.01"
                                                    min="0"
                                                    :max="lote.stock_planta"
                                                    class="flex-1 bg-transparent text-right text-[#e1e6eb] border-none px-2 py-1.5 text-sm focus:outline-none focus:ring-0 font-mono disabled:opacity-30"
                                                    v-model="lote.cantidad_salida"
                                                    :disabled="lote.selected"
                                                    :class="{ 'text-[#ff8c94]': !lote.selected && lote.cantidad_salida > lote.stock_planta }"
                                                />
                                            </div>
                                            <span v-if="lote.cantidad_utilizada > 0" class="text-[9px] text-industrial-muted text-right pr-1">
                                                Previo: {{ Number(lote.cantidad_utilizada).toLocaleString() }}
                                            </span>
                                        </div>
                                    </td>
                                    <!-- Stock en Planta = Cant Adquirida - (Cant Utilizada Histórica + Nueva Cant Utilizada) -->
                                    <td class="px-3 py-3 text-right font-mono text-xs">
                                        <span
                                            class="font-bold"
                                            :class="((lote.stock_planta - (!lote.selected ? lote.cantidad_salida : 0)) <= 0) ? 'text-[#ff8c94]' : 'text-white'"
                                        >
                                            {{ Number(lote.stock_planta - (!lote.selected ? lote.cantidad_salida : 0)).toLocaleString() }}
                                        </span>
                                    </td>
                                    <!-- Acciones Planificadas -->
                                    <td class="px-3 py-2">
                                        <input
                                            type="text"
                                            class="w-full bg-[#0e1113] text-[#e1e6eb] border border-[#2d3139] rounded-lg px-3 py-1.5 text-xs focus:outline-none focus:border-[#f27b00] font-sans disabled:opacity-50"
                                            v-model="lote.acciones_planificadas"
                                            :disabled="lote.selected"
                                            placeholder="Uso previsto..."
                                        />
                                    </td>
                                    <!-- Columna Concluir -->
                                    <td class="px-2 py-3 text-center">
                                        <input
                                            type="checkbox"
                                            class="w-4 h-4 rounded border-[#2d3139] bg-[#0e1113] text-[#f27b00] focus:ring-[#f27b00] focus:ring-offset-0 cursor-pointer"
                                            v-model="lote.selected"
                                            @change="toggleLote(lote)"
                                        />
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
                        {{ form.processing ? 'Registrando...' : 'Guardar Conciliación' }}
                    </button>
                </div>
            </form>
        </div>
    </AuthenticatedLayout>
</template>