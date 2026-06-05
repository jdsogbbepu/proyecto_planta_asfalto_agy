<script setup>
import { ref } from 'vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import { Link } from '@inertiajs/vue3';

const showingSidebar = ref(false);
const toggleSidebar = () => {
    showingSidebar.value = !showingSidebar.value;
};
</script>

<template>
    <div class="min-h-screen bg-[#080a0d] text-[#e8eaed] font-sans flex flex-col md:flex-row">
        <!-- BACKGROUND AMBIENT GLOWS -->
        <div class="absolute inset-0 pointer-events-none opacity-[0.03] overflow-hidden z-0">
            <div class="absolute top-1/4 left-1/4 w-[600px] h-[600px] bg-[#f27b00] rounded-full blur-[130px]"></div>
            <div class="absolute bottom-1/4 right-1/4 w-[500px] h-[500px] bg-[#3b82f6] rounded-full blur-[120px]"></div>
        </div>

        <!-- 1. LEFT SIDEBAR (Desktop) -->
        <aside class="hidden md:flex md:w-60 lg:w-64 flex-col bg-[#0d1014] border-r border-[rgba(255,255,255,0.06)] shrink-0 z-20 relative">
            <!-- Sidebar Header / Logo -->
            <div class="h-14 flex items-center px-5 border-b border-[rgba(255,255,255,0.06)] bg-[#080a0d]/50">
                <Link :href="route('dashboard')" class="flex items-center gap-2.5">
                    <span class="h-5 w-1.5 bg-[#f27b00] rounded-full shadow-[0_0_8px_rgba(242,123,0,0.5)]"></span>
                    <span class="font-bold tracking-widest font-mono text-white text-sm">ASPHALT-AGY</span>
                </Link>
            </div>

            <!-- Current User Info Section -->
            <div class="px-4 py-3 border-b border-[rgba(255,255,255,0.06)] bg-[#0d1014]/50">
                <div class="flex items-center gap-3">
                    <!-- Industrial Avatar Initials -->
                    <div class="h-9 w-9 rounded-lg bg-[#11161a] border border-[rgba(255,255,255,0.08)] flex items-center justify-center font-bold font-mono text-xs text-[#f27b00]">
                        {{ $page.props.auth.user.name.substring(0, 2).toUpperCase() }}
                    </div>
                    <div class="truncate">
                        <div class="text-xs font-bold text-white truncate">{{ $page.props.auth.user.name }}</div>
                        <div class="text-[9px] font-mono font-semibold text-[#5c6370] uppercase tracking-wider mt-0.5">
                            {{ $page.props.auth.user.role }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar Navigation Links Stack -->
            <nav class="flex-1 px-3 py-4 space-y-1 overflow-y-auto scrollbar-control">
                <Link
                    v-if="$page.props.auth.permissions.includes('ver_dashboard')"
                    :href="route('dashboard')"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-xs font-semibold transition-all duration-150"
                    :class="route().current('dashboard') ? 'bg-[rgba(242,123,0,0.1)] text-[#f27b00] border border-[rgba(242,123,0,0.15)]' : 'text-[#5c6370] hover:text-white hover:bg-[#161b21]'"
                >
                    <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z" />
                    </svg>
                    <span>Panel de Control</span>
                </Link>

                <div v-if="$page.props.auth.permissions.includes('gestionar_ingresos') || $page.props.auth.permissions.includes('gestionar_salidas') || $page.props.auth.permissions.includes('ver_reportes')" class="h-px bg-[rgba(255,255,255,0.06)] my-3"></div>
                <div v-if="$page.props.auth.permissions.includes('gestionar_ingresos') || $page.props.auth.permissions.includes('gestionar_salidas') || $page.props.auth.permissions.includes('ver_reportes')" class="text-[9px] font-semibold text-[#3d434d] uppercase tracking-wider px-3 mb-1">Operación</div>

                <Link
                    v-if="$page.props.auth.permissions.includes('gestionar_ingresos')"
                    :href="route('ingresos.index')"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-xs font-semibold transition-all duration-150"
                    :class="route().current('ingresos.*') ? 'bg-[rgba(242,123,0,0.1)] text-[#f27b00] border border-[rgba(242,123,0,0.15)]' : 'text-[#5c6370] hover:text-white hover:bg-[#161b21]'"
                >
                    <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                    </svg>
                    <span>Ingresos (Lotes)</span>
                </Link>

                <Link
                    v-if="$page.props.auth.permissions.includes('gestionar_salidas')"
                    :href="route('despachos.index')"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-xs font-semibold transition-all duration-150"
                    :class="route().current('despachos.*') ? 'bg-[rgba(242,123,0,0.1)] text-[#f27b00] border border-[rgba(242,123,0,0.15)]' : 'text-[#5c6370] hover:text-white hover:bg-[#161b21]'"
                >
                    <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 12L3.269 3.126A59.768 59.768 0 0121.485 12 59.77 59.77 0 013.27 20.876L5.999 12zm0 0h7.5" />
                    </svg>
                    <span>Despachos (PEPS)</span>
                </Link>

                <Link
                    v-if="$page.props.auth.permissions.includes('ver_reportes')"
                    :href="route('kardex.index')"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-xs font-semibold transition-all duration-150"
                    :class="route().current('kardex.*') ? 'bg-[rgba(242,123,0,0.1)] text-[#f27b00] border border-[rgba(242,123,0,0.15)]' : 'text-[#5c6370] hover:text-white hover:bg-[#161b21]'"
                >
                    <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                    </svg>
                    <span>Kardex Físico</span>
                </Link>

                <div v-if="$page.props.auth.permissions.includes('gestionar_materiales') || $page.props.auth.permissions.includes('gestionar_proveedores') || $page.props.auth.permissions.includes('gestionar_proyectos') || $page.props.auth.permissions.includes('gestionar_funcionarios')" class="h-px bg-[rgba(255,255,255,0.06)] my-3"></div>
                <div v-if="$page.props.auth.permissions.includes('gestionar_materiales') || $page.props.auth.permissions.includes('gestionar_proveedores') || $page.props.auth.permissions.includes('gestionar_proyectos') || $page.props.auth.permissions.includes('gestionar_funcionarios')" class="text-[9px] font-semibold text-[#3d434d] uppercase tracking-wider px-3 mb-1">Catálogos</div>

                <Link
                    v-if="$page.props.auth.permissions.includes('gestionar_materiales')"
                    :href="route('materials.index')"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-xs font-semibold transition-all duration-150"
                    :class="route().current('materials.*') ? 'bg-[rgba(242,123,0,0.1)] text-[#f27b00] border border-[rgba(242,123,0,0.15)]' : 'text-[#5c6370] hover:text-white hover:bg-[#161b21]'"
                >
                    <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h7.5c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z" />
                    </svg>
                    <span>Materiales</span>
                </Link>

                <Link
                    v-if="$page.props.auth.permissions.includes('gestionar_proveedores')"
                    :href="route('proveedores.index')"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-xs font-semibold transition-all duration-150"
                    :class="route().current('proveedores.*') ? 'bg-[rgba(242,123,0,0.1)] text-[#f27b00] border border-[rgba(242,123,0,0.15)]' : 'text-[#5c6370] hover:text-white hover:bg-[#161b21]'"
                >
                    <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21M3 3h12m-.75 4.5H21m-3.75 3.75h.008v.008h-.008v-.008zm0 3h.008v.008h-.008v-.008zm0 3h.008v.008h-.008v-.008z" />
                    </svg>
                    <span>Proveedores</span>
                </Link>

                <Link
                    v-if="$page.props.auth.permissions.includes('gestionar_proyectos')"
                    :href="route('proyectos.index')"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-xs font-semibold transition-all duration-150"
                    :class="route().current('proyectos.*') ? 'bg-[rgba(242,123,0,0.1)] text-[#f27b00] border border-[rgba(242,123,0,0.15)]' : 'text-[#5c6370] hover:text-white hover:bg-[#161b21]'"
                >
                    <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z" />
                    </svg>
                    <span>Proyectos</span>
                </Link>

                <Link
                    v-if="$page.props.auth.permissions.includes('gestionar_funcionarios')"
                    :href="route('funcionarios.index')"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-xs font-semibold transition-all duration-150"
                    :class="route().current('funcionarios.*') ? 'bg-[rgba(242,123,0,0.1)] text-[#f27b00] border border-[rgba(242,123,0,0.15)]' : 'text-[#5c6370] hover:text-white hover:bg-[#161b21]'"
                >
                    <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
                    </svg>
                    <span>Funcionarios</span>
                </Link>

                <div v-if="$page.props.auth.permissions.includes('gestionar_usuarios') || $page.props.auth.permissions.includes('gestionar_permisos') || $page.props.auth.permissions.includes('ver_bitacora')" class="h-px bg-[rgba(255,255,255,0.06)] my-3"></div>
                <div v-if="$page.props.auth.permissions.includes('gestionar_usuarios') || $page.props.auth.permissions.includes('gestionar_permisos') || $page.props.auth.permissions.includes('ver_bitacora')" class="text-[9px] font-semibold text-[#3d434d] uppercase tracking-wider px-3 mb-1">Seguridad</div>

                <Link
                    v-if="$page.props.auth.permissions.includes('gestionar_usuarios')"
                    :href="route('users.index')"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-xs font-semibold transition-all duration-150"
                    :class="route().current('users.*') ? 'bg-[rgba(242,123,0,0.1)] text-[#f27b00] border border-[rgba(242,123,0,0.15)]' : 'text-[#5c6370] hover:text-white hover:bg-[#161b21]'"
                >
                    <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z" />
                    </svg>
                    <span>Usuarios</span>
                </Link>

                <Link
                    v-if="$page.props.auth.permissions.includes('gestionar_permisos')"
                    :href="route('permisos.index')"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-xs font-semibold transition-all duration-150"
                    :class="route().current('permisos.*') ? 'bg-[rgba(242,123,0,0.1)] text-[#f27b00] border border-[rgba(242,123,0,0.15)]' : 'text-[#5c6370] hover:text-white hover:bg-[#161b21]'"
                >
                    <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 6h9.75M10.5 6a1.5 1.5 0 11-3 0m3 0a1.5 1.5 0 10-3 0M3.75 6H7.5m3 12h9.75m-9.75 0a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m-3.75 0H7.5m9-6h3.75m-3.75 0a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m-9.75 0h9.75" />
                    </svg>
                    <span>Permisos</span>
                </Link>

                <Link
                    v-if="$page.props.auth.permissions.includes('ver_bitacora')"
                    :href="route('bitacora.index')"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-xs font-semibold transition-all duration-150"
                    :class="route().current('bitacora.*') ? 'bg-[rgba(242,123,0,0.1)] text-[#f27b00] border border-[rgba(242,123,0,0.15)]' : 'text-[#5c6370] hover:text-white hover:bg-[#161b21]'"
                >
                    <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25" />
                    </svg>
                    <span>Bitácora</span>
                </Link>
            </nav>

            <!-- Sidebar Footer / Profile options / Logout -->
            <div class="p-4 border-t border-[rgba(255,255,255,0.06)] bg-[#080a0d]/50">
                <div class="flex items-center justify-between">
                    <Link :href="route('profile.edit')" class="text-xs font-semibold text-[#5c6370] hover:text-white transition">
                        Mi Perfil
                    </Link>
                    <Link :href="route('logout')" method="post" as="button" class="text-xs font-semibold text-[#f87171] hover:text-white transition">
                        Cerrar Sesión
                    </Link>
                </div>
            </div>
        </aside>

        <!-- 2. MOBILE HEADER & NAVIGATION -->
        <header class="md:hidden flex h-14 items-center justify-between px-5 bg-[#0d1014] border-b border-[rgba(255,255,255,0.06)] z-20">
            <Link :href="route('dashboard')" class="flex items-center gap-2">
                <span class="h-5 w-1.5 bg-[#f27b00] rounded-full shadow-[0_0_8px_rgba(242,123,0,0.5)]"></span>
                <span class="font-bold tracking-widest font-mono text-white text-sm">ASPHALT-AGY</span>
            </Link>

            <button @click="toggleSidebar" class="p-2 text-[#5c6370] hover:text-white transition">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path v-if="!showingSidebar" stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                    <path v-else stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </header>

        <!-- Mobile Drawer Overlay Menu -->
        <div v-if="showingSidebar" class="md:hidden fixed inset-0 bg-black/70 backdrop-blur-sm z-30 flex" @click.self="toggleSidebar">
            <div class="w-64 bg-[#0d1014] border-r border-[rgba(255,255,255,0.06)] h-full flex flex-col justify-between">
                <div>
                    <div class="h-14 flex items-center px-5 border-b border-[rgba(255,255,255,0.06)]">
                        <span class="font-bold tracking-widest font-mono text-white text-xs">MENÚ PRINCIPAL</span>
                    </div>

                    <nav class="p-3 space-y-1" @click="toggleSidebar">
                        <Link v-if="$page.props.auth.permissions.includes('ver_dashboard')" :href="route('dashboard')" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-xs font-semibold text-white" :class="route().current('dashboard') ? 'bg-[rgba(242,123,0,0.1)] text-[#f27b00]' : ''">
                            Panel de Control
                        </Link>
                        <Link v-if="$page.props.auth.permissions.includes('gestionar_ingresos')" :href="route('ingresos.index')" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-xs font-semibold text-white" :class="route().current('ingresos.*') ? 'bg-[rgba(242,123,0,0.1)] text-[#f27b00]' : ''">
                            Ingresos (Lotes)
                        </Link>
                        <Link v-if="$page.props.auth.permissions.includes('gestionar_salidas')" :href="route('despachos.index')" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-xs font-semibold text-white" :class="route().current('despachos.*') ? 'bg-[rgba(242,123,0,0.1)] text-[#f27b00]' : ''">
                            Despachos (PEPS)
                        </Link>
                        <Link v-if="$page.props.auth.permissions.includes('ver_reportes')" :href="route('kardex.index')" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-xs font-semibold text-white" :class="route().current('kardex.*') ? 'bg-[rgba(242,123,0,0.1)] text-[#f27b00]' : ''">
                            Kardex Físico
                        </Link>
                        <Link v-if="$page.props.auth.permissions.includes('gestionar_materiales')" :href="route('materials.index')" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-xs font-semibold text-white" :class="route().current('materials.*') ? 'bg-[rgba(242,123,0,0.1)] text-[#f27b00]' : ''">
                            Materiales
                        </Link>
                        <Link v-if="$page.props.auth.permissions.includes('gestionar_proveedores')" :href="route('proveedores.index')" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-xs font-semibold text-white" :class="route().current('proveedores.*') ? 'bg-[rgba(242,123,0,0.1)] text-[#f27b00]' : ''">
                            Proveedores
                        </Link>
                        <Link v-if="$page.props.auth.permissions.includes('gestionar_proyectos')" :href="route('proyectos.index')" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-xs font-semibold text-white" :class="route().current('proyectos.*') ? 'bg-[rgba(242,123,0,0.1)] text-[#f27b00]' : ''">
                            Proyectos
                        </Link>
                        <Link v-if="$page.props.auth.permissions.includes('gestionar_funcionarios')" :href="route('funcionarios.index')" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-xs font-semibold text-white" :class="route().current('funcionarios.*') ? 'bg-[rgba(242,123,0,0.1)] text-[#f27b00]' : ''">
                            Funcionarios
                        </Link>
                        <Link v-if="$page.props.auth.permissions.includes('gestionar_usuarios')" :href="route('users.index')" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-xs font-semibold text-white" :class="route().current('users.*') ? 'bg-[rgba(242,123,0,0.1)] text-[#f27b00]' : ''">
                            Usuarios
                        </Link>
                        <Link v-if="$page.props.auth.permissions.includes('gestionar_permisos')" :href="route('permisos.index')" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-xs font-semibold text-white" :class="route().current('permisos.*') ? 'bg-[rgba(242,123,0,0.1)] text-[#f27b00]' : ''">
                            Permisos
                        </Link>
                        <Link v-if="$page.props.auth.permissions.includes('ver_bitacora')" :href="route('bitacora.index')" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-xs font-semibold text-white" :class="route().current('bitacora.*') ? 'bg-[rgba(242,123,0,0.1)] text-[#f27b00]' : ''">
                            Bitácora
                        </Link>
                    </nav>
                </div>

                <div class="p-4 border-t border-[rgba(255,255,255,0.06)] bg-[#080a0d]/50" @click="toggleSidebar">
                    <Link :href="route('profile.edit')" class="block text-xs font-semibold text-[#5c6370] hover:text-white transition py-1">
                        Mi Perfil
                    </Link>
                    <Link :href="route('logout')" method="post" as="button" class="block text-left text-xs font-semibold text-[#f87171] hover:text-white transition py-1 w-full">
                        Cerrar Sesión
                    </Link>
                </div>
            </div>
        </div>

        <!-- 3. MAIN WORKSPACE / CONTENT AREA -->
        <div class="flex-1 flex flex-col min-w-0 z-10 relative overflow-y-auto">
            <!-- Top bar context information -->
            <header class="hidden md:flex h-12 items-center justify-between px-6 border-b border-[rgba(255,255,255,0.06)] bg-[#0d1014]/80 backdrop-blur-sm sticky top-0 z-10">
                <div class="flex items-center gap-4">
                    <span class="text-[10px] text-[#5c6370] font-mono uppercase tracking-wider">El Alto • Planta de Asfalto</span>
                </div>

                <!-- Quick User Profile drop menu or info -->
                <div class="flex items-center gap-4">
                    <div class="text-right">
                        <span class="text-xs font-semibold text-white block">{{ $page.props.auth.user.name }}</span>
                        <span class="text-[9px] font-mono text-[#5c6370] uppercase">{{ $page.props.auth.user.role }}</span>
                    </div>
                </div>
            </header>

            <!-- Slot Header (if pages inject title and custom elements) -->
            <div class="border-b border-[rgba(255,255,255,0.06)] bg-[#0d1014]/80 backdrop-blur-sm px-6 py-4 sm:px-8" v-if="$slots.header">
                <slot name="header" />
            </div>

            <!-- Main Content Slot -->
            <main class="flex-1 relative">
                <slot />
            </main>
        </div>
    </div>
</template>
