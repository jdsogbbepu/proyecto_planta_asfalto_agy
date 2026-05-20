<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { ref, computed } from 'vue';

const props = defineProps({
    funcionarios: {
        type: Array,
        required: true,
    },
});

// Search state
const searchQuery = ref('');

// Filtered officers
const filteredFuncionarios = computed(() => {
    return props.funcionarios.filter(f => 
        f.nombre.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
        (f.cargo && f.cargo.toLowerCase().includes(searchQuery.value.toLowerCase())) ||
        (f.area && f.area.toLowerCase().includes(searchQuery.value.toLowerCase()))
    );
});

// Modal state
const isModalOpen = ref(false);
const isEditing = ref(false);
const editingFuncionarioId = ref(null);

const form = useForm({
    nombre: '',
    cargo: '',
    area: '',
    activo: true,
});

const openCreateModal = () => {
    form.clearErrors();
    form.reset();
    isEditing.value = false;
    editingFuncionarioId.value = null;
    isModalOpen.value = true;
};

const openEditModal = (funcionario) => {
    form.clearErrors();
    form.nombre = funcionario.nombre;
    form.cargo = funcionario.cargo || '';
    form.area = funcionario.area || '';
    form.activo = !!funcionario.activo;
    isEditing.value = true;
    editingFuncionarioId.value = funcionario.id;
    isModalOpen.value = true;
};

const submitForm = () => {
    if (isEditing.value) {
        form.put(route('funcionarios.update', editingFuncionarioId.value), {
            onSuccess: () => isModalOpen.value = false,
        });
    } else {
        form.post(route('funcionarios.store'), {
            onSuccess: () => isModalOpen.value = false,
        });
    }
};

const deleteForm = useForm({});
const confirmDeleteFuncionario = (funcionario) => {
    if (confirm(`¿Está seguro de eliminar al funcionario receptor "${funcionario.nombre}"?`)) {
        deleteForm.delete(route('funcionarios.destroy', funcionario.id));
    }
};
</script>

