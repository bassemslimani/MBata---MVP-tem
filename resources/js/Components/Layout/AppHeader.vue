<script setup>
import { ref, computed } from 'vue'
import { Link, usePage } from '@inertiajs/vue3'
import LanguageSelector from './LanguageSelector.vue'

const page = usePage()
const user = computed(() => page.props.auth?.user)

const mobileMenuOpen = ref(false)

const navigation = computed(() => {
    const items = [
        { name: 'Home', href: '/' },
    ]

    if (user.value) {
        if (user.value.role === 'admin' || user.value.role === 'super_admin') {
            items.push({ name: 'Admin', href: '/admin/dashboard' })
        }
        if (user.value.role === 'owner') {
            items.push({ name: 'Owner Dashboard', href: '/owner/dashboard' })
        }
        if (user.value.role === 'client') {
            items.push({ name: 'My Bookings', href: '/client/reservations' })
        }
        items.push({ name: 'Dashboard', href: '/dashboard' })
    } else {
        items.push({ name: 'Properties', href: '/search' })
        items.push({ name: 'Promotions', href: '/promotions' })
    }

    return items
})
</script>

<template>
    <header class="bg-white shadow-sm border-b border-gray-200">
        <nav class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="flex h-16 justify-between">
                <!-- Logo -->
                <div class="flex items-center">
                    <Link :href="route('home')" class="flex items-center gap-2">
                        <svg class="h-8 w-8 text-indigo-600" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z" />
                        </svg>
                        <span class="text-xl font-bold text-gray-900">Mbata</span>
                    </Link>
                </div>

                <!-- Desktop Navigation -->
                <div class="hidden md:flex md:items-center md:gap-6">
                    <template v-for="item in navigation" :key="item.href">
                        <Link
                            :href="item.href"
                            class="text-gray-600 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium transition-colors"
                            :class="{ 'text-indigo-600 bg-indigo-50': $page.url === item.href }"
                        >
                            {{ item.name }}
                        </Link>
                    </template>

                    <LanguageSelector />

                    <div class="flex items-center gap-3 ml-2">
                        <template v-if="user">
                            <Link
                                :href="route('profile.edit')"
                                class="flex items-center gap-2 text-gray-600 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium transition-colors"
                            >
                                <span>{{ user.name }}</span>
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                            </Link>
                        </template>
                        <template v-else>
                            <Link
                                :href="route('login')"
                                class="text-gray-600 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium transition-colors"
                            >
                                Log in
                            </Link>
                            <Link
                                :href="route('register')"
                                class="bg-indigo-600 text-white px-4 py-2 rounded-md text-sm font-medium hover:bg-indigo-700 transition-colors"
                            >
                                Register
                            </Link>
                        </template>
                    </div>
                </div>

                <!-- Mobile menu button -->
                <div class="flex items-center md:hidden">
                    <LanguageSelector />
                    <button
                        type="button"
                        @click="mobileMenuOpen = !mobileMenuOpen"
                        class="ml-2 inline-flex items-center justify-center rounded-md p-2 text-gray-400 hover:bg-gray-100 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-indigo-500"
                    >
                        <span class="sr-only">Open menu</span>
                        <svg v-if="!mobileMenuOpen" class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                        <svg v-else class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>
        </nav>

        <!-- Mobile menu -->
        <div v-if="mobileMenuOpen" class="md:hidden border-t border-gray-200">
            <div class="space-y-1 px-4 pb-3 pt-2">
                <template v-for="item in navigation" :key="item.href">
                    <Link
                        :href="item.href"
                        class="block rounded-md px-3 py-2 text-base font-medium text-gray-700 hover:bg-gray-50 hover:text-gray-900"
                        :class="{ 'bg-indigo-50 text-indigo-600': $page.url === item.href }"
                        @click="mobileMenuOpen = false"
                    >
                        {{ item.name }}
                    </Link>
                </template>
                <div class="border-t border-gray-200 pt-4 mt-4">
                    <template v-if="user">
                        <Link
                            :href="route('profile.edit')"
                            class="block rounded-md px-3 py-2 text-base font-medium text-gray-700 hover:bg-gray-50"
                            @click="mobileMenuOpen = false"
                        >
                            {{ user.name }}
                        </Link>
                    </template>
                    <template v-else>
                        <Link
                            :href="route('login')"
                            class="block rounded-md px-3 py-2 text-base font-medium text-gray-700 hover:bg-gray-50"
                            @click="mobileMenuOpen = false"
                        >
                            Log in
                        </Link>
                        <Link
                            :href="route('register')"
                            class="block rounded-md px-3 py-2 text-base font-medium text-indigo-600 hover:bg-indigo-50"
                            @click="mobileMenuOpen = false"
                        >
                            Register
                        </Link>
                    </template>
                </div>
            </div>
        </div>
    </header>
</template>
