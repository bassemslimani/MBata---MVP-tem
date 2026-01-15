<script setup>
import { computed, ref } from 'vue'
import { Link, usePage } from '@inertiajs/vue3'
import { Head } from '@inertiajs/vue3'

const page = usePage()
const user = computed(() => page.props.auth?.user)

const sidebarOpen = ref(false)

const navigation = computed(() => {
    const role = user.value?.role
    const items = [
        { name: 'Dashboard', href: '/admin/dashboard', icon: 'home' },
        { name: 'Users', href: '/admin/users', icon: 'users' },
        { name: 'Properties', href: '/admin/properties', icon: 'home' },
        { name: 'Reservations', href: '/admin/reservations', icon: 'calendar' },
        { name: 'Reviews', href: '/admin/reviews', icon: 'star' },
        { name: 'Payments', href: '/admin/payments', icon: 'credit-card' },
    ]

    if (role === 'super_admin') {
        items.push({ name: 'Admins', href: '/super-admin/admins', icon: 'shield' })
    }

    items.push({ name: 'Settings', href: '/admin/settings/currency', icon: 'cog' })

    return items
})

const iconMap = {
    home: 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6',
    users: 'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z',
    calendar: 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z',
    star: 'M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z',
    'credit-card': 'M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z',
    shield: 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z',
    cog: 'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z M15 12a3 3 0 11-6 0 3 3 0 016 0z',
}

const isActive = (href) => {
    return page.url.startsWith(href)
}
</script>

<template>
    <div class="min-h-screen bg-gray-100">
        <Head :title="`${$page.props.title || 'Admin'} - Mbata Admin`" />

        <!-- Mobile sidebar backdrop -->
        <div
            v-if="sidebarOpen"
            class="fixed inset-0 z-40 lg:hidden"
            @click="sidebarOpen = false"
        >
            <div class="absolute inset-0 bg-gray-600 opacity-75"></div>
        </div>

        <!-- Sidebar -->
        <div
            class="fixed inset-y-0 left-0 z-50 w-64 bg-white shadow-lg transform transition-transform duration-300 ease-in-out lg:translate-x-0"
            :class="{ '-translate-x-full': !sidebarOpen, 'translate-x-0': sidebarOpen }"
        >
            <div class="flex flex-col h-full">
                <!-- Logo -->
                <div class="flex items-center justify-between h-16 px-6 border-b border-gray-200">
                    <Link href="/admin/dashboard" class="flex items-center gap-2">
                        <svg class="h-8 w-8 text-indigo-600" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z" />
                        </svg>
                        <span class="text-xl font-bold text-gray-900">Mbata Admin</span>
                    </Link>
                    <button
                        @click="sidebarOpen = false"
                        class="lg:hidden text-gray-400 hover:text-gray-500"
                    >
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- Navigation -->
                <nav class="flex-1 px-4 py-6 space-y-1 overflow-y-auto">
                    <Link
                        v-for="item in navigation"
                        :key="item.href"
                        :href="item.href"
                        class="flex items-center gap-3 px-4 py-3 rounded-lg text-sm font-medium transition-colors"
                        :class="isActive(item.href) ? 'bg-indigo-50 text-indigo-600' : 'text-gray-700 hover:bg-gray-50'"
                    >
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="iconMap[item.icon] || iconMap.home" />
                        </svg>
                        {{ item.name }}
                    </Link>
                </nav>

                <!-- User section -->
                <div class="p-4 border-t border-gray-200">
                    <div class="flex items-center gap-3">
                        <div class="h-10 w-10 bg-indigo-100 rounded-full flex items-center justify-center">
                            <span class="text-indigo-600 font-semibold">
                                {{ user?.name?.charAt(0).toUpperCase() || 'A' }}
                            </span>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900 truncate">{{ user?.name }}</p>
                            <p class="text-xs text-gray-500 capitalize">{{ user?.role?.replace('_', ' ') }}</p>
                        </div>
                    </div>
                    <div class="mt-4 space-y-1">
                        <Link
                            :href="route('profile.edit')"
                            class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 rounded-lg"
                        >
                            Profile
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
                        @click="sidebarOpen = true"
                        class="lg:hidden text-gray-400 hover:text-gray-500"
                    >
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>

                    <div class="flex-1"></div>

                    <div class="flex items-center gap-4">
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
