<script setup>
import { ref, onMounted, computed } from 'vue';
import { useWeather } from '@/composables/useWeather';

const {
    currentWeather,
    dailyForecast,
    loading,
    error,
    lastUpdated,
    fetchWeather,
} = useWeather();

const isHovered = ref(false);

onMounted(() => {
    fetchWeather();
    setInterval(fetchWeather, 30 * 60 * 1000);
});

const hasData = computed(() => currentWeather.value !== null);

const getTrendIcon = (index) => {
    if (index === 0) return '';
    if (dailyForecast.value[index]?.tempMax > dailyForecast.value[index - 1]?.tempMax) return '↑';
    if (dailyForecast.value[index]?.tempMax < dailyForecast.value[index - 1]?.tempMax) return '↓';
    return '→';
};

const getTrendClass = (index) => {
    if (index === 0) return '';
    const current = dailyForecast.value[index]?.tempMax;
    const previous = dailyForecast.value[index - 1]?.tempMax;
    if (current > previous) return 'text-red-400';
    if (current < previous) return 'text-blue-400';
    return 'text-gray-400';
};
</script>

<template>
    <div class="bg-[#0e1113] border border-[#2d3139] rounded-xl overflow-hidden">
        <div class="px-4 py-3 border-b border-[#2d3139]/50 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <span class="text-sm font-bold text-white">Clima en El Alto</span>
                <span class="text-[9px] text-industrial-muted bg-[#1b1e22] px-2 py-1 rounded border border-[#2d3139]">
                    4,000 msnm
                </span>
            </div>
            <div class="flex items-center gap-2">
                <span v-if="lastUpdated" class="text-[10px] text-industrial-muted font-mono">
                    {{ lastUpdated }}
                </span>
                <button
                    @click="fetchWeather"
                    class="text-[10px] text-[#f27b00] hover:text-[#ff9426] font-semibold transition"
                    :disabled="loading"
                >
                    {{ loading ? '...' : '↻' }}
                </button>
            </div>
        </div>

        <div v-if="error" class="p-6 text-center">
            <div class="text-2xl mb-2">⚠️</div>
            <p class="text-xs text-red-400">{{ error }}</p>
            <button
                @click="fetchWeather"
                class="mt-2 text-xs text-[#f27b00] hover:underline"
            >
                Reintentar
            </button>
        </div>

        <div v-else-if="hasData">
            <div class="px-5 py-4 border-b border-[#2d3139]/30">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-4">
                        <span class="text-6xl">{{ currentWeather.icon }}</span>
                        <div>
                            <div class="flex items-baseline gap-3">
                                <span class="text-5xl font-extrabold font-mono text-white tracking-tight">
                                    {{ currentWeather.temperature }}°
                                </span>
                                <span class="text-lg text-industrial-muted">C</span>
                            </div>
                            <p class="text-sm text-industrial-foreground mt-1">{{ currentWeather.label }}</p>
                        </div>
                    </div>

                    <div class="flex gap-6">
                        <div class="text-center">
                            <p class="text-[9px] text-industrial-muted uppercase tracking-wider mb-1">Sensación</p>
                            <p class="text-lg font-bold font-mono text-white">{{ currentWeather.apparentTemperature }}°</p>
                        </div>
                        <div class="text-center">
                            <p class="text-[9px] text-industrial-muted uppercase tracking-wider mb-1">Humedad</p>
                            <p class="text-lg font-bold font-mono text-white">{{ currentWeather.humidity }}%</p>
                        </div>
                        <div class="text-center">
                            <p class="text-[9px] text-industrial-muted uppercase tracking-wider mb-1">Viento</p>
                            <p class="text-lg font-bold font-mono text-white">{{ currentWeather.windSpeed }}<span class="text-xs text-industrial-muted ml-1">km/h</span></p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="px-3 py-3">
                <div class="flex items-stretch gap-2 overflow-x-auto pb-1 scrollbar-thin">
                    <div
                        v-for="(day, index) in dailyForecast"
                        :key="day.date"
                        class="flex-shrink-0 flex flex-col items-center px-4 py-3 rounded-xl transition-all duration-200 min-w-[80px]"
                        :class="index === 0
                            ? 'bg-[#f27b00]/10 border-2 border-[#f27b00]/40 shadow-lg shadow-[#f27b00]/5'
                            : 'bg-[#1b1e22]/50 border border-[#2d3139]/50 hover:border-[#2d3139] hover:bg-[#1b1e22]'"
                    >
                        <span
                            class="text-[11px] font-bold uppercase tracking-wider"
                            :class="index === 0 ? 'text-[#f27b00]' : 'text-industrial-muted'"
                        >
                            {{ day.dayName }}
                        </span>
                        <span class="text-2xl my-2">{{ day.icon }}</span>
                        <div class="flex flex-col items-center">
                            <div class="flex items-center gap-1">
                                <span class="text-sm font-bold text-white">{{ day.tempMax }}°</span>
                                <span :class="getTrendClass(index)" class="text-xs">{{ getTrendIcon(index) }}</span>
                            </div>
                            <span class="text-[10px] text-industrial-muted">{{ day.tempMin }}°</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div v-else class="p-8 text-center">
            <div class="text-3xl mb-2 animate-pulse">⛅</div>
            <p class="text-xs text-industrial-muted">Cargando datos del clima...</p>
        </div>
    </div>
</template>

<style scoped>
.scrollbar-thin::-webkit-scrollbar {
    height: 4px;
}
.scrollbar-thin::-webkit-scrollbar-track {
    background: #1b1e22;
    border-radius: 4px;
}
.scrollbar-thin::-webkit-scrollbar-thumb {
    background: #2d3139;
    border-radius: 4px;
}
.scrollbar-thin::-webkit-scrollbar-thumb:hover {
    background: #3d4149;
}
</style>