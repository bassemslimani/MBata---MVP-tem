<script setup>
import { ref } from 'vue'
import { router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import ReviewCard from '@/Components/Admin/ReviewCard.vue'

const props = defineProps({
    reviews: {
        type: Array,
        default: () => []
    },
    filters: {
        type: Object,
        default: () => ({})
    }
})

const loading = ref(false)
const ratingFilter = ref('')
const visibilityFilter = ref('')

const ratings = [5, 4, 3, 2, 1]

const applyFilters = () => {
    loading.value = true
    const params = new URLSearchParams()
    if (ratingFilter.value) params.append('rating', ratingFilter.value)
    if (visibilityFilter.value) params.append('visible', visibilityFilter.value)

    router.get(route('admin.reviews'), Object.fromEntries(params), {
        onFinish: () => loading.value = false
    })
}

const clearFilters = () => {
    ratingFilter.value = ''
    visibilityFilter.value = ''
    router.get(route('admin.reviews'))
}

const toggleVisibility = (reviewId) => {
    loading.value = true
    router.post(route('admin.reviews.toggle-visibility', reviewId), {}, {
        onFinish: () => loading.value = false
    })
}

const deleteReview = (reviewId) => {
    loading.value = true
    router.delete(route('admin.reviews.destroy', reviewId), {
        onFinish: () => loading.value = false
    })
}
</script>

<template>
    <AdminLayout>
        <div class="mb-8">
            <h1 class="text-2xl font-bold text-gray-900">{{ $t('admin.reviews.title') }}</h1>
            <p class="text-gray-600 mt-1">{{ $t('admin.reviews.subtitle') }}</p>
        </div>

        <!-- Filters -->
        <div class="bg-white rounded-xl shadow-sm p-6 mb-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <!-- Rating Filter -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        {{ $t('admin.reviews.filter_rating') }}
                    </label>
                    <select
                        v-model="ratingFilter"
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 sm:text-sm"
                        @change="applyFilters"
                    >
                        <option value="">{{ $t('common.any') }}</option>
                        <option v-for="rating in ratings" :key="rating" :value="rating">
                            {{ rating }} {{ $t('property.reviews.rating') }}
                        </option>
                    </select>
                </div>

                <!-- Visibility Filter -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        {{ $t('admin.reviews.filter_status') }}
                    </label>
                    <select
                        v-model="visibilityFilter"
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 sm:text-sm"
                        @change="applyFilters"
                    >
                        <option value="">{{ $t('common.any') }}</option>
                        <option value="1">{{ $t('admin.shared.status_badge.visible') }}</option>
                        <option value="0">{{ $t('admin.shared.status_badge.hidden') }}</option>
                    </select>
                </div>

                <!-- Actions -->
                <div class="flex items-end gap-2">
                    <button
                        @click="applyFilters"
                        :disabled="loading"
                        class="flex-1 inline-flex items-center justify-center rounded-md border border-transparent bg-orange-600 px-4 py-2 text-sm font-medium text-white hover:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:ring-offset-2"
                    >
                        <svg v-if="loading" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        {{ $t('admin.shared.filters.apply') }}
                    </button>
                    <button
                        @click="clearFilters"
                        class="flex-1 inline-flex items-center justify-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50"
                    >
                        {{ $t('admin.shared.filters.clear') }}
                    </button>
                </div>
            </div>
        </div>

        <!-- Reviews Grid -->
        <div v-if="reviews.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <ReviewCard
                v-for="review in reviews"
                :key="review.id"
                :review="review"
                @toggle-visibility="toggleVisibility"
                @delete="deleteReview"
            />
        </div>

        <!-- Empty State -->
        <div v-else class="bg-white rounded-xl shadow-sm p-12 text-center">
            <svg class="mx-auto h-16 w-16 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
            </svg>
            <h3 class="mt-4 text-lg font-medium text-gray-900">{{ $t('admin.reviews.empty') }}</h3>
            <p class="mt-2 text-gray-500">{{ $t('admin.reviews.empty_subtitle') }}</p>
        </div>
    </AdminLayout>
</template>
