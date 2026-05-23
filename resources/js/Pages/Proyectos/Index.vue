<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { ref, computed } from 'vue';

const props = defineProps({
    proyectos: {
        type: Array,
        required: true,
    },
});

// Search state
const searchQuery = ref('');

// Filtered projects
const filteredProyectos = computed(() => {
    return props.proyectos.filter(p => 
        p.nombre.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
        (p.ubicacion && p.ubicacion.toLowerCase().includes(searchQuery.value.toLowerCase())) ||
        (p.encargado && p.encargado.toLowerCase().includes(searchQuery.value.toLowerCase()))
    );
});

// Modal state
const isModalOpen = ref(false);
const isEditing = ref(false);
const editingProyectoId = ref(null);

const form = useForm({
    nombre: '',
    ubicacion: '',
    encargado: '',
    fecha: '',
    estado: 'activo',
});

const openCreateModal = () => {
    form.clearErrors();
    form.reset();
    isEditing.value = false;
    editingProyectoId.value = null;
    isModalOpen.value = true;
};

const openEditModal = (proyecto) => {
    form.clearErrors();
    form.nombre = proyecto.nombre;
    form.ubicacion = proyecto.ubicacion || '';
    form.encargado = proyecto.encargado || '';
    form.fecha = proyecto.fecha || '';
    form.estado = proyecto.estado;
    isEditing.value = true;
    editingProyectoId.value = proyecto.id;
    isModalOpen.value = true;
};

const submitForm = () => {
    if (isEditing.value) {
        form.put(route('proyectos.update', editingProyectoId.value), {
            onSuccess: () => isModalOpen.value = false,
        });
    } else {
        form.post(route('proyectos.store'), {
            onSuccess: () => isModalOpen.value = false,
        });
    }
};

const deleteForm = useForm({});
const confirmDeleteProyecto = (proyecto) => {
    if (confirm(`¿Está seguro de eliminar el proyecto "${proyecto.nombre}"?`)) {
        deleteForm.delete(route('proyectos.destroy', proyecto.id));
    }
};

// Helper for status label styling
const getStatusClasses = (status) => {
    const classes = {
        'activo': 'bg-[#0f3d2a] border border-[#1a7f4c] text-[#a6ffcc]',
        'pausado': 'bg-[#f27b00]/10 border border-[#f27b00]/30 text-[#f27b00]',
        'finalizado': 'bg-[#2d3139] border border-[#383d47] text-[#8a949f]',
    };
    return classes[status] || 'bg-gray-800 text-gray-200';
};

const getStatusLabel = (status) => {
    const labels = {
        'activo': 'Activo',
        'pausado': 'Pausado',
        'finalizado': 'Finalizado',
    };
    return labels[status] || status;
};
</script>

