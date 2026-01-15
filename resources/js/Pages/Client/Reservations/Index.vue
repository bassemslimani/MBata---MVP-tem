<script setup>
import { ref, computed } from 'vue'
import { Link } from '@inertiajs/vue3'
import ClientLayout from '@/Layouts/ClientLayout.vue'

const props = defineProps({
    reservations: {
        type: Array,
        default: () => [],
    },
})

const statusFilter = ref('all')
const activeTab = ref('upcoming')

const tabs = [
    { id: 'upcoming', label: 'Upcoming' },
    { id: 'completed', label: 'Completed' },
    { id: 'cancelled', label: 'Cancelled' },
]

const filteredReservations = computed(() => {
    let filtered = props.reservations

    if (activeTab.value === 'upcoming') {
        filtered = filtered.filter(r => r.status === 'confirmed' || r.status === 'pending')
    } else if (activeTab.value === 'completed') {
        filtered = filtered.filter(r => r.status === 'completed')
    } else if (activeTab.value === 'cancelled') {
        filtered = filtered.filter(r => r.status === 'cancelled')
    }

    if (statusFilter.value !== 'all') {
        filtered = filtered.filter(r => r.status === statusFilter.value)
    }

    return filtered.sort((a, b) => new Date(b.created_at) - new Date(a.created_at))
})

const formatDate = (dateString) => {
    const date = new Date(dateString)
    return date.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' })
}

