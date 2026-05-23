<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref, computed } from 'vue';

const props = defineProps({
    materials: { type: Array, required: true },
    proveedores: { type: Array, required: true },
    proyectos: { type: Array, required: true },
    funcionarios: { type: Array, required: true },
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
    odc: '',
    observaciones: '',
    items: [
        { id_material: '', id_proyecto: '', id_proveedor: '', fecha_lote: getTodayDate(), cantidad: 1 }
    ]
});

const getMaterialUnit = (materialId) => {
    const mat = props.materials.find(m => m.id === Number(materialId));
    return mat && mat.medida ? mat.medida.abreviacion : '';
};

const getRowNumber = (index) => index + 1;

const getSelectedProyectoFecha = () => {
    const proj = props.proyectos.find(p => p.id === Number(selectedProyectoId.value));
    return proj?.fecha || getTodayDate();
};

const onProyectoChange = () => {
    headerFecha.value = getSelectedProyectoFecha();
    form.items.forEach(item => {
        item.id_proyecto = selectedProyectoId.value;
        item.fecha_lote = headerFecha.value;
    });
};

const addItemRow = () => {
    form.items.push({
        id_material: props.materials[0]?.id || '',
        id_proyecto: selectedProyectoId.value || props.proyectos[0]?.id || '',
        id_proveedor: props.proveedores[0]?.id || '',
        fecha_lote: headerFecha.value,
        cantidad: 1,
    });
};

const removeItemRow = (index) => {
    if (form.items.length > 1) {
        form.items.splice(index, 1);
    } else {
        alert('Debe registrar al menos un lote de material.');
    }
};

const isFormInvalid = computed(() => {
    return form.items.some(item =>
        !item.id_material || !item.id_proyecto || !item.id_proveedor || !item.cantidad || Number(item.cantidad) <= 0
    );
});

const submitForm = () => {
    if (isFormInvalid.value) {
        alert('Complete todos los campos obligatorios de cada fila.');
        return;
    }
    // Debug: mostrar datos antes de enviar
    console.log('Datos a enviar:', {
        id_funcionario: form.id_funcionario,
        odc: form.odc,
        observaciones: form.observaciones,
        items: form.items
    });
    alert('Enviando datos... revisa la consola para ver qué se envía');
    form.post(route('ingresos.store'));
};
</script>

