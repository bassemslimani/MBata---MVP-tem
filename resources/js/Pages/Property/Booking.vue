<script setup>
import { ref, computed, onMounted } from 'vue'
import { Link, useForm } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import PropertyCard from '@/Components/Property/PropertyCard.vue'
import PriceSummary from '@/Components/Booking/PriceSummary.vue'

const props = defineProps({
    property: {
        type: Object,
        required: true,
    },
    checkIn: {
        type: String,
        required: true,
    },
    checkOut: {
        type: String,
        required: true,
    },
    guests: {
        type: Number,
        default: 1,
    },
    priceBreakdown: {
        type: Object,
        default: () => ({}),
    },
    user: {
        type: Object,
        default: null,
    },
})

const nightsCount = computed(() => {
    const start = new Date(props.checkIn)
    const end = new Date(props.checkOut)
    return Math.max(0, Math.ceil((end - start) / (1000 * 60 * 60 * 24)))
})

const total = computed(() => {
    const base = (props.priceBreakdown.price_per_night || props.property.price_per_night_dzd) * nightsCount.value
    return base + (props.priceBreakdown.cleaning_fee || 0) + (props.priceBreakdown.service_fee || 0)
})

const form = useForm({
    property_id: props.property.id,
    check_in: props.checkIn,
    check_out: props.checkOut,
    guests: props.guests,
    first_name: props.user?.first_name || '',
    last_name: props.user?.last_name || '',
    email: props.user?.email || '',
    phone: props.user?.phone || '',
    message_to_host: '',
    payment_method: 'card',
    card_name: '',
    card_number: '',
    card_expiry: '',
    card_cvv: '',
    accept_terms: false,
})

const errors = ref({})
const processing = ref(false)
const stripeReady = ref(false)

const paymentMethods = [
    { id: 'card', label: 'Credit/Debit Card', icon: 'credit-card' },
    { id: ' ccp', label: 'CCP (Algeria)', icon: 'bank' },
    { id: 'cash', label: 'Pay on Arrival', icon: 'cash' },
]

const submitBooking = async () => {
    // Validate form
    const newErrors = {}

    if (!form.first_name.trim()) newErrors.first_name = 'First name is required'
    if (!form.last_name.trim()) newErrors.last_name = 'Last name is required'
    if (!form.email.trim()) newErrors.email = 'Email is required'
    if (!form.phone.trim()) newErrors.phone = 'Phone number is required'
    if (!form.accept_terms) newErrors.accept_terms = 'You must accept the terms'

    if (form.payment_method === 'card') {
        if (!form.card_name.trim()) newErrors.card_name = 'Cardholder name is required'
        if (!form.card_number.trim()) newErrors.card_number = 'Card number is required'
        if (!form.card_expiry) newErrors.card_expiry = 'Expiry date is required'
        if (!form.card_cvv) newErrors.card_cvv = 'CVV is required'
    }

    errors.value = newErrors

    if (Object.keys(newErrors).length > 0) {
        return
    }

    processing.value = true

    try {
        // Submit booking via Inertia
        form.post('/bookings', {
            onSuccess: () => {
                // Redirect to success page
            },
            onError: (errs) => {
                errors.value = errs
            },
            onFinish: () => {
                processing.value = false
            },
        })
    } catch (error) {
        processing.value = false
    }
}

const formatCardNumber = (value) => {
    const v = value.replace(/\s+/g, '').replace(/[^0-9]/gi, '')
    const matches = v.match(/\d{4,16}/g)
    const match = (matches && matches[0]) || ''
    const parts = []

    for (let i = 0, len = match.length; i < len; i += 4) {
        parts.push(match.substring(i, i + 4))
    }

    if (parts.length) {
        return parts.join(' ')
    } else {
        return v
    }
}

const handleCardInput = (e) => {
    form.card_number = formatCardNumber(e.target.value)
}

const formatExpiry = (e) => {
    let value = e.target.value.replace(/\D/g, '')

    if (value.length >= 2) {
        value = value.substring(0, 2) + '/' + value.substring(2)
    }

    form.card_expiry = value
}

const checkInDate = computed(() => {
    const date = new Date(props.checkIn)
    return date.toLocaleDateString('en-US', { weekday: 'short', month: 'short', day: 'numeric' })
})

const checkOutDate = computed(() => {
    const date = new Date(props.checkOut)
    return date.toLocaleDateString('en-US', { weekday: 'short', month: 'short', day: 'numeric' })
})
</script>

