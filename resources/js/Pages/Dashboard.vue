<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { ref, onMounted, onUnmounted, computed } from 'vue';
import WeatherWidget from '@/Components/WeatherWidget.vue';
import CityMap from '@/Components/CityMap.vue';

const props = defineProps({
    materials: {
        type: Array,
        required: true,
    },
    proyectosConInventario: {
        type: Array,
        required: true,
    },
    recentMovements: {
        type: Array,
        required: true,
    },
});

// Telemetry simulation
const scaleWeight = ref(12450); // kg
const mixerTemp = ref(152); // °C
const activeStatus = ref('PRODUCCIÓN ACTIVA');

let interval = null;

onMounted(() => {
    // Simulate real-time sensor updates
    interval = setInterval(() => {
        scaleWeight.value = Math.floor(12400 + Math.random() * 120);
        mixerTemp.value = Math.floor(150 + Math.random() * 5);
    }, 2000);
});

onUnmounted(() => {
    if (interval) clearInterval(interval);
});

// Calculate stock status labels and percentages
const processedMaterials = computed(() => {
    return props.materials.map(m => {
        const isCritical = m.stock_actual < m.stock_minimo;
        
        // Progress bar percentage (capped at 100, relative to stock_minimo * 2)
        const denominator = m.stock_minimo > 0 ? m.stock_minimo * 2 : 10000;
        const percent = Math.min((m.stock_actual / denominator) * 100, 100);
        
        return {
            ...m,
            isCritical,
            percent
        };
    });
});

// Check if any material is in critical state
const hasCriticalStock = computed(() => {
    return processedMaterials.value.some(m => m.isCritical);
});
</script>

