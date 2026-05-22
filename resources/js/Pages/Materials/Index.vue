<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { ref, computed } from 'vue';

const props = defineProps({
    materials: {
        type: Array,
        required: true,
    },
    unidadMedidas: {
        type: Array,
        required: true,
    },
});

// Tab states
const activeTab = ref('materials'); // 'materials' or 'units'

// Search states
const searchMaterialQuery = ref('');
const searchUnitQuery = ref('');

// Filtered materials
const filteredMaterials = computed(() => {
    return props.materials.filter(m => 
        m.nombre.toLowerCase().includes(searchMaterialQuery.value.toLowerCase()) ||
        (m.codigo_interno && m.codigo_interno.toLowerCase().includes(searchMaterialQuery.value.toLowerCase())) ||
        (m.descripcion && m.descripcion.toLowerCase().includes(searchMaterialQuery.value.toLowerCase()))
    );
});

// Filtered units
const filteredUnits = computed(() => {
    return props.unidadMedidas.filter(u => 
        u.nombre.toLowerCase().includes(searchUnitQuery.value.toLowerCase()) ||
        u.abreviacion.toLowerCase().includes(searchUnitQuery.value.toLowerCase())
    );
});

// Modal state for Material
const isMaterialModalOpen = ref(false);
const isEditingMaterial = ref(false);
const editingMaterialId = ref(null);

const materialForm = useForm({
    codigo_interno: '',
    nombre: '',
    descripcion: '',
    id_medida: '',
    stock_minimo: 0,
});

// Modal state for Unit of Measure
const isUnitModalOpen = ref(false);
const isEditingUnit = ref(false);
const editingUnitId = ref(null);

const unitForm = useForm({
    nombre: '',
    abreviacion: '',
});

// -- Material Handlers --
const openCreateMaterialModal = () => {
    materialForm.clearErrors();
    materialForm.reset();
    materialForm.codigo_interno = '';
    if (props.unidadMedidas.length > 0) {
        materialForm.id_medida = props.unidadMedidas[0].id;
    }
    isEditingMaterial.value = false;
    editingMaterialId.value = null;
    isMaterialModalOpen.value = true;
};

const openEditMaterialModal = (material) => {
    materialForm.clearErrors();
    materialForm.codigo_interno = material.codigo_interno || '';
    materialForm.nombre = material.nombre;
    materialForm.descripcion = material.descripcion || '';
    materialForm.id_medida = material.id_medida;
    materialForm.stock_minimo = material.stock_minimo;
    isEditingMaterial.value = true;
    editingMaterialId.value = material.id;
    isMaterialModalOpen.value = true;
};

const submitMaterialForm = () => {
    if (isEditingMaterial.value) {
        materialForm.put(route('materials.update', editingMaterialId.value), {
            onSuccess: () => isMaterialModalOpen.value = false,
        });
    } else {
        materialForm.post(route('materials.store'), {
            onSuccess: () => isMaterialModalOpen.value = false,
        });
    }
};

const deleteMaterialForm = useForm({});
const confirmDeleteMaterial = (material) => {
    if (confirm(`¿Está seguro de eliminar el material "${material.nombre}"?`)) {
        deleteMaterialForm.delete(route('materials.destroy', material.id));
    }
};

// -- Unit Handlers --
const openCreateUnitModal = () => {
    unitForm.clearErrors();
    unitForm.reset();
    isEditingUnit.value = false;
    editingUnitId.value = null;
    isUnitModalOpen.value = true;
};

const openEditUnitModal = (unit) => {
    unitForm.clearErrors();
    unitForm.nombre = unit.nombre;
    unitForm.abreviacion = unit.abreviacion;
    isEditingUnit.value = true;
    editingUnitId.value = unit.id;
    isUnitModalOpen.value = true;
};

const submitUnitForm = () => {
    if (isEditingUnit.value) {
        unitForm.put(route('unidad-medidas.update', editingUnitId.value), {
            onSuccess: () => isUnitModalOpen.value = false,
        });
    } else {
        unitForm.post(route('unidad-medidas.store'), {
            onSuccess: () => isUnitModalOpen.value = false,
        });
    }
};