<template>
    <Head title="Registrar Ingreso de Almacén" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center gap-4">
                <Link :href="route('ingresos.index')" class="text-industrial-muted hover:text-white transition">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                </Link>
                <div>
                    <h2 class="text-2xl font-bold tracking-tight text-white">Registrar Ingreso de Almacén</h2>
                    <p class="text-sm text-industrial-muted mt-1">Genere lotes (RGTR) para cada material ingresado a almacén.</p>
                </div>
            </div>
        </template>

        <div class="py-6 px-4 sm:px-6 lg:px-8 mx-auto max-w-7xl font-sans">
            <form @submit.prevent="submitForm" class="space-y-6">
                <div v-if="form.hasErrors" class="bg-[#3b1d22] border border-[#8a252c] rounded-xl p-4 mb-4">
                    <p class="text-[#ff8c94] font-bold text-sm">Error al registrar. Revise los campos:</p>
                    <ul v-if="Object.keys(form.errors).length > 0" class="mt-2 text-[#ff8c94] text-xs list-disc list-inside">
                        <li v-for="(error, key) in form.errors" :key="key">{{ error }}</li>
                    </ul>
                </div>
                <div class="bg-[#1b1e22] border border-[#2d3139] rounded-xl p-6 shadow-xl">
                    <h3 class="text-base font-bold text-white tracking-tight mb-4 border-b border-[#2d3139] pb-2">
                        1. Datos Generales del Registro
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
                            <label class="block text-xs font-semibold text-industrial-muted uppercase tracking-wider mb-2">Fecha de Ingreso</label>
                            <input
                                type="date"
                                class="w-full bg-[#0e1113] text-[#e1e6eb] border border-[#2d3139] rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:border-[#f27b00] font-mono"
                                v-model="headerFecha"
                            />
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-industrial-muted uppercase tracking-wider mb-2">Orden de Compra (ODC)</label>
                            <input
                                type="text"
                                class="w-full bg-[#0e1113] text-[#e1e6eb] border border-[#2d3139] rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:border-[#f27b00] font-mono uppercase"
                                v-model="form.odc"
                                placeholder="XX/XXXX/ODC/XXX/YY"
                                maxlength="50"
                            />
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-industrial-muted uppercase tracking-wider mb-2">Funcionario Receptor</label>
                            <select
                                class="w-full bg-[#0e1113] text-[#e1e6eb] border border-[#2d3139] rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:border-[#f27b00]"
                                v-model="form.id_funcionario"
                            >
                                <option value="">-- Seleccionar --</option>
                                <option v-for="func in funcionarios" :key="func.id" :value="func.id">
                                    {{ func.nombre }}
                                </option>
                            </select>
                        </div>
                    </div>

                    <div class="mt-4">
                        <label class="block text-xs font-semibold text-industrial-muted uppercase tracking-wider mb-2">Observaciones</label>
                        <input
                            type="text"
                            class="w-full bg-[#0e1113] text-[#e1e6eb] border border-[#2d3139] rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:border-[#f27b00]"
                            v-model="form.observaciones"
                            placeholder="Transportista, placa, observaciones generales..."
                        />
                    </div>
                </div>

                <div class="bg-[#1b1e22] border border-[#2d3139] rounded-xl p-6 shadow-xl">
                    <div class="flex items-center justify-between border-b border-[#2d3139] pb-3 mb-4">
                        <h3 class="text-base font-bold text-white tracking-tight">
                            2. Materiales e Insumos Ingresados (Lotes)
                        </h3>
                        <button
                            type="button"
                            @click="addItemRow"
                            class="bg-[#2d3139] hover:bg-[#383d47] text-white font-semibold py-1.5 px-4 rounded border border-[#2d3139] text-xs uppercase tracking-wider transition flex items-center gap-1.5"
                        >
                            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                            </svg>
                            Agregar Fila
                        </button>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm text-[#e1e6eb]">
                            <thead class="text-xs text-industrial-muted uppercase bg-[#0e1113] border-b border-[#2d3139]">
                                <tr>
                                    <th class="px-3 py-3 font-semibold text-center w-12">Nro</th>
                                    <th class="px-3 py-3 font-semibold">Material / Insumo</th>
                                    <th class="px-3 py-3 font-semibold text-center w-36">Und | Cantidad</th>
                                    <th class="px-3 py-3 font-semibold">Proveedor</th>
                                    <th class="px-3 py-3 font-semibold w-40">Fecha Lote</th>
                                    <th class="px-3 py-3 font-semibold text-center w-20">Acción</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-[#2d3139]">
                                <tr v-for="(item, index) in form.items" :key="index" class="hover:bg-[#1f2329]/10">
                                    <td class="px-3 py-3 text-center font-mono font-bold text-[#f27b00]">
                                        {{ getRowNumber(index) }}
                                    </td>

                                    <td class="px-3 py-3">
                                        <select
                                            class="w-full bg-[#0e1113] text-[#e1e6eb] border border-[#2d3139] rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-[#f27b00]"
                                            v-model="item.id_material"
                                            required
                                        >
                                            <option value="" disabled>-- Material --</option>
                                            <option v-for="mat in materials" :key="mat.id" :value="mat.id">
                                                {{ mat.nombre }}
                                            </option>
                                        </select>
                                    </td>

                                    <td class="px-3 py-3">
                                        <div class="flex items-center rounded-lg overflow-hidden bg-[#0e1113] border border-[#2d3139]">
                                            <span class="bg-[#1b1e22] text-xs font-mono font-bold text-industrial-muted px-3 py-2 border-r border-[#2d3139] select-none whitespace-nowrap">
                                                {{ getMaterialUnit(item.id_material) || '—' }}
                                            </span>
                                            <input
                                                type="number"
                                                step="0.01"
                                                min="0.01"
                                                class="flex-1 bg-transparent text-right text-[#e1e6eb] border-none px-3 py-2 text-sm focus:outline-none focus:ring-0 font-mono"
                                                v-model="item.cantidad"
                                                required
                                            />
                                        </div>
                                    </td>

                                    <td class="px-3 py-3">
                                        <select
                                            class="w-full bg-[#0e1113] text-[#e1e6eb] border border-[#2d3139] rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-[#f27b00]"
                                            v-model="item.id_proveedor"
                                            required
                                        >
                                            <option value="" disabled>-- Proveedor --</option>
                                            <option v-for="prov in proveedores" :key="prov.id" :value="prov.id">
                                                {{ prov.razon_social }}
                                            </option>
                                        </select>
                                    </td>

                                    <td class="px-3 py-3">
                                        <input
                                            type="date"
                                            class="w-full bg-[#0e1113] text-[#e1e6eb] border border-[#2d3139] rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-[#f27b00] font-mono"
                                            v-model="item.fecha_lote"
                                            required
                                        />
                                    </td>

                                    <td class="px-3 py-3 text-center">
                                        <button
                                            type="button"
                                            @click="removeItemRow(index)"
                                            class="text-xs bg-[#3b1d22] hover:bg-[#52252a] text-[#ff8c94] font-semibold py-1.5 px-3 rounded border border-[#8a252c] transition"
                                        >
                                            Quitar
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="flex justify-end gap-3 pt-4">
                    <Link
                        :href="route('ingresos.index')"
                        class="bg-[#2d3139] hover:bg-[#383d47] text-white font-bold py-2.5 px-5 rounded-lg text-sm tracking-wider uppercase transition"
                    >
                        Cancelar
                    </Link>
                    <button
                        type="submit"
                        class="bg-[#f27b00] hover:bg-[#ff9426] text-[#111417] font-bold py-2.5 px-8 rounded-lg text-sm tracking-wider uppercase transition disabled:opacity-50"
                        :disabled="form.processing || isFormInvalid"
                    >
                        {{ form.processing ? 'Registrando...' : 'Registrar Lote(s)' }}
                    </button>
                </div>
            </form>
        </div>
    </AuthenticatedLayout>
</template>