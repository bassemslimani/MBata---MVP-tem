<script setup>
import { ref, computed } from 'vue'

const props = defineProps({
    propertyId: {
        type: Number,
        required: true,
    },
    existingReview: {
        type: Object,
        default: null,
    },
    loading: {
        type: Boolean,
        default: false,
    },
})

const emit = defineEmits(['submit'])

const rating = ref(props.existingReview?.rating || 0)
const comment = ref(props.existingReview?.comment || '')
const hoverRating = ref(0)
const showForm = ref(false)

const ratingLabels = {
    1: 'Poor',
    2: 'Fair',
    3: 'Good',
    4: 'Very Good',
    5: 'Excellent',
}

const ratingLabel = computed(() => {
    const effectiveRating = hoverRating.value || rating.value
    return ratingLabels[effectiveRating] || 'Select a rating'
})

const isValid = computed(() => {
    return rating.value > 0 && comment.value.trim().length >= 50
})

const setRating = (value) => {
    rating.value = value
}

const submitReview = () => {
    if (!isValid.value) return

    emit('submit', {
        propertyId: props.propertyId,
        rating: rating.value,
        comment: comment.value.trim(),
    })
}

const cancelReview = () => {
    if (props.existingReview) {
        rating.value = props.existingReview.rating
        comment.value = props.existingReview.comment
    } else {
        rating.value = 0
        comment.value = ''
    }
    showForm.value = false
}

const remainingChars = computed(() => {
    return Math.max(0, 50 - comment.value.trim().length)
})
</script>

<template>
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <!-- Header -->
        <div class="p-5 border-b border-gray-100">
            <div class="flex items-center justify-between">
                <h3 class="font-semibold text-gray-900">
                    {{ existingReview ? 'Update Your Review' : 'Write a Review' }}
                </h3>
                <button
                    v-if="!showForm && !existingReview"
                    type="button"
                    @click="showForm = true"
                    class="text-sm font-medium text-orange-600 hover:text-orange-700"
                >
                    Write a review
                </button>
            </div>
        </div>

        <!-- Form -->
        <div v-if="showForm || existingReview" class="p-5">
            <!-- Rating -->
            <div class="mb-5">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Your Rating
                </label>
                <div class="flex items-center gap-2">
                    <div class="flex items-center gap-1">
                        <button
                            v-for="n in 5"
                            :key="n"
                            type="button"
                            @click="setRating(n)"
                            @mouseenter="hoverRating = n"
                            @mouseleave="hoverRating = 0"
                            class="p-1 transition-colors"
                        >
                            <svg
                                class="h-8 w-8 transition-colors"
                                :class="n <= (hoverRating || rating) ? 'text-yellow-400 fill-current' : 'text-gray-300'"
                                viewBox="0 0 20 20"
                            >
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                            </svg>
                        </button>
                    </div>
                    <span class="text-sm font-medium text-gray-700">{{ ratingLabel }}</span>
                </div>
            </div>

            <!-- Comment -->
            <div class="mb-5">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Your Review
                </label>
                <textarea
                    v-model="comment"
                    rows="4"
                    placeholder="Share your experience with this property. What did you like? What could be improved?"
                    class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 resize-none"
                    :class="{ 'border-red-300 focus:ring-red-500 focus:border-red-500': comment.length > 0 && comment.trim().length < 50 }"
                ></textarea>
                <div class="flex items-center justify-between mt-2">
                    <p
                        class="text-xs"
                        :class="remainingChars > 0 ? 'text-gray-500' : 'text-green-600'"
                    >
                        {{ remainingChars > 0 ? `${remainingChars} more characters required` : 'Minimum length met' }}
                    </p>
                    <p class="text-xs text-gray-400">{{ comment.length }} / 1000</p>
                </div>
            </div>

            <!-- Actions -->
            <div class="flex items-center gap-3">
                <button
                    v-if="existingReview || showForm"
                    type="button"
                    @click="cancelReview"
                    :disabled="loading"
                    class="px-5 py-2.5 border border-gray-300 text-gray-700 rounded-xl font-medium hover:bg-gray-50 transition-colors disabled:opacity-50"
                >
                    Cancel
                </button>
                <button
                    type="button"
                    @click="submitReview"
                    :disabled="!isValid || loading"
                    class="px-5 py-2.5 bg-orange-500 text-white rounded-xl font-medium hover:bg-orange-600 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                >
                    <span v-if="loading">Submitting...</span>
                    <span v-else>{{ existingReview ? 'Update Review' : 'Submit Review' }}</span>
                </button>
            </div>

            <!-- Tips -->
            <div class="mt-5 pt-5 border-t border-gray-100">
                <p class="text-xs text-gray-500 mb-2 font-medium">Review Guidelines:</p>
                <ul class="text-xs text-gray-500 space-y-1">
                    <li class="flex items-start gap-2">
                        <svg class="h-4 w-4 text-green-500 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                        Be honest and specific about your experience
                    </li>
                    <li class="flex items-start gap-2">
                        <svg class="h-4 w-4 text-green-500 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                        Mention the cleanliness, amenities, and host
                    </li>
                    <li class="flex items-start gap-2">
                        <svg class="h-4 w-4 text-green-500 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                        Avoid personal info or offensive language
                    </li>
                </ul>
            </div>
        </div>

        <!-- Empty State -->
        <div v-else class="p-8 text-center">
            <svg class="h-12 w-12 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
            </svg>
            <p class="text-gray-500 mb-3">Share your experience with others</p>
            <button
                type="button"
                @click="showForm = true"
                class="px-5 py-2.5 bg-orange-500 text-white rounded-xl font-medium hover:bg-orange-600 transition-colors"
            >
                Write a Review
            </button>
        </div>
    </div>
</template>