const deleteUnitForm = useForm({});
const confirmDeleteUnit = (unit) => {
    if (confirm(`¿Está seguro de eliminar la unidad de medida "${unit.nombre}"?`)) {
        deleteUnitForm.delete(route('unidad-medidas.destroy', unit.id));
    }
};
</script>

<template>
    <Head title="Materiales de Planta" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h2 class="text-2xl font-bold tracking-tight text-white">Catálogo de Materiales y Medidas</h2>
                    <p class="text-sm text-industrial-muted mt-1">Configure los materiales (asfaltos, emulsiones, agregados) y sus unidades de despacho.</p>
                </div>
                
                <!-- Action Buttons depending on role -->
                <div v-if="$page.props.auth.user.role !== 'visor'" class="flex items-center gap-2">
                    <button
                        v-if="activeTab === 'materials'"
                        @click="openCreateMaterialModal"
                        class="bg-[#f27b00] hover:bg-[#ff9426] text-[#111417] font-bold py-2.5 px-4 rounded-lg text-sm tracking-wider uppercase transition duration-150 flex items-center gap-2"
                    >
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                        </svg>
                        <span>Registrar Material</span>
                    </button>
                    <button
                        v-if="activeTab === 'units'"
                        @click="openCreateUnitModal"
                        class="bg-[#f27b00] hover:bg-[#ff9426] text-[#111417] font-bold py-2.5 px-4 rounded-lg text-sm tracking-wider uppercase transition duration-150 flex items-center gap-2"
                    >
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                        </svg>
                        <span>Nueva Unidad</span>
                    </button>
                </div>
            </div>
        </template>

        <div class="py-6 px-4 sm:px-6 lg:px-8 mx-auto max-w-7xl">
            <!-- Custom Tabs -->
            <div class="flex border-b border-[#2d3139] mb-6">
                <button
                    @click="activeTab = 'materials'"
                    class="py-3 px-6 text-sm font-semibold border-b-2 transition duration-150"
                    :class="activeTab === 'materials' ? 'border-[#f27b00] text-white' : 'border-transparent text-industrial-muted hover:text-white'"
                >
                    Materiales / Insumos
                </button>
                <button
                    @click="activeTab = 'units'"
                    class="py-3 px-6 text-sm font-semibold border-b-2 transition duration-150"
                    :class="activeTab === 'units' ? 'border-[#f27b00] text-white' : 'border-transparent text-industrial-muted hover:text-white'"
                >
                    Unidades de Medida
                </button>
            </div>

            <!-- Tab 1: Materials -->
            <div v-if="activeTab === 'materials'">
                <!-- Filter Bar -->
                <div class="bg-[#1b1e22] border border-[#2d3139] rounded-xl p-4 mb-6">
                    <label class="block text-xs font-semibold text-industrial-muted uppercase tracking-wider mb-1.5">Búsqueda rápida</label>
                    <input
                        type="text"
                        placeholder="Buscar por nombre o descripción de material..."
                        class="w-full bg-[#0e1113] text-[#e1e6eb] border border-[#2d3139] rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-[#f27b00]"
                        v-model="searchMaterialQuery"
                    />
                </div>

                <!-- Table -->
                <div class="bg-[#1b1e22] border border-[#2d3139] rounded-xl overflow-hidden shadow-xl">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm text-[#e1e6eb]">
                            <thead class="text-xs text-industrial-muted uppercase bg-[#0e1113] border-b border-[#2d3139]">
                                <tr>
                                    <th class="px-6 py-4 font-semibold">Código</th>
                                    <th class="px-6 py-4 font-semibold">Material / Insumo</th>
                                    <th class="px-6 py-4 font-semibold">Descripción</th>
                                    <th class="px-6 py-4 font-semibold">Unidad</th>
                                    <th class="px-6 py-4 font-semibold text-right">Stock Mínimo</th>
                                    <th v-if="$page.props.auth.user.role !== 'visor'" class="px-6 py-4 font-semibold text-right">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-[#2d3139]">
                                <tr 
                                    v-for="material in filteredMaterials" 
                                    :key="material.id" 
                                    class="hover:bg-[#1f2329]/50 transition duration-150"
                                >
                                    <td class="px-6 py-4 font-mono text-xs font-semibold text-industrial-muted">{{ material.codigo_interno || 'S/C' }}</td>
                                    <td class="px-6 py-4 font-medium text-white">{{ material.nombre }}</td>
                                    <td class="px-6 py-4 text-industrial-muted text-xs">{{ material.descripcion || 'Sin descripción' }}</td>
                                    <td class="px-6 py-4">
                                        <span class="bg-industrial-card border border-[#2d3139] text-[#e1e6eb] px-2 py-0.5 rounded text-xs font-mono">
                                            {{ material.medida?.nombre }} ({{ material.medida?.abreviacion }})
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-right font-mono text-xs font-semibold text-[#f27b00]">
                                        {{ Number(material.stock_minimo).toLocaleString() }}
                                    </td>
                                    <td v-if="$page.props.auth.user.role !== 'visor'" class="px-6 py-4 text-right space-x-2">
                                        <button
                                            @click="openEditMaterialModal(material)"
                                            class="text-xs bg-[#2d3139] hover:bg-[#383d47] text-white font-semibold py-1.5 px-3 rounded border border-[#2d3139] transition duration-150"
                                        >
                                            Editar
                                        </button>
                                        <button
                                            @click="confirmDeleteMaterial(material)"
                                            class="text-xs bg-[#3b1d22] hover:bg-[#52252a] text-[#ff8c94] font-semibold py-1.5 px-3 rounded border border-[#8a252c] transition duration-150"
                                        >
                                            Eliminar
                                        </button>
                                    </td>
                                </tr>
                                <tr v-if="filteredMaterials.length === 0">
                                    <td colspan="6" class="px-6 py-12 text-center text-industrial-muted">
                                        No se encontraron materiales registrados.
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Tab 2: Units of Measure -->
            <div v-if="activeTab === 'units'">
                <!-- Filter Bar -->
                <div class="bg-[#1b1e22] border border-[#2d3139] rounded-xl p-4 mb-6">
                    <label class="block text-xs font-semibold text-industrial-muted uppercase tracking-wider mb-1.5">Búsqueda rápida</label>
                    <input
                        type="text"
                        placeholder="Buscar por nombre o abreviación..."
                        class="w-full bg-[#0e1113] text-[#e1e6eb] border border-[#2d3139] rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-[#f27b00]"
                        v-model="searchUnitQuery"
                    />
                </div>

                <!-- Table -->
                <div class="bg-[#1b1e22] border border-[#2d3139] rounded-xl overflow-hidden shadow-xl">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm text-[#e1e6eb]">
                            <thead class="text-xs text-industrial-muted uppercase bg-[#0e1113] border-b border-[#2d3139]">
                                <tr>
                                    <th class="px-6 py-4 font-semibold">Nombre de Unidad</th>
                                    <th class="px-6 py-4 font-semibold">Abreviación</th>
                                    <th v-if="$page.props.auth.user.role !== 'visor'" class="px-6 py-4 font-semibold text-right">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-[#2d3139] font-mono text-xs">
                                <tr 
                                    v-for="unit in filteredUnits" 
                                    :key="unit.id" 
                                    class="hover:bg-[#1f2329]/50 transition duration-150"
                                >
                                    <td class="px-6 py-4 font-sans text-sm font-medium text-white">{{ unit.nombre }}</td>
                                    <td class="px-6 py-4 text-industrial-muted font-bold">{{ unit.abreviacion }}</td>
                                    <td v-if="$page.props.auth.user.role !== 'visor'" class="px-6 py-4 text-right space-x-2 font-sans">
                                        <button
                                            @click="openEditUnitModal(unit)"
                                            class="text-xs bg-[#2d3139] hover:bg-[#383d47] text-white font-semibold py-1.5 px-3 rounded border border-[#2d3139] transition duration-150"
                                        >
                                            Editar
                                        </button>
                                        <button
                                            @click="confirmDeleteUnit(unit)"
                                            class="text-xs bg-[#3b1d22] hover:bg-[#52252a] text-[#ff8c94] font-semibold py-1.5 px-3 rounded border border-[#8a252c] transition duration-150"
                                        >
                                            Eliminar
                                        </button>
                                    </td>
                                </tr>
                                <tr v-if="filteredUnits.length === 0">
                                    <td colspan="3" class="px-6 py-12 text-center text-industrial-muted font-sans text-sm">
                                        No se encontraron unidades de medida registradas.
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Material Modal -->
        <div 
            v-if="isMaterialModalOpen" 
            class="fixed inset-0 z-50 flex items-center justify-center overflow-x-hidden overflow-y-auto outline-none"
        >
            <div @click="isMaterialModalOpen = false" class="fixed inset-0 bg-black/75"></div>
            <div class="relative w-full max-w-lg mx-auto my-6 z-10 px-4">
                <div class="bg-[#1b1e22] rounded-xl border-t-4 border-[#f27b00] border-x border-b border-[#2d3139] shadow-2xl overflow-hidden">
                    <div class="px-6 py-4 border-b border-[#2d3139] flex items-center justify-between">
                        <h3 class="text-lg font-bold text-white">
                            {{ isEditingMaterial ? 'Editar Material de Planta' : 'Registrar Nuevo Material' }}
                        </h3>
                        <button @click="isMaterialModalOpen = false" class="text-industrial-muted hover:text-white">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <form @submit.prevent="submitMaterialForm" class="p-6 space-y-4">
                        <div>
                            <label class="block text-xs font-semibold text-industrial-muted uppercase tracking-wider mb-2">Código Interno</label>
                            <input
                                type="text"
                                class="w-full bg-[#0e1113] text-[#e1e6eb] border border-[#2d3139] rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:border-[#f27b00] font-mono"
                                v-model="materialForm.codigo_interno"
                                placeholder="Ej. CA-6070 (Opcional)"
                            />
                            <div v-if="materialForm.errors.codigo_interno" class="mt-1 text-xs text-[#ff8c94] font-mono">{{ materialForm.errors.codigo_interno }}</div>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-industrial-muted uppercase tracking-wider mb-2">Nombre del Material</label>
                            <input
                                type="text"
                                class="w-full bg-[#0e1113] text-[#e1e6eb] border border-[#2d3139] rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:border-[#f27b00]"
                                v-model="materialForm.nombre"
                                required
                                placeholder="Ej. Cemento Asfáltico PEN 60/70"
                            />
                            <div v-if="materialForm.errors.nombre" class="mt-1 text-xs text-[#ff8c94] font-mono">{{ materialForm.errors.nombre }}</div>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-industrial-muted uppercase tracking-wider mb-2">Descripción</label>
                            <textarea
                                class="w-full bg-[#0e1113] text-[#e1e6eb] border border-[#2d3139] rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:border-[#f27b00] h-20 resize-none"
                                v-model="materialForm.descripcion"
                                placeholder="Ej. Insumo principal para mezclas asfálticas en caliente."
                            ></textarea>
                            <div v-if="materialForm.errors.descripcion" class="mt-1 text-xs text-[#ff8c94] font-mono">{{ materialForm.errors.descripcion }}</div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-semibold text-industrial-muted uppercase tracking-wider mb-2">Unidad de Medida</label>
                                <select
                                    class="w-full bg-[#0e1113] text-[#e1e6eb] border border-[#2d3139] rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:border-[#f27b00]"
                                    v-model="materialForm.id_medida"
                                    required
                                >
                                    <option v-for="unit in unidadMedidas" :key="unit.id" :value="unit.id">
                                        {{ unit.nombre }} ({{ unit.abreviacion }})
                                    </option>
                                </select>
                                <div v-if="materialForm.errors.id_medida" class="mt-1 text-xs text-[#ff8c94] font-mono">{{ materialForm.errors.id_medida }}</div>
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-industrial-muted uppercase tracking-wider mb-2">Stock de Alerta Mínimo</label>
                                <input
                                    type="number"
                                    step="0.01"
                                    class="w-full bg-[#0e1113] text-[#e1e6eb] border border-[#2d3139] rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:border-[#f27b00] font-mono"
                                    v-model="materialForm.stock_minimo"
                                    required
                                />
                                <div v-if="materialForm.errors.stock_minimo" class="mt-1 text-xs text-[#ff8c94] font-mono">{{ materialForm.errors.stock_minimo }}</div>
                            </div>
                        </div>

                        <div class="pt-4 border-t border-[#2d3139] flex justify-end gap-3 mt-6">
                            <button
                                type="button"
                                @click="isMaterialModalOpen = false"
                                class="bg-[#2d3139] hover:bg-[#383d47] text-white font-bold py-2.5 px-4 rounded-lg text-sm tracking-wider uppercase transition duration-150"
                            >
                                Cancelar
                            </button>
                            <button
                                type="submit"
                                class="bg-[#f27b00] hover:bg-[#ff9426] text-[#111417] font-bold py-2.5 px-6 rounded-lg text-sm tracking-wider uppercase transition duration-150 disabled:opacity-50"
                                :disabled="materialForm.processing"
                            >
                                {{ materialForm.processing ? 'Procesando...' : (isEditingMaterial ? 'Guardar' : 'Registrar') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Unit Modal -->
        <div 
            v-if="isUnitModalOpen" 
            class="fixed inset-0 z-50 flex items-center justify-center overflow-x-hidden overflow-y-auto outline-none"
        >
            <div @click="isUnitModalOpen = false" class="fixed inset-0 bg-black/75"></div>
            <div class="relative w-full max-w-md mx-auto my-6 z-10 px-4">
                <div class="bg-[#1b1e22] rounded-xl border-t-4 border-[#f27b00] border-x border-b border-[#2d3139] shadow-2xl overflow-hidden">
                    <div class="px-6 py-4 border-b border-[#2d3139] flex items-center justify-between">
                        <h3 class="text-lg font-bold text-white">
                            {{ isEditingUnit ? 'Editar Unidad de Medida' : 'Nueva Unidad de Medida' }}
                        </h3>
                        <button @click="isUnitModalOpen = false" class="text-industrial-muted hover:text-white">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <form @submit.prevent="submitUnitForm" class="p-6 space-y-4">
                        <div>
                            <label class="block text-xs font-semibold text-industrial-muted uppercase tracking-wider mb-2">Nombre de Unidad</label>
                            <input
                                type="text"
                                class="w-full bg-[#0e1113] text-[#e1e6eb] border border-[#2d3139] rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:border-[#f27b00]"
                                v-model="unitForm.nombre"
                                required
                                placeholder="Ej. Kilogramo"
                            />
                            <div v-if="unitForm.errors.nombre" class="mt-1 text-xs text-[#ff8c94] font-mono">{{ unitForm.errors.nombre }}</div>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-industrial-muted uppercase tracking-wider mb-2">Abreviación</label>
                            <input
                                type="text"
                                class="w-full bg-[#0e1113] text-[#e1e6eb] border border-[#2d3139] rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:border-[#f27b00]"
                                v-model="unitForm.abreviacion"
                                required
                                placeholder="Ej. kg"
                            />
                            <div v-if="unitForm.errors.abreviacion" class="mt-1 text-xs text-[#ff8c94] font-mono">{{ unitForm.errors.abreviacion }}</div>
                        </div>

                        <div class="pt-4 border-t border-[#2d3139] flex justify-end gap-3 mt-6">
                            <button
                                type="button"
                                @click="isUnitModalOpen = false"
                                class="bg-[#2d3139] hover:bg-[#383d47] text-white font-bold py-2.5 px-4 rounded-lg text-sm tracking-wider uppercase transition duration-150"
                            >
                                Cancelar
                            </button>
                            <button
                                type="submit"
                                class="bg-[#f27b00] hover:bg-[#ff9426] text-[#111417] font-bold py-2.5 px-6 rounded-lg text-sm tracking-wider uppercase transition duration-150 disabled:opacity-50"
                                :disabled="unitForm.processing"
                            >
                                {{ unitForm.processing ? 'Procesando...' : (isEditingUnit ? 'Guardar' : 'Registrar') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </AuthenticatedLayout>
</template>