<template>
    <Head title="Proyectos y Obras" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h2 class="text-2xl font-bold tracking-tight text-white">Catálogo de Proyectos / Obras Municipales</h2>
                    <p class="text-sm text-industrial-muted mt-1 font-sans">Administre los destinos de despacho del asfalto producido por la planta.</p>
                </div>
                
                <div v-if="$page.props.auth.user.role !== 'visor'">
                    <button
                        @click="openCreateModal"
                        class="bg-[#f27b00] hover:bg-[#ff9426] text-[#111417] font-bold py-2.5 px-4 rounded-lg text-sm tracking-wider uppercase transition duration-150 flex items-center gap-2"
                    >
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                        </svg>
                        <span>Registrar Proyecto</span>
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
                    placeholder="Buscar por Nombre del Proyecto, Ubicación o Responsable..."
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
                                <th class="px-6 py-4 font-semibold">Proyecto / Obra</th>
                                <th class="px-6 py-4 font-semibold">Ubicación</th>
                                <th class="px-6 py-4 font-semibold">Encargado de Obra</th>
                                <th class="px-6 py-4 font-semibold font-mono">Fecha</th>
                                <th class="px-6 py-4 font-semibold text-center">Estado</th>
                                <th v-if="$page.props.auth.user.role !== 'visor'" class="px-6 py-4 font-semibold text-right">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#2d3139]">
                            <tr 
                                v-for="proyecto in filteredProyectos" 
                                :key="proyecto.id" 
                                class="hover:bg-[#1f2329]/50 transition duration-150"
                            >
                                <td class="px-6 py-4 font-medium text-white">{{ proyecto.nombre }}</td>
                                <td class="px-6 py-4 text-xs text-industrial-foreground">{{ proyecto.ubicacion || 'Sin ubicación' }}</td>
                                <td class="px-6 py-4 text-xs text-industrial-foreground">{{ proyecto.encargado || 'Sin encargado' }}</td>
                                <td class="px-6 py-4 font-mono text-xs text-industrial-muted">
                                    {{ proyecto.fecha || 'S/F' }}
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span 
                                        class="px-2.5 py-0.5 rounded text-[10px] font-bold font-mono tracking-wide uppercase"
                                        :class="getStatusClasses(proyecto.estado)"
                                    >
                                        {{ getStatusLabel(proyecto.estado) }}
                                    </span>
                                </td>
                                <td v-if="$page.props.auth.user.role !== 'visor'" class="px-6 py-4 text-right space-x-2">
                                    <button
                                        @click="openEditModal(proyecto)"
                                        class="text-xs bg-[#2d3139] hover:bg-[#383d47] text-white font-semibold py-1.5 px-3 rounded border border-[#2d3139] transition duration-150"
                                    >
                                        Editar
                                    </button>
                                    <button
                                        @click="confirmDeleteProyecto(proyecto)"
                                        class="text-xs bg-[#3b1d22] hover:bg-[#52252a] text-[#ff8c94] font-semibold py-1.5 px-3 rounded border border-[#8a252c] transition duration-150"
                                    >
                                        Eliminar
                                    </button>
                                </td>
                            </tr>
                            <tr v-if="filteredProyectos.length === 0">
                                <td colspan="6" class="px-6 py-12 text-center text-industrial-muted">
                                    No se encontraron proyectos registrados.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Project Modal -->
        <div 
            v-if="isModalOpen" 
            class="fixed inset-0 z-50 flex items-center justify-center overflow-x-hidden overflow-y-auto outline-none"
        >
            <div @click="isModalOpen = false" class="fixed inset-0 bg-black/75"></div>
            <div class="relative w-full max-w-lg mx-auto my-6 z-10 px-4">
                <div class="bg-[#1b1e22] rounded-xl border-t-4 border-[#f27b00] border-x border-b border-[#2d3139] shadow-2xl overflow-hidden">
                    <div class="px-6 py-4 border-b border-[#2d3139] flex items-center justify-between">
                        <h3 class="text-lg font-bold text-white">
                            {{ isEditing ? 'Editar Proyecto / Obra' : 'Registrar Nuevo Proyecto' }}
                        </h3>
                        <button @click="isModalOpen = false" class="text-industrial-muted hover:text-white">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <form @submit.prevent="submitForm" class="p-6 space-y-4 font-sans">
                        <div>
                            <label class="block text-xs font-semibold text-industrial-muted uppercase tracking-wider mb-2">Nombre del Proyecto / Obra</label>
                            <input
                                type="text"
                                class="w-full bg-[#0e1113] text-[#e1e6eb] border border-[#2d3139] rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:border-[#f27b00]"
                                v-model="form.nombre"
                                required
                                placeholder="Ej. Doble Vía Viacha - Pavimento Rígido"
                            />
                            <div v-if="form.errors.nombre" class="mt-1 text-xs text-[#ff8c94] font-mono">{{ form.errors.nombre }}</div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-semibold text-industrial-muted uppercase tracking-wider mb-2">Ubicación / Distrito</label>
                                <input
                                    type="text"
                                    class="w-full bg-[#0e1113] text-[#e1e6eb] border border-[#2d3139] rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:border-[#f27b00]"
                                    v-model="form.ubicacion"
                                    placeholder="Ej. Distrito 8, El Alto"
                                />
                                <div v-if="form.errors.ubicacion" class="mt-1 text-xs text-[#ff8c94] font-mono">{{ form.errors.ubicacion }}</div>
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-industrial-muted uppercase tracking-wider mb-2">Encargado / Supervisor de Obra</label>
                                <input
                                    type="text"
                                    class="w-full bg-[#0e1113] text-[#e1e6eb] border border-[#2d3139] rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:border-[#f27b00]"
                                    v-model="form.encargado"
                                    placeholder="Ej. Ing. Carlos Flores"
                                />
                                <div v-if="form.errors.encargado" class="mt-1 text-xs text-[#ff8c94] font-mono">{{ form.errors.encargado }}</div>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-semibold text-industrial-muted uppercase tracking-wider mb-2">Fecha del Proyecto</label>
                                <input
                                    type="date"
                                    class="w-full bg-[#0e1113] text-[#e1e6eb] border border-[#2d3139] rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:border-[#f27b00] font-mono"
                                    v-model="form.fecha"
                                />
                                <div v-if="form.errors.fecha" class="mt-1 text-xs text-[#ff8c94] font-mono">{{ form.errors.fecha }}</div>
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-industrial-muted uppercase tracking-wider mb-2">Estado de Ejecución</label>
                                <select
                                    class="w-full bg-[#0e1113] text-[#e1e6eb] border border-[#2d3139] rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:border-[#f27b00]"
                                    v-model="form.estado"
                                    required
                                >
                                    <option value="activo">Activo / En Ejecución</option>
                                    <option value="pausado">Pausado / En Espera</option>
                                    <option value="finalizado">Finalizado / Concluido</option>
                                </select>
                                <div v-if="form.errors.estado" class="mt-1 text-xs text-[#ff8c94] font-mono">{{ form.errors.estado }}</div>
                            </div>
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
