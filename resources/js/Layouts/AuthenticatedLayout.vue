<script setup>
import { computed, ref } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';

const page = usePage();
const showingSidebar = ref(false);

const permissions = computed(() => page.props.auth.permissions || []);
const user = computed(() => page.props.auth.user || {});

const hasPermission = (permission) => permissions.value.includes(permission);

const toggleSidebar = () => {
    showingSidebar.value = !showingSidebar.value;
};

const closeSidebar = () => {
    showingSidebar.value = false;
};

const navigationGroups = computed(() => [
    {
        label: 'Operacion',
        items: [
            {
                label: 'Panel de Control',
                routeName: 'dashboard',
                active: 'dashboard',
                permission: 'ver_dashboard',
                icon: 'dashboard',
            },
            {
                label: 'Ingresos (Lotes)',
                routeName: 'ingresos.index',
                active: 'ingresos.*',
                permission: 'gestionar_ingresos',
                icon: 'ingresos',
            },
            {
                label: 'Despachos (PEPS)',
                routeName: 'despachos.index',
                active: 'despachos.*',
                permission: 'gestionar_salidas',
                icon: 'despachos',
            },
            {
                label: 'Kardex Fisico',
                routeName: 'kardex.index',
                active: 'kardex.*',
                permission: 'ver_reportes',
                icon: 'kardex',
            },
        ],
    },
    {
        label: 'Catalogos',
        items: [
            {
                label: 'Materiales',
                routeName: 'materials.index',
                active: 'materials.*',
                permission: 'gestionar_materiales',
                icon: 'materiales',
            },
            {
                label: 'Proveedores',
                routeName: 'proveedores.index',
                active: 'proveedores.*',
                permission: 'gestionar_proveedores',
                icon: 'proveedores',
            },
            {
                label: 'Proyectos',
                routeName: 'proyectos.index',
                active: 'proyectos.*',
                permission: 'gestionar_proyectos',
                icon: 'proyectos',
            },
            {
                label: 'Funcionarios',
                routeName: 'funcionarios.index',
                active: 'funcionarios.*',
                permission: 'gestionar_funcionarios',
                icon: 'funcionarios',
            },
        ],
    },
    {
        label: 'Seguridad',
        items: [
            {
                label: 'Usuarios',
                routeName: 'users.index',
                active: 'users.*',
                permission: 'gestionar_usuarios',
                icon: 'usuarios',
            },
            {
                label: 'Permisos',
                routeName: 'permisos.index',
                active: 'permisos.*',
                permission: 'gestionar_permisos',
                icon: 'permisos',
            },
            {
                label: 'Bitacora',
                routeName: 'bitacora.index',
                active: 'bitacora.*',
                permission: 'ver_bitacora',
                icon: 'bitacora',
            },
        ],
    },
].map(group => ({
    ...group,
    items: group.items.filter(item => hasPermission(item.permission)),
})).filter(group => group.items.length > 0));
</script>