<template>
    <Head title="Funcionarios Receptores" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h2 class="text-2xl font-bold tracking-tight text-white">Catálogo de Funcionarios Receptores</h2>
                    <p class="text-sm text-industrial-muted mt-1 font-sans">Administre a los funcionarios públicos autorizados para firmar los vales y tickets de entrega en obra.</p>
                </div>
                
                <div v-if="$page.props.auth.user.role !== 'visor'">
                    <button
                        @click="openCreateModal"
                        class="bg-[#f27b00] hover:bg-[#ff9426] text-[#111417] font-bold py-2.5 px-4 rounded-lg text-sm tracking-wider uppercase transition duration-150 flex items-center gap-2"
                    >
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                        </svg>
                        <span>Registrar Funcionario</span>
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
                    placeholder="Buscar por Nombre, Cargo o Área..."
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
                                <th class="px-6 py-4 font-semibold">Nombre del Funcionario</th>
                                <th class="px-6 py-4 font-semibold">Cargo</th>
                                <th class="px-6 py-4 font-semibold">Área / Dirección</th>
                                <th class="px-6 py-4 font-semibold text-center">Estado</th>
                                <th v-if="$page.props.auth.user.role !== 'visor'" class="px-6 py-4 font-semibold text-right">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#2d3139]">
                            <tr 
                                v-for="funcionario in filteredFuncionarios" 
                                :key="funcionario.id" 
                                class="hover:bg-[#1f2329]/50 transition duration-150"
                            >
                                <td class="px-6 py-4 font-medium text-white">{{ funcionario.nombre }}</td>
                                <td class="px-6 py-4 text-xs text-industrial-foreground">{{ funcionario.cargo || 'No especificado' }}</td>
                                <td class="px-6 py-4 text-xs text-industrial-muted">{{ funcionario.area || 'No especificada' }}</td>
                                <td class="px-6 py-4 text-center">
                                    <span 
                                        class="px-2 py-0.5 rounded text-[10px] font-bold font-mono tracking-wide"
                                        :class="funcionario.activo ? 'bg-[#0f3d2a] border border-[#1a7f4c] text-[#a6ffcc]' : 'bg-[#3b1d22] border border-[#8a252c] text-[#ff8c94]'"
                                    >
                                        {{ funcionario.activo ? 'HABILITADO' : 'DESHABILITADO' }}
                                    </span>
                                </td>
                                <td v-if="$page.props.auth.user.role !== 'visor'" class="px-6 py-4 text-right space-x-2">
                                    <button
                                        @click="openEditModal(funcionario)"
                                        class="text-xs bg-[#2d3139] hover:bg-[#383d47] text-white font-semibold py-1.5 px-3 rounded border border-[#2d3139] transition duration-150"
                                    >
                                        Editar
                                    </button>
                                    <button
                                        @click="confirmDeleteFuncionario(funcionario)"
                                        class="text-xs bg-[#3b1d22] hover:bg-[#52252a] text-[#ff8c94] font-semibold py-1.5 px-3 rounded border border-[#8a252c] transition duration-150"
                                    >
                                        Eliminar
                                    </button>
                                </td>
                            </tr>
                            <tr v-if="filteredFuncionarios.length === 0">
                                <td colspan="5" class="px-6 py-12 text-center text-industrial-muted">
                                    No se encontraron funcionarios registrados.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Officer Modal -->
        <div 
            v-if="isModalOpen" 
            class="fixed inset-0 z-50 flex items-center justify-center overflow-x-hidden overflow-y-auto outline-none"
        >
            <div @click="isModalOpen = false" class="fixed inset-0 bg-black/75"></div>
            <div class="relative w-full max-w-lg mx-auto my-6 z-10 px-4">
                <div class="bg-[#1b1e22] rounded-xl border-t-4 border-[#f27b00] border-x border-b border-[#2d3139] shadow-2xl overflow-hidden">
                    <div class="px-6 py-4 border-b border-[#2d3139] flex items-center justify-between">
                        <h3 class="text-lg font-bold text-white">
                            {{ isEditing ? 'Editar Funcionario Receptor' : 'Registrar Funcionario' }}
                        </h3>
                        <button @click="isModalOpen = false" class="text-industrial-muted hover:text-white">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <form @submit.prevent="submitForm" class="p-6 space-y-4 font-sans">
                        <div>
                            <label class="block text-xs font-semibold text-industrial-muted uppercase tracking-wider mb-2">Nombre Completo del Funcionario</label>
                            <input
                                type="text"
                                class="w-full bg-[#0e1113] text-[#e1e6eb] border border-[#2d3139] rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:border-[#f27b00]"
                                v-model="form.nombre"
                                required
                                placeholder="Ej. Ing. Wilfredo Condori Mamani"
                            />
                            <div v-if="form.errors.nombre" class="mt-1 text-xs text-[#ff8c94] font-mono">{{ form.errors.nombre }}</div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-semibold text-industrial-muted uppercase tracking-wider mb-2">Cargo Oficial</label>
                                <input
                                    type="text"
                                    class="w-full bg-[#0e1113] text-[#e1e6eb] border border-[#2d3139] rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:border-[#f27b00]"
                                    v-model="form.cargo"
                                    placeholder="Ej. Residente de Obra"
                                />
                                <div v-if="form.errors.cargo" class="mt-1 text-xs text-[#ff8c94] font-mono">{{ form.errors.cargo }}</div>
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-industrial-muted uppercase tracking-wider mb-2">Área / Dirección Municipal</label>
                                <input
                                    type="text"
                                    class="w-full bg-[#0e1113] text-[#e1e6eb] border border-[#2d3139] rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:border-[#f27b00]"
                                    v-model="form.area"
                                    placeholder="Ej. Dirección de Obras Municipales"
                                />
                                <div v-if="form.errors.area" class="mt-1 text-xs text-[#ff8c94] font-mono">{{ form.errors.area }}</div>
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-industrial-muted uppercase tracking-wider mb-2">Estado de Habilitación</label>
                            <div class="flex items-center h-10 mt-1">
                                <input 
                                    id="modal-officer-active" 
                                    type="checkbox" 
                                    class="w-4 h-4 rounded bg-[#0e1113] border-[#2d3139] text-[#f27b00] focus:ring-[#f27b00] focus:ring-offset-[#1b1e22]" 
                                    v-model="form.activo"
                                />
                                <label for="modal-officer-active" class="ml-2 text-sm text-[#e1e6eb] cursor-pointer select-none">
                                    {{ form.activo ? 'Habilitado para Recibir Despachos' : 'Inhabilitado / Suspendido' }}
                                </label>
                            </div>
                            <div v-if="form.errors.activo" class="mt-1 text-xs text-[#ff8c94] font-mono">{{ form.errors.activo }}</div>
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
