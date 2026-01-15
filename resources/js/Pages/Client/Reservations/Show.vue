<script setup>
import { ref, computed } from 'vue'
import { Link, useForm } from '@inertiajs/vue3'
import ClientLayout from '@/Layouts/ClientLayout.vue'
import ReviewCard from '@/Components/Review/ReviewCard.vue'
import ReviewForm from '@/Components/Review/ReviewForm.vue'

const props = defineProps({
    reservation: {
        type: Object,
        required: true,
    },
    userReview: {
        type: Object,
        default: null,
    },
})

const cancelling = ref(false)
const reviewSubmitting = ref(false)

const formatDate = (dateString) => {
    const date = new Date(dateString)
    return date.toLocaleDateString('en-US', { weekday: 'long', month: 'long', day: 'numeric', year: 'numeric' })
}

const formatPrice = (price) => {
    return new Intl.NumberFormat('fr-DZ').format(price) + ' DA'
}

const nightsCount = computed(() => {
    const start = new Date(props.reservation.check_in)
    const end = new Date(props.reservation.check_out)
    return Math.max(0, Math.ceil((end - start) / (1000 * 60 * 60 * 24)))
})

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
    const labels = {
        pending: 'Pending Confirmation',
        confirmed: 'Confirmed',
        completed: 'Completed',
        cancelled: 'Cancelled',
    }
    return labels[status] || status
}

const canCancel = computed(() => {
    const checkIn = new Date(props.reservation.check_in)
    const today = new Date()
    const daysUntil = Math.ceil((checkIn - today) / (1000 * 60 * 60 * 24))
    return props.reservation.status === 'confirmed' && daysUntil > 2
})

const showReviewForm = computed(() => {
    return props.reservation.status === 'completed' && !props.userReview
})

const cancelReservation = async () => {
    if (!confirm('Are you sure you want to cancel this reservation?')) return

    cancelling.value = true
    try {
        await new Promise(resolve => setTimeout(resolve, 1000))
        window.location.href = `/client/reservations/${props.reservation.id}/cancel`
    } catch (error) {
        console.error('Failed to cancel reservation')
    } finally {
        cancelling.value = false
    }
}

const handleReviewSubmit = async (reviewData) => {
    reviewSubmitting.value = true
    try {
        await fetch(`/api/properties/${props.reservation.property.id}/reviews`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(reviewData),
        })
        window.location.reload()
    } catch (error) {
        console.error('Failed to submit review')
    } finally {
        reviewSubmitting.value = false
    }
}

const getDirections = () => {
    if (props.reservation.property?.address) {
        window.open(`https://www.google.com/maps/search/?api=1&query=${encodeURIComponent(props.reservation.property.address)}`, '_blank')
    }
}

const downloadInvoice = () => {
    window.open(`/client/reservations/${props.reservation.id}/invoice`, '_blank')
}
</script>