<template>
    <div class="h-screen overflow-hidden bg-[#080a0d] text-[#e8eaed] font-sans md:flex">
        <div class="absolute inset-0 pointer-events-none opacity-[0.025] overflow-hidden">
            <div class="absolute top-[-120px] left-[18%] h-[420px] w-[420px] rounded-full bg-[#f27b00] blur-[150px]"></div>
            <div class="absolute bottom-[-160px] right-[12%] h-[460px] w-[460px] rounded-full bg-[#0a5c8f] blur-[150px]"></div>
        </div>

        <aside class="hidden md:flex h-screen w-64 shrink-0 flex-col border-r border-[rgba(255,255,255,0.07)] bg-[#0b0e12]/95 relative z-20">
            <div class="h-16 shrink-0 flex items-center px-5 border-b border-[rgba(255,255,255,0.06)]">
                <Link :href="route('dashboard')" class="flex min-w-0 items-center gap-3">
                    <span class="h-8 w-8 rounded-lg border border-[#f27b00]/25 bg-[#f27b00]/10 flex items-center justify-center">
                        <span class="h-4 w-1.5 rounded-full bg-[#f27b00] shadow-[0_0_12px_rgba(242,123,0,0.6)]"></span>
                    </span>
                    <span class="min-w-0">
                        <span class="block text-sm font-bold tracking-[0.18em] text-white font-mono">ASPHALT</span>
                        <span class="block text-[9px] uppercase tracking-[0.22em] text-[#5c6370]">AGY Control</span>
                    </span>
                </Link>
            </div>

            <div class="shrink-0 px-4 py-4 border-b border-[rgba(255,255,255,0.06)]">
                <div class="rounded-xl border border-[rgba(255,255,255,0.06)] bg-[#11161a] p-3">
                    <div class="flex items-center gap-3">
                        <div class="h-10 w-10 rounded-lg bg-[#080a0d] border border-[rgba(255,255,255,0.08)] flex items-center justify-center text-xs font-bold font-mono text-[#f27b00]">
                            {{ user.name?.substring(0, 2).toUpperCase() }}
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="truncate text-xs font-bold text-white">{{ user.name }}</p>
                            <p class="mt-0.5 text-[9px] font-mono uppercase tracking-wider text-[#5c6370]">{{ user.role }}</p>
                        </div>
                    </div>
                    <div class="mt-3 flex items-center justify-between rounded-lg bg-[#080a0d] px-2.5 py-2">
                        <span class="text-[9px] uppercase tracking-wider text-[#5c6370]">Sesion</span>
                        <span class="flex items-center gap-2 text-[10px] font-mono font-semibold text-[#4ade80]">
                            <span class="indicator indicator-active"></span>
                            Activa
                        </span>
                    </div>
                </div>
            </div>

            <nav class="min-h-0 flex-1 overflow-y-auto px-3 py-4 scrollbar-control">
                <div v-for="group in navigationGroups" :key="group.label" class="mb-5 last:mb-0">
                    <div class="mb-2 px-3 text-[9px] font-semibold uppercase tracking-[0.18em] text-[#3d434d]">
                        {{ group.label }}
                    </div>
                    <div class="space-y-1">
                        <Link
                            v-for="item in group.items"
                            :key="item.routeName"
                            :href="route(item.routeName)"
                            class="group flex min-h-11 items-center gap-3 rounded-lg px-3 py-2.5 text-xs font-semibold transition-all duration-150"
                            :class="route().current(item.active)
                                ? 'bg-[#f27b00]/10 text-white ring-1 ring-[#f27b00]/20 shadow-[inset_3px_0_0_#f27b00]'
                                : 'text-[#707783] hover:bg-[#161b21] hover:text-white'"
                        >
                            <span
                                class="flex h-7 w-7 shrink-0 items-center justify-center rounded-md border transition"
                                :class="route().current(item.active)
                                    ? 'border-[#f27b00]/25 bg-[#f27b00]/15 text-[#f27b00]'
                                    : 'border-[rgba(255,255,255,0.06)] bg-[#0d1014] text-[#5c6370] group-hover:text-[#f27b00]'"
                            >
                                <svg v-if="item.icon === 'dashboard'" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.6">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 8.25V6zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z" />
                                </svg>
                                <svg v-else-if="item.icon === 'ingresos'" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.6">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v12m0 0l-4-4m4 4l4-4M4.5 19.5h15" />
                                </svg>
                                <svg v-else-if="item.icon === 'despachos'" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.6">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 12h16m0 0l-5-5m5 5l-5 5" />
                                </svg>
                                <svg v-else-if="item.icon === 'kardex'" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.6">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M7.5 3.75h6l4.5 4.5v12H7.5a2.25 2.25 0 01-2.25-2.25V6A2.25 2.25 0 017.5 3.75zM13.5 3.75v4.5H18M8.25 13.5h7.5M8.25 16.5h5.25" />
                                </svg>
                                <svg v-else-if="item.icon === 'materiales'" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.6">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 7.5l7.5-4 7.5 4-7.5 4-7.5-4zM4.5 12l7.5 4 7.5-4M4.5 16.5l7.5 4 7.5-4" />
                                </svg>
                                <svg v-else-if="item.icon === 'proveedores'" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.6">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 21h16.5M5.25 21V5.25A1.5 1.5 0 016.75 3.75h7.5a1.5 1.5 0 011.5 1.5V21M8.25 8.25h3.75M8.25 12h3.75M8.25 15.75h3.75M15.75 9.75h3V21" />
                                </svg>
                                <svg v-else-if="item.icon === 'proyectos'" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.6">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 21s6-5.25 6-11.25a6 6 0 10-12 0C6 15.75 12 21 12 21z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 11.25a2.25 2.25 0 100-4.5 2.25 2.25 0 000 4.5z" />
                                </svg>
                                <svg v-else-if="item.icon === 'funcionarios'" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.6">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 7.5a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.5 20.25a7.5 7.5 0 0115 0" />
                                </svg>
                                <svg v-else-if="item.icon === 'usuarios'" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.6">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 3.75l7.5 3v5.25c0 4.5-3 7.5-7.5 9-4.5-1.5-7.5-4.5-7.5-9V6.75l7.5-3zM9.75 12l1.5 1.5 3-3" />
                                </svg>
                                <svg v-else-if="item.icon === 'permisos'" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.6">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 7.5h9M17.25 7.5h2.25M4.5 12h2.25M10.5 12h9M4.5 16.5h9M17.25 16.5h2.25" />
                                </svg>
                                <svg v-else class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.6">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5.25 4.5h13.5v15H5.25zM8.25 8.25h7.5M8.25 12h7.5M8.25 15.75h4.5" />
                                </svg>
                            </span>
                            <span class="truncate">{{ item.label }}</span>
                        </Link>
                    </div>
                </div>
            </nav>

            <div class="shrink-0 border-t border-[rgba(255,255,255,0.06)] p-4">
                <div class="grid grid-cols-2 gap-2">
                    <Link :href="route('profile.edit')" class="rounded-lg border border-[rgba(255,255,255,0.06)] bg-[#11161a] px-3 py-2 text-center text-xs font-semibold text-[#9aa0a9] transition hover:text-white">
                        Mi Perfil
                    </Link>
                    <Link :href="route('logout')" method="post" as="button" class="rounded-lg border border-[#ef4444]/15 bg-[#ef4444]/10 px-3 py-2 text-center text-xs font-semibold text-[#f87171] transition hover:bg-[#ef4444]/15 hover:text-white">
                        Salir
                    </Link>
                </div>
            </div>
        </aside>

        <header class="md:hidden relative z-20 flex h-14 shrink-0 items-center justify-between border-b border-[rgba(255,255,255,0.06)] bg-[#0d1014] px-5">
            <Link :href="route('dashboard')" class="flex items-center gap-2">
                <span class="h-5 w-1.5 bg-[#f27b00] rounded-full shadow-[0_0_8px_rgba(242,123,0,0.5)]"></span>
                <span class="font-bold tracking-widest font-mono text-white text-sm">ASPHALT-AGY</span>
            </Link>

            <button @click="toggleSidebar" class="rounded-lg p-2 text-[#9aa0a9] hover:bg-[#161b21] hover:text-white transition" aria-label="Abrir navegacion">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path v-if="!showingSidebar" stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                    <path v-else stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </header>

        <div v-if="showingSidebar" class="md:hidden fixed inset-0 z-40 flex bg-black/70 backdrop-blur-sm" @click.self="closeSidebar">
            <div class="flex h-full w-72 max-w-[82vw] flex-col border-r border-[rgba(255,255,255,0.08)] bg-[#0d1014]">
                <div class="h-14 shrink-0 flex items-center justify-between px-5 border-b border-[rgba(255,255,255,0.06)]">
                    <span class="font-bold tracking-widest font-mono text-white text-xs">PANEL DE NAVEGACION</span>
                    <button @click="closeSidebar" class="rounded-lg p-2 text-[#9aa0a9] hover:text-white" aria-label="Cerrar navegacion">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <nav class="min-h-0 flex-1 overflow-y-auto p-3 scrollbar-control">
                    <div v-for="group in navigationGroups" :key="group.label" class="mb-5 last:mb-0">
                        <div class="mb-2 px-3 text-[9px] font-semibold uppercase tracking-[0.18em] text-[#5c6370]">
                            {{ group.label }}
                        </div>
                        <div class="space-y-1">
                            <Link
                                v-for="item in group.items"
                                :key="item.routeName"
                                :href="route(item.routeName)"
                                class="block rounded-lg px-3 py-3 text-xs font-semibold text-white transition"
                                :class="route().current(item.active) ? 'bg-[#f27b00]/10 text-[#f27b00]' : 'hover:bg-[#161b21]'"
                                @click="closeSidebar"
                            >
                                {{ item.label }}
                            </Link>
                        </div>
                    </div>
                </nav>

                <div class="shrink-0 border-t border-[rgba(255,255,255,0.06)] p-4">
                    <Link :href="route('profile.edit')" class="block py-2 text-xs font-semibold text-[#9aa0a9] hover:text-white" @click="closeSidebar">
                        Mi Perfil
                    </Link>
                    <Link :href="route('logout')" method="post" as="button" class="block w-full py-2 text-left text-xs font-semibold text-[#f87171] hover:text-white" @click="closeSidebar">
                        Cerrar Sesion
                    </Link>
                </div>
            </div>
        </div>

        <div class="relative z-10 flex h-[calc(100vh-3.5rem)] min-w-0 flex-1 flex-col overflow-hidden md:h-screen">
            <header class="hidden md:flex h-12 shrink-0 items-center justify-between border-b border-[rgba(255,255,255,0.06)] bg-[#0d1014]/95 px-6 backdrop-blur-sm">
                <div class="flex items-center gap-3">
                    <span class="indicator indicator-active"></span>
                    <span class="text-[10px] text-[#5c6370] font-mono uppercase tracking-wider">El Alto - Planta de Asfalto</span>
                </div>
                <div class="flex items-center gap-4">
                    <div class="text-right">
                        <span class="text-xs font-semibold text-white block">{{ user.name }}</span>
                        <span class="text-[9px] font-mono text-[#5c6370] uppercase">{{ user.role }}</span>
                    </div>
                </div>
            </header>

            <div class="shrink-0 border-b border-[rgba(255,255,255,0.06)] bg-[#0d1014]/90 px-4 py-4 backdrop-blur-sm sm:px-6 lg:px-8" v-if="$slots.header">
                <slot name="header" />
            </div>

            <main class="min-h-0 flex-1 overflow-y-auto scrollbar-control">
                <slot />
            </main>
        </div>
    </div>
</template>
