<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    bitacora: {
        type: Object,
        required: true,
    },
    filters: {
        type: Object,
        default: () => ({ search: '' }),
    },
});

const searchQuery = ref(props.filters.search || '');

const handleSearch = () => {
    router.get(
        route('bitacora.index'),
        { search: searchQuery.value },
        {
            preserveState: true,
            replace: true,
        }
    );
};

const clearSearch = () => {
    searchQuery.value = '';
    handleSearch();
};
</script>

<template>
    <Head title="Bitácora de Auditoría" />

    <AuthenticatedLayout>
        <template #header>
            <div>
                <h2 class="text-2xl font-bold tracking-tight text-white">Bitácora de Actividades (Auditoría)</h2>
                <p class="text-sm text-industrial-muted mt-1">Historial cronológico de creación, modificación y eliminación de datos en el sistema.</p>
            </div>
        </template>

        <div class="py-6 px-4 sm:px-6 lg:px-8 mx-auto max-w-7xl">
            <!-- Filter & Search Bar -->
            <div class="bg-[#1b1e22] border border-[#2d3139] rounded-xl p-4 mb-6 flex flex-col sm:flex-row gap-4 items-end">
                <div class="flex-1 w-full">
                    <label class="block text-xs font-semibold text-industrial-muted uppercase tracking-wider mb-1.5">Buscador global</label>
                    <input
                        type="text"
                        placeholder="Buscar por usuario, acción, detalle o IP..."
                        class="w-full bg-[#0e1113] text-[#e1e6eb] border border-[#2d3139] rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-[#f27b00]"
                        v-model="searchQuery"
                        @keyup.enter="handleSearch"
                    />
                </div>
                <div class="flex gap-2 w-full sm:w-auto">
                    <button
                        @click="handleSearch"
                        class="w-full sm:w-auto bg-[#f27b00] hover:bg-[#ff9426] text-[#111417] font-bold py-2 px-4 rounded-lg text-sm tracking-wider uppercase transition duration-150"
                    >
                        Buscar
                    </button>
                    <button
                        @click="clearSearch"
                        class="w-full sm:w-auto bg-[#2d3139] hover:bg-[#383d47] text-white font-bold py-2 px-4 rounded-lg text-sm tracking-wider uppercase transition duration-150"
                    >
                        Limpiar
                    </button>
                </div>
            </div>

            <!-- Table -->
            <div class="bg-[#1b1e22] border border-[#2d3139] rounded-xl overflow-hidden shadow-xl mb-6">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-[#e1e6eb]">
                        <thead class="text-xs text-industrial-muted uppercase bg-[#0e1113] border-b border-[#2d3139]">
                            <tr>
                                <th class="px-6 py-4 font-semibold w-1/6">Fecha y Hora</th>
                                <th class="px-6 py-4 font-semibold w-1/5">Usuario / Rol</th>
                                <th class="px-6 py-4 font-semibold w-1/6">Acción</th>
                                <th class="px-6 py-4 font-semibold">Detalles de Operación</th>
                                <th class="px-6 py-4 font-semibold text-right w-1/12">Dirección IP</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#2d3139]">
                            <tr 
                                v-for="log in bitacora.data" 
                                :key="log.id" 
                                class="hover:bg-[#1f2329]/50 transition duration-150"
                            >
                                <td class="px-6 py-4 font-mono text-xs text-industrial-muted">
                                    {{ new Date(log.created_at).toLocaleString('es-ES', { year: 'numeric', month: '2-digit', day: '2-digit', hour: '2-digit', minute: '2-digit', second: '2-digit' }) }}
                                </td>
                                <td class="px-6 py-4">
                                    <div class="font-medium text-white">{{ log.usuario ? log.usuario.name : 'Sistema / Anónimo' }}</div>
                                    <div class="text-xs text-industrial-muted font-mono" v-if="log.usuario">
                                        @{{ log.usuario.username }}
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="bg-[#2d3139] border border-[#383d47] text-white px-2 py-0.5 rounded text-xs font-semibold">
                                        {{ log.accion }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-xs text-[#e1e6eb]">
                                    {{ log.detalle }}
                                </td>
                                <td class="px-6 py-4 text-right font-mono text-xs text-industrial-muted">
                                    {{ log.ip_address || 'N/A' }}
                                </td>
                            </tr>
                            <tr v-if="bitacora.data.length === 0">
                                <td colspan="5" class="px-6 py-12 text-center text-industrial-muted">
                                    No se encontraron registros de actividades.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Pagination -->
            <div v-if="bitacora.links.length > 3" class="flex justify-center items-center gap-1.5 mt-4">
                <template v-for="(link, key) in bitacora.links" :key="key">
                    <div 
                        v-if="link.url === null" 
                        class="px-3.5 py-2 text-xs font-semibold text-industrial-muted bg-[#1b1e22]/50 border border-[#2d3139] rounded-lg"
                        v-html="link.label"
                    />
                    <button
                        v-else
                        @click="router.get(link.url, { search: searchQuery }, { preserveState: true, replace: true })"
                        class="px-3.5 py-2 text-xs font-semibold rounded-lg border transition duration-150"
                        :class="link.active 
                            ? 'bg-[#f27b00] border-[#f27b00] text-[#111417] font-bold' 
                            : 'bg-[#1b1e22] border-[#2d3139] text-[#e1e6eb] hover:border-[#f27b00] hover:text-white'"
                        v-html="link.label"
                    />
                </template>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
