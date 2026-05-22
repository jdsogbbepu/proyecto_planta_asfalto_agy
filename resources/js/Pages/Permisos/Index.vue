<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    roles: {
        type: Array,
        required: true,
    },
    permisosDisponibles: {
        type: Array,
        required: true,
    },
    permisosActuales: {
        type: Object, // { operador: [...], visor: [...], administrador: [...] }
        required: true,
    },
});

// Friendly labels for permissions
const permissionLabels = {
    ver_dashboard: 'Ver Dashboard y Monitoreo',
    gestionar_usuarios: 'Gestionar Usuarios del Sistema',
    gestionar_materiales: 'Gestionar Materiales y Medidas',
    gestionar_proveedores: 'Gestionar Proveedores',
    gestionar_proyectos: 'Gestionar Proyectos / Obras',
    gestionar_funcionarios: 'Gestionar Funcionarios Receptores',
    gestionar_ingresos: 'Registrar Ingresos de Almacén',
    gestionar_salidas: 'Registrar Despachos / Salidas (PEPS)',
    ver_reportes: 'Ver Reportes y Kardex de Valoración',
    gestionar_permisos: 'Administrar Roles y Permisos',
    ver_bitacora: 'Consultar Bitácora de Actividades (Auditoría)',
};

// Initialize form structure from props
const initialMatrix = {};
props.roles.forEach(role => {
    initialMatrix[role] = props.permisosActuales[role] ? [...props.permisosActuales[role]] : [];
});

const form = useForm({
    matrix: initialMatrix,
});

// Toggle permission in the matrix
const togglePermission = (role, permission) => {
    // If the role is administrator, we lock all permissions to true to prevent self-lockout
    if (role === 'administrador') return;

    const index = form.matrix[role].indexOf(permission);
    if (index > -1) {
        form.matrix[role].splice(index, 1);
    } else {
        form.matrix[role].push(permission);
    }
};

const savePermissions = () => {
    form.post(route('permisos.update'), {
        preserveScroll: true,
    });
};
</script>

<template>
    <Head title="Control de Permisos" />

    <AuthenticatedLayout>
        <template #header>
            <div>
                <h2 class="text-2xl font-bold tracking-tight text-white">Matriz de Roles y Permisos</h2>
                <p class="text-sm text-industrial-muted mt-1">Configure dinámicamente qué módulos y acciones puede realizar cada rol en la Planta.</p>
            </div>
        </template>

        <div class="py-6 px-4 sm:px-6 lg:px-8 mx-auto max-w-7xl">
            <div class="bg-[#1b1e22] border border-[#2d3139] rounded-xl overflow-hidden shadow-xl p-6 mb-6">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 border-b border-[#2d3139] pb-4 mb-6">
                    <div>
                        <h3 class="text-lg font-bold text-white">Matriz de Configuración</h3>
                        <p class="text-xs text-industrial-muted mt-0.5">Nota: El rol Administrador cuenta con todos los accesos habilitados por defecto en el sistema.</p>
                    </div>
                    <button
                        @click="savePermissions"
                        :disabled="form.processing"
                        class="bg-[#f27b00] hover:bg-[#ff9426] disabled:opacity-50 text-[#111417] font-bold py-2.5 px-6 rounded-lg text-sm tracking-wider uppercase transition duration-150 flex items-center justify-center gap-2"
                    >
                        <span v-if="form.processing">Guardando...</span>
                        <span v-else>Guardar Cambios</span>
                    </button>
                </div>

                <!-- Matrix Table -->
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-[#e1e6eb]">
                        <thead class="text-xs text-industrial-muted uppercase bg-[#0e1113] border-b border-[#2d3139]">
                            <tr>
                                <th class="px-6 py-4 font-semibold w-1/2">Permiso / Módulo</th>
                                <th 
                                    v-for="role in roles" 
                                    :key="role" 
                                    class="px-6 py-4 font-semibold text-center capitalize"
                                >
                                    {{ role }}
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#2d3139]">
                            <tr 
                                v-for="permission in permisosDisponibles" 
                                :key="permission"
                                class="hover:bg-[#1f2329]/50 transition duration-150"
                            >
                                <td class="px-6 py-4">
                                    <div class="font-medium text-white">{{ permissionLabels[permission] || permission }}</div>
                                    <div class="text-xs text-industrial-muted font-mono">{{ permission }}</div>
                                </td>
                                <td 
                                    v-for="role in roles" 
                                    :key="role" 
                                    class="px-6 py-4 text-center"
                                >
                                    <label class="inline-flex items-center justify-center cursor-pointer p-2">
                                        <input
                                            type="checkbox"
                                            :checked="role === 'administrador' || form.matrix[role].includes(permission)"
                                            :disabled="role === 'administrador' || form.processing"
                                            @change="togglePermission(role, permission)"
                                            class="w-5 h-5 bg-[#0e1113] border border-[#2d3139] rounded text-[#f27b00] focus:ring-offset-[#1b1e22] focus:ring-[#f27b00] transition duration-150 disabled:opacity-50"
                                        />
                                    </label>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
