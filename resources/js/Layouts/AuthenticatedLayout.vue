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
    <div class="min-h-screen bg-[#111417] text-[#e1e6eb] font-sans flex flex-col md:flex-row">
        <!-- BACKGROUND AMBIENT GLOWS -->
        <div class="absolute inset-0 pointer-events-none opacity-5 overflow-hidden z-0">
            <div class="absolute top-1/4 left-1/4 w-[600px] h-[600px] bg-[#f27b00]/10 rounded-full blur-[130px]"></div>
            <div class="absolute bottom-1/4 right-1/4 w-[500px] h-[500px] bg-[#0a5c8f]/10 rounded-full blur-[120px]"></div>
        </div>

        <!-- 1. LEFT SIDEBAR (Desktop) -->
        <aside class="hidden md:flex md:w-64 lg:w-72 flex-col bg-[#1b1e22] border-r border-[#2d3139] shrink-0 z-20 relative select-none">
            <!-- Sidebar Header / Logo -->
            <div class="h-16 flex items-center px-6 border-b border-[#2d3139] bg-[#111417]/35">
                <Link :href="route('dashboard')" class="flex items-center gap-2.5">
                    <span class="h-6 w-2 bg-[#f27b00] rounded-full shadow-[0_0_8px_#f27b00] animate-pulse"></span>
                    <span class="font-bold tracking-widest font-mono text-white text-base">ASPHALT-AGY</span>
                </Link>
            </div>

            <!-- Current User Info Section -->
            <div class="px-5 py-4 border-b border-[#2d3139] bg-[#16191c]/50">
                <div class="flex items-center gap-3">
                    <!-- Industrial Avatar Initials -->
                    <div class="h-9 w-9 rounded-lg bg-[#0e1113] border border-[#2d3139] flex items-center justify-center font-bold font-mono text-xs text-[#f27b00]">
                        {{ $page.props.auth.user.name.substring(0, 2).toUpperCase() }}
                    </div>
                    <div class="truncate">
                        <div class="text-xs font-bold text-white truncate">{{ $page.props.auth.user.name }}</div>
                        <div class="text-[9px] font-mono font-bold text-industrial-muted uppercase tracking-wider mt-0.5">
                            {{ $page.props.auth.user.role }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar Navigation Links Stack -->
            <nav class="flex-1 px-4 py-4 space-y-1.5 overflow-y-auto">
                <Link
                    v-if="$page.props.auth.permissions.includes('ver_dashboard')"
                    :href="route('dashboard')"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-bold uppercase tracking-wider transition-all duration-150"
                    :class="route().current('dashboard') ? 'bg-[#0a5c8f]/20 border border-[#0a5c8f]/40 text-[#5ab8ff]' : 'text-industrial-muted hover:text-white hover:bg-[#252a30]/50'"
                >
                    <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2H6a2 2 0 01-2-2v-4zM14 16a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 01-2-2v-4z" />
                    </svg>
                    <span>Panel de Control</span>
                </Link>

                <div v-if="$page.props.auth.permissions.includes('gestionar_ingresos') || $page.props.auth.permissions.includes('gestionar_salidas') || $page.props.auth.permissions.includes('ver_reportes')" class="h-px bg-[#2d3139]/50 my-2"></div>
                <div v-if="$page.props.auth.permissions.includes('gestionar_ingresos') || $page.props.auth.permissions.includes('gestionar_salidas') || $page.props.auth.permissions.includes('ver_reportes')" class="text-[9px] font-bold text-industrial-muted uppercase tracking-wider px-3 mb-1">Operación</div>

                <Link
                    v-if="$page.props.auth.permissions.includes('gestionar_ingresos')"
                    :href="route('ingresos.index')"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-bold uppercase tracking-wider transition-all duration-150"
                    :class="route().current('ingresos.*') ? 'bg-[#0a5c8f]/20 border border-[#0a5c8f]/40 text-[#5ab8ff]' : 'text-industrial-muted hover:text-white hover:bg-[#252a30]/50'"
                >
                    <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                    </svg>
                    <span>Ingresos (Lotes)</span>
                </Link>

                <Link
                    v-if="$page.props.auth.permissions.includes('gestionar_salidas')"
                    :href="route('despachos.index')"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-bold uppercase tracking-wider transition-all duration-150"
                    :class="route().current('despachos.*') ? 'bg-[#0a5c8f]/20 border border-[#0a5c8f]/40 text-[#5ab8ff]' : 'text-industrial-muted hover:text-white hover:bg-[#252a30]/50'"
                >
                    <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                    </svg>
                    <span>Despachos (PEPS)</span>
                </Link>

                <Link
                    v-if="$page.props.auth.permissions.includes('ver_reportes')"
                    :href="route('kardex.index')"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-bold uppercase tracking-wider transition-all duration-150"
                    :class="route().current('kardex.*') ? 'bg-[#0a5c8f]/20 border border-[#0a5c8f]/40 text-[#5ab8ff]' : 'text-industrial-muted hover:text-white hover:bg-[#252a30]/50'"
                >
                    <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <span>Kardex Físico</span>
                </Link>

                <div v-if="$page.props.auth.permissions.includes('gestionar_materiales') || $page.props.auth.permissions.includes('gestionar_proveedores') || $page.props.auth.permissions.includes('gestionar_proyectos') || $page.props.auth.permissions.includes('gestionar_funcionarios')" class="h-px bg-[#2d3139]/50 my-2"></div>
                <div v-if="$page.props.auth.permissions.includes('gestionar_materiales') || $page.props.auth.permissions.includes('gestionar_proveedores') || $page.props.auth.permissions.includes('gestionar_proyectos') || $page.props.auth.permissions.includes('gestionar_funcionarios')" class="text-[9px] font-bold text-industrial-muted uppercase tracking-wider px-3 mb-1">Catálogos</div>

                <Link
                    v-if="$page.props.auth.permissions.includes('gestionar_materiales')"
                    :href="route('materials.index')"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-bold uppercase tracking-wider transition-all duration-150"
                    :class="route().current('materials.*') ? 'bg-[#0a5c8f]/20 border border-[#0a5c8f]/40 text-[#5ab8ff]' : 'text-industrial-muted hover:text-white hover:bg-[#252a30]/50'"
                >
                    <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                    </svg>
                    <span>Materiales</span>
                </Link>

                <Link
                    v-if="$page.props.auth.permissions.includes('gestionar_proveedores')"
                    :href="route('proveedores.index')"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-bold uppercase tracking-wider transition-all duration-150"
                    :class="route().current('proveedores.*') ? 'bg-[#0a5c8f]/20 border border-[#0a5c8f]/40 text-[#5ab8ff]' : 'text-industrial-muted hover:text-white hover:bg-[#252a30]/50'"
                >
                    <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                    <span>Proveedores</span>
                </Link>

                <Link
                    v-if="$page.props.auth.permissions.includes('gestionar_proyectos')"
                    :href="route('proyectos.index')"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-bold uppercase tracking-wider transition-all duration-150"
                    :class="route().current('proyectos.*') ? 'bg-[#0a5c8f]/20 border border-[#0a5c8f]/40 text-[#5ab8ff]' : 'text-industrial-muted hover:text-white hover:bg-[#252a30]/50'"
                >
                    <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    <span>Proyectos</span>
                </Link>

                <Link
                    v-if="$page.props.auth.permissions.includes('gestionar_funcionarios')"
                    :href="route('funcionarios.index')"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-bold uppercase tracking-wider transition-all duration-150"
                    :class="route().current('funcionarios.*') ? 'bg-[#0a5c8f]/20 border border-[#0a5c8f]/40 text-[#5ab8ff]' : 'text-industrial-muted hover:text-white hover:bg-[#252a30]/50'"
                >
                    <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                    <span>Funcionarios</span>
                </Link>

                <div v-if="$page.props.auth.permissions.includes('gestionar_usuarios') || $page.props.auth.permissions.includes('gestionar_permisos') || $page.props.auth.permissions.includes('ver_bitacora')" class="h-px bg-[#2d3139]/50 my-2"></div>
                <div v-if="$page.props.auth.permissions.includes('gestionar_usuarios') || $page.props.auth.permissions.includes('gestionar_permisos') || $page.props.auth.permissions.includes('ver_bitacora')" class="text-[9px] font-bold text-industrial-muted uppercase tracking-wider px-3 mb-1">Seguridad</div>

                <Link
                    v-if="$page.props.auth.permissions.includes('gestionar_usuarios')"
                    :href="route('users.index')"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-bold uppercase tracking-wider transition-all duration-150"
                    :class="route().current('users.*') ? 'bg-[#0a5c8f]/20 border border-[#0a5c8f]/40 text-[#5ab8ff]' : 'text-industrial-muted hover:text-white hover:bg-[#252a30]/50'"
                >
                    <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                    </svg>
                    <span>Usuarios</span>
                </Link>

                <Link
                    v-if="$page.props.auth.permissions.includes('gestionar_permisos')"
                    :href="route('permisos.index')"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-bold uppercase tracking-wider transition-all duration-150"
                    :class="route().current('permisos.*') ? 'bg-[#0a5c8f]/20 border border-[#0a5c8f]/40 text-[#5ab8ff]' : 'text-industrial-muted hover:text-white hover:bg-[#252a30]/50'"
                >
                    <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" />
                    </svg>
                    <span>Permisos</span>
                </Link>

                <Link
                    v-if="$page.props.auth.permissions.includes('ver_bitacora')"
                    :href="route('bitacora.index')"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-bold uppercase tracking-wider transition-all duration-150"
                    :class="route().current('bitacora.*') ? 'bg-[#0a5c8f]/20 border border-[#0a5c8f]/40 text-[#5ab8ff]' : 'text-industrial-muted hover:text-white hover:bg-[#252a30]/50'"
                >
                    <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                    </svg>
                    <span>Bitácora</span>
                </Link>
            </nav>

            <!-- Sidebar Footer / Profile options / Logout -->
            <div class="p-4 border-t border-[#2d3139] bg-[#111417]/35">
                <div class="flex items-center justify-between">
                    <Link :href="route('profile.edit')" class="text-xs font-semibold text-industrial-muted hover:text-white transition">
                        Mi Perfil
                    </Link>
                    <Link :href="route('logout')" method="post" as="button" class="text-xs font-bold text-[#ff8c94] hover:text-[#ffa6ad] transition">
                        Cerrar Sesión
                    </Link>
                </div>
            </div>
        </aside>

        <!-- 2. MOBILE HEADER & NAVIGATION -->
        <header class="md:hidden flex h-16 items-center justify-between px-6 bg-[#1b1e22] border-b border-[#2d3139] z-20">
            <Link :href="route('dashboard')" class="flex items-center gap-2">
                <span class="h-5 w-1.5 bg-[#f27b00] rounded-full shadow-[0_0_8px_#f27b00]"></span>
                <span class="font-bold tracking-widest font-mono text-white text-sm">ASPHALT-AGY</span>
            </Link>

            <button @click="toggleSidebar" class="p-2 text-industrial-muted hover:text-white transition">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path v-if="!showingSidebar" stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                    <path v-else stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </header>

        <!-- Mobile Drawer Overlay Menu -->
        <div v-if="showingSidebar" class="md:hidden fixed inset-0 bg-black/60 backdrop-blur-sm z-30 flex" @click.self="toggleSidebar">
            <div class="w-64 bg-[#1b1e22] border-r border-[#2d3139] h-full flex flex-col justify-between">
                <div>
                    <div class="h-16 flex items-center px-6 border-b border-[#2d3139]">
                        <span class="font-bold tracking-widest font-mono text-white text-sm">MENÚ PRINCIPAL</span>
                    </div>

                    <nav class="p-4 space-y-1.5" @click="toggleSidebar">
                        <Link v-if="$page.props.auth.permissions.includes('ver_dashboard')" :href="route('dashboard')" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-bold uppercase text-white" :class="route().current('dashboard') ? 'bg-[#0a5c8f]/20 text-[#5ab8ff]' : ''">
                            Panel de Control
                        </Link>
                        <Link v-if="$page.props.auth.permissions.includes('gestionar_ingresos')" :href="route('ingresos.index')" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-bold uppercase text-white" :class="route().current('ingresos.*') ? 'bg-[#0a5c8f]/20 text-[#5ab8ff]' : ''">
                            Ingresos (Lotes)
                        </Link>
                        <Link v-if="$page.props.auth.permissions.includes('gestionar_salidas')" :href="route('despachos.index')" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-bold uppercase text-white" :class="route().current('despachos.*') ? 'bg-[#0a5c8f]/20 text-[#5ab8ff]' : ''">
                            Despachos (PEPS)
                        </Link>
                        <Link v-if="$page.props.auth.permissions.includes('ver_reportes')" :href="route('kardex.index')" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-bold uppercase text-white" :class="route().current('kardex.*') ? 'bg-[#0a5c8f]/20 text-[#5ab8ff]' : ''">
                            Kardex Físico
                        </Link>
                        <Link v-if="$page.props.auth.permissions.includes('gestionar_materiales')" :href="route('materials.index')" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-bold uppercase text-white" :class="route().current('materials.*') ? 'bg-[#0a5c8f]/20 text-[#5ab8ff]' : ''">
                            Materiales
                        </Link>
                        <Link v-if="$page.props.auth.permissions.includes('gestionar_proveedores')" :href="route('proveedores.index')" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-bold uppercase text-white" :class="route().current('proveedores.*') ? 'bg-[#0a5c8f]/20 text-[#5ab8ff]' : ''">
                            Proveedores
                        </Link>
                        <Link v-if="$page.props.auth.permissions.includes('gestionar_proyectos')" :href="route('proyectos.index')" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-bold uppercase text-white" :class="route().current('proyectos.*') ? 'bg-[#0a5c8f]/20 text-[#5ab8ff]' : ''">
                            Proyectos
                        </Link>
                        <Link v-if="$page.props.auth.permissions.includes('gestionar_funcionarios')" :href="route('funcionarios.index')" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-bold uppercase text-white" :class="route().current('funcionarios.*') ? 'bg-[#0a5c8f]/20 text-[#5ab8ff]' : ''">
                            Funcionarios
                        </Link>
                        <Link v-if="$page.props.auth.permissions.includes('gestionar_usuarios')" :href="route('users.index')" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-bold uppercase text-white" :class="route().current('users.*') ? 'bg-[#0a5c8f]/20 text-[#5ab8ff]' : ''">
                            Usuarios
                        </Link>
                        <Link v-if="$page.props.auth.permissions.includes('gestionar_permisos')" :href="route('permisos.index')" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-bold uppercase text-white" :class="route().current('permisos.*') ? 'bg-[#0a5c8f]/20 text-[#5ab8ff]' : ''">
                            Permisos
                        </Link>
                        <Link v-if="$page.props.auth.permissions.includes('ver_bitacora')" :href="route('bitacora.index')" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-bold uppercase text-white" :class="route().current('bitacora.*') ? 'bg-[#0a5c8f]/20 text-[#5ab8ff]' : ''">
                            Bitácora
                        </Link>
                    </nav>
                </div>

                <div class="p-4 border-t border-[#2d3139] bg-[#111417]/35" @click="toggleSidebar">
                    <Link :href="route('profile.edit')" class="block text-xs font-semibold text-industrial-muted hover:text-white transition py-1">
                        Mi Perfil
                    </Link>
                    <Link :href="route('logout')" method="post" as="button" class="block text-left text-xs font-bold text-[#ff8c94] hover:text-[#ffa6ad] transition py-1 w-full">
                        Cerrar Sesión
                    </Link>
                </div>
            </div>
        </div>

        <!-- 3. MAIN WORKSPACE / CONTENT AREA -->
        <div class="flex-1 flex flex-col min-w-0 z-10 relative overflow-y-auto">
            <!-- Top bar context information -->
            <header class="hidden md:flex h-16 items-center justify-between px-8 border-b border-[#2d3139] bg-[#1b1e22]/50 backdrop-blur-sm sticky top-0 z-10">
                <div class="flex items-center gap-4">
                    <span class="text-xs text-industrial-muted font-mono">UBICACIÓN: EL ALTO - PLANTA DE ASFALTO</span>
                </div>
                
                <!-- Quick User Profile drop menu or info -->
                <div class="flex items-center gap-4">
                    <div class="text-right">
                        <span class="text-xs font-bold text-white block">{{ $page.props.auth.user.name }}</span>
                        <span class="text-[9px] font-mono text-industrial-muted uppercase">{{ $page.props.auth.user.role }}</span>
                    </div>
                </div>
            </header>

            <!-- Slot Header (if pages inject title and custom elements) -->
            <div class="border-b border-[#2d3139]/80 bg-[#16191c]/80 backdrop-blur-sm px-6 py-5 sm:px-8" v-if="$slots.header">
                <slot name="header" />
            </div>

            <!-- Main Content Slot -->
            <main class="flex-1 relative">
                <slot />
            </main>
        </div>
    </div>
</template>
