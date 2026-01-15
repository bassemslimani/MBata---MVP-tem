<script setup>
import { Link } from '@inertiajs/vue3'
import OwnerLayout from '@/Layouts/OwnerLayout.vue'

const props = defineProps({
    stats: {
        type: Object,
        default: () => ({
            totalProperties: 0,
            activeProperties: 0,
            totalReservations: 0,
            pendingReservations: 0,
            totalEarnings: 0,
            currentMonthEarnings: 0,
            averageRating: 0,
        }),
    },
    recentReservations: {
        type: Array,
        default: () => [],
    },
})

const formatCurrency = (amount) => {
    return new Intl.NumberFormat('fr-DZ').format(amount) + ' DA'
}
</script>

<template>
    <OwnerLayout>
        <div class="mb-8">
            <h1 class="text-2xl font-bold text-gray-900">Dashboard</h1>
            <p class="text-gray-600 mt-1">Welcome back! Here's an overview of your properties.</p>
        </div>

        <!-- Stats Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- Total Properties -->
            <div class="bg-white rounded-xl shadow-sm p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Total Properties</p>
                        <p class="text-3xl font-bold text-gray-900 mt-2">{{ stats.totalProperties }}</p>
                    </div>
                    <div class="h-12 w-12 bg-indigo-100 rounded-lg flex items-center justify-center">
                        <svg class="h-6 w-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                        </svg>
                    </div>
                </div>
                <div class="mt-4 flex items-center text-sm">
                    <span class="text-green-600 font-medium">{{ stats.activeProperties }} active</span>
                    <span class="text-gray-400 ml-2">properties</span>
                </div>
            </div>

            <!-- Total Reservations -->
            <div class="bg-white rounded-xl shadow-sm p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Total Reservations</p>
                        <p class="text-3xl font-bold text-gray-900 mt-2">{{ stats.totalReservations }}</p>
                    </div>
                    <div class="h-12 w-12 bg-green-100 rounded-lg flex items-center justify-center">
                        <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                </div>
                <div class="mt-4 flex items-center text-sm">
                    <span class="text-orange-600 font-medium">{{ stats.pendingReservations }} pending</span>
                    <span class="text-gray-400 ml-2">confirmation</span>
                </div>
            </div>

            <!-- Earnings -->
            <div class="bg-white rounded-xl shadow-sm p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Total Earnings</p>
                        <p class="text-3xl font-bold text-gray-900 mt-2">{{ formatCurrency(stats.totalEarnings) }}</p>
                    </div>
                    <div class="h-12 w-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                        <svg class="h-6 w-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
                <div class="mt-4 flex items-center text-sm">
                    <span class="text-green-600 font-medium">{{ formatCurrency(stats.currentMonthEarnings) }}</span>
                    <span class="text-gray-400 ml-2">this month</span>
                </div>
            </div>

            <!-- Rating -->
            <div class="bg-white rounded-xl shadow-sm p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Average Rating</p>
                        <p class="text-3xl font-bold text-gray-900 mt-2">{{ stats.averageRating.toFixed(1) }}</p>
                    </div>
                    <div class="h-12 w-12 bg-purple-100 rounded-lg flex items-center justify-center">
                        <svg class="h-6 w-6 text-purple-600" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                        </svg>
                    </div>
                </div>
                <div class="mt-4 flex items-center text-sm">
                    <div class="flex text-yellow-400">
                        <svg v-for="i in 5" :key="i" class="h-4 w-4" :class="{ 'text-gray-300': i > Math.round(stats.averageRating) }" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="bg-white rounded-xl shadow-sm p-6 mb-8">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Quick Actions</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <Link
                    href="/owner/properties/create"
                    class="flex items-center gap-3 p-4 border border-gray-200 rounded-lg hover:border-indigo-300 hover:bg-indigo-50 transition-colors"
                >
                    <div class="h-10 w-10 bg-indigo-100 rounded-lg flex items-center justify-center">
                        <svg class="h-5 w-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                    </div>
                    <span class="font-medium text-gray-900">Add Property</span>
                </Link>

                <Link
                    href="/owner/properties"
                    class="flex items-center gap-3 p-4 border border-gray-200 rounded-lg hover:border-indigo-300 hover:bg-indigo-50 transition-colors"
                >
                    <div class="h-10 w-10 bg-indigo-100 rounded-lg flex items-center justify-center">
                        <svg class="h-5 w-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                        </svg>
                    </div>
                    <span class="font-medium text-gray-900">Manage Properties</span>
                </Link>

                <Link
                    href="/owner/reservations"
                    class="flex items-center gap-3 p-4 border border-gray-200 rounded-lg hover:border-indigo-300 hover:bg-indigo-50 transition-colors"
                >
                    <div class="h-10 w-10 bg-indigo-100 rounded-lg flex items-center justify-center">
                        <svg class="h-5 w-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <span class="font-medium text-gray-900">View Bookings</span>
                </Link>

                <Link
                    href="/owner/earnings"
                    class="flex items-center gap-3 p-4 border border-gray-200 rounded-lg hover:border-indigo-300 hover:bg-indigo-50 transition-colors"
                >
                    <div class="h-10 w-10 bg-indigo-100 rounded-lg flex items-center justify-center">
                        <svg class="h-5 w-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                    </div>
                    <span class="font-medium text-gray-900">View Earnings</span>
                </Link>
            </div>
        </div>

        <!-- Recent Reservations -->
        <div class="bg-white rounded-xl shadow-sm">
            <div class="p-6 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-gray-900">Recent Reservations</h2>
                    <Link
                        v-if="recentReservations.length > 0"
                        href="/owner/reservations"
                        class="text-sm text-indigo-600 hover:text-indigo-700 font-medium"
                    >
                        View all
                    </Link>
                </div>
            </div>

            <div v-if="recentReservations.length > 0" class="divide-y divide-gray-200">
                <div
                    v-for="reservation in recentReservations"
                    :key="reservation.id"
                    class="p-6 hover:bg-gray-50 transition-colors"
                >
                    <div class="flex items-start justify-between">
                        <div class="flex items-start gap-4">
                            <div class="h-12 w-12 bg-gray-100 rounded-lg overflow-hidden">
                                <img
                                    v-if="reservation.property?.images?.[0]"
                                    :src="reservation.property.images[0].image_path"
                                    class="h-full w-full object-cover"
                                />
                                <div v-else class="h-full w-full flex items-center justify-center">
                                    <svg class="h-6 w-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                                    </svg>
                                </div>
                            </div>
                            <div>
                                <h3 class="font-medium text-gray-900">{{ reservation.property?.title }}</h3>
                                <p class="text-sm text-gray-600 mt-1">
                                    {{ reservation.client?.name }}
                                </p>
                                <p class="text-sm text-gray-500 mt-1">
                                    {{ new Date(reservation.check_in_date).toLocaleDateString() }} - {{ new Date(reservation.check_out_date).toLocaleDateString() }}
                                </p>
                            </div>
                        </div>
                        <div class="text-right">
                            <span
                                class="inline-flex px-2 py-1 text-xs font-medium rounded-full"
                                :class="{
                                    'bg-yellow-100 text-yellow-800': reservation.status === 'pending',
                                    'bg-green-100 text-green-800': reservation.status === 'confirmed',
                                    'bg-blue-100 text-blue-800': reservation.status === 'completed',
                                    'bg-red-100 text-red-800': reservation.status === 'cancelled',
                                }"
                            >
                                {{ reservation.status }}
                            </span>
                            <p class="text-sm font-medium text-gray-900 mt-2">
                                {{ formatCurrency(reservation.total_price_dzd) }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <div v-else class="p-12 text-center">
                <svg class="mx-auto h-16 w-16 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                <h3 class="mt-4 text-lg font-medium text-gray-900">No reservations yet</h3>
                <p class="mt-2 text-gray-500">When guests book your properties, they'll appear here.</p>
            </div>
        </div>
    </OwnerLayout>
</template>
