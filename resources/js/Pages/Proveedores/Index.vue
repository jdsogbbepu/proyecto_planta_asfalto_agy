<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { ref, computed } from 'vue';

const props = defineProps({
    proveedores: {
        type: Array,
        required: true,
    },
});

// Search state
const searchQuery = ref('');

// Filtered suppliers
const filteredProveedores = computed(() => {
    return props.proveedores.filter(p => 
        p.razon_social.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
        (p.nit && p.nit.toLowerCase().includes(searchQuery.value.toLowerCase())) ||
        (p.telefono && p.telefono.toLowerCase().includes(searchQuery.value.toLowerCase()))
    );
});

// Modal state
const isModalOpen = ref(false);
const isEditing = ref(false);
const editingProveedorId = ref(null);

const form = useForm({
    razon_social: '',
    nit: '',
    telefono: '',
    direccion: '',
});

const openCreateModal = () => {
    form.clearErrors();
    form.reset();
    isEditing.value = false;
    editingProveedorId.value = null;
    isModalOpen.value = true;
};

const openEditModal = (proveedor) => {
    form.clearErrors();
    form.razon_social = proveedor.razon_social;
    form.nit = proveedor.nit || '';
    form.telefono = proveedor.telefono || '';
    form.direccion = proveedor.direccion || '';
    isEditing.value = true;
    editingProveedorId.value = proveedor.id;
    isModalOpen.value = true;
};

const submitForm = () => {
    if (isEditing.value) {
        form.put(route('proveedores.update', editingProveedorId.value), {
            onSuccess: () => isModalOpen.value = false,
        });
    } else {
        form.post(route('proveedores.store'), {
            onSuccess: () => isModalOpen.value = false,
        });
    }
};

const deleteForm = useForm({});
const confirmDeleteProveedor = (proveedor) => {
    if (confirm(`¿Está seguro de eliminar al proveedor "${proveedor.razon_social}"?`)) {
        deleteForm.delete(route('proveedores.destroy', proveedor.id));
    }
};
</script>

