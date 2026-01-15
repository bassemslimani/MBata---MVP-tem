<script setup>
import { ref, computed } from 'vue'
import { Link } from '@inertiajs/vue3'
import OwnerLayout from '@/Layouts/OwnerLayout.vue'
import AvailabilityCalendar from '@/Components/Property/AvailabilityCalendar.vue'

const props = defineProps({
    property: {
        type: Object,
        required: true,
    },
    availabilities: {
        type: Array,
        default: () => [],
    },
})

const availabilities = ref([...props.availabilities])

const stats = computed(() => {
    const total = availabilities.value.length
    const available = availabilities.value.filter(a => a.status === 'available').length
    const unavailable = availabilities.value.filter(a => a.status === 'unavailable').length
    const booked = availabilities.value.filter(a => a.status === 'booked').length

    return { total, available, unavailable, booked }
})

const handleAvailabilitiesUpdate = (newAvailabilities) => {
    availabilities.value = [...newAvailabilities]
}
</script>

<template>
    <OwnerLayout>
        <div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <div class="flex items-center gap-2">
                    <Link
                        href="/owner/properties"
                        class="text-gray-400 hover:text-gray-600"
                    >
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                    </Link>
                    <h1 class="text-2xl font-bold text-gray-900">Availability Calendar</h1>
                </div>
                <p class="text-gray-600 mt-1">{{ property.title }}</p>
            </div>

            <div class="flex items-center gap-3">
                <Link
                    :href="`/properties/${property.id}`"
                    target="_blank"
                    class="inline-flex items-center gap-2 px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors"
                >
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                    Preview
                </Link>
                <Link
                    :href="`/owner/properties/${property.id}/edit`"
                    class="inline-flex items-center gap-2 px-4 py-2 bg-orange-500 text-white rounded-lg hover:bg-orange-600 transition-colors"
                >
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                    Edit Property
                </Link>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
            <div class="bg-white rounded-xl shadow-sm p-5">
                <div class="flex items-center gap-3">
                    <div class="h-10 w-10 bg-gray-100 rounded-lg flex items-center justify-center">
                        <svg class="h-5 w-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs text-gray-600">Total Days</p>
                        <p class="text-lg font-bold text-gray-900">{{ stats.total }}</p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-xl shadow-sm p-5">
                <div class="flex items-center gap-3">
                    <div class="h-10 w-10 bg-green-100 rounded-lg flex items-center justify-center">
                        <svg class="h-5 w-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs text-gray-600">Available</p>
                        <p class="text-lg font-bold text-green-600">{{ stats.available }}</p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-xl shadow-sm p-5">
                <div class="flex items-center gap-3">
                    <div class="h-10 w-10 bg-gray-200 rounded-lg flex items-center justify-center">
                        <svg class="h-5 w-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs text-gray-600">Unavailable</p>
                        <p class="text-lg font-bold text-gray-500">{{ stats.unavailable }}</p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-xl shadow-sm p-5">
                <div class="flex items-center gap-3">
                    <div class="h-10 w-10 bg-red-100 rounded-lg flex items-center justify-center">
                        <svg class="h-5 w-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs text-gray-600">Booked</p>
                        <p class="text-lg font-bold text-red-600">{{ stats.booked }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Calendar -->
        <div class="mb-8">
            <AvailabilityCalendar
                :property-id="property.id"
                :existing-availabilities="availabilities"
                @update:availabilities="handleAvailabilitiesUpdate"
            />
        </div>

        <!-- Quick Links -->
        <div class="bg-white rounded-xl shadow-sm p-6">
            <h3 class="font-semibold text-gray-900 mb-4">Quick Actions</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <Link
                    :href="`/owner/properties/${property.id}/photos`"
                    class="flex items-center gap-3 p-4 border border-gray-200 rounded-lg hover:border-orange-300 hover:bg-orange-50 transition-colors"
                >
                    <div class="h-10 w-10 bg-purple-100 rounded-lg flex items-center justify-center">
                        <svg class="h-5 w-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <span class="font-medium text-gray-900">Manage Photos</span>
                </Link>

                <Link
                    :href="`/owner/properties/${property.id}/edit`"
                    class="flex items-center gap-3 p-4 border border-gray-200 rounded-lg hover:border-orange-300 hover:bg-orange-50 transition-colors"
                >
                    <div class="h-10 w-10 bg-blue-100 rounded-lg flex items-center justify-center">
                        <svg class="h-5 w-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                    </div>
                    <span class="font-medium text-gray-900">Edit Details</span>
                </Link>

                <Link
                    :href="`/owner/reservations?property_id=${property.id}`"
                    class="flex items-center gap-3 p-4 border border-gray-200 rounded-lg hover:border-orange-300 hover:bg-orange-50 transition-colors"
                >
                    <div class="h-10 w-10 bg-green-100 rounded-lg flex items-center justify-center">
                        <svg class="h-5 w-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                    </div>
                    <span class="font-medium text-gray-900">View Bookings</span>
                </Link>

                <Link
                    href="/owner/properties"
                    class="flex items-center gap-3 p-4 border border-gray-200 rounded-lg hover:border-orange-300 hover:bg-orange-50 transition-colors"
                >
                    <div class="h-10 w-10 bg-gray-100 rounded-lg flex items-center justify-center">
                        <svg class="h-5 w-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </div>
                    <span class="font-medium text-gray-900">All Properties</span>
                </Link>
            </div>
        </div>
    </OwnerLayout>
</template>
