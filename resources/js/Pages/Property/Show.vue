<script setup>
import { ref, computed } from 'vue'
import { Link } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import PropertyGallery from '@/Components/Property/PropertyGallery.vue'
import AmenitiesList from '@/Components/Property/AmenitiesList.vue'
import BookingWidget from '@/Components/Booking/BookingWidget.vue'
import ReviewCard from '@/Components/Review/ReviewCard.vue'
import ReviewForm from '@/Components/Review/ReviewForm.vue'
import PropertyCard from '@/Components/Property/PropertyCard.vue'

const props = defineProps({
    property: {
        type: Object,
        required: true,
    },
    similarProperties: {
        type: Array,
        default: () => [],
    },
    reviews: {
        type: Array,
        default: () => [],
    },
    userReview: {
        type: Object,
        default: null,
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
})

const activeTab = ref('overview')
const bookingLoading = ref(false)
const availabilityLoading = ref(false)
const availabilityError = ref('')
const availablePrice = ref(null)
const reviewSubmitting = ref(false)

const tabs = [
    { id: 'overview', label: 'Overview' },
    { id: 'amenities', label: 'Amenities' },
    { id: 'reviews', label: `Reviews (${props.reviews.length})` },
    { id: 'location', label: 'Location' },
]

const isFavorited = ref(false)

const ratingBreakdown = computed(() => {
    const ratings = props.reviews.map(r => r.rating)
    if (ratings.length === 0) return null

    const avg = ratings.reduce((a, b) => a + b, 0) / ratings.length
    const counts = [0, 0, 0, 0, 0]
    ratings.forEach(r => counts[r - 1]++)

    return { average: avg, counts, total: ratings.length }
})

const checkAvailability = async (data) => {
    availabilityLoading.value = true
    availabilityError.value = ''

    try {
        // API call would go here
        const response = await fetch(`/api/properties/${data.propertyId}/availability`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                check_in: data.checkIn,
                check_out: data.checkOut,
                guests: data.guests,
            }),
        })
        const result = await response.json()

        if (result.available) {
            availablePrice.value = result.price
        } else {
            availabilityError.value = result.message || 'Dates not available'
        }
    } catch (error) {
        availabilityError.value = 'Unable to check availability'
    } finally {
        availabilityLoading.value = false
    }
}

const handleBookNow = (bookingData) => {
    // Navigate to booking page with data
    window.location.href = `/properties/${props.property.id}/book?${new URLSearchParams({
        check_in: bookingData.checkIn,
        check_out: bookingData.checkOut,
        guests: bookingData.guests,
    })}`
}

const toggleFavorite = async () => {
    if (!props.isLoggedIn) {
        window.location.href = '/login'
        return
    }

    try {
        const response = await fetch(`/api/properties/${props.property.id}/favorite`, {
            method: 'POST',
        })
        isFavorited.value = !isFavorited.value
    } catch (error) {
        console.error('Failed to toggle favorite')
    }
}

const handleReviewSubmit = async (reviewData) => {
    reviewSubmitting.value = true

    try {
        await fetch(`/api/properties/${props.property.id}/reviews`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(reviewData),
        })
        // Reload page to show new review
        window.location.reload()
    } catch (error) {
        console.error('Failed to submit review')
    } finally {
        reviewSubmitting.value = false
    }
}

const contactHost = () => {
    if (!props.isLoggedIn) {
        window.location.href = '/login?redirect=' + encodeURIComponent(window.location.pathname + '#contact')
        return
    }
    document.getElementById('contact-form')?.scrollIntoView({ behavior: 'smooth' })
}

const contactMessage = ref('')
const contactSending = ref(false)

const sendContactMessage = async () => {
    if (!contactMessage.value.trim()) return

    contactSending.value = true
    try {
        await fetch(`/api/properties/${props.property.id}/contact`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ message: contactMessage.value }),
        })
        contactMessage.value = ''
        alert('Message sent successfully!')
    } catch (error) {
        alert('Failed to send message')
    } finally {
        contactSending.value = false
    }
}
</script>

