<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import CityMap from '@/Components/CityMap.vue';
import WeatherWidget from '@/Components/WeatherWidget.vue';
import { Head, Link } from '@inertiajs/vue3';
import { computed, onMounted, onUnmounted, ref } from 'vue';

const props = defineProps({
    materials: { type: Array, required: true },
    proyectosConInventario: { type: Array, required: true },
    recentMovements: { type: Array, required: true },
});

const scaleWeight = ref(12450);
const mixerTemp = ref(152);
const productionRate = ref(87);

let interval = null;

onMounted(() => {
    interval = setInterval(() => {
        scaleWeight.value = Math.floor(12400 + Math.random() * 120);
        mixerTemp.value = Math.floor(150 + Math.random() * 5);
        productionRate.value = Math.floor(85 + Math.random() * 12);
    }, 5000);
});

onUnmounted(() => {
    if (interval) clearInterval(interval);
});

const normalizedProjects = computed(() => props.proyectosConInventario.map(project => ({
    ...project,
    estadoNormalizado: String(project.estado || '').toLowerCase(),
    inventario: project.inventario || [],
})));

const activeProjects = computed(() => normalizedProjects.value.filter(project => project.estadoNormalizado === 'activo').length);

const processedMaterials = computed(() => props.materials.map(material => {
    const stockActual = Number(material.stock_actual || 0);
    const stockMinimo = Number(material.stock_minimo || 0);
    const isCritical = stockActual <= stockMinimo;
    const isEmpty = stockActual <= 0;
    const denominator = stockMinimo > 0 ? stockMinimo * 2 : Math.max(stockActual, 1);
    const percent = Math.min((stockActual / denominator) * 100, 100);

    return {
        ...material,
        stock_actual: stockActual,
        stock_minimo: stockMinimo,
        isCritical,
        isEmpty,
        percent,
        status: isEmpty ? 'Sin stock' : (isCritical ? 'Reordenar' : 'Operativo'),
    };
}));

const criticalMaterials = computed(() => processedMaterials.value.filter(material => material.isCritical));
const topCriticalMaterials = computed(() => criticalMaterials.value.slice(0, 5));

const totalStockValue = computed(() => processedMaterials.value.reduce((sum, material) => sum + material.stock_actual, 0));

const stockHealthPercent = computed(() => {
    if (processedMaterials.value.length === 0) return 100;
    const stable = processedMaterials.value.filter(material => !material.isCritical).length;
    return Math.round((stable / processedMaterials.value.length) * 100);
});

const stockStatusLabel = computed(() => {
    if (processedMaterials.value.length === 0) return 'Sin datos';
    if (criticalMaterials.value.length === 0) return 'Operacion estable';
    return `${criticalMaterials.value.length} alerta(s)`;
});

const stockStatusClass = computed(() => {
    if (processedMaterials.value.length === 0) return 'text-[#9aa0a9]';
    return criticalMaterials.value.length === 0 ? 'text-[#4ade80]' : 'text-[#fbbf24]';
});

const movementTotals = computed(() => {
    return props.recentMovements.reduce((totals, movement) => {
        const isIngreso = String(movement.tipo || '').startsWith('INGRESO');
        if (isIngreso) totals.ingresos += 1;
        else totals.despachos += 1;
        return totals;
    }, { ingresos: 0, despachos: 0 });
});

const lastMovement = computed(() => props.recentMovements[0] || null);

const latestMovements = computed(() => props.recentMovements.slice(0, 6));

const projectLoad = computed(() => normalizedProjects.value.map(project => {
    const stock = project.inventario.reduce((sum, item) => sum + Number(item.stock || 0), 0);
    return { ...project, stock };
}).sort((a, b) => b.stock - a.stock));

const leadingProjects = computed(() => projectLoad.value.slice(0, 4));
</script>

