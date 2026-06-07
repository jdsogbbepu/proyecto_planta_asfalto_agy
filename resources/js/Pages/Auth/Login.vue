<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

defineProps({
    status: {
        type: String,
    },
});

const form = useForm({
    username: '',
    password: '',
    remember: false,
});

// Quick login credential selection helper
const selectCredentials = (user, pass) => {
    form.username = user;
    form.password = pass;
};

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <div class="min-h-screen flex flex-col md:flex-row bg-[#080a0d] text-[#e8eaed] font-sans relative overflow-hidden select-none">
        <Head title="Iniciar Sesión - Asphalt-AGY" />

        <!-- TECH GRID & CRT MONITOR EFFECT OVERLAY -->
        <div class="absolute inset-0 z-0 pointer-events-none opacity-[0.03]">
            <div class="absolute inset-0 bg-[linear-gradient(to_right,#ffffff_1px,transparent_1px),linear-gradient(to_bottom,#ffffff_1px,transparent_1px)] bg-[size:3rem_3rem]"></div>
        </div>
        <div class="absolute inset-0 z-0 pointer-events-none scanlines opacity-[0.07]"></div>

        <!-- AMBIENT GLOWS -->
        <div class="absolute inset-0 z-0 pointer-events-none opacity-20">
            <div class="absolute top-1/2 left-1/4 -translate-y-1/2 w-[600px] h-[600px] bg-[#f27b00]/15 rounded-full blur-[140px]"></div>
            <div class="absolute top-1/4 right-1/4 w-[400px] h-[400px] bg-[#0a5c8f]/15 rounded-full blur-[110px]"></div>
        </div>

        <!-- LEFT PANEL: Telemetry and Brand Visuals (60% Desktop) -->
        <div class="hidden md:flex md:w-3/5 relative overflow-hidden bg-[#050608] z-10 border-r border-[rgba(255,255,255,0.06)] flex-col justify-between p-12">
            <!-- Background Image with scanner overlay -->
            <div class="absolute inset-0 w-full h-full z-0">
                <img 
                    src="/images/asphalt_plant_grayscale.png" 
                    alt="Planta de Asfalto AGY" 
                    class="w-full h-full object-cover opacity-[0.22] filter grayscale contrast-125 transition-transform duration-[12000ms] hover:scale-105"
                />
                <div class="absolute inset-0 bg-gradient-to-t from-[#050608] via-[#050608]/75 to-transparent"></div>
                <!-- Radar sweep line -->
                <div class="absolute inset-x-0 h-[3px] bg-gradient-to-r from-transparent via-[#f27b00]/60 to-transparent radar-sweep"></div>
            </div>

            <!-- Hazard strip border left -->
            <div class="absolute left-0 top-0 bottom-0 w-[4px] bg-gradient-to-b from-[#f27b00] via-[#fbbf24] to-[#f27b00]"></div>

            <!-- Top Telemetry header -->
            <div class="z-10 flex items-center justify-between font-mono text-[9px] text-[#5c6370] tracking-widest uppercase">
                <div class="flex items-center gap-2">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-ping"></span>
                    <span>SYSTEM ID: AGY-PLC-01</span>
                </div>
                <span>LAT: 16.5042 S • LON: 68.1501 W</span>
            </div>

            <!-- Central Branding content -->
            <div class="z-10 my-auto max-w-2xl relative">
                <!-- Glowing corner frame details -->
                <div class="absolute -top-6 -left-6 w-3 h-3 border-t-2 border-l-2 border-[#f27b00]/40"></div>
                <div class="absolute -bottom-6 -right-6 w-3 h-3 border-b-2 border-r-2 border-[#f27b00]/40"></div>

                <div class="flex items-center gap-3.5 mb-6">
                    <span class="h-9 w-2 bg-[#f27b00] rounded-full shadow-[0_0_15px_#f27b00]"></span>
                    <h1 class="text-3xl font-extrabold tracking-[0.25em] font-mono text-white">ASPHALT-AGY</h1>
                </div>

                <p class="text-4xl lg:text-5xl font-black tracking-tight leading-[1.08] text-white">
                    Precision.<br />
                    <span class="bg-gradient-to-r from-[#f27b00] via-[#fbbf24] to-[#f27b00] bg-clip-text text-transparent filter drop-shadow-[0_0_12px_rgba(242,123,0,0.4)]">Resilience.</span><br />
                    Performance.
                </p>

                <p class="mt-5 text-[#9aa0a9] text-sm lg:text-base leading-relaxed max-w-lg">
                    Panel integrado para la administración del stock físico municipal de insumos y agregados viales, optimizado bajo el algoritmo atómico PEPS/FIFO para cumplimiento de auditorías.
                </p>
            </div>

            <!-- Bottom Dashboard metadata -->
            <div class="z-10 grid grid-cols-3 gap-6 border-t border-[rgba(255,255,255,0.06)] pt-8">
                <div class="font-mono">
                    <div class="text-[9px] text-[#5c6370] uppercase tracking-wider">Estado de Red</div>
                    <div class="text-xs text-white font-bold mt-1 flex items-center gap-1.5">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                        <span>INTRANET / LOCAL</span>
                    </div>
                </div>
                <div class="font-mono">
                    <div class="text-[9px] text-[#5c6370] uppercase tracking-wider">Algoritmo Lógico</div>
                    <div class="text-xs text-[#f27b00] font-bold mt-1">PEPS / FIFO VALORADO</div>
                </div>
                <div class="font-mono">
                    <div class="text-[9px] text-[#5c6370] uppercase tracking-wider">Altitud</div>
                    <div class="text-xs text-[#9aa0a9] font-bold mt-1">4000 msnm</div>
                </div>
            </div>
        </div>

        <!-- RIGHT PANEL: Login Form (40% Desktop) -->
        <div class="w-full md:w-2/5 flex flex-col justify-center px-6 py-10 lg:px-16 z-10 bg-[#080a0d]/95 backdrop-blur-md relative">
            <div class="w-full max-w-md mx-auto">
                <!-- Mobile Logo Header -->
                <div class="flex items-center gap-3 mb-10 md:hidden justify-center">
                    <span class="h-6 w-2.5 bg-[#f27b00] rounded-full shadow-[0_0_12px_#f27b00]"></span>
                    <h1 class="text-2xl font-bold tracking-widest font-mono text-white">ASPHALT-AGY</h1>
                </div>

                <!-- FORM CARD (Glassmorphism & Neon Aura) -->
                <div class="glass-card rounded-2xl border border-[rgba(255,255,255,0.05)] shadow-[0_25px_60px_-15px_rgba(0,0,0,0.8)] relative p-8 hover:border-[#f27b00]/25 transition-all duration-300">
                    <!-- Top orange accent glow bar -->
                    <div class="absolute top-0 inset-x-0 h-[2.5px] bg-gradient-to-r from-transparent via-[#f27b00] to-transparent rounded-t-2xl"></div>

                    <div class="mb-8">
                        <h2 class="text-xl font-bold text-white tracking-tight flex items-center gap-2">
                            <span>Ingreso al Sistema</span>
                        </h2>
                        <p class="text-xs text-[#9aa0a9] mt-1.5 font-sans">
                            Autenticación local para operadores y administradores de planta.
                        </p>
                    </div>

                    <form @submit.prevent="submit" class="space-y-6">
                        <!-- Username input -->
                        <div>
                            <label for="username" class="block text-[10px] font-bold text-[#9aa0a9] uppercase tracking-widest mb-2">Nombre de Usuario</label>
                            <div class="relative group">
                                <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-[#5c6370] group-focus-within:text-[#f27b00] transition-colors">
                                    <!-- User SVG Icon -->
                                    <svg class="h-4.5 w-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                </span>
                                <input
                                    id="username"
                                    type="text"
                                    class="w-full bg-[#050608] text-[#e8eaed] border border-[rgba(255,255,255,0.08)] focus:border-[#f27b00] rounded-xl pl-11 pr-4 py-3.5 text-sm focus:outline-none focus:ring-4 focus:ring-[#f27b00]/10 transition-all font-mono tracking-wide placeholder-[#3d434d]"
                                    v-model="form.username"
                                    required
                                    autofocus
                                    autocomplete="username"
                                    placeholder="ej. admin"
                                />
                                <!-- Scanner glow line -->
                                <div class="absolute bottom-0 left-[12%] right-[12%] h-[1px] bg-gradient-to-r from-transparent via-[#f27b00]/30 to-transparent scale-x-0 group-focus-within:scale-x-100 transition-transform duration-300"></div>
                            </div>
                            <div v-if="form.errors.username" class="mt-2 text-xs text-[#f87171] font-mono tracking-wide">
                                {{ form.errors.username }}
                            </div>
                        </div>

                        <!-- Password input -->
                        <div>
                            <label for="password" class="block text-[10px] font-bold text-[#9aa0a9] uppercase tracking-widest mb-2">Contraseña</label>
                            <div class="relative group">
                                <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-[#5c6370] group-focus-within:text-[#f27b00] transition-colors">
                                    <!-- Lock SVG Icon -->
                                    <svg class="h-4.5 w-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                    </svg>
                                </span>
                                <input
                                    id="password"
                                    type="password"
                                    class="w-full bg-[#050608] text-[#e8eaed] border border-[rgba(255,255,255,0.08)] focus:border-[#f27b00] rounded-xl pl-11 pr-4 py-3.5 text-sm focus:outline-none focus:ring-4 focus:ring-[#f27b00]/10 transition-all font-mono tracking-wide placeholder-[#3d434d]"
                                    v-model="form.password"
                                    required
                                    autocomplete="current-password"
                                    placeholder="••••••••"
                                />
                                <!-- Scanner glow line -->
                                <div class="absolute bottom-0 left-[12%] right-[12%] h-[1px] bg-gradient-to-r from-transparent via-[#f27b00]/30 to-transparent scale-x-0 group-focus-within:scale-x-100 transition-transform duration-300"></div>
                            </div>
                            <div v-if="form.errors.password" class="mt-2 text-xs text-[#f87171] font-mono tracking-wide">
                                {{ form.errors.password }}
                            </div>
                        </div>

                        <!-- Remember Me (Custom Toggle Switch) -->
                        <div class="flex items-center justify-between py-1">
                            <label class="flex items-center cursor-pointer select-none">
                                <div class="relative">
                                    <input 
                                        id="remember" 
                                        type="checkbox" 
                                        class="sr-only" 
                                        v-model="form.remember"
                                    />
                                    <div class="w-9 h-5 bg-[#050608] border border-[rgba(255,255,255,0.08)] rounded-full transition-colors duration-200" :class="form.remember ? 'border-[#f27b00]/40' : ''"></div>
                                    <div class="absolute left-0.5 top-0.5 w-4 h-4 bg-[#5c6370] rounded-full transition-transform duration-200" :class="form.remember ? 'transform translate-x-4 bg-[#f27b00] shadow-[0_0_8px_#f27b00]' : ''"></div>
                                </div>
                                <span class="ml-3 text-xs font-semibold text-[#9aa0a9]">Recordar equipo</span>
                            </label>
                        </div>

                        <!-- Submit Button with telemetry diagnostics loader -->
                        <div>
                            <button
                                type="submit"
                                class="w-full bg-[#f27b00] hover:bg-[#ff9426] text-[#080a0d] font-bold py-3.5 px-4 rounded-xl text-xs tracking-widest uppercase transition-all duration-150 transform hover:scale-[1.01] active:scale-[0.99] disabled:opacity-50 disabled:cursor-not-allowed flex flex-col items-center justify-center gap-1 shadow-[0_4px_20px_rgba(242,123,0,0.25)] hover:shadow-[0_4px_25px_rgba(242,123,0,0.45)]"
                                :disabled="form.processing"
                            >
                                <div class="flex items-center gap-2">
                                    <svg v-if="form.processing" class="animate-spin h-3.5 w-3.5 text-[#080a0d]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    <span>{{ form.processing ? 'DIAGNÓSTICO EN CURSO...' : 'ACCEDER AL ALMACÉN' }}</span>
                                </div>
                                <div v-if="form.processing" class="w-3/4 h-[2px] bg-black/20 rounded-full mt-1.5 overflow-hidden">
                                    <div class="h-full bg-[#080a0d] animate-[loading-bar_1.5s_infinite]"></div>
                                </div>
                            </button>
                        </div>
                    </form>

                    <!-- Divider -->
                    <div class="relative my-7">
                        <div class="absolute inset-0 flex items-center" aria-hidden="true">
                            <div class="w-full border-t border-[rgba(255,255,255,0.06)]"></div>
                        </div>
                        <div class="relative flex justify-center text-[9px] uppercase font-bold tracking-[0.2em]">
                            <span class="bg-[#101317] px-3.5 text-[#5c6370]">Módulo de Simulación</span>
                        </div>
                    </div>

                    <!-- Credential Quick-Chips (PLC style toggles) -->
                    <div class="space-y-3.5">
                        <div class="text-[10px] text-[#5c6370] text-center font-sans tracking-wide">
                            Selecciona una clave de acceso rápido para autocompletar:
                        </div>
                        <div class="grid grid-cols-3 gap-2.5">
                            <button 
                                type="button"
                                @click="selectCredentials('admin', 'admin123')"
                                class="bg-[#050608] hover:bg-[#0a5c8f]/10 border border-[rgba(255,255,255,0.06)] hover:border-[#0a5c8f]/50 rounded-xl p-2.5 text-center transition duration-150 flex flex-col items-center gap-1.5 group select-btn"
                            >
                                <span class="w-2.5 h-2.5 rounded-full border border-black bg-[#1c2229] transition-all duration-200 group-hover:bg-[#0a5c8f] group-hover:shadow-[0_0_8px_#0a5c8f] led-dot"></span>
                                <span class="font-bold text-xs text-[#e8eaed] font-mono">admin</span>
                                <span class="text-[8px] text-[#5c6370] uppercase font-semibold">Administrador</span>
                            </button>
                            <button 
                                type="button"
                                @click="selectCredentials('operador', 'operador123')"
                                class="bg-[#050608] hover:bg-[#f27b00]/10 border border-[rgba(255,255,255,0.06)] hover:border-[#f27b00]/50 rounded-xl p-2.5 text-center transition duration-150 flex flex-col items-center gap-1.5 group select-btn"
                            >
                                <span class="w-2.5 h-2.5 rounded-full border border-black bg-[#1c2229] transition-all duration-200 group-hover:bg-[#f27b00] group-hover:shadow-[0_0_8px_#f27b00] led-dot"></span>
                                <span class="font-bold text-xs text-[#e8eaed] font-mono">operador</span>
                                <span class="text-[8px] text-[#5c6370] uppercase font-semibold">Operador</span>
                            </button>
                            <button 
                                type="button"
                                @click="selectCredentials('visor', 'visor123')"
                                class="bg-[#050608] hover:bg-[#3b82f6]/10 border border-[rgba(255,255,255,0.06)] hover:border-[#3b82f6]/50 rounded-xl p-2.5 text-center transition duration-150 flex flex-col items-center gap-1.5 group select-btn"
                            >
                                <span class="w-2.5 h-2.5 rounded-full border border-black bg-[#1c2229] transition-all duration-200 group-hover:bg-[#3b82f6] group-hover:shadow-[0_0_8px_#3b82f6] led-dot"></span>
                                <span class="font-bold text-xs text-[#e8eaed] font-mono">visor</span>
                                <span class="text-[8px] text-[#5c6370] uppercase font-semibold">Visor / Auditor</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
/* CRT scanlines effect */
.scanlines {
    background: linear-gradient(
        rgba(18, 16, 16, 0) 50%, 
        rgba(0, 0, 0, 0.25) 50%
    );
    background-size: 100% 4px;
    z-index: 1;
}

/* Glassmorphism panel card styling */
.glass-card {
    background: rgba(16, 19, 23, 0.85);
    backdrop-filter: blur(16px) saturate(130%);
    -webkit-backdrop-filter: blur(16px) saturate(130%);
}

/* Animated radar sweep line on left panel image */
@keyframes radarSweep {
    0% {
        top: -10%;
        opacity: 0;
    }
    10% {
        opacity: 1;
    }
    90% {
        opacity: 1;
    }
    100% {
        top: 110%;
        opacity: 0;
    }
}
.radar-sweep {
    animation: radarSweep 8s cubic-bezier(0.4, 0, 0.2, 1) infinite;
}

/* Animated loading bar inside primary button during login submission */
@keyframes loadingBar {
    0% {
        transform: translateX(-100%);
    }
    50% {
        transform: translateX(0);
    }
    100% {
        transform: translateX(100%);
    }
}
.animate-\[loading-bar_1\.5s_infinite\] {
    animation: loadingBar 1.5s ease-in-out infinite;
}

/* Industrial PLC Switch LED click reactions */
.select-btn:active .led-dot {
    transform: scale(0.85);
}
</style>
