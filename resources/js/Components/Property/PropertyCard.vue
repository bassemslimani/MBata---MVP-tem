<script setup>
import { computed } from 'vue'
import { Link } from '@inertiajs/vue3'

const props = defineProps({
    property: {
        type: Object,
        required: true,
    },
})

const formattedPrice = computed(() => {
    return props.property.price_per_night_dzd?.toLocaleString() || '0'
})

const primaryImage = computed(() => {
    if (props.property.images && props.property.images.length > 0) {
        const primary = props.property.images.find(img => img.is_primary)
        return primary?.image_path || props.property.images[0].image_path
    }
    return 'https://images.unsplash.com/photo-1600596542815-ffad4c1539a9?w=800&h=600&fit=crop'
})

const rating = computed(() => {
    return props.property.average_rating || 0
})
</script>

<template>
    <div class="bg-white rounded-xl shadow-md overflow-hidden hover:shadow-xl transition-all duration-300 group">
        <Link :href="`/properties/${property.id}`" class="block">
            <div class="relative h-56 overflow-hidden">
                <img
                    :src="primaryImage"
                    :alt="property.title"
                    class="h-full w-full object-cover group-hover:scale-105 transition-transform duration-300"
                />
                <div
                    v-if="property.is_verified"
                    class="absolute top-4 left-4 bg-green-500 text-white px-2 py-1 rounded-md text-xs font-medium flex items-center gap-1"
                >
                    <svg class="h-3 w-3" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                    </svg>
                    Verified
                </div>
                <button
                    @click.prevent
                    class="absolute top-4 right-4 p-2 bg-white/80 hover:bg-white rounded-full transition-colors"
                >
                    <svg class="h-5 w-5 text-gray-400 hover:text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                    </svg>
                </button>
            </div>
        </Link>

        <Link :href="`/properties/${property.id}`" class="block">
            <div class="p-5">
                <div class="flex items-start justify-between">
                    <div class="flex-1">
                        <h3 class="text-lg font-semibold text-gray-900 line-clamp-1">
                            {{ property.title }}
                        </h3>
                        <p class="mt-1 text-gray-600 text-sm flex items-center gap-1">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            {{ property.commune?.name }}, {{ property.wilaya?.name }}
                        </p>
                    </div>
                    <div v-if="rating > 0" class="flex items-center gap-1 text-sm">
                        <svg class="h-4 w-4 text-yellow-400 fill-current" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                        </svg>
                        <span class="text-gray-600">{{ rating.toFixed(1) }}</span>
                    </div>
                </div>

                <div class="mt-3 flex items-center gap-4 text-sm text-gray-500">
                    <span v-if="property.guests" class="flex items-center gap-1">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        {{ property.guests }} guests
                    </span>
                    <span v-if="property.bedrooms" class="flex items-center gap-1">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                        </svg>
                        {{ property.bedrooms }} beds
                    </span>
                    <span v-if="property.surface_area" class="flex items-center gap-1">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4" />
                        </svg>
                        {{ property.surface_area }} m²
                    </span>
                </div>

                <div class="mt-4 pt-4 border-t border-gray-100 flex items-center justify-between">
                    <p class="text-lg font-bold text-gray-900">
                        {{ formattedPrice }} DA
                        <span class="text-sm font-normal text-gray-500">/ night</span>
                    </p>
                    <span
                        class="text-xs px-2 py-1 rounded-full"
                        :class="property.is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500'"
                    >
                        {{ property.is_active ? 'Available' : 'Unavailable' }}
                    </span>
                </div>
            </div>
        </Link>
    </div>
</template>