<template>
    <AppLayout>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <!-- Breadcrumb -->
            <nav class="flex items-center gap-2 text-sm text-gray-600 mb-6">
                <Link href="/properties" class="hover:text-orange-600">Properties</Link>
                <span>/</span>
                <Link :href="`/properties/${property.id}`" class="hover:text-orange-600 truncate max-w-xs">{{ property.title }}</Link>
                <span>/</span>
                <span class="text-gray-900">Book</span>
            </nav>

            <h1 class="text-2xl font-bold text-gray-900 mb-8">Confirm and pay</h1>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Main Form -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Trip Details -->
                    <div class="bg-white rounded-xl border border-gray-200 p-6">
                        <h2 class="text-lg font-semibold text-gray-900 mb-4">Your trip</h2>

                        <div class="flex items-start gap-4 p-4 bg-gray-50 rounded-xl">
                            <img
                                :src="property.thumbnail_url || property.images?.[0]?.url || '/placeholder-property.jpg'"
                                :alt="property.title"
                                class="w-24 h-24 object-cover rounded-lg"
                            />
                            <div class="flex-1">
                                <h3 class="font-semibold text-gray-900">{{ property.title }}</h3>
                                <p class="text-sm text-gray-600">{{ property.wilaya }}{{ property.commune ? `, ${property.commune}` : '' }}</p>
                                <div class="flex items-center gap-4 mt-2 text-sm">
                                    <div class="flex items-center gap-1">
                                        <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                        <div>
                                            <p class="text-gray-900 font-medium">{{ checkInDate }}</p>
                                            <p class="text-gray-500 text-xs">Check-in</p>
                                        </div>
                                    </div>
                                    <svg class="h-4 w-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                                    </svg>
                                    <div class="flex items-center gap-1">
                                        <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                        <div>
                                            <p class="text-gray-900 font-medium">{{ checkOutDate }}</p>
                                            <p class="text-gray-500 text-xs">Check-out</p>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-1">
                                        <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2a2 2 0 01-2-2v-6a2 2 0 012-2h5l2 2h5l2-2h5a2 2 0 012 2v6a2 2 0 01-2 2z" />
                                        </svg>
                                        <div>
                                            <p class="text-gray-900 font-medium">{{ guests }} guest{{ guests > 1 ? 's' : '' }}</p>
                                            <p class="text-gray-500 text-xs">Guests</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mt-4 pt-4 border-t border-gray-200">
                            <Link
                                :href="`/properties/${property.id}`"
                                class="text-orange-600 hover:underline text-sm font-medium"
                            >
                                Change dates or guests
                            </Link>
                        </div>
                    </div>

                    <!-- Guest Information -->
                    <div class="bg-white rounded-xl border border-gray-200 p-6">
                        <h2 class="text-lg font-semibold text-gray-900 mb-4">
                            Guest information
                            <span class="text-sm font-normal text-gray-500">(Required for booking)</span>
                        </h2>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">First name</label>
                                <input
                                    v-model="form.first_name"
                                    type="text"
                                    class="w-full px-4 py-3 border rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500"
                                    :class="errors.first_name ? 'border-red-500' : 'border-gray-300'"
                                />
                                <p v-if="errors.first_name" class="text-xs text-red-600 mt-1">{{ errors.first_name }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Last name</label>
                                <input
                                    v-model="form.last_name"
                                    type="text"
                                    class="w-full px-4 py-3 border rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500"
                                    :class="errors.last_name ? 'border-red-500' : 'border-gray-300'"
                                />
                                <p v-if="errors.last_name" class="text-xs text-red-600 mt-1">{{ errors.last_name }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                                <input
                                    v-model="form.email"
                                    type="email"
                                    class="w-full px-4 py-3 border rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500"
                                    :class="errors.email ? 'border-red-500' : 'border-gray-300'"
                                />
                                <p v-if="errors.email" class="text-xs text-red-600 mt-1">{{ errors.email }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Phone</label>
                                <input
                                    v-model="form.phone"
                                    type="tel"
                                    placeholder="+213 XXX XXX XXX"
                                    class="w-full px-4 py-3 border rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500"
                                    :class="errors.phone ? 'border-red-500' : 'border-gray-300'"
                                />
                                <p v-if="errors.phone" class="text-xs text-red-600 mt-1">{{ errors.phone }}</p>
                            </div>
                        </div>

                        <div class="mt-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Message to host (optional)</label>
                            <textarea
                                v-model="form.message_to_host"
                                rows="3"
                                placeholder="Let the host know your arrival time or any special requests..."
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 resize-none"
                            ></textarea>
                        </div>
                    </div>

                    <!-- Payment Method -->
                    <div class="bg-white rounded-xl border border-gray-200 p-6">
                        <h2 class="text-lg font-semibold text-gray-900 mb-4">Pay with</h2>

                        <div class="space-y-3">
                            <button
                                v-for="method in paymentMethods"
                                :key="method.id"
                                type="button"
                                @click="form.payment_method = method.id"
                                class="w-full flex items-center justify-between p-4 border rounded-xl transition-colors"
                                :class="form.payment_method === method.id
                                    ? 'border-orange-500 bg-orange-50 ring-1 ring-orange-500'
                                    : 'border-gray-300 hover:border-gray-400'"
                            >
                                <div class="flex items-center gap-3">
                                    <div
                                        class="h-5 w-5 rounded-full border-2 flex items-center justify-center"
                                        :class="form.payment_method === method.id
                                            ? 'border-orange-500 bg-orange-500'
                                            : 'border-gray-300'"
                                    >
                                        <div
                                            v-if="form.payment_method === method.id"
                                            class="h-2 w-2 bg-white rounded-full"
                                        ></div>
                                    </div>
                                    <span class="font-medium text-gray-900">{{ method.label }}</span>
                                </div>
                            </button>
                        </div>

                        <!-- Card Details -->
                        <div v-if="form.payment_method === 'card'" class="mt-6 space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Name on card</label>
                                <input
                                    v-model="form.card_name"
                                    type="text"
                                    placeholder="Name as shown on card"
                                    class="w-full px-4 py-3 border rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500"
                                    :class="errors.card_name ? 'border-red-500' : 'border-gray-300'"
                                />
                                <p v-if="errors.card_name" class="text-xs text-red-600 mt-1">{{ errors.card_name }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Card number</label>
                                <div class="relative">
                                    <input
                                        :value="form.card_number"
                                        @input="handleCardInput"
                                        type="text"
                                        placeholder="1234 5678 9012 3456"
                                        maxlength="19"
                                        class="w-full px-4 py-3 pr-12 border rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500"
                                        :class="errors.card_number ? 'border-red-500' : 'border-gray-300'"
                                    />
                                    <div class="absolute right-4 top-1/2 -translate-y-1/2 flex items-center gap-1">
                                        <svg class="h-6 w-8" viewBox="0 0 32 20" fill="none">
                                            <rect width="32" height="20" rx="2" fill="#1434CB"/>
                                            <text x="3" y="13" fill="white" font-size="7" font-weight="bold">VISA</text>
                                        </svg>
                                        <svg class="h-6 w-8" viewBox="0 0 32 20" fill="none">
                                            <rect width="32" height="20" rx="2" fill="#EB001B"/>
                                            <circle cx="12" cy="10" r="6" fill="#EB001B"/>
                                            <circle cx="20" cy="10" r="6" fill="#F79E1B"/>
                                            <path d="M16 5.5a6 6 0 000 9" fill="#FF5F00"/>
                                        </svg>
                                    </div>
                                </div>
                                <p v-if="errors.card_number" class="text-xs text-red-600 mt-1">{{ errors.card_number }}</p>
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Expiry date</label>
                                    <input
                                        :value="form.card_expiry"
                                        @input="formatExpiry"
                                        type="text"
                                        placeholder="MM/YY"
                                        maxlength="5"
                                        class="w-full px-4 py-3 border rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500"
                                        :class="errors.card_expiry ? 'border-red-500' : 'border-gray-300'"
                                    />
                                    <p v-if="errors.card_expiry" class="text-xs text-red-600 mt-1">{{ errors.card_expiry }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">CVV</label>
                                    <div class="relative">
                                        <input
                                            v-model="form.card_cvv"
                                            type="text"
                                            placeholder="123"
                                            maxlength="4"
                                            class="w-full px-4 py-3 pr-10 border rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500"
                                            :class="errors.card_cvv ? 'border-red-500' : 'border-gray-300'"
                                        />
                                        <svg class="absolute right-3 top-1/2 -translate-y-1/2 h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </div>
                                    <p v-if="errors.card_cvv" class="text-xs text-red-600 mt-1">{{ errors.card_cvv }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- CCP Payment -->
                        <div v-if="form.payment_method === 'ccp'" class="mt-6 space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">CCP Account Number</label>
                                <input
                                    type="text"
                                    placeholder="XXXXXXXXXX"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500"
                                />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Account Name</label>
                                <input
                                    type="text"
                                    placeholder="Name on account"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500"
                                />
                            </div>
                            <p class="text-sm text-gray-500">You will receive payment instructions to complete your booking via CCP.</p>
                        </div>

                        <!-- Cash on Arrival -->
                        <div v-if="form.payment_method === 'cash'" class="mt-6 p-4 bg-yellow-50 rounded-xl">
                            <div class="flex items-start gap-3">
                                <svg class="h-6 w-6 text-yellow-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                                <div>
                                    <p class="font-medium text-yellow-800">Pay on arrival</p>
                                    <p class="text-sm text-yellow-700 mt-1">You'll pay the full amount when you arrive at the property. Please bring cash.</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Cancellation Policy -->
                    <div class="bg-white rounded-xl border border-gray-200 p-6">
                        <h2 class="text-lg font-semibold text-gray-900 mb-4">Cancellation policy</h2>

                        <div class="space-y-4">
                            <div class="flex items-start gap-3">
                                <svg class="h-5 w-5 text-green-500 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                </svg>
                                <div>
                                    <p class="font-medium text-gray-900">Free cancellation for 48 hours</p>
                                    <p class="text-sm text-gray-600">Cancel before check-in on Jan 12 for a full refund</p>
                                </div>
                            </div>
                            <div class="flex items-start gap-3">
                                <svg class="h-5 w-5 text-gray-400 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                </svg>
                                <div>
                                    <p class="font-medium text-gray-900">After that, cancel anytime before check-in and get a 50% refund</p>
                                </div>
                            </div>
                            <div class="flex items-start gap-3">
                                <svg class="h-5 w-5 text-red-500 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                </svg>
                                <div>
                                    <p class="font-medium text-gray-900">No refund if cancelled after check-in</p>
                                </div>
                            </div>
                        </div>

                        <a href="#" class="text-orange-600 hover:underline text-sm font-medium mt-4 inline-block">
                            Learn more about cancellation policy
                        </a>
                    </div>

                    <!-- Terms -->
                    <div class="flex items-start gap-3">
                        <input
                            v-model="form.accept_terms"
                            type="checkbox"
                            id="terms"
                            class="mt-1 h-4 w-4 text-orange-500 border-gray-300 rounded focus:ring-orange-500"
                        />
                        <label for="terms" class="text-sm text-gray-600">
                            I agree to the
                            <a href="#" class="text-orange-600 hover:underline">House Rules</a>,
                            <a href="#" class="text-orange-600 hover:underline">Terms of Service</a>, and
                            <a href="#" class="text-orange-600 hover:underline">Cancellation Policy</a>.
                        </label>
                    </div>
                    <p v-if="errors.accept_terms" class="text-xs text-red-600">{{ errors.accept_terms }}</p>

                    <!-- Submit Button -->
                    <button
                        @click="submitBooking"
                        :disabled="processing"
                        class="w-full py-4 bg-orange-500 text-white rounded-xl font-semibold hover:bg-orange-600 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                    >
                        <span v-if="processing">Processing...</span>
                        <span v-else>Confirm and pay {{ new Intl.NumberFormat('fr-DZ').format(total) }} DA</span>
                    </button>

                    <p class="text-xs text-gray-500 text-center">
                        By clicking the button, you agree to our Terms of Service and Privacy Policy.
                    </p>
                </div>

                <!-- Price Summary -->
                <div class="lg:col-span-1">
                    <div class="lg:sticky lg:top-6">
                        <div class="bg-white rounded-xl border border-gray-200 p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Price details</h3>

                            <PriceSummary
                                :price-per-night="priceBreakdown.price_per_night || property.price_per_night_dzd"
                                :nights="nightsCount"
                                :cleaning-fee="priceBreakdown.cleaning_fee || 0"
                                :service-fee="priceBreakdown.service_fee || 0"
                            />

                            <!-- Security Badge -->
                            <div class="mt-6 pt-6 border-t border-gray-200">
                                <div class="flex items-center gap-2 text-sm text-gray-600">
                                    <svg class="h-5 w-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                    </svg>
                                    <span>Secure payment powered by Stripe</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