<template>
    <Head title="Proveedores de Planta" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h2 class="text-2xl font-bold tracking-tight text-white">Catálogo de Proveedores</h2>
                    <p class="text-sm text-industrial-muted mt-1 font-sans">Administre las empresas proveedoras que suministran asfalto, agregados y emulsiones a la planta.</p>
                </div>
                
                <div v-if="$page.props.auth.user.role !== 'visor'">
                    <button
                        @click="openCreateModal"
                        class="bg-[#f27b00] hover:bg-[#ff9426] text-[#111417] font-bold py-2.5 px-4 rounded-lg text-sm tracking-wider uppercase transition duration-150 flex items-center gap-2"
                    >
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                        </svg>
                        <span>Registrar Proveedor</span>
                    </button>
                </div>
            </div>
        </template>

        <div class="py-6 px-4 sm:px-6 lg:px-8 mx-auto max-w-7xl font-sans">
            <!-- Filter Bar -->
            <div class="bg-[#1b1e22] border border-[#2d3139] rounded-xl p-4 mb-6">
                <label class="block text-xs font-semibold text-industrial-muted uppercase tracking-wider mb-1.5">Búsqueda rápida</label>
                <input
                    type="text"
                    placeholder="Buscar por Razón Social, NIT o Teléfono..."
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
                                <th class="px-6 py-4 font-semibold">Razón Social</th>
                                <th class="px-6 py-4 font-semibold">NIT / CI</th>
                                <th class="px-6 py-4 font-semibold">Teléfono</th>
                                <th class="px-6 py-4 font-semibold">Dirección</th>
                                <th v-if="$page.props.auth.user.role !== 'visor'" class="px-6 py-4 font-semibold text-right">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#2d3139]">
                            <tr 
                                v-for="proveedor in filteredProveedores" 
                                :key="proveedor.id" 
                                class="hover:bg-[#1f2329]/50 transition duration-150"
                            >
                                <td class="px-6 py-4 font-medium text-white">{{ proveedor.razon_social }}</td>
                                <td class="px-6 py-4 font-mono text-xs text-industrial-muted">{{ proveedor.nit || 'S/N' }}</td>
                                <td class="px-6 py-4 font-mono text-xs text-industrial-muted">{{ proveedor.telefono || 'S/T' }}</td>
                                <td class="px-6 py-4 text-xs text-industrial-foreground">{{ proveedor.direccion || 'S/D' }}</td>
                                <td v-if="$page.props.auth.user.role !== 'visor'" class="px-6 py-4 text-right space-x-2">
                                    <button
                                        @click="openEditModal(proveedor)"
                                        class="text-xs bg-[#2d3139] hover:bg-[#383d47] text-white font-semibold py-1.5 px-3 rounded border border-[#2d3139] transition duration-150"
                                    >
                                        Editar
                                    </button>
                                    <button
                                        @click="confirmDeleteProveedor(proveedor)"
                                        class="text-xs bg-[#3b1d22] hover:bg-[#52252a] text-[#ff8c94] font-semibold py-1.5 px-3 rounded border border-[#8a252c] transition duration-150"
                                    >
                                        Eliminar
                                    </button>
                                </td>
                            </tr>
                            <tr v-if="filteredProveedores.length === 0">
                                <td colspan="5" class="px-6 py-12 text-center text-industrial-muted">
                                    No se encontraron proveedores registrados.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Supplier Modal -->
        <div 
            v-if="isModalOpen" 
            class="fixed inset-0 z-50 flex items-center justify-center overflow-x-hidden overflow-y-auto outline-none"
        >
            <div @click="isModalOpen = false" class="fixed inset-0 bg-black/75"></div>
            <div class="relative w-full max-w-lg mx-auto my-6 z-10 px-4">
                <div class="bg-[#1b1e22] rounded-xl border-t-4 border-[#f27b00] border-x border-b border-[#2d3139] shadow-2xl overflow-hidden">
                    <div class="px-6 py-4 border-b border-[#2d3139] flex items-center justify-between">
                        <h3 class="text-lg font-bold text-white">
                            {{ isEditing ? 'Editar Proveedor' : 'Registrar Nuevo Proveedor' }}
                        </h3>
                        <button @click="isModalOpen = false" class="text-industrial-muted hover:text-white">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <form @submit.prevent="submitForm" class="p-6 space-y-4 font-sans">
                        <div>
                            <label class="block text-xs font-semibold text-industrial-muted uppercase tracking-wider mb-2">Razón Social</label>
                            <input
                                type="text"
                                class="w-full bg-[#0e1113] text-[#e1e6eb] border border-[#2d3139] rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:border-[#f27b00]"
                                v-model="form.razon_social"
                                required
                                placeholder="Ej. YPFB Corporación"
                            />
                            <div v-if="form.errors.razon_social" class="mt-1 text-xs text-[#ff8c94] font-mono">{{ form.errors.razon_social }}</div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-semibold text-industrial-muted uppercase tracking-wider mb-2">NIT / Identificación</label>
                                <input
                                    type="text"
                                    class="w-full bg-[#0e1113] text-[#e1e6eb] border border-[#2d3139] rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:border-[#f27b00] font-mono"
                                    v-model="form.nit"
                                    placeholder="Ej. 1020304021"
                                />
                                <div v-if="form.errors.nit" class="mt-1 text-xs text-[#ff8c94] font-mono">{{ form.errors.nit }}</div>
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-industrial-muted uppercase tracking-wider mb-2">Teléfono de Contacto</label>
                                <input
                                    type="text"
                                    class="w-full bg-[#0e1113] text-[#e1e6eb] border border-[#2d3139] rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:border-[#f27b00] font-mono"
                                    v-model="form.telefono"
                                    placeholder="Ej. 22812400"
                                />
                                <div v-if="form.errors.telefono" class="mt-1 text-xs text-[#ff8c94] font-mono">{{ form.errors.telefono }}</div>
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-industrial-muted uppercase tracking-wider mb-2">Dirección Fiscal / Oficina</label>
                            <input
                                type="text"
                                class="w-full bg-[#0e1113] text-[#e1e6eb] border border-[#2d3139] rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:border-[#f27b00]"
                                v-model="form.direccion"
                                placeholder="Ej. Av. Mariscal Santa Cruz #1024, La Paz"
                            />
                            <div v-if="form.errors.direccion" class="mt-1 text-xs text-[#ff8c94] font-mono">{{ form.errors.direccion }}</div>
                        </div>

                        <div class="pt-4 border-t border-[#2d3139] flex justify-end gap-3 mt-6">
                            <button
                                type="button"
                                @click="isModalOpen = false"
                                class="bg-[#2d3139] hover:bg-[#383d47] text-white font-bold py-2.5 px-4 rounded-lg text-sm tracking-wider uppercase transition duration-150"
                            >
                                Cancelar
                            </button>
                            <button
                                type="submit"
                                class="bg-[#f27b00] hover:bg-[#ff9426] text-[#111417] font-bold py-2.5 px-6 rounded-lg text-sm tracking-wider uppercase transition duration-150 disabled:opacity-50"
                                :disabled="form.processing"
                            >
                                {{ form.processing ? 'Procesando...' : (isEditing ? 'Guardar' : 'Registrar') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </AuthenticatedLayout>
</template>
