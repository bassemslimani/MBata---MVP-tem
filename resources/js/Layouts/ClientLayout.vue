<script setup>
import { ref, computed } from 'vue'
import { Link, usePage } from '@inertiajs/vue3'
import LanguageSelector from '@/Components/Layout/LanguageSelector.vue'

const page = usePage()
const currentRoute = computed(() => page.props.ziggy?.location?.split('?')[0] || '')
const user = computed(() => page.props.auth?.user)

const mobileMenuOpen = ref(false)

const navigation = [
    { name: 'Dashboard', href: '/client/dashboard', icon: 'home' },
    { name: 'My Trips', href: '/client/reservations', icon: 'calendar' },
    { name: 'Favorites', href: '/client/favorites', icon: 'heart' },
    { name: 'Profile', href: '/client/profile', icon: 'user' },
]

const isActive = (href) => {
    return currentRoute.value === href || currentRoute.value.startsWith(href + '/')
}
</script>

<template>
    <div class="min-h-screen bg-gray-50">
        <!-- Header -->
        <header class="bg-white border-b border-gray-200 sticky top-0 z-30">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between h-16">
                    <!-- Logo -->
                    <Link href="/" class="flex items-center gap-2">
                        <div class="h-8 w-8 bg-orange-500 rounded-lg flex items-center justify-center">
                            <span class="text-white font-bold">M</span>
                        </div>
                        <span class="font-bold text-gray-900">Mbata</span>
                    </Link>

                    <!-- Desktop Navigation -->
                    <nav class="hidden md:flex items-center gap-1">
                        <Link
                            v-for="item in navigation"
                            :key="item.name"
                            :href="item.href"
                            class="px-4 py-2 rounded-lg text-sm font-medium transition-colors"
                            :class="isActive(item.href)
                                ? 'bg-orange-50 text-orange-600'
                                : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900'"
                        >
                            {{ item.name }}
                        </Link>
                    </nav>

                    <!-- Right Side -->
                    <div class="flex items-center gap-4">
                        <LanguageSelector />
                        <div class="hidden sm:flex items-center gap-3">
                            <div class="h-9 w-9 bg-gradient-to-br from-orange-400 to-orange-600 rounded-full flex items-center justify-center">
                                <span class="text-white font-medium text-sm">
                                    {{ user?.name?.charAt(0)?.toUpperCase() || 'U' }}
                                </span>
                            </div>
                            <span class="text-sm font-medium text-gray-900">{{ user?.name }}</span>
                        </div>
                        <button
                            @click="mobileMenuOpen = !mobileMenuOpen"
                            class="md:hidden p-2 rounded-lg hover:bg-gray-100"
                        >
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="mobileMenuOpen ? 'M6 18L18 6M6 6l12 12' : 'M4 6h16M4 12h16M4 18h16'" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Mobile Menu -->
            <div v-if="mobileMenuOpen" class="md:hidden border-t border-gray-200 bg-white">
                <nav class="px-4 py-3 space-y-1">
                    <Link
                        v-for="item in navigation"
                        :key="item.name"
                        :href="item.href"
                        class="flex items-center gap-3 px-4 py-3 rounded-lg text-sm font-medium"
                        :class="isActive(item.href)
                            ? 'bg-orange-50 text-orange-600'
                            : 'text-gray-600 hover:bg-gray-100'"
                        >
                            <component :is="'svg'" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path v-if="item.icon === 'home'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                                <path v-else-if="item.icon === 'calendar'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                <path v-else-if="item.icon === 'heart'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                                <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </component>
                            {{ item.name }}
                        </Link>
                </nav>
            </div>
        </header>

        <!-- Main Content -->
        <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <slot />
        </main>

        <!-- Footer -->
        <footer class="bg-white border-t border-gray-200 mt-12">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
                <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                    <p class="text-sm text-gray-500">&copy; {{ new Date().getFullYear() }} Mbata. All rights reserved.</p>
                    <nav class="flex items-center gap-6 text-sm text-gray-600">
                        <Link href="/help" class="hover:text-gray-900">Help Center</Link>
                        <Link href="/terms" class="hover:text-gray-900">Terms</Link>
                        <Link href="/privacy" class="hover:text-gray-900">Privacy</Link>
                    </nav>
                </div>
            </div>
        </footer>
    </div>
</template>
