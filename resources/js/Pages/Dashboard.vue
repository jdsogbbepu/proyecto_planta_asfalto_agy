<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { ref, onMounted, onUnmounted, computed } from 'vue';

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
    <Head title="Panel de Control" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <h2 class="text-2xl font-bold tracking-tight text-white">Panel de Control de Planta</h2>
                    <p class="text-sm text-industrial-muted mt-1 font-sans font-medium">Monitoreo de stocks y telemetría de tolvas en tiempo real.</p>
                </div>
                <div class="flex items-center gap-3 bg-[#1b1e22] border border-[#2d3139] px-4 py-2 rounded-lg">
                    <span class="h-2.5 w-2.5 rounded-full bg-emerald-500 animate-ping"></span>
                    <span class="text-xs font-mono tracking-wider font-bold text-white uppercase">{{ activeStatus }}</span>
                </div>
            </div>
        </template>

        <div class="py-6 px-4 sm:px-6 lg:px-8 mx-auto max-w-7xl font-sans">
            <!-- Bento Grid Layout -->
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 auto-rows-[minmax(180px,_auto)]">
                
                <!-- Card 1: Critical Stock (Col-span-4 / Row-span-2) -->
                <div class="bento-card lg:col-span-4 lg:row-span-2 p-6 flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-bold text-white tracking-tight">Estado de Stocks</h3>
                            <span 
                                class="text-xs font-mono px-2 py-0.5 rounded font-bold uppercase"
                                :class="hasCriticalStock ? 'bg-[#3b1d22] border border-[#8a252c] text-[#ff8c94]' : 'bg-[#0f3d2a] border border-[#1a7f4c] text-[#a6ffcc]'"
                            >
                                {{ hasCriticalStock ? 'Reordenar' : 'Estable' }}
                            </span>
                        </div>
                        
                        <!-- Stock Items -->
                        <div class="space-y-4">
                            <div v-for="mat in processedMaterials" :key="mat.id">
                                <div class="flex justify-between text-sm mb-1">
                                    <span class="text-industrial-foreground font-medium text-xs">{{ mat.nombre }}</span>
                                    <span class="text-white font-mono font-semibold text-xs">
                                        {{ Number(mat.stock_actual).toLocaleString() }} {{ mat.medida?.abreviacion }}
                                    </span>
                                </div>
                                <div class="w-full bg-[#0e1113] rounded-full h-2 border border-[#2d3139]">
                                    <div 
                                        class="h-1.5 rounded-full transition-all duration-500" 
                                        :class="mat.isCritical ? 'bg-[#f27b00]' : 'bg-emerald-500'"
                                        :style="{ width: mat.percent + '%' }"
                                    ></div>
                                </div>
                                <div class="flex justify-between text-[10px] font-mono mt-0.5">
                                    <span class="text-industrial-muted">Mín: {{ Number(mat.stock_minimo).toLocaleString() }}</span>
                                    <span 
                                        :class="mat.isCritical ? 'text-[#f27b00] font-bold' : 'text-emerald-400 font-semibold'"
                                    >
                                        {{ mat.isCritical ? 'Stock Crítico' : 'Stock Suficiente' }}
                                    </span>
                                </div>
                            </div>
                            
                            <div v-if="processedMaterials.length === 0" class="text-center py-6 text-industrial-muted text-xs">
                                No hay materiales catalogados.
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Card 2: Live Telemetry (Col-span-8 / Row-span-4) -->
                <div class="bento-card lg:col-span-8 lg:row-span-4 p-6 flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between mb-6">
                            <h3 class="text-lg font-bold text-white tracking-tight">Telemetría de Báscula y Tolvas</h3>
                            <span class="text-xs font-mono bg-industrial-card border border-[#2d3139] text-industrial-muted px-2 py-0.5 rounded">
                                Sensores en Vivo
                            </span>
                        </div>

                        <!-- Live Metrics Display -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 mb-8">
                            <!-- Weight Card -->
                            <div class="bg-[#0e1113] border border-[#2d3139] rounded-xl p-6 flex flex-col justify-between shadow-inner">
                                <span class="text-xs text-industrial-muted font-bold tracking-wider uppercase">Báscula de Descarga (Peso Neto)</span>
                                <div class="flex items-baseline gap-2 mt-2">
                                    <span class="text-4xl font-extrabold font-mono text-[#f27b00] tracking-tight">{{ scaleWeight }}</span>
                                    <span class="text-base text-industrial-muted font-mono font-bold">kg</span>
                                </div>
                                <div class="flex items-center gap-2 mt-4 text-xs text-emerald-400">
                                    <span class="h-1.5 w-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                    <span>Transmisión estable (PLC-01)</span>
                                </div>
                            </div>

                            <!-- Temp Card -->
                            <div class="bg-[#0e1113] border border-[#2d3139] rounded-xl p-6 flex flex-col justify-between shadow-inner">
                                <span class="text-xs text-industrial-muted font-bold tracking-wider uppercase">Temperatura de Mezclador</span>
                                <div class="flex items-baseline gap-2 mt-2">
                                    <span class="text-4xl font-extrabold font-mono text-[#f27b00] tracking-tight">{{ mixerTemp }}</span>
                                    <span class="text-base text-industrial-muted font-mono font-bold">°C</span>
                                </div>
                                <div class="flex items-center gap-2 mt-4 text-xs text-emerald-400">
                                    <span class="h-1.5 w-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                    <span>Rango óptimo (140°C - 165°C)</span>
                                </div>
                            </div>
                        </div>

                        <!-- Hoppers (Tolvas) graphic indicators -->
                        <div>
                            <h4 class="text-xs font-semibold text-industrial-muted uppercase tracking-wider mb-4">Estado de Tolvas de Agregados</h4>
                            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                                <div v-for="n in 4" :key="n" class="bg-[#0e1113] border border-[#2d3139] rounded-lg p-3 text-center">
                                    <span class="text-[11px] text-industrial-muted font-bold font-mono">TOLVA 0{{ n }}</span>
                                    <div class="w-8 h-20 bg-[#111417] border border-[#2d3139] mx-auto my-3 relative rounded overflow-hidden">
                                        <div 
                                            class="absolute bottom-0 left-0 right-0 bg-[#f27b00] transition-all duration-1000"
                                            :style="{ height: [75, 45, 90, 20][n-1] + '%' }"
                                        ></div>
                                    </div>
                                    <span class="text-xs font-mono font-bold text-white">{{ [75, 45, 90, 20][n-1] }}%</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Card 3: Quick Links (Col-span-4 / Row-span-2) -->
                <div class="bento-card lg:col-span-4 lg:row-span-2 p-6 flex flex-col justify-between">
                    <div>
                        <h3 class="text-lg font-bold text-white tracking-tight mb-4">Operaciones directas</h3>
                        <p class="text-xs text-industrial-muted mb-6">Operaciones rápidas sobre los inventarios y lotes PEPS.</p>
                        
                        <div class="space-y-3">
                            <Link 
                                v-if="$page.props.auth.user.role !== 'visor'"
                                :href="route('ingresos.create')" 
                                class="w-full bg-[#f27b00] hover:bg-[#ff9426] text-[#111417] font-bold py-3 px-4 rounded-lg text-sm transition duration-150 flex items-center justify-between shadow-lg"
                            >
                                <span>Registrar Ingreso</span>
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                                </svg>
                            </Link>
                            
                            <button 
                                v-if="$page.props.auth.user.role !== 'visor'"
                                class="w-full bg-[#1b1e22] hover:bg-[#252a30] text-white font-semibold py-3 px-4 rounded-lg text-sm border border-[#2d3139] transition duration-150 flex items-center justify-between"
                            >
                                <span>Despachar (PEPS)</span>
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M20 12H4" />
                                </svg>
                            </button>

                            <Link 
                                :href="route('materials.index')" 
                                class="w-full bg-[#1b1e22] hover:bg-[#252a30] text-[#e1e6eb] font-semibold py-3 px-4 rounded-lg text-sm border border-[#2d3139] transition duration-150 flex items-center justify-between"
                            >
                                <span>Ver Catálogo Materiales</span>
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 10h16M4 14h16M4 18h16" />
                                </svg>
                            </Link>
                        </div>
                    </div>
                </div>

                <!-- Card 4: Bento Inventory General Por Proyecto (Col-span-12 / Row-span-2) -->
                <div class="bento-card lg:col-span-12 lg:row-span-2 p-6 flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between mb-4">
                            <div>
                                <h3 class="text-lg font-bold text-white tracking-tight">Inventario Acumulado por Proyecto (Tolvas / Silos virtuales)</h3>
                                <p class="text-xs text-industrial-muted mt-1">Saldo físico actual del material reservado e ingresado para cada proyecto en ejecución.</p>
                            </div>
                            <span class="text-xs font-mono bg-industrial-card border border-[#2d3139] text-industrial-muted px-2 py-0.5 rounded">
                                Distribución PEPS
                            </span>
                        </div>

                        <!-- Grid of Projects Bento Cells -->
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-4">
                            <div 
                                v-for="proj in proyectosConInventario" 
                                :key="proj.id" 
                                class="bg-[#0e1113] border border-[#2d3139] rounded-xl p-4 flex flex-col justify-between shadow-inner"
                            >
                                <div>
                                    <div class="flex items-start justify-between gap-2 border-b border-[#2d3139] pb-2 mb-3">
                                        <h4 class="text-xs font-bold text-white uppercase tracking-wide truncate" :title="proj.nombre">
                                            {{ proj.nombre }}
                                        </h4>
                                        <span class="text-[9px] font-mono font-bold bg-[#0f3d2a] border border-[#1a7f4c] text-[#a6ffcc] px-1.5 py-0.2 rounded uppercase">
                                            {{ proj.estado }}
                                        </span>
                                    </div>
                                    
                                    <!-- Materials balances list inside project -->
                                    <div class="space-y-2">
                                        <div 
                                            v-for="item in proj.inventario" 
                                            :key="item.material" 
                                            class="flex justify-between items-center text-xs font-mono"
                                        >
                                            <span class="text-industrial-muted truncate max-w-[170px]" :title="item.material">
                                                {{ item.material }}
                                            </span>
                                            <span class="text-[#f27b00] font-bold">
                                                {{ Number(item.stock).toLocaleString() }} {{ item.unidad }}
                                            </span>
                                        </div>

                                        <div v-if="proj.inventario.length === 0" class="text-center py-4 text-industrial-muted text-[11px] font-sans">
                                            Sin insumos acumulados en almacén.
                                        </div>
                                    </div>
                                </div>
                                <div class="mt-4 pt-2 border-t border-[#1b1e22] text-[10px] text-industrial-muted flex justify-between">
                                    <span>Encargado:</span>
                                    <span class="text-white truncate font-medium max-w-[120px]">{{ proj.encargado || 'No asignado' }}</span>
                                </div>
                            </div>

                            <div v-if="proyectosConInventario.length === 0" class="col-span-3 text-center py-8 text-industrial-muted text-xs">
                                No se encontraron proyectos municipales activos en ejecución.
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Card 5: Recent Movements (Col-span-12 / Row-span-3) -->
                <div class="bento-card lg:col-span-12 lg:row-span-3 p-6 flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between mb-6">
                            <div>
                                <h3 class="text-lg font-bold text-white tracking-tight">Últimos Movimientos de Almacén</h3>
                                <p class="text-xs text-industrial-muted mt-1">Registros procesados recientemente bajo lógica PEPS.</p>
                            </div>
                            <span class="text-xs font-mono bg-industrial-card border border-[#2d3139] text-industrial-muted px-2 py-0.5 rounded">
                                Registro de Transacciones
                            </span>
                        </div>

                        <!-- Transactions Table -->
                        <div class="overflow-x-auto">
                            <table class="w-full text-left text-sm text-[#e1e6eb]">
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
                                <tbody class="divide-y divide-[#2d3139] font-mono text-xs">
                                    <tr 
                                        v-for="move in recentMovements" 
                                        :key="move.id" 
                                        class="hover:bg-[#1f2329]/50 transition duration-150"
                                    >
                                        <td class="px-4 py-3.5 text-industrial-muted">{{ move.fecha }}</td>
                                        <td class="px-4 py-3.5" :class="move.tipo_class">{{ move.tipo }}</td>
                                        <td class="px-4 py-3.5 font-bold text-white">{{ move.material }}</td>
                                        <td class="px-4 py-3.5 text-industrial-foreground font-sans">{{ move.proyecto }}</td>
                                        <td class="px-4 py-3.5 text-right font-bold text-[#f27b00]">
                                            {{ Number(move.cantidad).toLocaleString() }} {{ move.unidad }}
                                        </td>
                                        <td class="px-4 py-3.5 text-center text-industrial-muted font-sans text-[11px]">{{ move.usuario }}</td>
                                    </tr>
                                    <tr v-if="recentMovements.length === 0">
                                        <td colspan="6" class="px-6 py-8 text-center text-industrial-muted font-sans text-xs">
                                            No se registran movimientos recientes de almacén.
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
