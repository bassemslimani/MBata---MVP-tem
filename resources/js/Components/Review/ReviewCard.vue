<script setup>
import { ref, computed } from 'vue'

const props = defineProps({
    review: {
        type: Object,
        required: true,
    },
})

const initials = computed(() => {
    const name = props.review.user?.name || 'U'
    return name.charAt(0).toUpperCase()
})

const formatDate = (dateString) => {
    const date = new Date(dateString)
    const now = new Date()
    const diffTime = Math.abs(now - date)
    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24))

    if (diffDays === 0) return 'Today'
    if (diffDays === 1) return 'Yesterday'
    if (diffDays < 7) return `${diffDays} days ago`
    if (diffDays < 30) return `${Math.floor(diffDays / 7)} weeks ago`
    if (diffDays < 365) return `${Math.floor(diffDays / 30)} months ago`
    return `${Math.floor(diffDays / 365)} years ago`
}
</script>

<template>
    <div class="bg-gray-50 rounded-xl p-5">
        <div class="flex items-start justify-between mb-3">
            <div class="flex items-center gap-3">
                <div class="h-10 w-10 bg-gradient-to-br from-orange-400 to-orange-600 rounded-full flex items-center justify-center">
                    <span class="text-white font-semibold text-sm">{{ initials }}</span>
                </div>
                <div>
                    <h4 class="font-semibold text-gray-900">{{ review.user?.name || 'Anonymous' }}</h4>
                    <p class="text-xs text-gray-500">{{ formatDate(review.created_at) }}</p>
                </div>
            </div>
            <div class="flex items-center gap-1">
                <svg
                    v-for="n in 5"
                    :key="n"
                    class="h-4 w-4"
                    :class="n <= review.rating ? 'text-yellow-400 fill-current' : 'text-gray-300'"
                    viewBox="0 0 20 20"
                >
                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                </svg>
                <span class="text-sm font-medium text-gray-700">{{ review.rating }}</span>
            </div>
        </div>

        <p class="text-gray-700 text-sm leading-relaxed">{{ review.comment }}</p>

        <div v-if="review.property_response" class="mt-4 pt-4 border-t border-gray-200">
            <p class="text-xs font-medium text-gray-500 mb-1">Owner Response</p>
            <p class="text-sm text-gray-600">{{ review.property_response }}</p>
        </div>
    </div>
</template>
