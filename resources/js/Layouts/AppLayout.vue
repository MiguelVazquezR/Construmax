<script setup>
import { ref, onMounted } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import ApplicationMark from '@/Components/ApplicationMark.vue';
import Banner from '@/Components/Banner.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import SideNav from '@/Components/MyComponents/SideNav.vue';
import NotificationsCenter from '@/Components/MyComponents/NotificationsCenter.vue';
import SideNavMobile from '@/Components/MyComponents/SideNavMobile.vue';
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue';

defineProps({
    title: String,
});

const showingNavigationDropdown = ref(false);

const logout = () => {
    router.post(route('logout'));
};

</script>

<template>
    <div>

        <Head :title="title" />
        <Banner />

        <div class="overflow-hidden h-screen bg-[#F2F2F2] md:grid md:grid-cols-12">
            <aside>
                <SideNav />
            </aside>

            <main class="md:col-span-11">
                <nav class="bg-white border-b border-[#f2f2f2]">
                    <!-- Primary Navigation Menu -->
                    <div class="mx-auto pl-2 pr-16">
                        <div class="flex justify-between h-16">
                            <div class="flex">
                                <!-- Logo -->
                                <div class="shrink-0 flex items-center">
                                    <Link :href="route('dashboard')">
                                    <ApplicationMark class="block w-32" />
                                    </Link>
                                </div>
                            </div>

                            <div class="hidden sm:flex sm:items-center sm:ml-6 items-center">
                                <!-- calendar -->
                                <div class="relative mt-2">
                                    <el-tooltip content="Calendario">
                                        <Link :href="route('calendars.index')">
                                        <button :class="route().current('calendars.*') ? 'text-primary' : 'text-[#97989A]'">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="17" height="17"
                                                fill="currentColor" class="bi bi-calendar4-event" viewBox="0 0 16 16">
                                                <path
                                                    d="M3.5 0a.5.5 0 0 1 .5.5V1h8V.5a.5.5 0 0 1 1 0V1h1a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V3a2 2 0 0 1 2-2h1V.5a.5.5 0 0 1 .5-.5zM2 2a1 1 0 0 0-1 1v1h14V3a1 1 0 0 0-1-1H2zm13 3H1v9a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V5z" />
                                                <path
                                                    d="M11 7.5a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5v-1z" />
                                            </svg>
                                        </button>
                                        </Link>
                                    </el-tooltip>
                                    <div v-if="$page.props.auth.user?.notifications?.some(notification => {
                                        return notification.data.module === 'calendar';
                                    })"
                                        class="bg-primary w-[10px] h-[10px] border border-white rounded-full absolute -top-1 -right-2">
                                    </div>
                                </div>

                                <!-- notifications -->
                                <NotificationsCenter />

                                <!-- Settings Dropdown -->
                                <div class="ml-3 relative">
                                    <Dropdown align="right" width="48">
                                        <template #trigger>
                                            <button v-if="$page.props.jetstream.managesProfilePhotos"
                                                class="flex text-sm border-2 border-transparent rounded-full focus:outline-none focus:border-gray-300 transition">
                                                <img class="h-8 w-8 rounded-full object-cover"
                                                    :src="$page.props.auth.user.profile_photo_url"
                                                    :alt="$page.props.auth.user.name">
                                            </button>

                                            <span v-else class="inline-flex rounded-md">
                                                <button type="button"
                                                    class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-[#97989A] bg-white focus:outline-none focus:bg-gray-50 active:bg-gray-50 transition ease-in-out duration-150">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18"
                                                        fill="currentColor" class="bi bi-person-circle" viewBox="0 0 16 16">
                                                        <path d="M11 6a3 3 0 1 1-6 0 3 3 0 0 1 6 0z" />
                                                        <path fill-rule="evenodd"
                                                            d="M0 8a8 8 0 1 1 16 0A8 8 0 0 1 0 8zm8-7a7 7 0 0 0-5.468 11.37C3.242 11.226 4.805 10 8 10s4.757 1.225 5.468 2.37A7 7 0 0 0 8 1z" />
                                                    </svg>
                                                </button>
                                            </span>
                                        </template>

                                        <template #content>
                                            <!-- Account Management -->
                                            <div class="block px-4 py-2 text-xs text-gray-400">
                                                Cuenta
                                            </div>

                                            <DropdownLink :href="route('profile.show')">
                                                Perfil
                                            </DropdownLink>

                                            <DropdownLink v-if="$page.props.jetstream.hasApiFeatures"
                                                :href="route('api-tokens.index')">
                                                API Tokens
                                            </DropdownLink>

                                            <div class="border-t border-gray-200" />

                                            <!-- Authentication -->
                                            <form @submit.prevent="logout">
                                                <DropdownLink as="button">
                                                    Cerrar sesión
                                                </DropdownLink>
                                            </form>
                                        </template>
                                    </Dropdown>
                                </div>
                            </div>

                            <!-- Hamburger -->
                            <div class="-mr-12 flex items-center sm:hidden">
                                <!-- calendar -->
                                <div class="relative mt-2">
                                    <el-tooltip content="Calendario">
                                        <Link :href="route('calendars.index')">
                                        <button :class="route().current('calendars.*') ? 'text-primary' : 'text-[#97989A]'">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="17" height="17"
                                                fill="currentColor" class="bi bi-calendar4-event" viewBox="0 0 16 16">
                                                <path
                                                    d="M3.5 0a.5.5 0 0 1 .5.5V1h8V.5a.5.5 0 0 1 1 0V1h1a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V3a2 2 0 0 1 2-2h1V.5a.5.5 0 0 1 .5-.5zM2 2a1 1 0 0 0-1 1v1h14V3a1 1 0 0 0-1-1H2zm13 3H1v9a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V5z" />
                                                <path
                                                    d="M11 7.5a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5v-1z" />
                                            </svg>
                                        </button>
                                        </Link>
                                    </el-tooltip>
                                    <div v-if="$page.props.auth.user?.notifications?.some(notification => {
                                        return notification.data.module === 'calendar';
                                    })"
                                        class="bg-primary w-[10px] h-[10px] border border-white rounded-full absolute -top-1 -right-2">
                                    </div>
                                </div>

                                <!-- notifications -->
                                <NotificationsCenter />
                                
                                <button
                                    class="inline-flex items-center justify-center ml-1 p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out"
                                    @click="showingNavigationDropdown = !showingNavigationDropdown">
                                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                                        <path
                                            :class="{ 'hidden': showingNavigationDropdown, 'inline-flex': !showingNavigationDropdown }"
                                            stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M4 6h16M4 12h16M4 18h16" />
                                        <path
                                            :class="{ 'hidden': !showingNavigationDropdown, 'inline-flex': showingNavigationDropdown }"
                                            stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Responsive Navigation Menu -->
                    <div :class="{ 'block': showingNavigationDropdown, 'hidden': !showingNavigationDropdown }"
                        class="z-40 rounded-tl-[6px] rounded-bl-[6px] sm:hidden bg-gray1 w-4/6 absolute right-0 top-14 min-h-[50%] max-h-[90%] overflow-y-scroll overflow-x-hidden shadow-lg border pt-4">
                        <!-- Responsive Settings Options -->
                        <div class="pb-4 mb-4 border-b border-gray-200">
                            <div class="flex flex-col items-center justify-center mx-4">
                                <img class="h-14 w-14 rounded-full object-cover mx-auto"
                                    :src="$page.props.auth.user.profile_photo_url" :alt="$page.props.auth.user.name">
                                <div class="font-semibold text-center text-base text-white w-full truncate">
                                    {{ $page.props.auth.user.name }}
                                </div>
                                <div class="font-medium text-center text-sm text-gray-50 w-full truncate">
                                    {{ $page.props.auth.user.email }}
                                </div>
                            </div>
                        </div>
                        <div class="flex flex-col justify-between">
                            <SideNavMobile />
                            <!-- Authentication -->
                            <form method="POST" @submit.prevent="logout" class="text-red-500 absolute bottom-3 right-3">
                                <button>
                                    <i class="fa-solid fa-arrow-right-from-bracket mr-[7px]"></i> Cerrar sesión
                                </button>
                            </form>
                        </div>
                    </div>
                </nav>

                <!-- Page Heading -->
                <header v-if="$slots.header" class="bg-white shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        <slot name="header" />
                    </div>
                </header>

                <div class="overflow-y-auto h-[calc(100vh-4.1rem)] bg-white">
                    <slot />
                </div>
            </main>
        </div>
    </div>
</template>
