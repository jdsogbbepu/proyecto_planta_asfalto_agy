<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref, computed, watch } from 'vue';

const props = defineProps({
    funcionarios: {
        type: Array,
        required: true,
    },
    proyectos: {
        type: Array,
        required: true,
    },
    materials: {
        type: Array,
        required: true,
    },
    stocksPorProyecto: {
        type: Array,
        required: true,
    },
});

// Setup default date to today
const getTodayDate = () => {
    const today = new Date();
    const yyyy = today.getFullYear();
    const mm = String(today.getMonth() + 1).padStart(2, '0');
    const dd = String(today.getDate()).padStart(2, '0');
    return `${yyyy}-${mm}-${dd}`;
};

const form = useForm({
    id_funcionario: '',
    id_proyecto: '',
    uso: '',
    fecha_salida: getTodayDate(),
    items: [
        { id_material: '', cantidad: 1 }
    ]
});

// Reset items if project changes (since stock balances belong strictly to specific project pools)
watch(() => form.id_proyecto, () => {
    form.items = [{ id_material: '', cantidad: 1 }];
});

// Get list of materials that have available stock for the currently selected project
const availableMaterials = computed(() => {
    if (!form.id_proyecto) return [];
    
    // Filter stocksPorProyecto by chosen project
    const projectStocks = props.stocksPorProyecto.filter(s => s.id_proyecto === Number(form.id_proyecto));
    
    // Map materials matching those project stocks
    return projectStocks.map(stock => {
        const mat = props.materials.find(m => m.id === stock.id_material);
        if (!mat) return null;
        return {
            id: mat.id,
            nombre: mat.nombre,
            unidad: mat.medida?.abreviacion,
            stock_disponible: stock.stock_disponible
        };
    }).filter(Boolean);
});

// Get maximum stock available for a selected material in the chosen project
const getAvailableStock = (materialId) => {
    if (!form.id_proyecto || !materialId) return 0;
    const stockRecord = props.stocksPorProyecto.find(
        s => s.id_proyecto === Number(form.id_proyecto) && s.id_material === Number(materialId)
    );
    return stockRecord ? Number(stockRecord.stock_disponible) : 0;
};

const getMaterialUnit = (materialId) => {
    const mat = props.materials.find(m => m.id === Number(materialId));
    return mat && mat.medida ? mat.medida.abreviacion : '';
};

// Add item row
const addItemRow = () => {
    form.items.push({ id_material: '', cantidad: 1 });
};

// Remove item row
const removeItemRow = (index) => {
    if (form.items.length > 1) {
        form.items.splice(index, 1);
    } else {
        alert('Debe registrar al menos un material/insumo en el despacho.');
    }
};

// Check if the form is invalid due to over-dispatching stock
const isOverDispatched = computed(() => {
    if (!form.id_proyecto) return true;
    
    return form.items.some(item => {
        if (!item.id_material) return true;
        const available = getAvailableStock(item.id_material);
        return Number(item.cantidad) <= 0 || Number(item.cantidad) > available;
    });
});

// Submit form
const submitForm = () => {
    if (isOverDispatched.value) {
        alert('No se puede registrar el despacho: verifique que las cantidades ingresadas no superen el stock disponible en almacén para esta obra.');
        return;
    }
    form.post(route('despachos.store'));
};
</script>

