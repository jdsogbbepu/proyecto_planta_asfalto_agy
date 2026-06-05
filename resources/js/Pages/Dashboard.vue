<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { ref, onMounted, onUnmounted, computed } from 'vue';

const props = defineProps({
    materials: { type: Array, required: true },
    proyectosConInventario: { type: Array, required: true },
    recentMovements: { type: Array, required: true },
});

const scaleWeight = ref(12450);
const mixerTemp = ref(152);
const activeStatus = ref('PRODUCCIÓN ACTIVA');
const productionRate = ref(87);

let interval = null;

onMounted(() => {
    interval = setInterval(() => {
        scaleWeight.value = Math.floor(12400 + Math.random() * 120);
        mixerTemp.value = Math.floor(150 + Math.random() * 5);
        productionRate.value = Math.floor(85 + Math.random() * 12);
    }, 2000);
});

onUnmounted(() => {
    if (interval) clearInterval(interval);
});

const processedMaterials = computed(() => {
    return props.materials.map(m => {
        const isCritical = m.stock_actual < m.stock_minimo;
        const denominator = m.stock_minimo > 0 ? m.stock_minimo * 2 : 10000;
        const percent = Math.min((m.stock_actual / denominator) * 100, 100);
        return { ...m, isCritical, percent };
    });
});

const hasCriticalStock = computed(() => processedMaterials.value.some(m => m.isCritical));

const totalStockValue = computed(() => {
    return processedMaterials.value.reduce((sum, m) => sum + (m.stock_actual || 0), 0);
});

const stockHealthPercent = computed(() => {
    if (processedMaterials.value.length === 0) return 100;
    const stable = processedMaterials.value.filter(m => !m.isCritical).length;
    return Math.round((stable / processedMaterials.value.length) * 100);
});

const activeProjects = computed(() => props.proyectosConInventario.filter(p => p.estado === 'ACTIVO').length);

const lastMovement = computed(() => {
    if (props.recentMovements.length === 0) return null;
    return props.recentMovements[0];
});
</script>

