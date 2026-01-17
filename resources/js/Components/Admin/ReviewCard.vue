<script setup>
import { ref } from 'vue'
import StatusBadge from './StatusBadge.vue'
import ConfirmDialog from './ConfirmDialog.vue'

const props = defineProps({
    review: {
        type: Object,
        required: true
    }
})

const emit = defineEmits(['toggle-visibility', 'delete'])

const showDeleteDialog = ref(false)

const stars = (rating) => {
    return Array(5).fill(0).map((_, i) => i < rating)
}

const formatDate = (date) => {
    if (!date) return '-'
    return new Date(date).toLocaleDateString()
}

const confirmDelete = () => {
    showDeleteDialog.value = true
}

const handleDelete = () => {
    emit('delete', props.review.id)
    showDeleteDialog.value = false
}

const toggleVisibility = () => {
    emit('toggle-visibility', props.review.id)
}
</script>

<template>
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden" :class="{ 'opacity-60': !review.is_visible }">
        <div class="p-5">
            <!-- Header: Rating and Date -->
            <div class="flex items-start justify-between mb-4">
                <div class="flex items-center gap-2">
                    <div class="flex items-center">
                        <svg
                            v-for="filled in stars(review.rating || 0)"
                            :key="filled"
                            class="h-5 w-5 text-yellow-400"
                            fill="currentColor"
                            viewBox="0 0 20 20"
                        >
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                        </svg>
                    </div>
                    <span class="text-sm font-medium text-gray-900">{{ review.rating || 0 }}/5</span>
                </div>
                <StatusBadge
                    :status="review.is_visible ? 'visible' : 'hidden'"
                    type="review"
                />
            </div>

            <!-- Comment -->
            <p class="text-gray-700 text-sm mb-4 line-clamp-3">
                {{ review.comment || $t('admin.reviews.table.no_comment') }}
            </p>

            <!-- Reviewer Info -->
            <div class="flex items-center gap-3 mb-4 pb-4 border-b border-gray-100">
                <div class="h-10 w-10 rounded-full bg-orange-100 flex items-center justify-center">
                    <span class="text-orange-600 font-medium">
                        {{ review.user?.name?.charAt(0).toUpperCase() || '?' }}
                    </span>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-gray-900 truncate">{{ review.user?.name || '-' }}</p>
                    <p class="text-xs text-gray-500">{{ formatDate(review.created_at) }}</p>
                </div>
            </div>

            <!-- Property Info -->
            <div class="mb-4">
                <p class="text-xs text-gray-500 mb-1">{{ $t('property.show.title') }}</p>
                <p class="text-sm font-medium text-gray-900 truncate">{{ review.property?.title || '-' }}</p>
                <p class="text-xs text-gray-500">{{ review.property?.wilaya }}</p>
            </div>

            <!-- Actions -->
            <div class="flex items-center justify-between pt-3 border-t border-gray-100">
                <button
                    @click="toggleVisibility"
                    class="text-sm font-medium"
                    :class="review.is_visible ? 'text-yellow-600 hover:text-yellow-700' : 'text-green-600 hover:text-green-700'"
                >
                    <svg class="h-5 w-5 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path v-if="review.is_visible" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                        <g v-else>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </g>
                    </svg>
                    {{ review.is_visible ? $t('admin.shared.actions.hide') : $t('admin.shared.actions.show') }}
                </button>

                <button
                    @click="confirmDelete"
                    class="text-sm font-medium text-red-600 hover:text-red-700"
                >
                    <svg class="h-5 w-5 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                    {{ $t('admin.shared.actions.delete') }}
                </button>
            </div>
        </div>

        <!-- Delete Confirmation Dialog -->
        <ConfirmDialog
            :show="showDeleteDialog"
            :title="$t('admin.reviews.confirm_delete')"
            :message="$t('admin.reviews.confirm_delete')"
            @confirm="handleDelete"
            @cancel="showDeleteDialog = false"
            @close="showDeleteDialog = false"
        />
    </div>
</template>
