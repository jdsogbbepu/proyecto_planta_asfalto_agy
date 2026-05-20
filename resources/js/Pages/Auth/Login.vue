<script setup>
import { Head, useForm } from '@inertiajs/vue3';

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

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <div class="min-h-screen flex flex-col md:flex-row bg-[#111417] text-[#e1e6eb] font-sans">
        <Head title="Iniciar Sesión" />

        <!-- Left Panel: Visual Brand (60%) -->
        <div class="hidden md:flex md:w-3/5 relative overflow-hidden bg-black select-none">
            <!-- Grayscale background image -->
            <img 
                src="/images/asphalt_plant_grayscale.png" 
                alt="Planta de Asfalto AGY" 
                class="absolute inset-0 w-full h-full object-cover opacity-45 filter grayscale contrast-125"
            />
            <!-- Dark Overlay Gradient -->
            <div class="absolute inset-0 bg-gradient-to-r from-black/60 to-[#111417]"></div>
            
            <!-- Minimalist text logo overlay -->
            <div class="absolute bottom-16 left-16 z-10 max-w-lg">
                <div class="flex items-center gap-3 mb-6">
                    <span class="h-8 w-2 bg-[#f27b00] rounded-full animate-pulse"></span>
                    <h1 class="text-2xl font-bold tracking-widest font-mono text-[#e1e6eb]">ASPHALT-AGY</h1>
                </div>
                <p class="text-4xl lg:text-5xl font-extrabold tracking-tight leading-none text-white">
                    Precision. <span class="text-[#f27b00]">Resilience.</span> Performance.
                </p>
                <p class="mt-4 text-[#8a949f] text-sm lg:text-base font-medium max-w-sm">
                    Sistema de control de materiales e inventarios con método de valuación PEPS (FIFO) para planta de asfalto municipal.
                </p>
            </div>
        </div>

        <!-- Right Panel: Form (40%) -->
        <div class="w-full md:w-2/5 flex flex-col justify-center px-6 py-12 lg:px-16 bg-[#111417] border-t-4 md:border-t-0 md:border-l-2 border-[#2d3139]">
            <div class="w-full max-w-md mx-auto">
                
                <!-- Mobile Logo Header -->
                <div class="flex items-center gap-3 mb-8 md:hidden">
                    <span class="h-6 w-1.5 bg-[#f27b00] rounded-full"></span>
                    <h1 class="text-xl font-bold tracking-widest font-mono text-[#e1e6eb]">ASPHALT-AGY</h1>
                </div>

                <!-- Form Card -->
                <div class="bg-[#1b1e22] rounded-xl shadow-2xl border-t-4 border-[#f27b00] border-x border-b border-[#2d3139] p-8">
                    <div class="mb-6">
                        <h2 class="text-2xl font-bold text-white tracking-tight">Iniciar Sesión</h2>
                        <p class="text-sm text-[#8a949f] mt-1">
                            Ingrese sus credenciales de planta para acceder.
                        </p>
                    </div>

                    <form @submit.prevent="submit" class="space-y-6">
                        <!-- Username -->
                        <div>
                            <label for="username" class="block text-sm font-semibold text-[#8a949f] mb-2">Usuario</label>
                            <input
                                id="username"
                                type="text"
                                class="w-full bg-[#0e1113] text-[#e1e6eb] border border-[#2d3139] rounded-lg px-4 py-3 text-base focus:outline-none focus:border-[#f27b00] focus:ring-2 focus:ring-[#f27b00]/25 transition duration-150"
                                v-model="form.username"
                                required
                                autofocus
                                autocomplete="username"
                                placeholder="Ingrese su usuario"
                            />
                            <div v-if="form.errors.username" class="mt-2 text-sm text-[#ff8c94] font-mono">
                                {{ form.errors.username }}
                            </div>
                        </div>

                        <!-- Password -->
                        <div>
                            <label for="password" class="block text-sm font-semibold text-[#8a949f] mb-2">Contraseña</label>
                            <input
                                id="password"
                                type="password"
                                class="w-full bg-[#0e1113] text-[#e1e6eb] border border-[#2d3139] rounded-lg px-4 py-3 text-base focus:outline-none focus:border-[#f27b00] focus:ring-2 focus:ring-[#f27b00]/25 transition duration-150"
                                v-model="form.password"
                                required
                                autocomplete="current-password"
                                placeholder="••••••••"
                            />
                            <div v-if="form.errors.password" class="mt-2 text-sm text-[#ff8c94] font-mono">
                                {{ form.errors.password }}
                            </div>
                        </div>

                        <!-- Remember Me -->
                        <div class="flex items-center">
                            <input 
                                id="remember" 
                                type="checkbox" 
                                class="w-4 h-4 rounded bg-[#0e1113] border-[#2d3139] text-[#f27b00] focus:ring-[#f27b00] focus:ring-offset-[#111417]" 
                                v-model="form.remember"
                            />
                            <label for="remember" class="ml-2 text-sm text-[#8a949f] cursor-pointer select-none">
                                Recordar sesión en este equipo
                            </label>
                        </div>

                        <!-- Submit Button -->
                        <div>
                            <button
                                type="submit"
                                class="w-full bg-[#f27b00] hover:bg-[#ff9426] text-[#111417] font-bold py-3 px-4 rounded-lg text-base tracking-wider uppercase transition duration-150 transform hover:scale-[1.01] active:scale-[0.99] disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2"
                                :disabled="form.processing"
                            >
                                <svg v-if="form.processing" class="animate-spin h-5 w-5 text-[#111417]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                <span>{{ form.processing ? 'Verificando...' : 'INICIAR SESIÓN' }}</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>