<template>
    <Head title="Panel de Control - Asphalt-AGY" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <div class="flex items-center gap-3">
                        <div class="w-1.5 h-8 bg-[#f27b00] rounded-full shadow-[0_0_12px_rgba(242,123,0,0.5)]"></div>
                        <div>
                            <h2 class="text-lg font-bold text-white tracking-tight">Control Center</h2>
                            <p class="text-[10px] text-[#5c6370] font-mono uppercase tracking-widest">El Alto • Planta de Asfalto</p>
                        </div>
                    </div>
                </div>

                <div class="flex items-center gap-6">
                    <div class="flex items-center gap-3 px-4 py-2 rounded-lg bg-[#11161a] border border-[rgba(255,255,255,0.06)]">
                        <div class="indicator indicator-active"></div>
                        <span class="text-xs font-mono font-semibold text-white tracking-wide">{{ activeStatus }}</span>
                    </div>

                    <div class="flex items-center gap-4">
                        <div class="text-right">
                            <p class="text-[10px] text-[#5c6370] uppercase tracking-wider">Temperatura Mezcla</p>
                            <p class="text-sm font-mono font-bold text-white">{{ mixerTemp }}<span class="text-[#5c6370] ml-1">°C</span></p>
                        </div>
                        <div class="w-px h-8 bg-[rgba(255,255,255,0.06)]"></div>
                        <div class="text-right">
                            <p class="text-[10px] text-[#5c6370] uppercase tracking-wider">Flujo Producción</p>
                            <p class="text-sm font-mono font-bold text-white">{{ scaleWeight.toLocaleString() }}<span class="text-[#5c6370] ml-1">kg/h</span></p>
                        </div>
                    </div>
                </div>
            </div>
        </template>

        <div class="p-6 mx-auto max-w-[1600px] font-sans">
            <div class="grid grid-cols-12 gap-4">
                <div class="col-span-12 lg:col-span-3 space-y-4">
                    <div class="control-panel p-4">
                        <div class="section-header">Stock de Materias Primas</div>

                        <div class="space-y-3">
                            <div
                                v-for="mat in processedMaterials"
                                :key="mat.id"
                                class="group"
                            >
                                <div class="flex items-center justify-between mb-1.5">
                                    <span class="text-xs font-medium text-[#9aa0a9] truncate pr-2">{{ mat.nombre }}</span>
                                    <span class="data-readout text-xs font-semibold text-white">{{ Number(mat.stock_actual).toLocaleString() }}</span>
                                </div>
                                <div class="control-bar">
                                    <div
                                        class="control-bar-fill"
                                        :class="mat.isCritical ? 'bg-[#ef4444]' : 'bg-[#f27b00]'"
                                        :style="{ width: mat.percent + '%' }"
                                    ></div>
                                </div>
                                <div class="flex items-center justify-between mt-1">
                                    <span class="text-[9px] text-[#5c6370]">Mín: {{ Number(mat.stock_minimo).toLocaleString() }}</span>
                                    <span
                                        class="text-[9px] font-mono font-semibold"
                                        :class="mat.isCritical ? 'text-[#ef4444]' : 'text-[#4ade80]'"
                                    >
                                        {{ mat.isCritical ? 'REORDENAR' : 'OK' }}
                                    </span>
                                </div>
                            </div>

                            <div v-if="processedMaterials.length === 0" class="py-6 text-center">
                                <p class="text-xs text-[#5c6370]">Sin materiales registrados</p>
                            </div>
                        </div>

                        <div class="mt-4 pt-4 border-t border-[rgba(255,255,255,0.06)] flex items-center justify-between">
                            <span class="text-[10px] text-[#5c6370] uppercase tracking-wider">Estado General</span>
                            <div class="flex items-center gap-2">
                                <div
                                    class="indicator"
                                    :class="hasCriticalStock ? 'indicator-danger' : 'indicator-active'"
                                ></div>
                                <span class="text-[10px] font-mono font-semibold" :class="hasCriticalStock ? 'text-[#ef4444]' : 'text-[#4ade80]'">
                                    {{ hasCriticalStock ? 'ATENCIÓN' : 'ÓPTIMO' }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="control-panel p-4">
                        <div class="section-header">Rendimiento</div>

                        <div class="flex items-end gap-3">
                            <div>
                                <p class="text-[10px] text-[#5c6370] uppercase tracking-wider mb-1">Tasa Producción</p>
                                <p class="data-readout text-3xl font-bold text-white">{{ productionRate }}<span class="text-lg text-[#5c6370] ml-1">%</span></p>
                            </div>
                        </div>

                        <div class="mt-3">
                            <div class="control-bar" style="height: 6px;">
                                <div
                                    class="control-bar-fill bg-[#f27b00]"
                                    :style="{ width: productionRate + '%' }"
                                ></div>
                            </div>
                        </div>

                        <div class="mt-4 grid grid-cols-2 gap-3">
                            <div class="bg-[#080a0d] rounded-lg p-3 border border-[rgba(255,255,255,0.04)]">
                                <p class="text-[9px] text-[#5c6370] uppercase tracking-wider">Proyectos Activos</p>
                                <p class="data-readout text-xl font-bold text-white mt-1">{{ activeProjects }}</p>
                            </div>
                            <div class="bg-[#080a0d] rounded-lg p-3 border border-[rgba(255,255,255,0.04)]">
                                <p class="text-[9px] text-[#5c6370] uppercase tracking-wider">Stock Salud</p>
                                <p class="data-readout text-xl font-bold text-white mt-1">{{ stockHealthPercent }}<span class="text-[#5c6370]">%</span></p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-span-12 lg:col-span-6 space-y-4">
                    <div class="control-panel p-4">
                        <div class="section-header">Flujo de Producción</div>

                        <div class="relative py-4">
                            <div class="flex items-center justify-between">
                                <div class="flex flex-col items-center gap-2">
                                    <div class="w-16 h-20 bg-[#11161a] rounded-lg border border-[rgba(255,255,255,0.08)] flex flex-col items-center justify-center">
                                        <svg class="w-6 h-6 text-[#5c6370]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                        </svg>
                                    </div>
                                    <span class="text-[9px] text-[#5c6370] uppercase tracking-wider">Silos</span>
                                    <span class="data-readout text-sm font-bold text-[#f27b00]">{{ totalStockValue.toLocaleString() }}</span>
                                    <span class="text-[8px] text-[#3d434d]">kg</span>
                                </div>

                                <div class="flex-1 flex items-center px-4">
                                    <div class="flex-1 h-px bg-gradient-to-r from-[rgba(242,123,0,0.4)] via-[#f27b00] to-[rgba(242,123,0,0.4)]"></div>
                                    <div class="w-2 h-2 rounded-full bg-[#f27b00] shadow-[0_0_8px_rgba(242,123,0,0.6)] mx-4"></div>
                                </div>

                                <div class="flex flex-col items-center gap-2">
                                    <div class="w-16 h-20 bg-[#11161a] rounded-lg border border-[rgba(255,255,255,0.08)] flex flex-col items-center justify-center">
                                        <svg class="w-6 h-6 text-[#5c6370]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
                                        </svg>
                                    </div>
                                    <span class="text-[9px] text-[#5c6370] uppercase tracking-wider">Mezclador</span>
                                    <span class="data-readout text-sm font-bold text-white">{{ mixerTemp }}<span class="text-[3d434d]">°C</span></span>
                                </div>

                                <div class="flex-1 flex items-center px-4">
                                    <div class="flex-1 h-px bg-gradient-to-r from-[rgba(242,123,0,0.4)] via-[#f27b00] to-[rgba(242,123,0,0.4)]"></div>
                                    <div class="w-2 h-2 rounded-full bg-[#f27b00] shadow-[0_0_8px_rgba(242,123,0,0.6)] mx-4"></div>
                                </div>

                                <div class="flex flex-col items-center gap-2">
                                    <div class="w-16 h-20 bg-[#11161a] rounded-lg border border-[rgba(255,255,255,0.08)] flex flex-col items-center justify-center">
                                        <svg class="w-6 h-6 text-[#5c6370]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 18.75a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 01-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h1.125c.621 0 1.129-.504 1.09-1.124a17.902 17.902 0 00-3.213-9.193 2.056 2.056 0 00-1.58-.86H14.25M16.5 18.75h-2.25m0-11.177v-.958c0-.568-.422-1.048-.987-1.106a48.554 48.554 0 00-10.026 0 1.106 1.106 0 00-.987 1.106v7.635m12-6.677v6.677m0 4.5v-4.5m0 0h-12" />
                                        </svg>
                                    </div>
                                    <span class="text-[9px] text-[#5c6370] uppercase tracking-wider">Despacho</span>
                                    <span class="data-readout text-sm font-bold text-[#4ade80]">{{ scaleWeight.toLocaleString() }}</span>
                                    <span class="text-[8px] text-[#3d434d]">kg/h</span>
                                </div>
                            </div>
                        </div>

                        <div class="mt-4 pt-4 border-t border-[rgba(255,255,255,0.06)] flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <div class="indicator indicator-active"></div>
                                <span class="text-[10px] font-mono text-[#4ade80]">SISTEMA OPERATIVO</span>
                            </div>
                            <div class="flex items-center gap-4 text-[10px] text-[#5c6370]">
                                <span>Uptime: 99.7%</span>
                                <span>Ciclo: PEPS</span>
                            </div>
                        </div>
                    </div>

                    <div class="control-panel p-4">
                        <div class="flex items-center justify-between mb-4">
                            <div class="section-header mb-0">Inventario por Proyecto</div>
                            <span class="text-[9px] font-mono text-[#5c6370] uppercase tracking-wider">{{ proyectosConInventario.length }} obras</span>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3 max-h-[280px] overflow-y-auto scrollbar-control pr-1">
                            <div
                                v-for="proj in proyectosConInventario"
                                :key="proj.id"
                                class="bg-[#080a0d] rounded-lg p-3 border border-[rgba(255,255,255,0.04)] hover:border-[rgba(242,123,0,0.2)] transition-colors"
                            >
                                <div class="flex items-start justify-between gap-2 mb-2">
                                    <h4 class="text-xs font-semibold text-white truncate">{{ proj.nombre }}</h4>
                                    <span
                                        class="status-badge text-[8px]"
                                        :class="proj.estado === 'ACTIVO' ? 'status-badge-active' : 'bg-[#1c2229] border border-[rgba(255,255,255,0.1)] text-[#5c6370]'"
                                    >
                                        {{ proj.estado }}
                                    </span>
                                </div>

                                <div class="space-y-1">
                                    <div
                                        v-for="item in proj.inventario.slice(0, 3)"
                                        :key="item.material"
                                        class="flex items-center justify-between text-[10px]"
                                    >
                                        <span class="text-[#5c6370] truncate pr-2">{{ item.material }}</span>
                                        <span class="data-readout text-[#f27b00] font-semibold">{{ Number(item.stock).toLocaleString() }}</span>
                                    </div>
                                    <div v-if="proj.inventario.length === 0" class="text-[9px] text-[#3d434d] py-1">Sin asignaciones</div>
                                    <div v-if="proj.inventario.length > 3" class="text-[9px] text-[#5c6370]">+{{ proj.inventario.length - 3 }} más</div>
                                </div>

                                <div class="mt-2 pt-2 border-t border-[rgba(255,255,255,0.04)] flex items-center justify-between">
                                    <span class="text-[9px] text-[#3d434d]">Residente:</span>
                                    <span class="text-[9px] text-[#9aa0a9] truncate max-w-[100px]">{{ proj.encargado || '—' }}</span>
                                </div>
                            </div>

                            <div v-if="proyectosConInventario.length === 0" class="col-span-2 py-8 text-center">
                                <p class="text-xs text-[#5c6370]">No hay proyectos activos</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-span-12 lg:col-span-3 space-y-4">
                    <div class="control-panel p-4">
                        <div class="section-header">Acciones Rápidas</div>

                        <div class="space-y-2">
                            <Link
                                v-if="$page.props.auth.user.role !== 'visor'"
                                :href="route('ingresos.create')"
                                class="flex items-center gap-3 p-3 rounded-lg bg-[#f27b00]/10 border border-[rgba(242,123,0,0.2)] hover:bg-[#f27b00]/20 transition-colors group"
                            >
                                <div class="w-8 h-8 rounded-lg bg-[#f27b00]/20 flex items-center justify-center">
                                    <svg class="w-4 h-4 text-[#f27b00]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                                    </svg>
                                </div>
                                <div class="flex-1">
                                    <span class="text-xs font-semibold text-white">Registrar Ingreso</span>
                                    <p class="text-[9px] text-[#5c6370]">Nuevo lote de materiales</p>
                                </div>
                                <svg class="w-4 h-4 text-[#5c6370] group-hover:text-[#f27b00] transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                                </svg>
                            </Link>

                            <Link
                                v-if="$page.props.auth.user.role !== 'visor'"
                                :href="route('despachos.create')"
                                class="flex items-center gap-3 p-3 rounded-lg bg-[#3b82f6]/10 border border-[rgba(59,130,246,0.2)] hover:bg-[#3b82f6]/20 transition-colors group"
                            >
                                <div class="w-8 h-8 rounded-lg bg-[#3b82f6]/20 flex items-center justify-center">
                                    <svg class="w-4 h-4 text-[#3b82f6]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                                    </svg>
                                </div>
                                <div class="flex-1">
                                    <span class="text-xs font-semibold text-white">Registrar Despacho</span>
                                    <p class="text-[9px] text-[#5c6370]">Salida PEPS a obra</p>
                                </div>
                                <svg class="w-4 h-4 text-[#5c6370] group-hover:text-[#3b82f6] transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                                </svg>
                            </Link>

                            <Link
                                :href="route('kardex.index')"
                                class="flex items-center gap-3 p-3 rounded-lg bg-[#11161a] border border-[rgba(255,255,255,0.06)] hover:border-[rgba(255,255,255,0.12)] transition-colors group"
                            >
                                <div class="w-8 h-8 rounded-lg bg-[#1c2229] flex items-center justify-center">
                                    <svg class="w-4 h-4 text-[#5c6370]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                </div>
                                <div class="flex-1">
                                    <span class="text-xs font-semibold text-white">Kardex PEPS</span>
                                    <p class="text-[9px] text-[#5c6370]">Movimiento físico valorado</p>
                                </div>
                                <svg class="w-4 h-4 text-[#5c6370] group-hover:text-white transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                                </svg>
                            </Link>
                        </div>
                    </div>

                    <div class="control-panel p-4">
                        <div class="section-header">Último Movimiento</div>

                        <div v-if="lastMovement" class="space-y-3">
                            <div class="flex items-center gap-2">
                                <div
                                    class="w-8 h-8 rounded-lg flex items-center justify-center"
                                    :class="lastMovement.tipo === 'INGRESO' ? 'bg-[rgba(34,197,94,0.1)]' : 'bg-[rgba(59,130,246,0.1)]'"
                                >
                                    <svg
                                        v-if="lastMovement.tipo === 'INGRESO'"
                                        class="w-4 h-4 text-[#4ade80]"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke="currentColor"
                                        stroke-width="2"
                                    >
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                                    </svg>
                                    <svg
                                        v-else
                                        class="w-4 h-4 text-[#3b82f6]"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke="currentColor"
                                        stroke-width="2"
                                    >
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                                    </svg>
                                </div>
                                <div class="flex-1">
                                    <span
                                        class="text-[10px] font-mono font-semibold"
                                        :class="lastMovement.tipo === 'INGRESO' ? 'text-[#4ade80]' : 'text-[#3b82f6]'"
                                    >
                                        {{ lastMovement.tipo }}
                                    </span>
                                    <p class="text-xs text-white font-medium">{{ lastMovement.material }}</p>
                                </div>
                            </div>

                            <div class="bg-[#080a0d] rounded-lg p-3 space-y-2">
                                <div class="flex items-center justify-between text-[10px]">
                                    <span class="text-[#5c6370]">Cantidad</span>
                                    <span class="data-readout text-white font-semibold">{{ Number(lastMovement.cantidad).toLocaleString() }} {{ lastMovement.unidad }}</span>
                                </div>
                                <div class="flex items-center justify-between text-[10px]">
                                    <span class="text-[#5c6370]">Proyecto</span>
                                    <span class="text-[#9aa0a9]">{{ lastMovement.proyecto }}</span>
                                </div>
                                <div class="flex items-center justify-between text-[10px]">
                                    <span class="text-[#5c6370]">Hora</span>
                                    <span class="data-readout text-[#5c6370]">{{ lastMovement.fecha }}</span>
                                </div>
                            </div>
                        </div>

                        <div v-else class="py-6 text-center">
                            <p class="text-xs text-[#5c6370]">Sin movimientos registrados</p>
                        </div>
                    </div>

                    <div class="control-panel p-4">
                        <div class="section-header">Clima • El Alto</div>

                        <div class="flex items-center gap-4">
                            <div class="text-4xl">🌤</div>
                            <div>
                                <p class="data-readout text-2xl font-bold text-white">12°</p>
                                <p class="text-xs text-[#5c6370]">Parcialmente nublado</p>
                            </div>
                        </div>

                        <div class="mt-4 grid grid-cols-3 gap-2 text-center">
                            <div class="bg-[#080a0d] rounded-lg p-2">
                                <p class="text-[9px] text-[#5c6370]">Humedad</p>
                                <p class="data-readout text-sm font-semibold text-white">45%</p>
                            </div>
                            <div class="bg-[#080a0d] rounded-lg p-2">
                                <p class="text-[9px] text-[#5c6370]">Viento</p>
                                <p class="data-readout text-sm font-semibold text-white">12km/h</p>
                            </div>
                            <div class="bg-[#080a0d] rounded-lg p-2">
                                <p class="text-[9px] text-[#5c6370]">Altitud</p>
                                <p class="data-readout text-sm font-semibold text-white">4000m</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-span-12">
                    <div class="control-panel p-4">
                        <div class="flex items-center justify-between mb-4">
                            <div class="section-header mb-0">Registro de Movimientos</div>
                            <span class="text-[9px] font-mono text-[#5c6370] uppercase tracking-wider">Auditoría PEPS</span>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="w-full text-left">
                                <thead>
                                    <tr class="border-b border-[rgba(255,255,255,0.06)]">
                                        <th class="pb-3 text-[9px] font-semibold text-[#5c6370] uppercase tracking-wider">Fecha / Hora</th>
                                        <th class="pb-3 text-[9px] font-semibold text-[#5c6370] uppercase tracking-wider">Tipo</th>
                                        <th class="pb-3 text-[9px] font-semibold text-[#5c6370] uppercase tracking-wider">Material</th>
                                        <th class="pb-3 text-[9px] font-semibold text-[#5c6370] uppercase tracking-wider">Proyecto</th>
                                        <th class="pb-3 text-[9px] font-semibold text-[#5c6370] uppercase tracking-wider text-right">Cantidad</th>
                                        <th class="pb-3 text-[9px] font-semibold text-[#5c6370] uppercase tracking-wider text-center">Usuario</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-[rgba(255,255,255,0.04)]">
                                    <tr
                                        v-for="move in recentMovements"
                                        :key="move.id"
                                        class="hover:bg-[#11161a]/50 transition-colors"
                                    >
                                        <td class="py-3 data-readout text-xs text-[#5c6370]">{{ move.fecha }}</td>
                                        <td class="py-3">
                                            <span
                                                class="text-[10px] font-mono font-semibold px-2 py-1 rounded"
                                                :class="move.tipo === 'INGRESO' ? 'bg-[rgba(34,197,94,0.1)] text-[#4ade80]' : 'bg-[rgba(59,130,246,0.1)] text-[#3b82f6]'"
                                            >
                                                {{ move.tipo }}
                                            </span>
                                        </td>
                                        <td class="py-3 text-xs font-medium text-white">{{ move.material }}</td>
                                        <td class="py-3 text-xs text-[#9aa0a9]">{{ move.proyecto }}</td>
                                        <td class="py-3 data-readout text-xs text-right font-semibold text-[#f27b00]">{{ Number(move.cantidad).toLocaleString() }} {{ move.unidad }}</td>
                                        <td class="py-3 text-xs text-center text-[#5c6370]">{{ move.usuario }}</td>
                                    </tr>
                                    <tr v-if="recentMovements.length === 0">
                                        <td colspan="6" class="py-8 text-center text-xs text-[#5c6370]">
                                            No se registran movimientos de almacén en el sistema.
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>