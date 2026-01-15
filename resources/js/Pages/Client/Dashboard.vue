<script setup>
import { ref, computed } from 'vue'
import { Link } from '@inertiajs/vue3'
import ClientLayout from '@/Layouts/ClientLayout.vue'

const props = defineProps({
    stats: {
        type: Object,
        default: () => ({
            upcoming_trips: 0,
            completed_trips: 0,
            total_spent: 0,
            favorite_count: 0,
        }),
    },
    upcomingReservations: {
        type: Array,
        default: () => [],
    },
    recentReservations: {
        type: Array,
        default: () => [],
    },
    favoriteProperties: {
        type: Array,
        default: () => [],
    },
})

const formatDate = (dateString) => {
    const date = new Date(dateString)
    return date.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' })
}

const formatPrice = (price) => {
    return new Intl.NumberFormat('fr-DZ').format(price) + ' DA'
}

const getDaysUntil = (dateString) => {
    const date = new Date(dateString)
    const today = new Date()
    const diffTime = date - today
    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24))
    return diffDays
}

const getStatusColor = (status) => {
    const colors = {
        pending: 'bg-yellow-100 text-yellow-700',
        confirmed: 'bg-green-100 text-green-700',
        completed: 'bg-gray-100 text-gray-700',
        cancelled: 'bg-red-100 text-red-700',
    }
    return colors[status] || 'bg-gray-100 text-gray-700'
}

const getStatusLabel = (status) => {
    return status.charAt(0).toUpperCase() + status.slice(1)
}
</script>