<template>
    <Head title="Registrar Despacho de Materiales" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center gap-4">
                <Link
                    :href="route('despachos.index')"
                    class="text-industrial-muted hover:text-white transition duration-150"
                >
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                </Link>
                <div>
                    <h2 class="text-2xl font-bold tracking-tight text-white">Registrar Salida / Despacho (PEPS)</h2>
                    <p class="text-sm text-industrial-muted mt-1 font-sans">Efectúe despachos a obras municipales reduciendo existencias bajo el método de primeros en entrar, primeros en salir.</p>
                </div>
            </div>
        </template>

        <div class="py-6 px-4 sm:px-6 lg:px-8 mx-auto max-w-7xl font-sans">
            <form @submit.prevent="submitForm" class="space-y-6">
                <!-- Section 1: Dispatch Destination Details -->
                <div class="bg-[#1b1e22] border border-[#2d3139] rounded-xl p-6 shadow-xl">
                    <h3 class="text-base font-bold text-white tracking-tight mb-4 border-b border-[#2d3139] pb-2">
                        1. Información de la Entrega y Destino
                    </h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                        <!-- Project Selector -->
                        <div>
                            <label class="block text-xs font-semibold text-industrial-muted uppercase tracking-wider mb-2">Proyecto / Obra Municipal</label>
                            <select
                                class="w-full bg-[#0e1113] text-[#e1e6eb] border border-[#2d3139] rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:border-[#f27b00]"
                                v-model="form.id_proyecto"
                                required
                            >
                                <option value="" disabled>-- Seleccione Proyecto --</option>
                                <option v-for="proj in proyectos" :key="proj.id" :value="proj.id">
                                    {{ proj.nombre }}
                                </option>
                            </select>
                            <div v-if="form.errors.id_proyecto" class="mt-1 text-xs text-[#ff8c94] font-mono">{{ form.errors.id_proyecto }}</div>
                        </div>

                        <!-- Officer Receiver Selector -->
                        <div>
                            <label class="block text-xs font-semibold text-industrial-muted uppercase tracking-wider mb-2">Funcionario Receptor (Firma Ticket)</label>
                            <select
                                class="w-full bg-[#0e1113] text-[#e1e6eb] border border-[#2d3139] rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:border-[#f27b00]"
                                v-model="form.id_funcionario"
                                required
                            >
                                <option value="" disabled>-- Seleccione Funcionario --</option>
                                <option v-for="func in funcionarios" :key="func.id" :value="func.id">
                                    {{ func.nombre }} ({{ func.cargo }})
                                </option>
                            </select>
                            <div v-if="form.errors.id_funcionario" class="mt-1 text-xs text-[#ff8c94] font-mono">{{ form.errors.id_funcionario }}</div>
                        </div>

                        <!-- Date -->
                        <div>
                            <label class="block text-xs font-semibold text-industrial-muted uppercase tracking-wider mb-2">Fecha de Salida</label>
                            <input
                                type="date"
                                class="w-full bg-[#0e1113] text-[#e1e6eb] border border-[#2d3139] rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:border-[#f27b00] font-mono"
                                v-model="form.fecha_salida"
                                required
                            />
                            <div v-if="form.errors.fecha_salida" class="mt-1 text-xs text-[#ff8c94] font-mono">{{ form.errors.fecha_salida }}</div>
                        </div>

                        <!-- Use / Destination details -->
                        <div>
                            <label class="block text-xs font-semibold text-industrial-muted uppercase tracking-wider mb-2">Destino de Uso / Aplicación</label>
                            <input
                                type="text"
                                class="w-full bg-[#0e1113] text-[#e1e6eb] border border-[#2d3139] rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:border-[#f27b00]"
                                v-model="form.uso"
                                required
                                placeholder="Ej. Capa de liga progresiva 0+120"
                            />
                            <div v-if="form.errors.uso" class="mt-1 text-xs text-[#ff8c94] font-mono">{{ form.errors.uso }}</div>
                        </div>
                    </div>
                </div>

                <!-- Section 2: Items / Quantities to dispatch -->
                <div class="bg-[#1b1e22] border border-[#2d3139] rounded-xl p-6 shadow-xl">
                    <div class="flex items-center justify-between border-b border-[#2d3139] pb-3 mb-4">
                        <h3 class="text-base font-bold text-white tracking-tight">
                            2. Materiales e Insumos a Despachar (Deducción PEPS)
                        </h3>
                        <button
                            type="button"
                            @click="addItemRow"
                            :disabled="!form.id_proyecto"
                            class="bg-[#2d3139] hover:bg-[#383d47] text-white font-semibold py-1.5 px-4 rounded border border-[#2d3139] text-xs uppercase tracking-wider transition duration-150 flex items-center gap-1.5 disabled:opacity-20 disabled:cursor-not-allowed"
                        >
                            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                            </svg>
                            <span>Agregar Insumo</span>
                        </button>
                    </div>

                    <div v-if="!form.id_proyecto" class="text-center py-8 text-industrial-muted text-sm bg-[#0e1113]/50 rounded-lg border border-dashed border-[#2d3139]">
                        Seleccione primero el Proyecto/Obra Municipal de destino para cargar los insumos disponibles en stock.
                    </div>

                    <!-- Items rows list -->
                    <div v-else class="overflow-x-auto">
                        <table class="w-full text-left text-sm text-[#e1e6eb]">
                            <thead class="text-xs text-industrial-muted uppercase bg-[#0e1113] border-b border-[#2d3139]">
                                <tr>
                                    <th class="px-4 py-3 font-semibold">Material / Insumo Disponible</th>
                                    <th class="px-4 py-3 font-semibold text-right w-52">Cantidad a Despachar</th>
                                    <th class="px-4 py-3 font-semibold text-center w-20">Acción</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-[#2d3139]">
                                <tr v-for="(item, index) in form.items" :key="index" class="hover:bg-[#1f2329]/10">
                                    <!-- Material selector (only showing those with active stocks in this project) -->
                                    <td class="px-4 py-3">
                                        <select
                                            class="w-full bg-[#0e1113] text-[#e1e6eb] border border-[#2d3139] rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-[#f27b00]"
                                            v-model="item.id_material"
                                            required
                                        >
                                            <option value="" disabled>-- Seleccione Material --</option>
                                            <option v-for="mat in availableMaterials" :key="mat.id" :value="mat.id">
                                                {{ mat.nombre }} (Disponible: {{ Number(mat.stock_disponible).toLocaleString() }} {{ mat.unidad }})
                                            </option>
                                        </select>
                                        <div v-if="availableMaterials.length === 0" class="text-xs text-[#ff8c94] mt-1 font-sans">
                                            No hay stocks de materiales ingresados para este proyecto en el almacén.
                                        </div>
                                    </td>

                                    <!-- Dispatch Quantity -->
                                    <td class="px-4 py-3 text-right">
                                        <div class="relative flex items-center justify-end rounded-lg overflow-hidden bg-[#0e1113] border border-[#2d3139]">
                                            <input
                                                type="number"
                                                step="0.01"
                                                min="0.01"
                                                class="w-full bg-transparent text-right text-[#e1e6eb] border-none px-3 py-2 text-sm focus:outline-none focus:ring-0 font-mono"
                                                v-model="item.cantidad"
                                                required
                                            />
                                            <span 
                                                v-if="item.id_material"
                                                class="bg-[#1b1e22] text-xs font-mono font-bold text-industrial-muted px-3 py-2 border-l border-[#2d3139] select-none"
                                            >
                                                {{ getMaterialUnit(item.id_material) }}
                                            </span>
                                        </div>

                                        <!-- Real-time Validation warning -->
                                        <div 
                                            v-if="item.id_material && Number(item.cantidad) > getAvailableStock(item.id_material)"
                                            class="text-xs text-[#ff8c94] mt-1 font-sans text-left font-semibold flex items-center gap-1"
                                        >
                                            <svg class="h-3.5 w-3.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                            </svg>
                                            <span>Supera el stock de obra (Max: {{ Number(getAvailableStock(item.id_material)).toLocaleString() }})</span>
                                        </div>
                                    </td>

                                    <!-- Action -->
                                    <td class="px-4 py-3 text-center">
                                        <button
                                            type="button"
                                            @click="removeItemRow(index)"
                                            class="text-xs bg-[#3b1d22] hover:bg-[#52252a] text-[#ff8c94] font-semibold py-1.5 px-3 rounded border border-[#8a252c] transition duration-150"
                                        >
                                            Quitar
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Footer Actions -->
                <div class="flex justify-end gap-3 pt-4">
                    <Link
                        :href="route('despachos.index')"
                        class="bg-[#2d3139] hover:bg-[#383d47] text-white font-bold py-2.5 px-5 rounded-lg text-sm tracking-wider uppercase transition duration-150"
                    >
                        Cancelar
                    </Link>
                    <button
                        type="submit"
                        class="bg-[#f27b00] hover:bg-[#ff9426] text-[#111417] font-bold py-2.5 px-8 rounded-lg text-sm tracking-wider uppercase transition duration-150 disabled:opacity-50 disabled:cursor-not-allowed"
                        :disabled="form.processing || isOverDispatched"
                    >
                        {{ form.processing ? 'Registrando...' : 'Registrar Salida' }}
                    </button>
                </div>
            </form>
        </div>
    </AuthenticatedLayout>
</template>
