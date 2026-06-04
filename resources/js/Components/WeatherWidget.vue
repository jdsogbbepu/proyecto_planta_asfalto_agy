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

const isVisible = ref(true);

onMounted(() => {
    fetchWeather();
    setInterval(fetchWeather, 30 * 60 * 1000);
});

const hasData = computed(() => currentWeather.value !== null);
</script>

<template>
    <div class="bg-[#0e1113] border border-[#2d3139] rounded-xl overflow-hidden">
        <div class="px-4 py-3 border-b border-[#2d3139]/50 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <span class="text-sm font-bold text-white">🌤️ Clima - El Alto</span>
                <span v-if="lastUpdated" class="text-[10px] text-industrial-muted font-mono">
                    Actualizado {{ lastUpdated }}
                </span>
            </div>
            <button
                @click="fetchWeather"
                class="text-[10px] text-[#f27b00] hover:text-[#ff9426] font-semibold transition"
                :disabled="loading"
            >
                {{ loading ? 'Cargando...' : '↻' }}
            </button>
        </div>

        <div v-if="error" class="p-4 text-center">
            <p class="text-xs text-red-400">{{ error }}</p>
            <button
                @click="fetchWeather"
                class="mt-2 text-xs text-[#f27b00] hover:underline"
            >
                Reintentar
            </button>
        </div>

        <div v-else-if="hasData">
            <div class="px-4 py-4 flex items-center justify-between border-b border-[#2d3139]/30">
                <div class="flex items-center gap-4">
                    <span class="text-5xl">{{ currentWeather.icon }}</span>
                    <div>
                        <div class="flex items-baseline gap-2">
                            <span class="text-4xl font-extrabold font-mono text-white">
                                {{ currentWeather.temperature }}°C
                            </span>
                            <span class="text-sm text-industrial-muted">
                                {{ currentWeather.label }}
                            </span>
                        </div>
                        <div class="text-xs text-industrial-muted mt-1">
                            Sensación: {{ currentWeather.apparentTemperature }}°C
                        </div>
                    </div>
                </div>
                <div class="flex gap-4 text-center">
                    <div class="px-3 py-2 bg-[#1b1e22]/50 rounded-lg">
                        <div class="text-[10px] text-industrial-muted uppercase tracking-wider">💧 Humedad</div>
                        <div class="text-sm font-bold text-white">{{ currentWeather.humidity }}%</div>
                    </div>
                    <div class="px-3 py-2 bg-[#1b1e22]/50 rounded-lg">
                        <div class="text-[10px] text-industrial-muted uppercase tracking-wider">💨 Viento</div>
                        <div class="text-sm font-bold text-white">{{ currentWeather.windSpeed }} km/h</div>
                    </div>
                </div>
            </div>

            <div class="px-4 py-3">
                <div class="flex items-center gap-1 overflow-x-auto pb-1">
                    <div
                        v-for="(day, index) in dailyForecast"
                        :key="day.date"
                        class="flex-shrink-0 flex flex-col items-center px-3 py-2 rounded-lg transition-all"
                        :class="index === 0
                            ? 'bg-[#f27b00]/10 border border-[#f27b00]/30'
                            : 'bg-[#1b1e22]/30 border border-[#2d3139]/20 hover:border-[#2d3139]/50'"
                    >
                        <span
                            class="text-[10px] font-bold uppercase tracking-wider"
                            :class="index === 0 ? 'text-[#f27b00]' : 'text-industrial-muted'"
                        >
                            {{ day.dayName }}
                        </span>
                        <span class="text-xl my-1">{{ day.icon }}</span>
                        <div class="flex flex-col items-center text-[10px] font-mono">
                            <span class="text-white font-bold">{{ day.tempMax }}°</span>
                            <span class="text-industrial-muted">{{ day.tempMin }}°</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div v-else class="p-8 text-center">
            <div class="text-3xl mb-2">⛅</div>
            <p class="text-xs text-industrial-muted">Cargando clima...</p>
        </div>
    </div>
</template>