<template>
    <Head title="Panel de Control - Asphalt-AGY" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
                <div class="flex items-center gap-3">
                    <div class="flex h-11 w-11 items-center justify-center rounded-xl border border-[#f27b00]/25 bg-[#f27b00]/10">
                        <svg class="h-5 w-5 text-[#f27b00]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 18.75h15M6 18.75V9l6-3 6 3v9.75M8.25 18.75v-6h3v6M14.25 18.75v-6h3v6M7.5 7.5V5.25M16.5 7.5V5.25" />
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-lg font-bold tracking-tight text-white">Consola Industrial</h2>
                        <p class="text-[10px] text-[#5c6370] font-mono uppercase tracking-[0.18em]">
                            Asphalt-AGY / Inventario PEPS / El Alto
                        </p>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-2 sm:flex sm:items-center sm:gap-3">
                    <div class="rounded-lg border border-[rgba(255,255,255,0.06)] bg-[#11161a] px-3 py-2">
                        <p class="text-[9px] uppercase tracking-wider text-[#5c6370]">Temperatura</p>
                        <p class="data-readout text-sm font-bold text-white">{{ mixerTemp }}<span class="text-[#5c6370]"> C</span></p>
                    </div>
                    <div class="rounded-lg border border-[rgba(255,255,255,0.06)] bg-[#11161a] px-3 py-2">
                        <p class="text-[9px] uppercase tracking-wider text-[#5c6370]">Produccion</p>
                        <p class="data-readout text-sm font-bold text-white">{{ scaleWeight.toLocaleString() }}<span class="text-[#5c6370]"> kg/h</span></p>
                    </div>
                    <div class="col-span-2 flex items-center justify-center gap-2 rounded-lg border border-[#22c55e]/20 bg-[#22c55e]/10 px-3 py-2 sm:col-span-1">
                        <span class="indicator indicator-active"></span>
                        <span class="text-[10px] font-mono font-semibold uppercase tracking-wider text-[#4ade80]">Sistema operativo</span>
                    </div>
                </div>
            </div>
        </template>

        <div class="mx-auto max-w-[1680px] p-4 sm:p-5 lg:p-6">
            <div class="grid grid-cols-12 gap-4">
                <section class="col-span-12 grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-4">
                    <div class="command-card xl:col-span-2">
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <p class="command-label">Estado general de stock</p>
                                <h3 class="mt-2 text-2xl font-bold text-white sm:text-3xl">
                                    {{ stockHealthPercent }}<span class="text-base text-[#5c6370]">%</span>
                                </h3>
                                <p class="mt-1 text-xs text-[#9aa0a9]">Materiales por encima del minimo operativo.</p>
                            </div>
                            <div class="rounded-xl border px-3 py-2 text-right" :class="criticalMaterials.length ? 'border-[#f59e0b]/25 bg-[#f59e0b]/10' : 'border-[#22c55e]/25 bg-[#22c55e]/10'">
                                <p class="text-[9px] uppercase tracking-wider text-[#5c6370]">Semaforo</p>
                                <p class="mt-1 text-xs font-bold uppercase" :class="stockStatusClass">{{ stockStatusLabel }}</p>
                            </div>
                        </div>
                        <div class="mt-5 h-2 overflow-hidden rounded-full bg-[#080a0d]">
                            <div
                                class="h-full rounded-full transition-all duration-700"
                                :class="criticalMaterials.length ? 'bg-[#f59e0b]' : 'bg-[#22c55e]'"
                                :style="{ width: stockHealthPercent + '%' }"
                            ></div>
                        </div>
                    </div>

                    <div class="command-card">
                        <p class="command-label">Stock total consolidado</p>
                        <p class="data-readout mt-3 text-3xl font-bold text-white">{{ totalStockValue.toLocaleString() }}</p>
                        <p class="mt-1 text-xs text-[#5c6370]">Unidades fisicas registradas</p>
                    </div>

                    <div class="command-card">
                        <p class="command-label">Obras activas</p>
                        <p class="data-readout mt-3 text-3xl font-bold text-white">{{ activeProjects }}</p>
                        <p class="mt-1 text-xs text-[#5c6370]">{{ proyectosConInventario.length }} proyecto(s) con inventario</p>
                    </div>
                </section>

                <section class="col-span-12 xl:col-span-8">
                    <div class="command-card h-full">
                        <div class="flex flex-col gap-3 border-b border-[rgba(255,255,255,0.06)] pb-4 lg:flex-row lg:items-center lg:justify-between">
                            <div>
                                <p class="command-label">Flujo operativo de planta</p>
                                <h3 class="mt-1 text-lg font-bold text-white">Ciclo PEPS de materiales</h3>
                            </div>
                            <div class="flex items-center gap-3">
                                <span class="rounded-lg border border-[#f27b00]/20 bg-[#f27b00]/10 px-3 py-2 text-[10px] font-bold uppercase tracking-wider text-[#f27b00]">
                                    Rendimiento {{ productionRate }}%
                                </span>
                                <span class="rounded-lg border border-[rgba(255,255,255,0.06)] bg-[#080a0d] px-3 py-2 text-[10px] font-mono text-[#9aa0a9]">
                                    Turno operativo
                                </span>
                            </div>
                        </div>

                        <div class="mt-6 grid gap-4 md:grid-cols-4">
                            <div class="plant-stage">
                                <div class="plant-stage-icon">
                                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.6">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 7.5l7.5-4 7.5 4-7.5 4-7.5-4zM4.5 12l7.5 4 7.5-4M4.5 16.5l7.5 4 7.5-4" />
                                    </svg>
                                </div>
                                <p class="command-label mt-3">Lotes</p>
                                <p class="data-readout mt-1 text-xl font-bold text-white">{{ materials.length }}</p>
                            </div>

                            <div class="plant-flow-line hidden md:block"></div>

                            <div class="plant-stage">
                                <div class="plant-stage-icon">
                                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.6">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 19.5h16.5M6 19.5V8.25l6-3 6 3V19.5M8.25 12h7.5M8.25 15h7.5" />
                                    </svg>
                                </div>
                                <p class="command-label mt-3">Planta</p>
                                <p class="data-readout mt-1 text-xl font-bold text-white">{{ mixerTemp }} C</p>
                            </div>

                            <div class="plant-flow-line hidden md:block"></div>

                            <div class="plant-stage">
                                <div class="plant-stage-icon">
                                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.6">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12h15m0 0l-4.5-4.5M19.5 12l-4.5 4.5M4.5 17.25h6M4.5 6.75h6" />
                                    </svg>
                                </div>
                                <p class="command-label mt-3">Despacho</p>
                                <p class="data-readout mt-1 text-xl font-bold text-white">{{ movementTotals.despachos }}</p>
                            </div>
                        </div>

                        <div class="mt-6 grid gap-3 sm:grid-cols-3">
                            <div class="metric-strip">
                                <span>Ingresos recientes</span>
                                <strong>{{ movementTotals.ingresos }}</strong>
                            </div>
                            <div class="metric-strip">
                                <span>Despachos recientes</span>
                                <strong>{{ movementTotals.despachos }}</strong>
                            </div>
                            <div class="metric-strip">
                                <span>Ultimo movimiento</span>
                                <strong>{{ lastMovement?.tipo || 'Sin datos' }}</strong>
                            </div>
                        </div>
                    </div>
                </section>

                <aside class="col-span-12 xl:col-span-4">
                    <div class="command-card h-full">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="command-label">Alertas de stock</p>
                                <h3 class="mt-1 text-base font-bold text-white">Materiales criticos</h3>
                            </div>
                            <span class="data-readout text-xl font-bold" :class="criticalMaterials.length ? 'text-[#fbbf24]' : 'text-[#4ade80]'">
                                {{ criticalMaterials.length }}
                            </span>
                        </div>

                        <div class="mt-4 space-y-3">
                            <div
                                v-for="material in topCriticalMaterials"
                                :key="material.id"
                                class="rounded-xl border border-[#f59e0b]/20 bg-[#f59e0b]/10 p-3"
                            >
                                <div class="flex items-start justify-between gap-3">
                                    <div class="min-w-0">
                                        <p class="truncate text-sm font-semibold text-white">{{ material.nombre }}</p>
                                        <p class="mt-1 text-[10px] text-[#9aa0a9]">Minimo: {{ Number(material.stock_minimo).toLocaleString() }}</p>
                                    </div>
                                    <span class="rounded-md bg-[#080a0d] px-2 py-1 text-[9px] font-bold uppercase text-[#fbbf24]">
                                        {{ material.status }}
                                    </span>
                                </div>
                                <div class="mt-3 flex items-center gap-3">
                                    <div class="h-1.5 flex-1 overflow-hidden rounded-full bg-[#080a0d]">
                                        <div class="h-full rounded-full bg-[#f59e0b]" :style="{ width: material.percent + '%' }"></div>
                                    </div>
                                    <span class="data-readout text-xs text-white">{{ Number(material.stock_actual).toLocaleString() }}</span>
                                </div>
                            </div>

                            <div v-if="criticalMaterials.length === 0" class="rounded-xl border border-[#22c55e]/20 bg-[#22c55e]/10 p-4 text-center">
                                <p class="text-sm font-bold text-[#4ade80]">Inventario estable</p>
                                <p class="mt-1 text-xs text-[#9aa0a9]">No hay materiales bajo minimo.</p>
                            </div>

                            <div v-if="processedMaterials.length === 0" class="rounded-xl border border-[rgba(255,255,255,0.06)] bg-[#080a0d] p-4 text-center">
                                <p class="text-sm font-bold text-white">Sin materiales</p>
                                <p class="mt-1 text-xs text-[#9aa0a9]">Registre materiales para activar monitoreo.</p>
                            </div>
                        </div>
                    </div>
                </aside>

                <section class="col-span-12 xl:col-span-7">
                    <div class="command-card h-full">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="command-label">Inventario por proyecto</p>
                                <h3 class="mt-1 text-base font-bold text-white">Obras con material asignado</h3>
                            </div>
                            <span class="text-[10px] font-mono uppercase tracking-wider text-[#5c6370]">{{ leadingProjects.length }} visibles</span>
                        </div>

                        <div class="mt-4 grid gap-3 md:grid-cols-2">
                            <div
                                v-for="project in leadingProjects"
                                :key="project.id"
                                class="project-tile"
                            >
                                <div class="flex items-start justify-between gap-3">
                                    <div class="min-w-0">
                                        <p class="truncate text-sm font-bold text-white">{{ project.nombre }}</p>
                                        <p class="mt-1 truncate text-[10px] text-[#5c6370]">{{ project.encargado || 'Sin encargado' }}</p>
                                    </div>
                                    <span class="status-badge status-badge-active text-[8px]">{{ project.estado }}</span>
                                </div>

                                <div class="mt-4 space-y-2">
                                    <div
                                        v-for="item in project.inventario.slice(0, 3)"
                                        :key="item.material"
                                        class="flex items-center justify-between gap-3 text-xs"
                                    >
                                        <span class="truncate text-[#9aa0a9]">{{ item.material }}</span>
                                        <span class="data-readout font-semibold text-[#f27b00]">{{ Number(item.stock).toLocaleString() }} {{ item.unidad }}</span>
                                    </div>
                                    <p v-if="project.inventario.length === 0" class="text-xs text-[#5c6370]">Sin asignaciones activas</p>
                                </div>

                                <div class="mt-4 border-t border-[rgba(255,255,255,0.06)] pt-3">
                                    <div class="flex items-center justify-between text-[10px]">
                                        <span class="uppercase tracking-wider text-[#5c6370]">Stock proyecto</span>
                                        <span class="data-readout font-bold text-white">{{ project.stock.toLocaleString() }}</span>
                                    </div>
                                </div>
                            </div>

                            <div v-if="leadingProjects.length === 0" class="col-span-full rounded-xl border border-[rgba(255,255,255,0.06)] bg-[#080a0d] p-8 text-center">
                                <p class="text-sm font-bold text-white">No hay proyectos activos con inventario</p>
                                <p class="mt-1 text-xs text-[#9aa0a9]">Los ingresos por lote apareceran aqui.</p>
                            </div>
                        </div>
                    </div>
                </section>

                <section class="col-span-12 xl:col-span-5">
                    <div class="command-card h-full">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="command-label">Acciones rapidas</p>
                                <h3 class="mt-1 text-base font-bold text-white">Operacion diaria</h3>
                            </div>
                        </div>

                        <div class="mt-4 grid gap-3 sm:grid-cols-3 xl:grid-cols-1">
                            <Link
                                v-if="$page.props.auth.permissions.includes('gestionar_ingresos')"
                                :href="route('ingresos.create')"
                                class="action-tile action-tile-accent"
                            >
                                <span>Registrar Ingreso</span>
                                <small>Nuevo lote de material</small>
                            </Link>
                            <Link
                                v-if="$page.props.auth.permissions.includes('gestionar_salidas')"
                                :href="route('despachos.create')"
                                class="action-tile action-tile-info"
                            >
                                <span>Registrar Despacho</span>
                                <small>Salida PEPS a obra</small>
                            </Link>
                            <Link :href="route('kardex.index')" class="action-tile">
                                <span>Kardex Fisico</span>
                                <small>Auditoria y reportes</small>
                            </Link>
                        </div>
                    </div>
                </section>

                <section class="col-span-12 2xl:col-span-8">
                    <div class="command-card h-full">
                        <div class="flex items-center justify-between border-b border-[rgba(255,255,255,0.06)] pb-4">
                            <div>
                                <p class="command-label">Ultimos movimientos</p>
                                <h3 class="mt-1 text-base font-bold text-white">Registro operativo PEPS</h3>
                            </div>
                            <span class="text-[10px] font-mono uppercase tracking-wider text-[#5c6370]">{{ latestMovements.length }} eventos</span>
                        </div>

                        <div class="mt-4 overflow-x-auto">
                            <table class="w-full min-w-[720px] text-left">
                                <thead>
                                    <tr class="border-b border-[rgba(255,255,255,0.06)]">
                                        <th class="pb-3 text-[9px] font-semibold text-[#5c6370] uppercase tracking-wider">Fecha</th>
                                        <th class="pb-3 text-[9px] font-semibold text-[#5c6370] uppercase tracking-wider">Tipo</th>
                                        <th class="pb-3 text-[9px] font-semibold text-[#5c6370] uppercase tracking-wider">Material</th>
                                        <th class="pb-3 text-[9px] font-semibold text-[#5c6370] uppercase tracking-wider">Proyecto</th>
                                        <th class="pb-3 text-[9px] font-semibold text-[#5c6370] uppercase tracking-wider text-right">Cantidad</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-[rgba(255,255,255,0.04)]">
                                    <tr v-for="move in latestMovements" :key="move.id" class="hover:bg-[#11161a]/50 transition-colors">
                                        <td class="py-3 data-readout text-xs text-[#5c6370]">{{ move.fecha }}</td>
                                        <td class="py-3">
                                            <span
                                                class="rounded-md px-2 py-1 text-[10px] font-mono font-semibold"
                                                :class="String(move.tipo).startsWith('INGRESO') ? 'bg-[#22c55e]/10 text-[#4ade80]' : 'bg-[#3b82f6]/10 text-[#60a5fa]'"
                                            >
                                                {{ move.tipo }}
                                            </span>
                                        </td>
                                        <td class="py-3 text-xs font-medium text-white">{{ move.material }}</td>
                                        <td class="py-3 text-xs text-[#9aa0a9]">{{ move.proyecto }}</td>
                                        <td class="py-3 data-readout text-xs text-right font-semibold text-[#f27b00]">
                                            {{ Number(move.cantidad).toLocaleString() }} {{ move.unidad }}
                                        </td>
                                    </tr>
                                    <tr v-if="latestMovements.length === 0">
                                        <td colspan="5" class="py-8 text-center text-xs text-[#5c6370]">
                                            No se registran movimientos de almacen.
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </section>

                <aside class="col-span-12 grid gap-4 2xl:col-span-4">
                    <div class="command-card">
                        <p class="command-label">Contexto externo</p>
                        <h3 class="mt-1 text-base font-bold text-white">Clima operativo</h3>
                        <p class="mt-1 text-xs text-[#5c6370]">Informativo; no bloquea la operacion intranet.</p>
                        <div class="mt-4">
                            <WeatherWidget />
                        </div>
                    </div>
                </aside>

                <section class="col-span-12">
                    <div class="command-card">
                        <div class="flex flex-col gap-2 border-b border-[rgba(255,255,255,0.06)] pb-4 sm:flex-row sm:items-center sm:justify-between">
                            <div>
                                <p class="command-label">Mapa operativo secundario</p>
                                <h3 class="mt-1 text-base font-bold text-white">Distritos de El Alto</h3>
                            </div>
                            <span class="rounded-lg border border-[rgba(255,255,255,0.06)] bg-[#080a0d] px-3 py-2 text-[10px] font-mono text-[#9aa0a9]">
                                Referencia geografica
                            </span>
                        </div>
                        <div class="mt-4">
                            <CityMap />
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
