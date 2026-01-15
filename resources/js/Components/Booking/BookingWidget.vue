<script setup>
import { ref, computed } from 'vue'
import { Link } from '@inertiajs/vue3'
import DateRangePicker from './DateRangePicker.vue'
import GuestSelector from './GuestSelector.vue'
import PriceSummary from './PriceSummary.vue'

const props = defineProps({
    property: {
        type: Object,
        required: true,
    },
    isLoggedIn: {
        type: Boolean,
        default: false,
    },
    initialDates: {
        type: Object,
        default: () => ({ start: '', end: '' }),
    },
    initialGuests: {
        type: Number,
        default: 1,
    },
    loading: {
        type: Boolean,
        default: false,
    },
    priceLoading: {
        type: Boolean,
        default: false,
    },
    availabilityError: {
        type: String,
        default: '',
    },
})

const emit = defineEmits(['check-availability', 'book-now'])

const dates = ref({ ...props.initialDates })
const guests = ref(props.initialGuests)
const checkingAvailability = ref(false)

const availablePrice = ref(null)
const cleaningFee = ref(0)
const serviceFee = ref(0)

const nightsCount = computed(() => {
    if (!dates.value.start || !dates.value.end) return 0
    const start = new Date(dates.value.start)
    const end = new Date(dates.value.end)
    return Math.max(0, Math.ceil((end - start) / (1000 * 60 * 60 * 24)))
})

const canBook = computed(() => {
    return dates.value.start && dates.value.end && nightsCount.value > 0
})

const checkAvailability = async () => {
    if (!canBook.value) return

    checkingAvailability.value = true
    emit('check-availability', {
        propertyId: props.property.id,
        checkIn: dates.value.start,
        checkOut: dates.value.end,
        guests: guests.value,
    })
}

const bookNow = () => {
    if (!canBook.value) return

    emit('book-now', {
        propertyId: props.property.id,
        checkIn: dates.value.start,
        checkOut: dates.value.end,
        guests: guests.value,
        totalPrice: availablePrice.value * nightsCount.value + cleaningFee.value + serviceFee.value,
    })
}

const handleContactOwner = () => {
    // Scroll to contact form or open modal
    emit('contact-owner')
}

const formatPrice = (price) => {
    return new Intl.NumberFormat('fr-DZ').format(price)
}
</script>

<template>
    <div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden sticky top-4">
        <!-- Header -->
        <div class="p-6 border-b border-gray-100">
            <div class="flex items-baseline justify-between">
                <div>
                    <span class="text-2xl font-bold text-gray-900">{{ formatPrice(property.price_per_night_dzd) }} DA</span>
                    <span class="text-gray-500">/ night</span>
                </div>
                <div v-if="property.average_rating" class="flex items-center gap-1 text-sm">
                    <svg class="h-4 w-4 text-yellow-400 fill-current" viewBox="0 0 20 20">
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                    </svg>
                    <span class="font-medium">{{ property.average_rating.toFixed(1) }}</span>
                    <span class="text-gray-400">({ property.reviews_count || 0 })</span>
                </div>
            </div>
        </div>

        <!-- Form -->
        <div class="p-6 space-y-4">
            <!-- Date Range -->
            <DateRangePicker
                v-model="dates"
                :error="availabilityError"
            />

            <!-- Guests -->
            <GuestSelector
                v-model="guests"
                :max-guests="property.max_guests"
            />

            <!-- Check Availability Button -->
            <button
                v-if="!availablePrice"
                type="button"
                :disabled="!canBook || checkingAvailability || loading"
                @click="checkAvailability"
                class="w-full py-3 bg-orange-500 text-white rounded-xl font-semibold hover:bg-orange-600 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
            >
                <span v-if="checkingAvailability">Checking...</span>
                <span v-else>Check Availability</span>
            </button>

            <!-- Price Summary & Book -->
            <div v-if="availablePrice && !loading">
                <PriceSummary
                    :price-per-night="availablePrice"
                    :nights="nightsCount"
                    :cleaning-fee="cleaningFee"
                    :service-fee="serviceFee"
                    :loading="priceLoading"
                />

                <button
                    type="button"
                    :disabled="!canBook || loading"
                    @click="bookNow"
                    class="w-full mt-4 py-4 bg-orange-500 text-white rounded-xl font-semibold hover:bg-orange-600 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                >
                    <span v-if="loading">Processing...</span>
                    <span v-else>Book Now</span>
                </button>
            </div>

            <!-- Alternative Option -->
            <div v-if="!availablePrice && canBook" class="text-center">
                <button
                    type="button"
                    @click="handleContactOwner"
                    class="text-orange-600 hover:text-orange-700 font-medium text-sm"
                >
                    Contact owner instead
                </button>
            </div>

            <!-- Still Thinking Message -->
            <div v-if="canBook" class="text-center text-xs text-gray-500 mt-2">
                You won't be charged yet
            </div>
        </div>

        <!-- Footer Info -->
        <div class="px-6 pb-6">
            <div class="flex items-start gap-3 text-sm text-gray-600">
                <svg class="h-5 w-5 text-green-500 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                </svg>
                <p>Free cancellation for 48 hours</p>
            </div>
        </div>
    </div>
</template>
