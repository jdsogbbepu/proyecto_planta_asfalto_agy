<script setup>
import { ref } from 'vue';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import NavLink from '@/Components/NavLink.vue';
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue';
import { Link } from '@inertiajs/vue3';

const showingNavigationDropdown = ref(false);
</script>

<template>
    <div>
        <div class="min-h-screen bg-industrial-bg text-industrial-foreground font-sans">
            <nav
                class="border-b border-industrial-border bg-industrial-card"
            >
                <!-- Primary Navigation Menu -->
                <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                    <div class="flex h-16 justify-between">
                        <div class="flex">
                            <!-- Logo -->
                            <div class="flex shrink-0 items-center">
                                <Link :href="route('dashboard')">
                                    <div class="flex items-center gap-2">
                                        <span class="h-6 w-1.5 bg-[#f27b00] rounded-full"></span>
                                        <span class="font-bold tracking-widest font-mono text-white text-lg">ASPHALT-AGY</span>
                                    </div>
                                </Link>
                            </div>

                            <!-- Navigation Links -->
                            <div
                                class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex"
                            >
                                <NavLink
                                    :href="route('dashboard')"
                                    :active="route().current('dashboard')"
                                    class="text-industrial-muted hover:text-white"
                                >
                                    Panel de Control
                                </NavLink>
                                <NavLink
                                    :href="route('ingresos.index')"
                                    :active="route().current('ingresos.*')"
                                    class="text-industrial-muted hover:text-white"
                                >
                                    Ingresos (Lotes)
                                </NavLink>
                                <NavLink
                                    :href="route('despachos.index')"
                                    :active="route().current('despachos.*')"
                                    class="text-industrial-muted hover:text-white"
                                >
                                    Despachos (PEPS)
                                </NavLink>
                                <NavLink
                                    :href="route('kardex.index')"
                                    :active="route().current('kardex.*')"
                                    class="text-industrial-muted hover:text-white"
                                >
                                    Kardex (PEPS)
                                </NavLink>
                                <NavLink
                                    :href="route('materials.index')"
                                    :active="route().current('materials.*')"
                                    class="text-industrial-muted hover:text-white"
                                >
                                    Materiales
                                </NavLink>
                                <NavLink
                                    :href="route('proveedores.index')"
                                    :active="route().current('proveedores.*')"
                                    class="text-industrial-muted hover:text-white"
                                >
                                    Proveedores
                                </NavLink>
                                <NavLink
                                    :href="route('proyectos.index')"
                                    :active="route().current('proyectos.*')"
                                    class="text-industrial-muted hover:text-white"
                                >
                                    Proyectos
                                </NavLink>
                                <NavLink
                                    :href="route('funcionarios.index')"
                                    :active="route().current('funcionarios.*')"
                                    class="text-industrial-muted hover:text-white"
                                >
                                    Funcionarios
                                </NavLink>
                                <NavLink
                                    v-if="$page.props.auth.user.role === 'administrador'"
                                    :href="route('users.index')"
                                    :active="route().current('users.*')"
                                    class="text-industrial-muted hover:text-white"
                                >
                                    Usuarios
                                </NavLink>
                            </div>
                        </div>

                        <div class="hidden sm:ms-6 sm:flex sm:items-center">
                            <!-- Settings Dropdown -->
                            <div class="relative ms-3">
                                <Dropdown align="right" width="48">
                                    <template #trigger>
                                        <span class="inline-flex rounded-md">
                                            <button
                                                type="button"
                                                class="inline-flex items-center rounded-md border border-industrial-border bg-industrial-card px-3 py-2 text-sm font-medium leading-4 text-industrial-muted transition duration-150 ease-in-out hover:text-white focus:outline-none"
                                            >
                                                {{ $page.props.auth.user.name }} ({{ $page.props.auth.user.role }})

                                                <svg
                                                    class="-me-0.5 ms-2 h-4 w-4"
                                                    xmlns="http://www.w3.org/2000/svg"
                                                    viewBox="0 0 20 20"
                                                    fill="currentColor"
                                                >
                                                    <path
                                                        fill-rule="evenodd"
                                                        d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                                        clip-rule="evenodd"
                                                    />
                                                </svg>
                                            </button>
                                        </span>
                                    </template>

                                    <template #content>
                                        <DropdownLink
                                            :href="route('profile.edit')"
                                        >
                                            Perfil
                                        </DropdownLink>
                                        <DropdownLink
                                            :href="route('logout')"
                                            method="post"
                                            as="button"
                                        >
                                            Cerrar Sesión
                                        </DropdownLink>
                                    </template>
                                </Dropdown>
                            </div>
                        </div>

                        <!-- Hamburger -->
                        <div class="-me-2 flex items-center sm:hidden">
                            <button
                                @click="
                                    showingNavigationDropdown =
                                        !showingNavigationDropdown
                                "
                                class="inline-flex items-center justify-center rounded-md p-2 text-industrial-muted transition duration-150 ease-in-out hover:bg-industrial-card hover:text-white focus:bg-industrial-card focus:text-white focus:outline-none"
                            >
                                <svg
                                    class="h-6 w-6"
                                    stroke="currentColor"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        :class="{
                                            hidden: showingNavigationDropdown,
                                            'inline-flex':
                                                !showingNavigationDropdown,
                                        }"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M4 6h16M4 12h16M4 18h16"
                                    />
                                    <path
                                        :class="{
                                            hidden: !showingNavigationDropdown,
                                            'inline-flex':
                                                showingNavigationDropdown,
                                        }"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12"
                                    />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Responsive Navigation Menu -->
                <div
                    :class="{
                        block: showingNavigationDropdown,
                        hidden: !showingNavigationDropdown,
                    }"
                    class="sm:hidden"
                >
                    <div class="space-y-1 pb-3 pt-2">
                        <ResponsiveNavLink
                            :href="route('dashboard')"
                            :active="route().current('dashboard')"
                        >
                            Panel de Control
                        </ResponsiveNavLink>
                        <ResponsiveNavLink
                            :href="route('ingresos.index')"
                            :active="route().current('ingresos.*')"
                        >
                            Ingresos (Lotes)
                        </ResponsiveNavLink>
                        <ResponsiveNavLink
                            :href="route('despachos.index')"
                            :active="route().current('despachos.*')"
                        >
                            Despachos (PEPS)
                        </ResponsiveNavLink>
                        <ResponsiveNavLink
                            :href="route('kardex.index')"
                            :active="route().current('kardex.*')"
                        >
                            Kardex (PEPS)
                        </ResponsiveNavLink>
                        <ResponsiveNavLink
                            :href="route('materials.index')"
                            :active="route().current('materials.*')"
                        >
                            Materiales
                        </ResponsiveNavLink>
                        <ResponsiveNavLink
                            :href="route('proveedores.index')"
                            :active="route().current('proveedores.*')"
                        >
                            Proveedores
                        </ResponsiveNavLink>
                        <ResponsiveNavLink
                            :href="route('proyectos.index')"
                            :active="route().current('proyectos.*')"
                        >
                            Proyectos
                        </ResponsiveNavLink>
                        <ResponsiveNavLink
                            :href="route('funcionarios.index')"
                            :active="route().current('funcionarios.*')"
                        >
                            Funcionarios
                        </ResponsiveNavLink>
                        <ResponsiveNavLink
                            v-if="$page.props.auth.user.role === 'administrador'"
                            :href="route('users.index')"
                            :active="route().current('users.*')"
                        >
                            Gestión de Usuarios
                        </ResponsiveNavLink>
                    </div>

                    <!-- Responsive Settings Options -->
                    <div
                        class="border-t border-industrial-border pb-1 pt-4"
                    >
                        <div class="px-4">
                            <div
                                class="text-base font-medium text-white"
                            >
                                {{ $page.props.auth.user.name }}
                            </div>
                            <div class="text-sm font-medium text-industrial-muted font-mono uppercase">
                                {{ $page.props.auth.user.username }} | {{ $page.props.auth.user.role }}
                            </div>
                        </div>

                        <div class="mt-3 space-y-1">
                            <ResponsiveNavLink :href="route('profile.edit')">
                                Perfil
                            </ResponsiveNavLink>
                            <ResponsiveNavLink
                                :href="route('logout')"
                                method="post"
                                as="button"
                            >
                                Cerrar Sesión
                            </ResponsiveNavLink>
                        </div>
                    </div>
                </div>
            </nav>

            <!-- Page Heading -->
            <header
                class="bg-industrial-card border-b border-industrial-border shadow-sm"
                v-if="$slots.header"
            >
                <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
                    <slot name="header" />
                </div>
            </header>

            <!-- Page Content -->
            <main>
                <slot />
            </main>
        </div>
    </div>
</template>
