<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    materials: {
        type: Array,
        required: true,
    },
    proveedores: {
        type: Array,
        required: true,
    },
    proyectos: {
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
    nro_ticket: '',
    odc: '',
    id_proveedor: '',
    fecha_adquirida: getTodayDate(),
    observaciones: '',
    items: [
        { id_material: '', id_proyecto: '', cantidad: 1 }
    ]
});

// Add a row to items list
const addItemRow = () => {
    // Set default select values based on first available options
    const firstMaterial = props.materials[0]?.id || '';
    const firstProyecto = props.proyectos[0]?.id || '';
    form.items.push({
        id_material: firstMaterial,
        id_proyecto: firstProyecto,
        cantidad: 1
    });
};

// Remove a row from items list
const removeItemRow = (index) => {
    if (form.items.length > 1) {
        form.items.splice(index, 1);
    } else {
        alert('Debe registrar al menos un material/lote en el ingreso.');
    }
};

// Get measurement unit abbreviation for a selected material
const getMaterialUnit = (materialId) => {
    const mat = props.materials.find(m => m.id === Number(materialId));
    return mat && mat.medida ? mat.medida.abreviacion : '';
};

// Submit form
const submitForm = () => {
    form.post(route('ingresos.store'));
};
</script>

<template>
    <Head title="Registrar Ingreso de Materiales" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center gap-4">
                <Link
                    :href="route('ingresos.index')"
                    class="text-industrial-muted hover:text-white transition duration-150"
                >
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                </Link>
                <div>
                    <h2 class="text-2xl font-bold tracking-tight text-white">Registrar Ingreso de Almacén</h2>
                    <p class="text-sm text-industrial-muted mt-1 font-sans">Cree lotes de inventario ingresando tickets de báscula de proveedores.</p>
                </div>
            </div>
        </template>

        <div class="py-6 px-4 sm:px-6 lg:px-8 mx-auto max-w-7xl font-sans">
            <form @submit.prevent="submitForm" class="space-y-6">
                <!-- Section 1: Ticket Metadata -->
                <div class="bg-[#1b1e22] border border-[#2d3139] rounded-xl p-6 shadow-xl">
                    <h3 class="text-base font-bold text-white tracking-tight mb-4 border-b border-[#2d3139] pb-2">
                        1. Datos Generales del Documento / Ticket
                    </h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                        <!-- Ticket Number -->
                        <div>
                            <label class="block text-xs font-semibold text-industrial-muted uppercase tracking-wider mb-2">Nro. de Ticket Báscula</label>
                            <input
                                type="text"
                                class="w-full bg-[#0e1113] text-[#e1e6eb] border border-[#2d3139] rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:border-[#f27b00] font-mono font-bold"
                                v-model="form.nro_ticket"
                                placeholder="Ej. TKT-9840"
                            />
                            <div v-if="form.errors.nro_ticket" class="mt-1 text-xs text-[#ff8c94] font-mono">{{ form.errors.nro_ticket }}</div>
                        </div>

                        <!-- Purchase Order (ODC) -->
                        <div>
                            <label class="block text-xs font-semibold text-industrial-muted uppercase tracking-wider mb-2">Orden de Compra (ODC)</label>
                            <input
                                type="text"
                                class="w-full bg-[#0e1113] text-[#e1e6eb] border border-[#2d3139] rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:border-[#f27b00] font-mono"
                                v-model="form.odc"
                                placeholder="Ej. ODC-2026-042"
                            />
                            <div v-if="form.errors.odc" class="mt-1 text-xs text-[#ff8c94] font-mono">{{ form.errors.odc }}</div>
                        </div>

                        <!-- Supplier -->
                        <div>
                            <label class="block text-xs font-semibold text-industrial-muted uppercase tracking-wider mb-2">Proveedor</label>
                            <select
                                class="w-full bg-[#0e1113] text-[#e1e6eb] border border-[#2d3139] rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:border-[#f27b00]"
                                v-model="form.id_proveedor"
                            >
                                <option value="">-- Seleccionar Proveedor (Opcional) --</option>
                                <option v-for="prov in proveedores" :key="prov.id" :value="prov.id">
                                    {{ prov.razon_social }}
                                </option>
                            </select>
                            <div v-if="form.errors.id_proveedor" class="mt-1 text-xs text-[#ff8c94] font-mono">{{ form.errors.id_proveedor }}</div>
                        </div>

                        <!-- Acquired Date -->
                        <div>
                            <label class="block text-xs font-semibold text-industrial-muted uppercase tracking-wider mb-2">Fecha de Ingreso</label>
                            <input
                                type="date"
                                class="w-full bg-[#0e1113] text-[#e1e6eb] border border-[#2d3139] rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:border-[#f27b00] font-mono"
                                v-model="form.fecha_adquirida"
                                required
                            />
                            <div v-if="form.errors.fecha_adquirida" class="mt-1 text-xs text-[#ff8c94] font-mono">{{ form.errors.fecha_adquirida }}</div>
                        </div>
                    </div>

                    <!-- Observations -->
                    <div class="mt-6">
                        <label class="block text-xs font-semibold text-industrial-muted uppercase tracking-wider mb-2">Observaciones</label>
                        <textarea
                            class="w-full bg-[#0e1113] text-[#e1e6eb] border border-[#2d3139] rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:border-[#f27b00] h-16 resize-none"
                            v-model="form.observaciones"
                            placeholder="Detalles adicionales, transportista, placa del vehículo, etc..."
                        ></textarea>
                        <div v-if="form.errors.observaciones" class="mt-1 text-xs text-[#ff8c94] font-mono">{{ form.errors.observaciones }}</div>
                    </div>
                </div>

                <!-- Section 2: Items / Lotes -->
                <div class="bg-[#1b1e22] border border-[#2d3139] rounded-xl p-6 shadow-xl">
                    <div class="flex items-center justify-between border-b border-[#2d3139] pb-3 mb-4">
                        <h3 class="text-base font-bold text-white tracking-tight">
                            2. Materiales e Insumos Ingresados (Lotes)
                        </h3>
                        <button
                            type="button"
                            @click="addItemRow"
                            class="bg-[#2d3139] hover:bg-[#383d47] text-white font-semibold py-1.5 px-4 rounded border border-[#2d3139] text-xs uppercase tracking-wider transition duration-150 flex items-center gap-1.5"
                        >
                            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                            </svg>
                            <span>Agregar Fila</span>
                        </button>
                    </div>

                    <!-- Items rows table -->
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm text-[#e1e6eb]">
                            <thead class="text-xs text-industrial-muted uppercase bg-[#0e1113] border-b border-[#2d3139]">
                                <tr>
                                    <th class="px-4 py-3 font-semibold">Material / Insumo</th>
                                    <th class="px-4 py-3 font-semibold">Proyecto / Obra Municipal Destino</th>
                                    <th class="px-4 py-3 font-semibold text-right w-44">Cantidad Ingresada</th>
                                    <th class="px-4 py-3 font-semibold text-center w-20">Acción</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-[#2d3139]">
                                <tr v-for="(item, index) in form.items" :key="index" class="hover:bg-[#1f2329]/10">
                                    <!-- Material Selector -->
                                    <td class="px-4 py-3">
                                        <select
                                            class="w-full bg-[#0e1113] text-[#e1e6eb] border border-[#2d3139] rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-[#f27b00]"
                                            v-model="item.id_material"
                                            required
                                        >
                                            <option value="" disabled>-- Seleccione Material --</option>
                                            <option v-for="mat in materials" :key="mat.id" :value="mat.id">
                                                {{ mat.nombre }}
                                            </option>
                                        </select>
                                    </td>
                                    
                                    <!-- Project Selector -->
                                    <td class="px-4 py-3">
                                        <select
                                            class="w-full bg-[#0e1113] text-[#e1e6eb] border border-[#2d3139] rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-[#f27b00]"
                                            v-model="item.id_proyecto"
                                            required
                                        >
                                            <option value="" disabled>-- Seleccione Proyecto/Obra --</option>
                                            <option v-for="proj in proyectos" :key="proj.id" :value="proj.id">
                                                {{ proj.nombre }}
                                            </option>
                                        </select>
                                    </td>

                                    <!-- Quantity Input -->
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
                        :href="route('ingresos.index')"
                        class="bg-[#2d3139] hover:bg-[#383d47] text-white font-bold py-2.5 px-5 rounded-lg text-sm tracking-wider uppercase transition duration-150"
                    >
                        Cancelar
                    </Link>
                    <button
                        type="submit"
                        class="bg-[#f27b00] hover:bg-[#ff9426] text-[#111417] font-bold py-2.5 px-8 rounded-lg text-sm tracking-wider uppercase transition duration-150 disabled:opacity-50"
                        :disabled="form.processing"
                    >
                        {{ form.processing ? 'Registrando...' : 'Registrar Ingreso' }}
                    </button>
                </div>
            </form>
        </div>
    </AuthenticatedLayout>
</template>