<template>
    <Head title="Panel de Control - Asphalt-AGY" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <h2 class="text-2xl font-bold tracking-tight text-white">Panel de Control de Planta</h2>
                    <p class="text-sm text-industrial-muted mt-1 font-sans font-medium">Monitoreo de stocks y telemetría de tolvas en tiempo real.</p>
                </div>
                <!-- Connected telemetry badge -->
                <div class="flex items-center gap-3 bg-[#1b1e22] border border-[#2d3139] px-4 py-2 rounded-xl shadow-lg shadow-black/30">
                    <span class="h-2.5 w-2.5 rounded-full bg-emerald-500 animate-pulse shadow-[0_0_10px_#10b981]"></span>
                    <span class="text-xs font-mono tracking-wider font-bold text-white uppercase">{{ activeStatus }}</span>
                </div>
            </div>
        </template>

        <div class="py-6 px-4 sm:px-6 lg:px-8 mx-auto max-w-7xl font-sans relative">
            
            <!-- Bento Grid Layout -->
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
                
                <!-- Card 1: Critical Stock (Col-span-4 / Row-span-2) -->
                <div class="bento-card lg:col-span-4 p-6 flex flex-col justify-between bg-[#1b1e22]/90 backdrop-blur-md relative overflow-hidden">
                    <div class="absolute top-0 right-0 w-24 h-24 bg-[#f27b00]/5 rounded-full blur-xl pointer-events-none"></div>
                    <div>
                        <div class="flex items-center justify-between mb-6">
                            <h3 class="text-base font-bold text-white tracking-tight">Estado de Stocks</h3>
                            <span 
                                class="text-[10px] font-mono px-2 py-0.5 rounded-full font-bold uppercase"
                                :class="hasCriticalStock ? 'bg-[#3b1d22] border border-[#8a252c] text-[#ff8c94]' : 'bg-[#0f3d2a] border border-[#1a7f4c] text-[#a6ffcc]'"
                            >
                                {{ hasCriticalStock ? 'Reordenar' : 'Estable' }}
                            </span>
                        </div>
                        
                        <!-- Stock Items -->
                        <div class="space-y-4">
                            <div v-for="mat in processedMaterials" :key="mat.id" class="border-b border-[#2d3139]/40 pb-3 last:border-b-0 last:pb-0">
                                <div class="flex justify-between text-sm mb-1.5 items-center">
                                    <span class="text-industrial-foreground font-semibold text-xs">{{ mat.nombre }}</span>
                                    <span class="text-white font-mono font-bold text-xs bg-[#0e1113] px-2 py-0.5 rounded border border-[#2d3139]">
                                        {{ Number(mat.stock_actual).toLocaleString() }} {{ mat.medida?.abreviacion }}
                                    </span>
                                </div>
                                <div class="w-full bg-[#0e1113] rounded-full h-2 border border-[#2d3139] overflow-hidden">
                                    <div 
                                        class="h-2 rounded-full transition-all duration-1000" 
                                        :class="mat.isCritical ? 'bg-[#f27b00]' : 'bg-emerald-500'"
                                        :style="{ width: mat.percent + '%' }"
                                    ></div>
                                </div>
                                <div class="flex justify-between text-[9px] font-mono mt-1 text-industrial-muted">
                                    <span>Mínimo: {{ Number(mat.stock_minimo).toLocaleString() }}</span>
                                    <span 
                                        :class="mat.isCritical ? 'text-[#f27b00] font-bold' : 'text-emerald-400 font-semibold'"
                                    >
                                        {{ mat.isCritical ? '⚠️ Reordenar' : '✓ Suficiente' }}
                                    </span>
                                </div>
                            </div>
                            
                            <div v-if="processedMaterials.length === 0" class="text-center py-6 text-industrial-muted text-xs">
                                No hay materiales catalogados.
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Card 2: Clima y Mapa El Alto (Col-span-8) -->
                <div class="bento-card lg:col-span-8 p-6 flex flex-col justify-between bg-[#1b1e22]/90 backdrop-blur-md">
                    <div class="space-y-4">
                        <WeatherWidget />
                        <CityMap />
                    </div>
                </div>

                <!-- Card 3: Quick Links (Col-span-4) -->
                <div class="bento-card lg:col-span-4 p-6 flex flex-col justify-between bg-[#1b1e22]/90 backdrop-blur-md">
                    <div>
                        <h3 class="text-base font-bold text-white tracking-tight mb-4">Acciones Operativas</h3>
                        <p class="text-xs text-industrial-muted mb-6">Ejecute movimientos de almacén y consulte balances de forma inmediata.</p>
                        
                        <div class="space-y-3">
                            <Link 
                                v-if="$page.props.auth.user.role !== 'visor'"
                                :href="route('ingresos.create')" 
                                class="w-full bg-[#f27b00] hover:bg-[#ff9426] text-[#111417] font-bold py-3 px-4 rounded-xl text-xs uppercase tracking-wider font-sans transition duration-150 flex items-center justify-between shadow-lg shadow-[#f27b00]/10"
                            >
                                <span>Registrar Ingreso</span>
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                                </svg>
                            </Link>
                            
                            <Link 
                                v-if="$page.props.auth.user.role !== 'visor'"
                                :href="route('despachos.create')"
                                class="w-full bg-[#0a5c8f] hover:bg-[#0c6fa9] text-white font-bold py-3 px-4 rounded-xl text-xs uppercase tracking-wider font-sans transition duration-150 flex items-center justify-between shadow-lg shadow-[#0a5c8f]/10"
                            >
                                <span>Registrar Despacho</span>
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                                </svg>
                            </Link>

                            <Link 
                                :href="route('kardex.index')" 
                                class="w-full bg-[#0e1113] hover:bg-[#2d3139] text-[#e1e6eb] font-semibold py-3 px-4 rounded-xl text-xs uppercase tracking-wider font-sans border border-[#2d3139] transition duration-150 flex items-center justify-between"
                            >
                                <span>Kardex Físico (PEPS)</span>
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                            </Link>
                        </div>
                    </div>
                </div>

                <!-- Card 4: Bento Inventory General Por Proyecto (Col-span-8) -->
                <div class="bento-card lg:col-span-8 p-6 flex flex-col justify-between bg-[#1b1e22]/90 backdrop-blur-md">
                    <div>
                        <div class="flex items-center justify-between mb-6">
                            <div>
                                <h3 class="text-base font-bold text-white tracking-tight">Inventario Acumulado por Obra / Proyecto</h3>
                                <p class="text-xs text-industrial-muted mt-1">Silos virtuales de insumos reservados y acumulados en almacén por proyecto activo.</p>
                            </div>
                            <span class="text-[10px] font-mono bg-[#0e1113] border border-[#2d3139] text-industrial-muted px-2 py-0.5 rounded-full">
                                Asignaciones PEPS
                            </span>
                        </div>

                        <!-- Grid of Projects Bento Cells -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                            <div 
                                v-for="proj in proyectosConInventario" 
                                :key="proj.id" 
                                class="bg-[#0e1113] border border-[#2d3139]/80 rounded-xl p-4 flex flex-col justify-between shadow-inner hover:border-[#f27b00]/30 transition duration-150"
                            >
                                <div>
                                    <div class="flex items-start justify-between gap-2 border-b border-[#2d3139] pb-2 mb-3">
                                        <h4 class="text-xs font-bold text-white uppercase tracking-wide truncate" :title="proj.nombre">
                                            {{ proj.nombre }}
                                        </h4>
                                        <span class="text-[8px] font-mono font-bold bg-[#0f3d2a] border border-[#1a7f4c] text-[#a6ffcc] px-1.5 py-0.2 rounded uppercase">
                                            {{ proj.estado }}
                                        </span>
                                    </div>
                                    
                                    <!-- Materials balances list inside project -->
                                    <div class="space-y-1.5">
                                        <div 
                                            v-for="item in proj.inventario" 
                                            :key="item.material" 
                                            class="flex justify-between items-center text-xs font-mono"
                                        >
                                            <span class="text-industrial-muted truncate max-w-[170px]" :title="item.material">
                                                {{ item.material }}
                                            </span>
                                            <span class="text-[#f27b00] font-bold bg-[#1b1e22]/50 px-1.5 py-0.2 rounded border border-[#2d3139]/30">
                                                {{ Number(item.stock).toLocaleString() }} {{ item.unidad }}
                                            </span>
                                        </div>

                                        <div v-if="proj.inventario.length === 0" class="text-center py-4 text-industrial-muted text-[10px] font-sans">
                                            Sin agregados acumulados para esta obra.
                                        </div>
                                    </div>
                                </div>
                                <div class="mt-4 pt-2 border-t border-[#1b1e22] text-[9px] text-industrial-muted flex justify-between">
                                    <span>Residente:</span>
                                    <span class="text-white truncate font-medium max-w-[120px]">{{ proj.encargado || 'No asignado' }}</span>
                                </div>
                            </div>

                            <div v-if="proyectosConInventario.length === 0" class="col-span-2 text-center py-8 text-industrial-muted text-xs">
                                No se encontraron proyectos municipales activos en ejecución.
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Card 5: Recent Movements (Col-span-12) -->
                <div class="bento-card lg:col-span-12 p-6 flex flex-col justify-between bg-[#1b1e22]/90 backdrop-blur-md">
                    <div>
                        <div class="flex items-center justify-between mb-6">
                            <div>
                                <h3 class="text-base font-bold text-white tracking-tight">Registro de Movimientos Recientes</h3>
                                <p class="text-xs text-industrial-muted mt-1">Historial del flujo físico cronológico de ingresos y salidas.</p>
                            </div>
                            <span class="text-[10px] font-mono bg-[#0e1113] border border-[#2d3139] text-industrial-muted px-2 py-0.5 rounded-full">
                                Auditoría PEPS
                            </span>
                        </div>

                        <!-- Transactions Table -->
                        <div class="overflow-x-auto">
                            <table class="w-full text-left text-xs text-[#e1e6eb]">
                                <thead class="text-xs text-industrial-muted uppercase bg-[#0e1113] border-b border-[#2d3139]">
                                    <tr>
                                        <th class="px-4 py-3 font-semibold">Fecha / Hora</th>
                                        <th class="px-4 py-3 font-semibold">Operación</th>
                                        <th class="px-4 py-3 font-semibold">Insumo / Material</th>
                                        <th class="px-4 py-3 font-semibold">Obra / Proyecto Destino</th>
                                        <th class="px-4 py-3 font-semibold text-right">Cantidad</th>
                                        <th class="px-4 py-3 font-semibold text-center">Operador</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-[#2d3139] font-mono">
                                    <tr 
                                        v-for="move in recentMovements" 
                                        :key="move.id" 
                                        class="hover:bg-[#1f2329]/50 transition duration-150"
                                    >
                                        <td class="px-4 py-3.5 text-industrial-muted">{{ move.fecha }}</td>
                                        <td class="px-4 py-3.5 font-bold" :class="move.tipo_class">{{ move.tipo }}</td>
                                        <td class="px-4 py-3.5 font-bold text-white">{{ move.material }}</td>
                                        <td class="px-4 py-3.5 text-industrial-foreground font-sans">{{ move.proyecto }}</td>
                                        <td class="px-4 py-3.5 text-right font-bold text-[#f27b00]">
                                            {{ Number(move.cantidad).toLocaleString() }} {{ move.unidad }}
                                        </td>
                                        <td class="px-4 py-3.5 text-center text-industrial-muted font-sans text-[11px]">{{ move.usuario }}</td>
                                    </tr>
                                    <tr v-if="recentMovements.length === 0">
                                        <td colspan="6" class="px-6 py-8 text-center text-industrial-muted font-sans text-xs">
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
