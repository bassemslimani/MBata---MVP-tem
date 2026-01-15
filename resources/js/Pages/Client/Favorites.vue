<script setup>
import { ref } from 'vue'
import { Link } from '@inertiajs/vue3'
import ClientLayout from '@/Layouts/ClientLayout.vue'

const props = defineProps({
    favorites: {
        type: Array,
        default: () => [],
    },
})

const removing = ref(null)

const formatPrice = (price) => {
    return new Intl.NumberFormat('fr-DZ').format(price)
}

const removeFavorite = async (propertyId) => {
    removing.value = propertyId
    try {
        await fetch(`/api/properties/${propertyId}/favorite`, {
            method: 'DELETE',
        })
        // Reload page to update list
        window.location.reload()
    } catch (error) {
        console.error('Failed to remove favorite')
    } finally {
        removing.value = null
    }
}
</script>

<template>
    <ClientLayout>
        <div class="space-y-6">
            <!-- Header -->
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Favorites</h1>
                <p class="text-gray-600 mt-1">{{ favorites.length }} property{{ favorites.length !== 1 ? 's' : '' }} saved</p>
            </div>

            <!-- Favorites Grid -->
            <div v-if="favorites.length > 0" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                <div
                    v-for="favorite in favorites"
                    :key="favorite.property.id"
                    class="group bg-white rounded-xl border border-gray-200 overflow-hidden hover:shadow-lg transition-shadow"
                >
                    <div class="relative">
                        <Link :href="`/properties/${favorite.property.id}`" class="block">
                            <img
                                :src="favorite.property.thumbnail_url || favorite.property.images?.[0]?.url || '/placeholder-property.jpg'"
                                :alt="favorite.property.title"
                                class="w-full h-52 object-cover group-hover:scale-105 transition-transform duration-300"
                            />
                        </Link>

                        <!-- Remove Button -->
                        <button
                            @click="removeFavorite(favorite.property.id)"
                            :disabled="removing === favorite.property.id"
                            class="absolute top-3 right-3 h-9 w-9 bg-white rounded-full flex items-center justify-center shadow-md hover:scale-110 transition-transform disabled:opacity-50"
                        >
                            <svg v-if="removing === favorite.property.id" class="h-5 w-5 text-gray-400 animate-spin" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <svg v-else class="h-5 w-5 text-red-500 fill-current" viewBox="0 0 24 24">
                                <path d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                            </svg>
                        </button>

                        <!-- Rating Badge -->
                        <div v-if="favorite.property.average_rating" class="absolute bottom-3 left-3 px-2 py-1 bg-white/90 backdrop-blur rounded-lg flex items-center gap-1">
                            <svg class="h-3 w-3 text-yellow-400 fill-current" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                            </svg>
                            <span class="text-xs font-medium">{{ favorite.property.average_rating.toFixed(1) }}</span>
                        </div>
                    </div>

                    <div class="p-4">
                        <div class="flex items-start justify-between gap-2">
                            <Link
                                :href="`/properties/${favorite.property.id}`"
                                class="font-semibold text-gray-900 hover:text-orange-600 line-clamp-1"
                            >
                                {{ favorite.property.title }}
                            </Link>
                        </div>

                        <p class="text-sm text-gray-600 mt-1">
                            {{ favorite.property.wilaya }}{{ favorite.property.commune ? `, ${favorite.property.commune}` : '' }}
                        </p>

                        <div class="flex items-center gap-2 mt-2 text-sm text-gray-500">
                            <span>{{ favorite.property.property_type || 'Apartment' }}</span>
                            <span>•</span>
                            <span>{{ favorite.property.max_guests }} guests</span>
                        </div>

                        <div class="flex items-center justify-between mt-3 pt-3 border-t border-gray-100">
                            <div>
                                <span class="text-lg font-bold text-gray-900">{{ formatPrice(favorite.property.price_per_night_dzd) }}</span>
                                <span class="text-gray-500"> DA</span>
                                <span class="text-sm text-gray-500">/ night</span>
                            </div>
                            <span class="text-xs text-gray-400">Saved {{ new Date(favorite.created_at).toLocaleDateString() }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Empty State -->
            <div v-else class="bg-white rounded-xl border border-gray-200 p-12 text-center">
                <div class="h-20 w-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="h-10 w-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">No favorites yet</h3>
                <p class="text-gray-600 mb-6 max-w-md mx-auto">
                    Save properties you love by clicking the heart icon. They'll appear here for easy access.
                </p>
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

            <!-- Quick Links -->
            <div v-if="favorites.length > 0" class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <Link
                    href="/properties"
                    class="bg-white rounded-xl border border-gray-200 p-4 hover:border-orange-300 hover:shadow-md transition-all flex items-center gap-3"
                >
                    <div class="h-10 w-10 bg-orange-100 rounded-lg flex items-center justify-center">
                        <svg class="h-5 w-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                    <div>
                        <p class="font-medium text-gray-900">Discover More</p>
                        <p class="text-sm text-gray-500">Explore new places</p>
                    </div>
                </Link>

                <Link
                    href="/client/reservations"
                    class="bg-white rounded-xl border border-gray-200 p-4 hover:border-orange-300 hover:shadow-md transition-all flex items-center gap-3"
                >
                    <div class="h-10 w-10 bg-blue-100 rounded-lg flex items-center justify-center">
                        <svg class="h-5 w-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <div>
                        <p class="font-medium text-gray-900">My Trips</p>
                        <p class="text-sm text-gray-500">View bookings</p>
                    </div>
                </Link>

                <Link
                    href="/client/profile"
                    class="bg-white rounded-xl border border-gray-200 p-4 hover:border-orange-300 hover:shadow-md transition-all flex items-center gap-3"
                >
                    <div class="h-10 w-10 bg-purple-100 rounded-lg flex items-center justify-center">
                        <svg class="h-5 w-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                    </div>
                    <div>
                        <p class="font-medium text-gray-900">Edit Profile</p>
                        <p class="text-sm text-gray-500">Update preferences</p>
                    </div>
                </Link>
            </div>
        </div>
    </ClientLayout>
</template>