const formatPrice = (price) => {
    return new Intl.NumberFormat('fr-DZ').format(price) + ' DA'
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

const getTabCount = (tabId) => {
    if (tabId === 'upcoming') {
        return props.reservations.filter(r => r.status === 'confirmed' || r.status === 'pending').length
    } else if (tabId === 'completed') {
        return props.reservations.filter(r => r.status === 'completed').length
    } else if (tabId === 'cancelled') {
        return props.reservations.filter(r => r.status === 'cancelled').length
    }
    return 0
}
</script>

<template>
    <ClientLayout>
        <div class="space-y-6">
            <!-- Header -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">My Trips</h1>
                    <p class="text-gray-600 mt-1">Manage your bookings and reservations</p>
                </div>
                <Link
                    href="/properties"
                    class="inline-flex items-center gap-2 px-4 py-2 bg-orange-500 text-white font-medium rounded-xl hover:bg-orange-600 transition-colors"
                >
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Book New Trip
                </Link>
            </div>

            <!-- Tabs -->
            <div class="border-b border-gray-200">
                <nav class="flex gap-8">
                    <button
                        v-for="tab in tabs"
                        :key="tab.id"
                        @click="activeTab = tab.id"
                        class="pb-4 px-1 border-b-2 font-medium transition-colors relative"
                        :class="activeTab === tab.id
                            ? 'border-orange-500 text-orange-600'
                            : 'border-transparent text-gray-500 hover:text-gray-700'"
                    >
                        {{ tab.label }}
                        <span
                            v-if="getTabCount(tab.id) > 0"
                            class="ml-2 px-2 py-0.5 text-xs rounded-full"
                            :class="activeTab === tab.id ? 'bg-orange-100 text-orange-600' : 'bg-gray-100 text-gray-600'"
                        >
                            {{ getTabCount(tab.id) }}
                        </span>
                    </button>
                </nav>
            </div>

            <!-- Reservations List -->
            <div v-if="filteredReservations.length > 0" class="space-y-4">
                <div
                    v-for="reservation in filteredReservations"
                    :key="reservation.id"
                    class="bg-white rounded-xl border border-gray-200 overflow-hidden hover:shadow-md transition-shadow"
                >
                    <div class="flex flex-col sm:flex-row">
                        <!-- Property Image -->
                        <Link
                            :href="`/properties/${reservation.property?.id}`"
                            class="sm:w-64 flex-shrink-0"
                        >
                            <div class="relative h-48 sm:h-full">
                                <img
                                    :src="reservation.property?.thumbnail_url || reservation.property?.images?.[0]?.url || '/placeholder-property.jpg'"
                                    :alt="reservation.property?.title"
                                    class="w-full h-full object-cover"
                                />
                            </div>
                        </Link>

                        <!-- Content -->
                        <div class="flex-1 p-6">
                            <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
                                <div class="flex-1">
                                    <div class="flex items-start gap-3">
                                        <div>
                                            <Link
                                                :href="`/properties/${reservation.property?.id}`"
                                                class="text-lg font-semibold text-gray-900 hover:text-orange-600"
                                            >
                                                {{ reservation.property?.title }}
                                            </Link>
                                            <p class="text-sm text-gray-600 mt-1">
                                                <svg class="h-4 w-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                                </svg>
                                                {{ reservation.property?.wilaya }}{{ reservation.property?.commune ? `, ${reservation.property?.commune}` : '' }}
                                            </p>
                                        </div>
                                        <span
                                            class="px-3 py-1 text-xs font-medium rounded-full flex-shrink-0"
                                            :class="getStatusColor(reservation.status)"
                                        >
                                            {{ getStatusLabel(reservation.status) }}
                                        </span>
                                    </div>

                                    <div class="mt-4 space-y-2">
                                        <div class="flex items-center gap-2 text-sm text-gray-600">
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                            </svg>
                                            <span>
                                                <span class="font-medium">Check-in:</span> {{ formatDate(reservation.check_in) }}
                                            </span>
                                            <span class="text-gray-400">•</span>
                                            <span>
                                                <span class="font-medium">Check-out:</span> {{ formatDate(reservation.check_out) }}
                                            </span>
                                        </div>
                                        <div class="flex items-center gap-2 text-sm text-gray-600">
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2a2 2 0 01-2-2v-6a2 2 0 012-2h5l2 2h5l2-2h5a2 2 0 012 2v6a2 2 0 01-2 2z" />
                                            </svg>
                                            <span>{{ reservation.guests }} guest{{ reservation.guests > 1 ? 's' : '' }}</span>
                                        </div>
                                        <div class="flex items-center gap-2 text-sm text-gray-600">
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            <span>Booking #{{ reservation.id }}</span>
                                        </div>
                                    </div>

                                    <p class="mt-4 text-lg font-semibold text-gray-900">
                                        {{ formatPrice(reservation.total_price) }}
                                    </p>
                                </div>

                                <!-- Actions -->
                                <div class="flex sm:flex-col gap-2">
                                    <Link
                                        :href="`/client/reservations/${reservation.id}`"
                                        class="px-4 py-2 bg-orange-500 text-white text-sm font-medium rounded-lg hover:bg-orange-600 transition-colors text-center"
                                    >
                                        View Details
                                    </Link>
                                    <a
                                        v-if="reservation.status === 'confirmed'"
                                        :href="`mailto:${reservation.property?.owner?.email}`"
                                        class="px-4 py-2 border border-gray-300 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50 transition-colors text-center"
                                    >
                                        Contact Host
                                    </a>
                                    <button
                                        v-if="reservation.status === 'pending'"
                                        class="px-4 py-2 border border-red-300 text-red-600 text-sm font-medium rounded-lg hover:bg-red-50 transition-colors"
                                    >
                                        Cancel
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Empty State -->
            <div v-else class="bg-white rounded-xl border border-gray-200 p-12 text-center">
                <div class="h-16 w-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="h-8 w-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">No {{ activeTab }} trips</h3>
                <p class="text-gray-600 mb-6">
                    <template v-if="activeTab === 'upcoming'">You don't have any upcoming trips. Start exploring!</template>
                    <template v-else-if="activeTab === 'completed'">You haven't completed any trips yet.</template>
                    <template v-else>No cancelled trips.</template>
                </p>
                <Link
                    v-if="activeTab === 'upcoming'"
                    href="/properties"
                    class="inline-flex items-center gap-2 px-6 py-3 bg-orange-500 text-white font-medium rounded-xl hover:bg-orange-600 transition-colors"
                >
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                    Explore Properties
                </Link>
            </div>
        </div>
    </ClientLayout>
</template>
