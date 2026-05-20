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
    <div class="min-h-screen flex flex-col md:flex-row bg-[#111417] text-[#e1e6eb] font-sans relative overflow-hidden">
        <Head title="Iniciar Sesión - Asphalt-AGY" />

        <!-- Aesthetic Background grid/mesh glows (Industrial ambient theme) -->
        <div class="absolute inset-0 z-0 pointer-events-none opacity-20">
            <!-- Radial glow in center -->
            <div class="absolute top-1/2 left-1/4 -translate-y-1/2 w-[500px] h-[500px] bg-[#f27b00]/10 rounded-full blur-[120px]"></div>
            <div class="absolute top-1/3 right-1/4 w-[400px] h-[400px] bg-[#0a5c8f]/10 rounded-full blur-[100px]"></div>
            <!-- Fine industrial grid backdrop -->
            <div class="absolute inset-0 bg-[linear-gradient(to_right,#1b1e22_1px,transparent_1px),linear-gradient(to_bottom,#1b1e22_1px,transparent_1px)] bg-[size:4rem_4rem]"></div>
        </div>

        <!-- Left Panel: Brand & Mission Visuals (60%) -->
        <div class="hidden md:flex md:w-3/5 relative overflow-hidden bg-black select-none z-10 border-r border-[#2d3139]">
            <!-- Grayscale background image of asphalt plant -->
            <img 
                src="/images/asphalt_plant_grayscale.png" 
                alt="Planta de Asfalto AGY" 
                class="absolute inset-0 w-full h-full object-cover opacity-35 filter grayscale contrast-125 transition-transform duration-[10000ms] hover:scale-105"
            />
            <!-- Gradient Overlay matching industrial palette -->
            <div class="absolute inset-0 bg-gradient-to-tr from-black via-black/80 to-transparent"></div>
            <!-- Industrial Hazard Strip on the very edge -->
            <div class="absolute left-0 top-0 bottom-0 w-1 bg-gradient-to-b from-[#f27b00] via-yellow-500 to-[#f27b00]"></div>
            
            <div class="absolute bottom-16 left-16 z-10 max-w-lg">
                <div class="flex items-center gap-3 mb-6">
                    <span class="h-8 w-2.5 bg-[#f27b00] rounded-full animate-pulse shadow-[0_0_15px_#f27b00]"></span>
                    <h1 class="text-2xl font-bold tracking-widest font-mono text-[#e1e6eb]">ASPHALT-AGY</h1>
                </div>
                <p class="text-4xl lg:text-5xl font-extrabold tracking-tight leading-none text-white font-sans drop-shadow-md">
                    Control Absoluto. <br />
                    Lógica <span class="text-[#f27b00] shadow-[#f27b00]/20 drop-shadow-[0_2px_8px_rgba(242,123,0,0.5)]">PEPS / FIFO</span> en Obra.
                </p>
                <p class="mt-4 text-[#8a949f] text-sm lg:text-base font-medium max-w-md">
                    Sistema centralizado para la administración del stock físico municipal de insumos y agregados viales, alineado al cumplimiento de la auditoría GAMEA.
                </p>
                
                <!-- Badge counters mockup for aesthetic depth -->
                <div class="flex gap-4 mt-8">
                    <div class="bg-[#1b1e22]/80 backdrop-blur-sm border border-[#2d3139] px-4 py-2 rounded-lg font-mono">
                        <div class="text-[10px] text-industrial-muted font-bold">TELEMETRÍA PLC</div>
                        <div class="text-xs text-white font-bold flex items-center gap-1.5 mt-0.5">
                            <span class="h-1.5 w-1.5 rounded-full bg-emerald-500 animate-ping"></span>
                            <span>CONECTADO</span>
                        </div>
                    </div>
                    <div class="bg-[#1b1e22]/80 backdrop-blur-sm border border-[#2d3139] px-4 py-2 rounded-lg font-mono">
                        <div class="text-[10px] text-industrial-muted font-bold">ALGORITMO</div>
                        <div class="text-xs text-[#f27b00] font-bold mt-0.5">FIFO ATÓMICO</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Panel: Login Credentials Form (40%) -->
        <div class="w-full md:w-2/5 flex flex-col justify-center px-6 py-12 lg:px-12 z-10">
            <div class="w-full max-w-md mx-auto">
                
                <!-- Mobile Logo Header -->
                <div class="flex items-center gap-3 mb-8 md:hidden justify-center">
                    <span class="h-6 w-2 bg-[#f27b00] rounded-full shadow-[0_0_10px_#f27b00]"></span>
                    <h1 class="text-xl font-bold tracking-widest font-mono text-[#e1e6eb]">ASPHALT-AGY</h1>
                </div>

                <!-- Form Card with Glassmorphism and Neon glow -->
                <div class="bg-[#1b1e22]/90 backdrop-blur-md rounded-2xl shadow-[0_20px_50px_rgba(0,0,0,0.5)] border border-[#2d3139] hover:border-[#f27b00]/30 transition-all duration-300 p-8">
                    <div class="mb-6">
                        <h2 class="text-2xl font-bold text-white tracking-tight">Acceso al Sistema</h2>
                        <p class="text-xs text-[#8a949f] mt-1 font-sans">
                            Autenticación local offline para operadores y administradores.
                        </p>
                    </div>

                    <form @submit.prevent="submit" class="space-y-5">
                        <!-- Username -->
                        <div>
                            <label for="username" class="block text-xs font-bold text-industrial-muted uppercase tracking-wider mb-2">Nombre de Usuario</label>
                            <div class="relative">
                                <input
                                    id="username"
                                    type="text"
                                    class="w-full bg-[#0e1113] text-[#e1e6eb] border border-[#2d3139] focus:border-[#f27b00] rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-[#f27b00]/10 transition-all font-mono"
                                    v-model="form.username"
                                    required
                                    autofocus
                                    autocomplete="username"
                                    placeholder="ej. admin"
                                />
                            </div>
                            <div v-if="form.errors.username" class="mt-2 text-xs text-[#ff8c94] font-mono">
                                {{ form.errors.username }}
                            </div>
                        </div>

                        <!-- Password -->
                        <div>
                            <label for="password" class="block text-xs font-bold text-industrial-muted uppercase tracking-wider mb-2">Contraseña</label>
                            <input
                                id="password"
                                type="password"
                                class="w-full bg-[#0e1113] text-[#e1e6eb] border border-[#2d3139] focus:border-[#f27b00] rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-[#f27b00]/10 transition-all font-mono"
                                v-model="form.password"
                                required
                                autocomplete="current-password"
                                placeholder="••••••••"
                            />
                            <div v-if="form.errors.password" class="mt-2 text-xs text-[#ff8c94] font-mono">
                                {{ form.errors.password }}
                            </div>
                        </div>

                        <!-- Remember Me -->
                        <div class="flex items-center justify-between py-1">
                            <div class="flex items-center">
                                <input 
                                    id="remember" 
                                    type="checkbox" 
                                    class="w-4 h-4 rounded bg-[#0e1113] border-[#2d3139] text-[#f27b00] focus:ring-[#f27b00] focus:ring-offset-[#111417]" 
                                    v-model="form.remember"
                                />
                                <label for="remember" class="ml-2 text-xs font-semibold text-industrial-muted cursor-pointer select-none">
                                    Recordar equipo
                                </label>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div>
                            <button
                                type="submit"
                                class="w-full bg-[#f27b00] hover:bg-[#ff9426] text-[#111417] font-bold py-3 px-4 rounded-xl text-sm tracking-wider uppercase transition-all duration-150 transform hover:scale-[1.01] active:scale-[0.99] disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2 shadow-[0_4px_20px_rgba(242,123,0,0.25)] hover:shadow-[0_4px_25px_rgba(242,123,0,0.4)]"
                                :disabled="form.processing"
                            >
                                <svg v-if="form.processing" class="animate-spin h-4 w-4 text-[#111417]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                <span>{{ form.processing ? 'Comprobando...' : 'Acceder al Almacén' }}</span>
                            </button>
                        </div>
                    </form>

                    <!-- Divider -->
                    <div class="relative my-6">
                        <div class="absolute inset-0 flex items-center" aria-hidden="true">
                            <div class="w-full border-t border-[#2d3139]"></div>
                        </div>
                        <div class="relative flex justify-center text-[10px] uppercase font-bold tracking-widest">
                            <span class="bg-[#1b1e22] px-3 text-industrial-muted">Simulación Rápida</span>
                        </div>
                    </div>

                    <!-- Interactive Credential Quick-Chips for Testing ease -->
                    <div class="space-y-2">
                        <div class="text-[11px] text-industrial-muted text-center font-sans">
                            Selecciona una cuenta de prueba para autocompletar:
                        </div>
                        <div class="grid grid-cols-3 gap-2">
                            <button 
                                type="button"
                                @click="selectCredentials('admin', 'admin123')"
                                class="bg-[#0e1113] hover:bg-[#2d3139] border border-[#2d3139] hover:border-[#f27b00] rounded-lg p-2 text-center text-xs transition duration-150 flex flex-col items-center gap-1 group"
                            >
                                <span class="font-bold text-[#e1e6eb] group-hover:text-[#f27b00] font-mono">admin</span>
                                <span class="text-[9px] text-industrial-muted">Administrador</span>
                            </button>
                            <button 
                                type="button"
                                @click="selectCredentials('operador', 'operador123')"
                                class="bg-[#0e1113] hover:bg-[#2d3139] border border-[#2d3139] hover:border-[#f27b00] rounded-lg p-2 text-center text-xs transition duration-150 flex flex-col items-center gap-1 group"
                            >
                                <span class="font-bold text-[#e1e6eb] group-hover:text-[#f27b00] font-mono">operador</span>
                                <span class="text-[9px] text-industrial-muted">Operador</span>
                            </button>
                            <button 
                                type="button"
                                @click="selectCredentials('visor', 'visor123')"
                                class="bg-[#0e1113] hover:bg-[#2d3139] border border-[#2d3139] hover:border-[#f27b00] rounded-lg p-2 text-center text-xs transition duration-150 flex flex-col items-center gap-1 group"
                            >
                                <span class="font-bold text-[#e1e6eb] group-hover:text-[#f27b00] font-mono">visor</span>
                                <span class="text-[9px] text-industrial-muted">Visor / Auditor</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
