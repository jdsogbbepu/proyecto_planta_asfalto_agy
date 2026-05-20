<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { ref, computed } from 'vue';

const props = defineProps({
    users: {
        type: Array,
        required: true,
    },
});

// Search and filter state
const searchQuery = ref('');
const roleFilter = ref('');

// Computed filtered users
const filteredUsers = computed(() => {
    return props.users.filter(user => {
        const matchesSearch = 
            user.name.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
            user.username.toLowerCase().includes(searchQuery.value.toLowerCase());
        const matchesRole = roleFilter.value === '' || user.role === roleFilter.value;
        return matchesSearch && matchesRole;
    });
});

// Modal state
const isModalOpen = ref(false);
const isEditing = ref(false);
const editingUserId = ref(null);

const form = useForm({
    name: '',
    username: '',
    password: '',
    role: 'operador',
    active: true,
});

// Open modal for creating
const openCreateModal = () => {
    form.clearErrors();
    form.reset();
    isEditing.value = false;
    editingUserId.value = null;
    isModalOpen.value = true;
};

// Open modal for editing
const openEditModal = (user) => {
    form.clearErrors();
    form.name = user.name;
    form.username = user.username;
    form.password = ''; // Keep empty unless updating
    form.role = user.role;
    form.active = !!user.active;
    isEditing.value = true;
    editingUserId.value = user.id;
    isModalOpen.value = true;
};

// Close modal
const closeModal = () => {
    isModalOpen.value = false;
};

// Submit form (create or update)
const submitForm = () => {
    if (isEditing.value) {
        form.put(route('users.update', editingUserId.value), {
            onSuccess: () => {
                closeModal();
            },
        });
    } else {
        form.post(route('users.store'), {
            onSuccess: () => {
                closeModal();
            },
        });
    }
};

// Delete user with native confirmation (to keep it clean and robust)
const deleteForm = useForm({});
const confirmDeleteUser = (user) => {
    if (confirm(`¿Está seguro de eliminar al usuario "${user.name}" de la planta?`)) {
        deleteForm.delete(route('users.destroy', user.id));
    }
};

// Helper for role labels
const getRoleLabel = (role) => {
    const labels = {
        'administrador': 'Administrador',
        'operador': 'Operador de Planta',
        'visor': 'Visor / Auditor',
    };
    return labels[role] || role;
};
</script>