<template>
    <AppLayout>
        <div class="bg-white">
            <!-- Gallery -->
            <PropertyGallery
                :images="property.images || []"
                :title="property.title"
            />

            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="py-8 lg:py-12">
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-12">
                        <!-- Main Content -->
                        <div class="lg:col-span-2 space-y-10">
                            <!-- Header -->
                            <div>
                                <div class="flex items-start justify-between mb-4">
                                    <div>
                                        <h1 class="text-2xl lg:text-3xl font-bold text-gray-900 mb-2">
                                            {{ property.title }}
                                        </h1>
                                        <div class="flex items-center gap-2 text-sm text-gray-600">
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                            </svg>
                                            <span>{{ property.wilaya }}{{ property.commune ? `, ${property.commune}` : '' }}</span>
                                            <span class="text-gray-400">•</span>
                                            <button
                                                @click="activeTab = 'location'"
                                                class="text-orange-600 hover:underline"
                                            >
                                                Show on map
                                            </button>
                                        </div>
                                    </div>
                                    <button
                                        @click="toggleFavorite"
                                        class="p-2 rounded-full hover:bg-gray-100 transition-colors"
                                        :class="isFavorited ? 'text-red-500' : 'text-gray-400 hover:text-red-500'"
                                    >
                                        <svg class="h-7 w-7" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                                        </svg>
                                    </button>
                                </div>

                                <!-- Badges -->
                                <div class="flex flex-wrap gap-2 mb-4">
                                    <span v-if="property.is_new" class="px-3 py-1 bg-green-100 text-green-700 text-xs font-medium rounded-full">
                                        New Listing
                                    </span>
                                    <span v-if="property.superhost" class="px-3 py-1 bg-purple-100 text-purple-700 text-xs font-medium rounded-full">
                                        Superhost
                                    </span>
                                    <span class="px-3 py-1 bg-blue-100 text-blue-700 text-xs font-medium rounded-full">
                                        Instant Book
                                    </span>
                                </div>

                                <!-- Rating & Reviews -->
                                <div v-if="property.average_rating" class="flex items-center gap-2">
                                    <div class="flex items-center gap-1">
                                        <svg class="h-4 w-4 text-yellow-400 fill-current" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                        </svg>
                                        <span class="font-medium">{{ property.average_rating.toFixed(2) }}</span>
                                    </div>
                                    <span class="text-gray-600">•</span>
                                    <Link :href="`#reviews`" class="text-orange-600 hover:underline font-medium">
                                        {{ property.reviews_count }} review{{ property.reviews_count !== 1 ? 's' : '' }}
                                    </Link>
                                </div>
                            </div>

                            <!-- Host Info -->
                            <div class="border-b border-gray-200 pb-8">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <h3 class="text-xl font-semibold text-gray-900 mb-1">
                                            Hosted by {{ property.owner?.name || 'Property Owner' }}
                                        </h3>
                                        <div class="flex items-center gap-3 text-sm text-gray-600">
                                            <span v-if="property.owner?.joined_at">Joined {{ new Date(property.owner.joined_at).toLocaleDateString() }}</span>
                                            <span v-if="property.is_superhost" class="text-purple-600">Superhost</span>
                                        </div>
                                    </div>
                                    <div class="h-14 w-14 bg-gradient-to-br from-orange-400 to-orange-600 rounded-full flex items-center justify-center">
                                        <span class="text-white font-semibold text-lg">
                                            {{ (property.owner?.name || 'O').charAt(0).toUpperCase() }}
                                        </span>
                                    </div>
                                </div>
                                <button
                                    @click="contactHost"
                                    class="mt-4 px-6 py-3 border border-gray-300 rounded-xl font-medium hover:bg-gray-50 transition-colors"
                                >
                                    Contact Host
                                </button>
                            </div>

                            <!-- Tabs Navigation -->
                            <div>
                                <div class="border-b border-gray-200 -mb-px">
                                    <nav class="flex gap-8">
                                        <button
                                            v-for="tab in tabs"
                                            :key="tab.id"
                                            @click="activeTab = tab.id"
                                            class="pb-4 px-1 border-b-2 font-medium transition-colors"
                                            :class="activeTab === tab.id
                                                ? 'border-orange-500 text-orange-600'
                                                : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                                        >
                                            {{ tab.label }}
                                        </button>
                                    </nav>
                                </div>

                                <!-- Tab Content -->
                                <div class="py-6">
                                    <!-- Overview Tab -->
                                    <div v-if="activeTab === 'overview'" class="space-y-6">
                                        <div>
                                            <h3 class="text-lg font-semibold text-gray-900 mb-3">About this place</h3>
                                            <p class="text-gray-700 leading-relaxed">{{ property.description }}</p>
                                        </div>

                                        <!-- Quick Info -->
                                        <div class="grid grid-cols-2 gap-4 py-4 border-y border-gray-200">
                                            <div class="flex items-center gap-3">
                                                <svg class="h-5 w-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                                                </svg>
                                                <div>
                                                    <p class="font-medium text-gray-900">{{ property.property_type || 'Entire place' }}</p>
                                                    <p class="text-sm text-gray-500">Property type</p>
                                                </div>
                                            </div>
                                            <div class="flex items-center gap-3">
                                                <svg class="h-5 w-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                                </svg>
                                                <div>
                                                    <p class="font-medium text-gray-900">Free cancellation</p>
                                                    <p class="text-sm text-gray-500">48 hours notice</p>
                                                </div>
                                            </div>
                                            <div class="flex items-center gap-3">
                                                <svg class="h-5 w-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                                <div>
                                                    <p class="font-medium text-gray-900">Self check-in</p>
                                                    <p class="text-sm text-gray-500">Key pad available</p>
                                                </div>
                                            </div>
                                            <div class="flex items-center gap-3">
                                                <svg class="h-5 w-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                                </svg>
                                                <div>
                                                    <p class="font-medium text-gray-900">Verified property</p>
                                                    <p class="text-sm text-gray-500">By Mbata team</p>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Amenities Preview -->
                                        <div>
                                            <h3 class="text-lg font-semibold text-gray-900 mb-3">What this place offers</h3>
                                            <AmenitiesList
                                                :amenities="property.amenities || []"
                                                :limit="8"
                                            />
                                            <button
                                                @click="activeTab = 'amenities'"
                                                class="mt-4 text-orange-600 hover:underline font-medium"
                                            >
                                                See all amenities
                                            </button>
                                        </div>
                                    </div>

                                    <!-- Amenities Tab -->
                                    <div v-if="activeTab === 'amenities'">
                                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Amenities</h3>
                                        <AmenitiesList :amenities="property.amenities || []" />
                                    </div>

                                    <!-- Reviews Tab -->
                                    <div v-if="activeTab === 'reviews'" id="reviews">
                                        <div v-if="ratingBreakdown" class="mb-8">
                                            <h3 class="text-lg font-semibold text-gray-900 mb-4">
                                                {{ ratingBreakdown.total }} Review{{ ratingBreakdown.total !== 1 ? 's' : '' }}
                                            </h3>
                                            <div class="flex items-center gap-6 p-6 bg-gray-50 rounded-xl">
                                                <div class="text-center">
                                                    <p class="text-4xl font-bold text-gray-900">{{ ratingBreakdown.average.toFixed(1) }}</p>
                                                    <div class="flex justify-center my-1">
                                                        <svg class="h-5 w-5 text-yellow-400 fill-current" viewBox="0 0 20 20">
                                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                                        </svg>
                                                    </div>
                                                </div>
                                                <div class="flex-1 space-y-2">
                                                    <div v-for="(count, idx) in ratingBreakdown.counts.reverse()" :key="idx" class="flex items-center gap-2">
                                                        <span class="text-sm text-gray-600 w-6">{{ 5 - idx }}</span>
                                                        <div class="flex-1 h-2 bg-gray-200 rounded-full overflow-hidden">
                                                            <div
                                                                class="h-full bg-orange-500 rounded-full"
                                                                :style="{ width: `${(count / ratingBreakdown.total) * 100}%` }"
                                                            ></div>
                                                        </div>
                                                        <span class="text-sm text-gray-500 w-8">{{ count }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Reviews List -->
                                        <div class="space-y-4 mb-8">
                                            <ReviewCard
                                                v-for="review in reviews"
                                                :key="review.id"
                                                :review="review"
                                            />
                                        </div>

                                        <!-- Review Form -->
                                        <ReviewForm
                                            v-if="isLoggedIn"
                                            :property-id="property.id"
                                            :existing-review="userReview"
                                            :loading="reviewSubmitting"
                                            @submit="handleReviewSubmit"
                                        />
                                        <div v-else class="bg-gray-50 rounded-xl p-6 text-center">
                                            <p class="text-gray-600 mb-4">Log in to leave a review</p>
                                            <Link
                                                href="/login"
                                                class="inline-block px-6 py-2.5 bg-orange-500 text-white rounded-xl font-medium hover:bg-orange-600 transition-colors"
                                            >
                                                Log In
                                            </Link>
                                        </div>
                                    </div>

                                    <!-- Location Tab -->
                                    <div v-if="activeTab === 'location'">
                                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Location</h3>
                                        <div class="bg-gray-100 rounded-xl h-80 flex items-center justify-center">
                                            <div class="text-center text-gray-500">
                                                <svg class="h-12 w-12 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7" />
                                                </svg>
                                                <p>{{ property.wilaya }}{{ property.commune ? `, ${property.commune}` : '' }}</p>
                                                <p class="text-sm mt-1">Exact location provided after booking</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Contact Host Section -->
                            <div id="contact-form" class="border-t border-gray-200 pt-8">
                                <h3 class="text-lg font-semibold text-gray-900 mb-4">Contact the Host</h3>
                                <div v-if="isLoggedIn" class="bg-gray-50 rounded-xl p-6">
                                    <textarea
                                        v-model="contactMessage"
                                        rows="4"
                                        placeholder="Hi, I'm interested in your property. Is it available for my dates?"
                                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 resize-none"
                                    ></textarea>
                                    <div class="flex justify-end mt-3">
                                        <button
                                            @click="sendContactMessage"
                                            :disabled="!contactMessage.trim() || contactSending"
                                            class="px-6 py-2.5 bg-orange-500 text-white rounded-xl font-medium hover:bg-orange-600 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                                        >
                                            <span v-if="contactSending">Sending...</span>
                                            <span v-else>Send Message</span>
                                        </button>
                                    </div>
                                </div>
                                <div v-else class="bg-gray-50 rounded-xl p-6 text-center">
                                    <p class="text-gray-600 mb-4">Log in to contact the host</p>
                                    <Link
                                        href="/login"
                                        class="inline-block px-6 py-2.5 bg-orange-500 text-white rounded-xl font-medium hover:bg-orange-600 transition-colors"
                                    >
                                        Log In
                                    </Link>
                                </div>
                            </div>
                        </div>

                        <!-- Sidebar - Booking Widget -->
                        <div class="lg:col-span-1">
                            <div class="lg:sticky lg:top-6">
                                <BookingWidget
                                    :property="property"
                                    :is-logged-in="isLoggedIn"
                                    :initial-dates="initialDates"
                                    :initial-guests="initialGuests"
                                    :loading="bookingLoading"
                                    :price-loading="availabilityLoading"
                                    :availability-error="availabilityError"
                                    @check-availability="checkAvailability"
                                    @book-now="handleBookNow"
                                    @contact-owner="contactHost"
                                />
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Similar Properties -->
                <div v-if="similarProperties.length > 0" class="py-12 border-t border-gray-200">
                    <h2 class="text-2xl font-bold text-gray-900 mb-6">Similar Properties</h2>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                        <PropertyCard
                            v-for="similarProperty in similarProperties"
                            :key="similarProperty.id"
                            :property="similarProperty"
                        />
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
