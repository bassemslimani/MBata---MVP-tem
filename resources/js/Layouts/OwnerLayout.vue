<script setup>
import { computed, ref } from 'vue'
import { Link, usePage } from '@inertiajs/vue3'
import { Head } from '@inertiajs/vue3'

const page = usePage()
const user = computed(() => page.props.auth?.user)

const mobileMenuOpen = ref(false)

const navigation = [
    { name: 'Dashboard', href: '/owner/dashboard', icon: 'chart' },
    { name: 'My Properties', href: '/owner/properties', icon: 'home' },
    { name: 'Reservations', href: '/owner/reservations', icon: 'calendar' },
    { name: 'Reviews', href: '/owner/reviews', icon: 'star' },
    { name: 'Earnings', href: '/owner/earnings', icon: 'currency' },
]

const iconMap = {
    chart: 'M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z',
    home: 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6',
    calendar: 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z',
    star: 'M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z',
    currency: 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
}

const isActive = (href) => {
    return page.url.startsWith(href)
}
</script>

<template>
    <div class="min-h-screen bg-gray-50">
        <Head :title="`${$page.props.title || 'Owner'} - Mbata Owner`" />

        <!-- Mobile menu -->
        <div v-if="mobileMenuOpen" class="fixed inset-0 z-50 lg:hidden">
            <div class="fixed inset-0 bg-gray-600 opacity-75" @click="mobileMenuOpen = false"></div>
            <div class="fixed inset-y-0 left-0 flex w-full max-w-xs flex-col overflow-y-auto bg-white px-6 py-4 shadow-xl">
                <div class="flex items-center justify-between">
                    <Link href="/owner/dashboard" class="flex items-center gap-2">
                        <svg class="h-8 w-8 text-indigo-600" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z" />
                        </svg>
                        <span class="text-xl font-bold text-gray-900">Mbata Owner</span>
                    </Link>
                    <button
                        @click="mobileMenuOpen = false"
                        type="button"
                        class="text-gray-400 hover:text-gray-500"
                    >
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <nav class="mt-8 space-y-2">
                    <Link
                        v-for="item in navigation"
                        :key="item.href"
                        :href="item.href"
                        class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-colors"
                        :class="isActive(item.href) ? 'bg-indigo-50 text-indigo-600' : 'text-gray-700 hover:bg-gray-50'"
                        @click="mobileMenuOpen = false"
                    >
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="iconMap[item.icon] || iconMap.home" />
                        </svg>
                        {{ item.name }}
                    </Link>
                </nav>
            </div>
        </div>

        <!-- Desktop sidebar -->
        <div class="hidden lg:fixed lg:inset-y-0 lg:flex lg:w-64 lg:flex-col">
            <div class="flex flex-col flex-grow bg-white border-r border-gray-200 overflow-y-auto">
                <div class="flex items-center justify-between h-16 px-6 border-b border-gray-200">
                    <Link href="/owner/dashboard" class="flex items-center gap-2">
                        <svg class="h-8 w-8 text-indigo-600" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z" />
                        </svg>
                        <span class="text-xl font-bold text-gray-900">Mbata Owner</span>
                    </Link>
                </div>
                <nav class="flex-1 px-4 py-6 space-y-1">
                    <Link
                        v-for="item in navigation"
                        :key="item.href"
                        :href="item.href"
                        class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-colors"
                        :class="isActive(item.href) ? 'bg-indigo-50 text-indigo-600' : 'text-gray-700 hover:bg-gray-50'"
                    >
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="iconMap[item.icon] || iconMap.home" />
                        </svg>
                        {{ item.name }}
                    </Link>
                </nav>
                <div class="p-4 border-t border-gray-200">
                    <div class="flex items-center gap-3">
                        <div class="h-10 w-10 bg-indigo-100 rounded-full flex items-center justify-center">
                            <span class="text-indigo-600 font-semibold">
                                {{ user?.name?.charAt(0).toUpperCase() || 'O' }}
                            </span>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900 truncate">{{ user?.name }}</p>
                            <p class="text-xs text-gray-500">Property Owner</p>
                        </div>
                    </div>
                    <div class="mt-4 space-y-1">
                        <Link
                            :href="route('profile.edit')"
                            class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 rounded-lg"
                        >
                            Profile Settings
                        </Link>
                        <Link
                            :href="route('logout')"
                            method="post"
                            as="button"
                            class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 rounded-lg"
                        >
                            Logout
                        </Link>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main content -->
        <div class="lg:pl-64">
            <!-- Top bar -->
            <header class="bg-white shadow-sm border-b border-gray-200">
                <div class="flex items-center justify-between h-16 px-4 sm:px-6 lg:px-8">
                    <button
                        @click="mobileMenuOpen = true"
                        type="button"
                        class="lg:hidden text-gray-400 hover:text-gray-500"
                    >
                        <span class="sr-only">Open sidebar</span>
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>

                    <div class="flex-1"></div>

                    <div class="flex items-center gap-4">
                        <!-- Notifications -->
                        <button class="text-gray-400 hover:text-gray-500 relative">
                            <span class="sr-only">Notifications</span>
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                            </svg>
                            <span class="absolute top-0 right-0 h-2 w-2 bg-red-500 rounded-full"></span>
                        </button>

                        <Link
                            :href="route('home')"
                            class="text-sm text-gray-600 hover:text-gray-900"
                        >
                            View Site
                        </Link>
                    </div>
                </div>
            </header>

            <!-- Page content -->
            <main class="p-4 sm:p-6 lg:p-8">
                <slot />
            </main>
        </div>
    </div>
</template>
