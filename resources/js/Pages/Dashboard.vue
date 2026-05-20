<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { ref, onMounted, onUnmounted } from 'vue';

// Active telemetry states
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
</script>

<template>
    <Head title="Panel de Control" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <h2 class="text-2xl font-bold tracking-tight text-white">Panel de Control de Planta</h2>
                    <p class="text-sm text-industrial-muted mt-1 font-sans">Monitoreo de stocks y telemetría de tolvas en tiempo real.</p>
                </div>
                <div class="flex items-center gap-3 bg-[#1b1e22] border border-[#2d3139] px-4 py-2 rounded-lg">
                    <span class="h-2.5 w-2.5 rounded-full bg-emerald-500 animate-ping"></span>
                    <span class="text-xs font-mono tracking-wider font-bold text-white uppercase">{{ activeStatus }}</span>
                </div>
            </div>
        </template>

        <div class="py-6 px-4 sm:px-6 lg:px-8 mx-auto max-w-7xl font-sans">
            <!-- Bento Grid Layout -->
            <div class="grid grid-cols-1 md:grid-cols-12 gap-6 auto-rows-[minmax(180px,_auto)]">
                
                <!-- Component A: Métrica Crítica Stock (Col-span-4 / Row-span-2) -->
                <div class="bento-card md:col-span-4 md:row-span-2 p-6 flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-bold text-white tracking-tight">Estado de Stocks</h3>
                            <span class="text-xs font-mono bg-warning-bg border border-warning-border text-warning-text px-2 py-0.5 rounded">
                                Reordenar
                            </span>
                        </div>
                        
                        <!-- Stock Items -->
                        <div class="space-y-4">
                            <!-- Insumo 1 -->
                            <div>
                                <div class="flex justify-between text-sm mb-1">
                                    <span class="text-industrial-foreground font-medium">Cemento Asfáltico (PEN 60/70)</span>
                                    <span class="text-white font-mono font-semibold">12,450 kg</span>
                                </div>
                                <div class="w-full bg-[#0e1113] rounded-full h-2">
                                    <div class="bg-[#f27b00] h-2 rounded-full" style="width: 35%"></div>
                                </div>
                                <div class="flex justify-between text-[11px] text-industrial-muted mt-0.5">
                                    <span>Mín: 15,000 kg</span>
                                    <span class="text-[#ff8c94] font-semibold">Stock Crítico</span>
                                </div>
                            </div>
                            
                            <!-- Insumo 2 -->
                            <div>
                                <div class="flex justify-between text-sm mb-1">
                                    <span class="text-industrial-foreground font-medium">Emulsión Asfáltica (CSS-1h)</span>
                                    <span class="text-white font-mono font-semibold">45,800 L</span>
                                </div>
                                <div class="w-full bg-[#0e1113] rounded-full h-2">
                                    <div class="bg-emerald-500 h-2 rounded-full" style="width: 78%"></div>
                                </div>
                                <div class="flex justify-between text-[11px] text-industrial-muted mt-0.5">
                                    <span>Mín: 10,000 L</span>
                                    <span class="text-emerald-400 font-semibold">Stock Suficiente</span>
                                </div>
                            </div>

                            <!-- Insumo 3 -->
                            <div>
                                <div class="flex justify-between text-sm mb-1">
                                    <span class="text-industrial-foreground font-medium">Grava / Agregados (3/4")</span>
                                    <span class="text-white font-mono font-semibold">89,200 kg</span>
                                </div>
                                <div class="w-full bg-[#0e1113] rounded-full h-2">
                                    <div class="bg-emerald-500 h-2 rounded-full" style="width: 68%"></div>
                                </div>
                                <div class="flex justify-between text-[11px] text-industrial-muted mt-0.5">
                                    <span>Mín: 25,000 kg</span>
                                    <span class="text-emerald-400 font-semibold">Stock Suficiente</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Component B: Dosificación de Tolvas / Telemetría (Col-span-8 / Row-span-4) -->
                <div class="bento-card md:col-span-8 md:row-span-4 p-6 flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between mb-6">
                            <h3 class="text-lg font-bold text-white tracking-tight">Telemetría de Báscula y Tolvas</h3>
                            <span class="text-xs font-mono bg-industrial-blue/30 border border-industrial-blue text-industrial-foreground px-2 py-0.5 rounded">
                                Sensores en Vivo
                            </span>
                        </div>

                        <!-- Live Metrics Display -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 mb-8">
                            <!-- Weight Card -->
                            <div class="bg-[#0e1113] border border-[#2d3139] rounded-xl p-6 flex flex-col justify-between">
                                <span class="text-xs text-industrial-muted font-semibold tracking-wider uppercase">Báscula de Descarga (Peso Neto)</span>
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
                            <div class="bg-[#0e1113] border border-[#2d3139] rounded-xl p-6 flex flex-col justify-between">
                                <span class="text-xs text-industrial-muted font-semibold tracking-wider uppercase">Temperatura de Mezclador</span>
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

                <!-- Component C: Accesos Rápidos (Col-span-4 / Row-span-2) -->
                <div class="bento-card md:col-span-4 md:row-span-2 p-6 flex flex-col justify-between">
                    <div>
                        <h3 class="text-lg font-bold text-white tracking-tight mb-4">Accesos Rápidos</h3>
                        <p class="text-xs text-industrial-muted mb-6">Operaciones directas sobre inventarios y kardex PEPS.</p>
                        
                        <div class="space-y-3">
                            <button class="w-full bg-[#0a5c8f] hover:bg-[#0d75b5] text-white font-semibold py-3 px-4 rounded-lg text-sm transition duration-150 flex items-center justify-between">
                                <span>Registrar Ingreso</span>
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                                </svg>
                            </button>
                            
                            <button class="w-full bg-[#1b1e22] hover:bg-[#252a30] text-white font-semibold py-3 px-4 rounded-lg text-sm border border-[#2d3139] transition duration-150 flex items-center justify-between">
                                <span>Registrar Salida (PEPS)</span>
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M20 12H4" />
                                </svg>
                            </button>

                            <button class="w-full bg-[#1b1e22] hover:bg-[#252a30] text-industrial-muted hover:text-white font-semibold py-3 px-4 rounded-lg text-sm border border-[#2d3139] transition duration-150 flex items-center justify-between">
                                <span>Kardex PEPS</span>
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Component D: Últimas Transacciones (Col-span-12 / Row-span-3) -->
                <div class="bento-card md:col-span-12 md:row-span-3 p-6 flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between mb-6">
                            <div>
                                <h3 class="text-lg font-bold text-white tracking-tight">Últimos Movimientos de Almacén</h3>
                                <p class="text-xs text-industrial-muted mt-1">Registros procesados recientemente bajo lógica PEPS.</p>
                            </div>
                            <span class="text-xs font-mono bg-industrial-card border border-[#2d3139] text-industrial-muted px-2 py-0.5 rounded">
                                Registro Histórico
                            </span>
                        </div>

                        <!-- Transactions Table -->
                        <div class="overflow-x-auto">
                            <table class="w-full text-left text-sm text-[#e1e6eb]">
                                <thead class="text-xs text-industrial-muted uppercase bg-[#0e1113] border-b border-[#2d3139]">
                                    <tr>
                                        <th class="px-4 py-3 font-semibold">Fecha / Hora</th>
                                        <th class="px-4 py-3 font-semibold">Operación</th>
                                        <th class="px-4 py-3 font-semibold">Insumo</th>
                                        <th class="px-4 py-3 font-semibold">Obra / Proyecto Destino</th>
                                        <th class="px-4 py-3 font-semibold text-right">Cantidad</th>
                                        <th class="px-4 py-3 font-semibold text-center">Estado</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-[#2d3139] font-mono text-xs">
                                    <tr class="hover:bg-[#1f2329]/50">
                                        <td class="px-4 py-3.5 text-industrial-muted">2026-05-20 00:15</td>
                                        <td class="px-4 py-3.5 text-[#ff8c94] font-bold">SALIDA (PEPS)</td>
                                        <td class="px-4 py-3.5 font-bold text-white">Cemento Asfáltico</td>
                                        <td class="px-4 py-3.5 text-industrial-foreground">Viaducto San José - Lote 1</td>
                                        <td class="px-4 py-3.5 text-right font-bold text-white">2,500 kg</td>
                                        <td class="px-4 py-3.5 text-center"><span class="bg-[#0f3d2a] border border-[#1a7f4c] text-[#a6ffcc] px-2 py-0.5 rounded text-[10px]">PROCESADO</span></td>
                                    </tr>
                                    <tr class="hover:bg-[#1f2329]/50">
                                        <td class="px-4 py-3.5 text-industrial-muted">2026-05-20 00:08</td>
                                        <td class="px-4 py-3.5 text-[#ff8c94] font-bold">SALIDA (PEPS)</td>
                                        <td class="px-4 py-3.5 font-bold text-white">Cemento Asfáltico</td>
                                        <td class="px-4 py-3.5 text-industrial-foreground">Av. Juan Pablo II - Pavimentado</td>
                                        <td class="px-4 py-3.5 text-right font-bold text-white">3,800 kg</td>
                                        <td class="px-4 py-3.5 text-center"><span class="bg-[#0f3d2a] border border-[#1a7f4c] text-[#a6ffcc] px-2 py-0.5 rounded text-[10px]">PROCESADO</span></td>
                                    </tr>
                                    <tr class="hover:bg-[#1f2329]/50">
                                        <td class="px-4 py-3.5 text-industrial-muted">2026-05-19 23:40</td>
                                        <td class="px-4 py-3.5 text-emerald-400 font-bold">INGRESO</td>
                                        <td class="px-4 py-3.5 font-bold text-white">Grava (3/4")</td>
                                        <td class="px-4 py-3.5 text-industrial-foreground">Stock de Planta - Tolva 03</td>
                                        <td class="px-4 py-3.5 text-right font-bold text-white">25,000 kg</td>
                                        <td class="px-4 py-3.5 text-center"><span class="bg-[#0f3d2a] border border-[#1a7f4c] text-[#a6ffcc] px-2 py-0.5 rounded text-[10px]">PROCESADO</span></td>
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