<template>
    <Head title="Gestión de Usuarios" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h2 class="text-2xl font-bold tracking-tight text-white">Gestión de Usuarios de Planta</h2>
                    <p class="text-sm text-industrial-muted mt-1">Registre, edite y controle los roles y accesos de los operadores de la planta.</p>
                </div>
                <div>
                    <button
                        @click="openCreateModal"
                        class="bg-[#f27b00] hover:bg-[#ff9426] text-[#111417] font-bold py-2.5 px-4 rounded-lg text-sm tracking-wider uppercase transition duration-150 transform hover:scale-[1.01] active:scale-[0.99] flex items-center gap-2"
                    >
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                        </svg>
                        <span>Registrar Usuario</span>
                    </button>
                </div>
            </div>
        </template>

        <div class="py-6 px-4 sm:px-6 lg:px-8 mx-auto max-w-7xl">
            <!-- Filter Bar -->
            <div class="bg-[#1b1e22] border border-[#2d3139] rounded-xl p-4 mb-6 flex flex-col md:flex-row md:items-center gap-4">
                <div class="flex-1">
                    <label class="block text-xs font-semibold text-industrial-muted uppercase tracking-wider mb-1.5">Búsqueda rápida</label>
                    <input
                        type="text"
                        placeholder="Buscar por nombre o usuario..."
                        class="w-full bg-[#0e1113] text-[#e1e6eb] border border-[#2d3139] rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-[#f27b00]"
                        v-model="searchQuery"
                    />
                </div>
                <div class="w-full md:w-64">
                    <label class="block text-xs font-semibold text-industrial-muted uppercase tracking-wider mb-1.5">Filtrar por Rol</label>
                    <select
                        class="w-full bg-[#0e1113] text-[#e1e6eb] border border-[#2d3139] rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-[#f27b00]"
                        v-model="roleFilter"
                    >
                        <option value="">Todos los roles</option>
                        <option value="administrador">Administrador</option>
                        <option value="operador">Operador de Planta</option>
                        <option value="visor">Visor / Auditor</option>
                    </select>
                </div>
            </div>

            <!-- Users Table Container (Bento card style) -->
            <div class="bg-[#1b1e22] border border-[#2d3139] rounded-xl overflow-hidden shadow-xl">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-[#e1e6eb]">
                        <thead class="text-xs text-industrial-muted uppercase bg-[#0e1113] border-b border-[#2d3139]">
                            <tr>
                                <th class="px-6 py-4 font-semibold">Nombre del Funcionario</th>
                                <th class="px-6 py-4 font-semibold">Nombre de Usuario</th>
                                <th class="px-6 py-4 font-semibold">Rol del Sistema</th>
                                <th class="px-6 py-4 font-semibold text-center">Estado</th>
                                <th class="px-6 py-4 font-semibold text-right">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#2d3139] font-sans">
                            <tr 
                                v-for="user in filteredUsers" 
                                :key="user.id" 
                                class="hover:bg-[#1f2329]/50 transition duration-150"
                            >
                                <td class="px-6 py-4 font-medium text-white">{{ user.name }}</td>
                                <td class="px-6 py-4 font-mono text-xs text-industrial-muted">{{ user.username }}</td>
                                <td class="px-6 py-4">
                                    <span 
                                        class="px-2.5 py-1 rounded text-xs font-mono font-semibold"
                                        :class="{
                                            'bg-[#f27b00]/10 border border-[#f27b00]/30 text-[#f27b00]': user.role === 'administrador',
                                            'bg-industrial-blue/10 border border-industrial-blue/30 text-[#e1e6eb]': user.role === 'operador',
                                            'bg-[#8a949f]/10 border border-[#8a949f]/30 text-[#8a949f]': user.role === 'visor'
                                        }"
                                    >
                                        {{ getRoleLabel(user.role) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span 
                                        class="px-2 py-0.5 rounded text-[11px] font-bold font-mono"
                                        :class="user.active ? 'bg-[#0f3d2a] border border-[#1a7f4c] text-[#a6ffcc]' : 'bg-[#3b1d22] border border-[#8a252c] text-[#ff8c94]'"
                                    >
                                        {{ user.active ? 'ACTIVO' : 'INACTIVO' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right space-x-2">
                                    <button
                                        @click="openEditModal(user)"
                                        class="text-xs bg-[#2d3139] hover:bg-[#383d47] text-white font-semibold py-1.5 px-3 rounded border border-[#2d3139] transition duration-150"
                                    >
                                        Editar
                                    </button>
                                    <button
                                        @click="confirmDeleteUser(user)"
                                        :disabled="$page.props.auth.user.id === user.id"
                                        class="text-xs bg-[#3b1d22] hover:bg-[#52252a] text-[#ff8c94] font-semibold py-1.5 px-3 rounded border border-[#8a252c] transition duration-150 disabled:opacity-30 disabled:cursor-not-allowed"
                                    >
                                        Eliminar
                                    </button>
                                </td>
                            </tr>
                            <tr v-if="filteredUsers.length === 0">
                                <td colspan="5" class="px-6 py-12 text-center text-industrial-muted">
                                    No se encontraron usuarios registrados con los filtros seleccionados.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Add / Edit Modal -->
        <div 
            v-if="isModalOpen" 
            class="fixed inset-0 z-50 flex items-center justify-center overflow-x-hidden overflow-y-auto outline-none"
        >
            <!-- Backdrop -->
            <div 
                @click="closeModal"
                class="fixed inset-0 bg-black/75 transition-opacity"
            ></div>

            <!-- Modal Content -->
            <div class="relative w-full max-w-lg mx-auto my-6 z-10 px-4">
                <div class="bg-[#1b1e22] rounded-xl border-t-4 border-[#f27b00] border-x border-b border-[#2d3139] shadow-2xl overflow-hidden">
                    <!-- Header -->
                    <div class="px-6 py-4 border-b border-[#2d3139] flex items-center justify-between">
                        <h3 class="text-lg font-bold text-white tracking-tight">
                            {{ isEditing ? 'Editar Usuario de Planta' : 'Registrar Nuevo Usuario' }}
                        </h3>
                        <button 
                            @click="closeModal"
                            class="text-industrial-muted hover:text-white transition duration-150"
                        >
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <!-- Body / Form -->
                    <form @submit.prevent="submitForm" class="p-6 space-y-4">
                        <!-- Full Name -->
                        <div>
                            <label for="modal-name" class="block text-xs font-semibold text-industrial-muted uppercase tracking-wider mb-2">Nombre Completo</label>
                            <input
                                id="modal-name"
                                type="text"
                                class="w-full bg-[#0e1113] text-[#e1e6eb] border border-[#2d3139] rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:border-[#f27b00]"
                                v-model="form.name"
                                required
                                placeholder="Ej. Juan Pérez Mamani"
                            />
                            <div v-if="form.errors.name" class="mt-1 text-xs text-[#ff8c94] font-mono">
                                {{ form.errors.name }}
                            </div>
                        </div>

                        <!-- Username -->
                        <div>
                            <label for="modal-username" class="block text-xs font-semibold text-industrial-muted uppercase tracking-wider mb-2">Nombre de Usuario</label>
                            <input
                                id="modal-username"
                                type="text"
                                class="w-full bg-[#0e1113] text-[#e1e6eb] border border-[#2d3139] rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:border-[#f27b00]"
                                v-model="form.username"
                                required
                                placeholder="Ej. jperez"
                            />
                            <div v-if="form.errors.username" class="mt-1 text-xs text-[#ff8c94] font-mono">
                                {{ form.errors.username }}
                            </div>
                        </div>

                        <!-- Password -->
                        <div>
                            <label for="modal-password" class="block text-xs font-semibold text-industrial-muted uppercase tracking-wider mb-2">
                                Contraseña <span v-if="isEditing" class="text-xs text-industrial-muted normal-case font-normal">(Dejar en blanco para no modificar)</span>
                            </label>
                            <input
                                id="modal-password"
                                type="password"
                                class="w-full bg-[#0e1113] text-[#e1e6eb] border border-[#2d3139] rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:border-[#f27b00]"
                                v-model="form.password"
                                :required="!isEditing"
                                placeholder="••••••••"
                            />
                            <div v-if="form.errors.password" class="mt-1 text-xs text-[#ff8c94] font-mono">
                                {{ form.errors.password }}
                            </div>
                        </div>

                        <!-- Role & Active Toggle (Side by side) -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label for="modal-role" class="block text-xs font-semibold text-industrial-muted uppercase tracking-wider mb-2">Rol del Sistema</label>
                                <select
                                    id="modal-role"
                                    class="w-full bg-[#0e1113] text-[#e1e6eb] border border-[#2d3139] rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:border-[#f27b00]"
                                    v-model="form.role"
                                    required
                                >
                                    <option value="administrador">Administrador</option>
                                    <option value="operador">Operador de Planta</option>
                                    <option value="visor">Visor / Auditor</option>
                                </select>
                                <div v-if="form.errors.role" class="mt-1 text-xs text-[#ff8c94] font-mono">
                                    {{ form.errors.role }}
                                </div>
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-industrial-muted uppercase tracking-wider mb-2">Estado del Operador</label>
                                <div class="flex items-center h-10 mt-1">
                                    <input 
                                        id="modal-active" 
                                        type="checkbox" 
                                        class="w-4 h-4 rounded bg-[#0e1113] border-[#2d3139] text-[#f27b00] focus:ring-[#f27b00] focus:ring-offset-[#1b1e22]" 
                                        v-model="form.active"
                                    />
                                    <label for="modal-active" class="ml-2 text-sm text-[#e1e6eb] cursor-pointer select-none">
                                        {{ form.active ? 'Habilitado / Activo' : 'Deshabilitado / Inactivo' }}
                                    </label>
                                </div>
                                <div v-if="form.errors.active" class="mt-1 text-xs text-[#ff8c94] font-mono">
                                    {{ form.errors.active }}
                                </div>
                            </div>
                        </div>

                        <!-- Footer Actions -->
                        <div class="pt-4 border-t border-[#2d3139] flex justify-end gap-3 mt-6">
                            <button
                                type="button"
                                @click="closeModal"
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