<template>
    <ClientLayout>
        <div class="space-y-6">
            <!-- Breadcrumb -->
            <nav class="flex items-center gap-2 text-sm text-gray-600">
                <Link href="/client/reservations" class="hover:text-orange-600">My Trips</Link>
                <span>/</span>
                <span class="text-gray-900">Booking #{{ reservation.id }}</span>
            </nav>

            <!-- Header -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <div class="flex items-center gap-3">
                        <h1 class="text-2xl font-bold text-gray-900">{{ reservation.property?.title }}</h1>
                        <span
                            class="px-3 py-1 text-sm font-medium rounded-full"
                            :class="getStatusColor(reservation.status)"
                        >
                            {{ getStatusLabel(reservation.status) }}
                        </span>
                    </div>
                    <p class="text-gray-600 mt-1">
                        <svg class="h-4 w-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                        </svg>
                        {{ reservation.property?.wilaya }}{{ reservation.property?.commune ? `, ${reservation.property?.commune}` : '' }}
                    </p>
                </div>
                <div class="flex items-center gap-2">
                    <button
                        @click="downloadInvoice"
                        class="px-4 py-2 border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 transition-colors"
                    >
                        <svg class="h-4 w-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Download Invoice
                    </button>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Main Content -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Property Images -->
                    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
                        <div class="grid grid-cols-2 gap-1">
                            <img
                                :src="reservation.property?.images?.[0]?.url || reservation.property?.thumbnail_url || '/placeholder-property.jpg'"
                                :alt="reservation.property?.title"
                                class="w-full h-48 sm:h-64 object-cover"
                            />
                            <img
                                :src="reservation.property?.images?.[1]?.url || reservation.property?.thumbnail_url || '/placeholder-property.jpg'"
                                :alt="reservation.property?.title"
                                class="w-full h-48 sm:h-64 object-cover"
                            />
                            <img
                                :src="reservation.property?.images?.[2]?.url || reservation.property?.thumbnail_url || '/placeholder-property.jpg'"
                                :alt="reservation.property?.title"
                                class="w-full h-48 sm:h-64 object-cover"
                            />
                            <img
                                :src="reservation.property?.images?.[3]?.url || reservation.property?.thumbnail_url || '/placeholder-property.jpg'"
                                :alt="reservation.property?.title"
                                class="w-full h-48 sm:h-64 object-cover"
                            />
                        </div>
                    </div>

                    <!-- Booking Details -->
                    <div class="bg-white rounded-xl border border-gray-200 p-6">
                        <h2 class="text-lg font-semibold text-gray-900 mb-4">Booking Details</h2>

                        <div class="space-y-4">
                            <div class="flex items-start gap-4 p-4 bg-gray-50 rounded-xl">
                                <div class="h-12 w-12 bg-orange-100 rounded-xl flex items-center justify-center flex-shrink-0">
                                    <svg class="h-6 w-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                </div>
                                <div class="flex-1">
                                    <p class="text-sm text-gray-500">Check-in</p>
                                    <p class="font-semibold text-gray-900">{{ formatDate(reservation.check_in) }}</p>
                                    <p class="text-sm text-gray-600">From 3:00 PM</p>
                                </div>
                            </div>

                            <div class="flex items-start gap-4 p-4 bg-gray-50 rounded-xl">
                                <div class="h-12 w-12 bg-orange-100 rounded-xl flex items-center justify-center flex-shrink-0">
                                    <svg class="h-6 w-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                </div>
                                <div class="flex-1">
                                    <p class="text-sm text-gray-500">Check-out</p>
                                    <p class="font-semibold text-gray-900">{{ formatDate(reservation.check_out) }}</p>
                                    <p class="text-sm text-gray-600">Until 11:00 AM</p>
                                </div>
                            </div>

                            <div class="flex items-start gap-4 p-4 bg-gray-50 rounded-xl">
                                <div class="h-12 w-12 bg-orange-100 rounded-xl flex items-center justify-center flex-shrink-0">
                                    <svg class="h-6 w-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2a2 2 0 01-2-2v-6a2 2 0 012-2h5l2 2h5l2-2h5a2 2 0 012 2v6a2 2 0 01-2 2z" />
                                    </svg>
                                </div>
                                <div class="flex-1">
                                    <p class="text-sm text-gray-500">Guests</p>
                                    <p class="font-semibold text-gray-900">{{ reservation.guests }} guest{{ reservation.guests > 1 ? 's' : '' }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="mt-4 pt-4 border-t border-gray-200">
                            <button
                                @click="getDirections"
                                class="text-orange-600 hover:underline text-sm font-medium"
                            >
                                Get directions
                            </button>
                        </div>
                    </div>

                    <!-- Host Information -->
                    <div class="bg-white rounded-xl border border-gray-200 p-6">
                        <h2 class="text-lg font-semibold text-gray-900 mb-4">Your Host</h2>

                        <div class="flex items-center gap-4">
                            <div class="h-14 w-14 bg-gradient-to-br from-orange-400 to-orange-600 rounded-full flex items-center justify-center">
                                <span class="text-white font-semibold text-lg">
                                    {{ (reservation.property?.owner?.name || 'O').charAt(0).toUpperCase() }}
                                </span>
                            </div>
                            <div class="flex-1">
                                <p class="font-semibold text-gray-900">{{ reservation.property?.owner?.name || 'Property Owner' }}</p>
                                <p v-if="reservation.property?.owner?.response_rate" class="text-sm text-gray-600">
                                    Responds {{ reservation.property.owner.response_rate }}% of the time
                                </p>
                            </div>
                            <a
                                :href="`tel:${reservation.property?.owner?.phone}`"
                                class="px-4 py-2 bg-orange-500 text-white text-sm font-medium rounded-lg hover:bg-orange-600 transition-colors"
                            >
                                Contact
                            </a>
                        </div>
                    </div>

                    <!-- Review Section -->
                    <div v-if="reservation.status === 'completed'" class="bg-white rounded-xl border border-gray-200 p-6">
                        <h2 class="text-lg font-semibold text-gray-900 mb-4">Your Review</h2>

                        <div v-if="userReview" class="space-y-4">
                            <ReviewCard :review="userReview" />
                            <p class="text-sm text-gray-500">Thank you for your feedback!</p>
                        </div>

                        <ReviewForm
                            v-else
                            :property-id="reservation.property.id"
                            :loading="reviewSubmitting"
                            @submit="handleReviewSubmit"
                        />
                    </div>

                    <!-- Cancellation Policy -->
                    <div v-if="reservation.status === 'confirmed'" class="bg-white rounded-xl border border-gray-200 p-6">
                        <h2 class="text-lg font-semibold text-gray-900 mb-4">Cancellation Policy</h2>

                        <div class="space-y-3">
                            <div class="flex items-start gap-3">
                                <svg class="h-5 w-5 text-green-500 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                </svg>
                                <p class="text-sm text-gray-600">Free cancellation until 48 hours before check-in</p>
                            </div>
                            <div class="flex items-start gap-3">
                                <svg class="h-5 w-5 text-yellow-500 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                </svg>
                                <p class="text-sm text-gray-600">50% refund for cancellations within 48 hours of check-in</p>
                            </div>
                            <div class="flex items-start gap-3">
                                <svg class="h-5 w-5 text-red-500 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                </svg>
                                <p class="text-sm text-gray-600">No refund for cancellations after check-in</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="space-y-6">
                    <!-- Price Summary -->
                    <div class="bg-white rounded-xl border border-gray-200 p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Price Details</h3>

                        <div class="space-y-3">
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">
                                    {{ formatPrice(reservation.price_per_night) }} × {{ nightsCount }} night{{ nightsCount > 1 ? 's' : '' }}
                                </span>
                                <span class="font-medium">{{ formatPrice(reservation.price_per_night * nightsCount) }}</span>
                            </div>
                            <div v-if="reservation.cleaning_fee" class="flex justify-between text-sm">
                                <span class="text-gray-600">Cleaning fee</span>
                                <span class="font-medium">{{ formatPrice(reservation.cleaning_fee) }}</span>
                            </div>
                            <div v-if="reservation.service_fee" class="flex justify-between text-sm">
                                <span class="text-gray-600">Service fee</span>
                                <span class="font-medium">{{ formatPrice(reservation.service_fee) }}</span>
                            </div>
                            <div class="pt-3 border-t border-gray-200 flex justify-between">
                                <span class="font-semibold text-gray-900">Total</span>
                                <span class="text-lg font-bold text-gray-900">{{ formatPrice(reservation.total_price) }}</span>
                            </div>
                        </div>

                        <div class="mt-4 pt-4 border-t border-gray-200">
                            <p class="text-sm text-gray-500">Paid via {{ reservation.payment_method || 'Card' }}</p>
                            <p class="text-xs text-gray-400 mt-1">Paid on {{ formatDate(reservation.created_at) }}</p>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div v-if="reservation.status === 'confirmed'" class="bg-white rounded-xl border border-gray-200 p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Actions</h3>

                        <div class="space-y-3">
                            <a
                                :href="`mailto:${reservation.property?.owner?.email}?subject=Question about my booking #${reservation.id}`"
                                class="block w-full px-4 py-3 text-center border border-gray-300 text-gray-700 font-medium rounded-xl hover:bg-gray-50 transition-colors"
                            >
                                Message Host
                            </a>

                            <button
                                v-if="canCancel"
                                @click="cancelReservation"
                                :disabled="cancelling"
                                class="w-full px-4 py-3 border border-red-300 text-red-600 font-medium rounded-xl hover:bg-red-50 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                            >
                                <span v-if="cancelling">Cancelling...</span>
                                <span v-else>Cancel Reservation</span>
                            </button>

                            <p v-if="!canCancel && reservation.status === 'confirmed'" class="text-xs text-gray-500 text-center">
                                Cancellation is only available until 48 hours before check-in
                            </p>
                        </div>
                    </div>

                    <!-- Safety Info -->
                    <div class="bg-blue-50 rounded-xl border border-blue-200 p-6">
                        <h3 class="text-lg font-semibold text-blue-900 mb-3">Safety Information</h3>

                        <div class="space-y-2 text-sm text-blue-800">
                            <div class="flex items-start gap-2">
                                <svg class="h-5 w-5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                </svg>
                                <p>Verified property by Mbata</p>
                            </div>
                            <div class="flex items-start gap-2">
                                <svg class="h-5 w-5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                </svg>
                                <p>24/7 customer support available</p>
                            </div>
                            <div class="flex items-start gap-2">
                                <svg class="h-5 w-5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                </svg>
                                <p>Secure payment guaranteed</p>
                            </div>
                        </div>

                        <a
                            href="/help"
                            class="mt-4 inline-block text-blue-600 hover:underline text-sm font-medium"
                        >
                            Get help &rarr;
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </ClientLayout>
</template>