<template>
    <ClientLayout>
        <div class="space-y-8">
            <!-- Welcome -->
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Welcome back!</h1>
                <p class="text-gray-600 mt-1">Here's what's happening with your bookings</p>
            </div>

            <!-- Stats Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="bg-white rounded-xl border border-gray-200 p-6">
                    <div class="flex items-center gap-4">
                        <div class="h-12 w-12 bg-blue-100 rounded-xl flex items-center justify-center">
                            <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-2xl font-bold text-gray-900">{{ stats.upcoming_trips }}</p>
                            <p class="text-sm text-gray-500">Upcoming trips</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl border border-gray-200 p-6">
                    <div class="flex items-center gap-4">
                        <div class="h-12 w-12 bg-green-100 rounded-xl flex items-center justify-center">
                            <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-2xl font-bold text-gray-900">{{ stats.completed_trips }}</p>
                            <p class="text-sm text-gray-500">Completed trips</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl border border-gray-200 p-6">
                    <div class="flex items-center gap-4">
                        <div class="h-12 w-12 bg-purple-100 rounded-xl flex items-center justify-center">
                            <svg class="h-6 w-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-lg font-bold text-gray-900">{{ formatPrice(stats.total_spent) }}</p>
                            <p class="text-sm text-gray-500">Total spent</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl border border-gray-200 p-6">
                    <div class="flex items-center gap-4">
                        <div class="h-12 w-12 bg-red-100 rounded-xl flex items-center justify-center">
                            <svg class="h-6 w-6 text-red-600" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-2xl font-bold text-gray-900">{{ stats.favorite_count }}</p>
                            <p class="text-sm text-gray-500">Favorites</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Upcoming Trips -->
            <div v-if="upcomingReservations.length > 0" class="bg-white rounded-xl border border-gray-200">
                <div class="p-6 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <h2 class="text-lg font-semibold text-gray-900">Upcoming Trips</h2>
                        <Link href="/client/reservations" class="text-orange-600 hover:underline text-sm font-medium">
                            View all
                        </Link>
                    </div>
                </div>
                <div class="divide-y divide-gray-200">
                    <div
                        v-for="reservation in upcomingReservations"
                        :key="reservation.id"
                        class="p-6 hover:bg-gray-50 transition-colors"
                    >
                        <div class="flex items-start gap-4">
                            <img
                                :src="reservation.property?.thumbnail_url || '/placeholder-property.jpg'"
                                :alt="reservation.property?.title"
                                class="w-24 h-24 object-cover rounded-lg flex-shrink-0"
                            />
                            <div class="flex-1 min-w-0">
                                <div class="flex items-start justify-between gap-4">
                                    <div>
                                        <h3 class="font-semibold text-gray-900 truncate">{{ reservation.property?.title }}</h3>
                                        <p class="text-sm text-gray-600">{{ reservation.property?.wilaya }}</p>
                                    </div>
                                    <span
                                        class="px-3 py-1 text-xs font-medium rounded-full flex-shrink-0"
                                        :class="getStatusColor(reservation.status)"
                                    >
                                        {{ getStatusLabel(reservation.status) }}
                                    </span>
                                </div>
                                <div class="flex items-center gap-4 mt-2 text-sm text-gray-600">
                                    <div class="flex items-center gap-1">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                        <span>{{ formatDate(reservation.check_in) }} - {{ formatDate(reservation.check_out) }}</span>
                                    </div>
                                    <div class="flex items-center gap-1">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        <span>{{ getDaysUntil(reservation.check_in) }} days away</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="flex items-center gap-3 mt-4">
                            <Link
                                :href="`/client/reservations/${reservation.id}`"
                                class="px-4 py-2 bg-orange-500 text-white text-sm font-medium rounded-lg hover:bg-orange-600 transition-colors"
                            >
                                View Details
                            </Link>
                            <a
                                :href="`mailto:${reservation.property?.owner?.email}`"
                                class="px-4 py-2 border border-gray-300 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50 transition-colors"
                            >
                                Contact Host
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Empty State for Upcoming -->
            <div v-else class="bg-white rounded-xl border border-gray-200 p-12 text-center">
                <div class="h-16 w-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="h-8 w-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">No upcoming trips</h3>
                <p class="text-gray-600 mb-6">Start exploring and book your next adventure</p>
                <Link
                    href="/properties"
                    class="inline-flex items-center gap-2 px-6 py-3 bg-orange-500 text-white font-medium rounded-xl hover:bg-orange-600 transition-colors"
                >
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                    Explore Properties
                </Link>
            </div>

            <!-- Favorites Preview -->
            <div v-if="favoriteProperties.length > 0" class="bg-white rounded-xl border border-gray-200">
                <div class="p-6 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <h2 class="text-lg font-semibold text-gray-900">Your Favorites</h2>
                        <Link href="/client/favorites" class="text-orange-600 hover:underline text-sm font-medium">
                            View all
                        </Link>
                    </div>
                </div>
                <div class="p-6 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    <div
                        v-for="property in favoriteProperties.slice(0, 3)"
                        :key="property.id"
                        class="group"
                    >
                        <Link :href="`/properties/${property.id}`" class="block">
                            <div class="relative overflow-hidden rounded-xl mb-3">
                                <img
                                    :src="property.thumbnail_url || property.images?.[0]?.url || '/placeholder-property.jpg'"
                                    :alt="property.title"
                                    class="w-full h-48 object-cover group-hover:scale-105 transition-transform duration-300"
                                />
                                <button
                                    class="absolute top-3 right-3 h-8 w-8 bg-white/90 rounded-full flex items-center justify-center text-red-500 hover:bg-white transition-colors"
                                    @click.prevent
                                >
                                    <svg class="h-5 w-5 fill-current" viewBox="0 0 24 24">
                                        <path d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                                    </svg>
                                </button>
                            </div>
                            <h3 class="font-semibold text-gray-900 truncate">{{ property.title }}</h3>
                            <p class="text-sm text-gray-600">{{ property.wilaya }}</p>
                            <p class="mt-2 font-semibold text-gray-900">{{ formatPrice(property.price_per_night_dzd) }} <span class="text-sm font-normal text-gray-500">/ night</span></p>
                        </Link>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <Link
                    href="/properties"
                    class="bg-gradient-to-br from-orange-500 to-orange-600 rounded-xl p-6 text-white hover:shadow-lg transition-shadow"
                >
                    <svg class="h-8 w-8 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                    <h3 class="font-semibold text-lg">Find a Place</h3>
                    <p class="text-sm text-orange-100 mt-1">Discover your next stay</p>
                </Link>

                <Link
                    href="/client/reservations"
                    class="bg-white rounded-xl border border-gray-200 p-6 hover:border-orange-300 hover:shadow-md transition-all"
                >
                    <svg class="h-8 w-8 mb-3 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                    <h3 class="font-semibold text-lg text-gray-900">My Trips</h3>
                    <p class="text-sm text-gray-500 mt-1">View all bookings</p>
                </Link>

                <Link
                    href="/client/favorites"
                    class="bg-white rounded-xl border border-gray-200 p-6 hover:border-orange-300 hover:shadow-md transition-all"
                >
                    <svg class="h-8 w-8 mb-3 text-red-500" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                    </svg>
                    <h3 class="font-semibold text-lg text-gray-900">Favorites</h3>
                    <p class="text-sm text-gray-500 mt-1">Saved properties</p>
                </Link>
            </div>
        </div>
    </ClientLayout>
</template>
