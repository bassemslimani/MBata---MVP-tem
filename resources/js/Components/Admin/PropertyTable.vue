<script setup>
import { ref } from 'vue'
import { Link } from '@inertiajs/vue3'
import StatusBadge from './StatusBadge.vue'
import ConfirmDialog from './ConfirmDialog.vue'

const props = defineProps({
    properties: {
        type: Array,
        default: () => []
    }
})

const emit = defineEmits(['approve', 'reject', 'delete', 'toggle-status'])

const propertyToDelete = ref(null)
const showDeleteDialog = ref(false)
const propertyToApprove = ref(null)
const showApproveDialog = ref(false)
const propertyToReject = ref(null)
const showRejectDialog = ref(false)

const formatPrice = (price) => {
    if (!price) return '-'
    return new Intl.NumberFormat('fr-DZ').format(price) + ' DA'
}

const formatDate = (date) => {
    if (!date) return '-'
    return new Date(date).toLocaleDateString()
}

const confirmApprove = (property) => {
    propertyToApprove.value = property
    showApproveDialog.value = true
}

const handleApprove = () => {
    if (propertyToApprove.value) {
        emit('approve', propertyToApprove.value.id)
    }
    showApproveDialog.value = false
    propertyToApprove.value = null
}

const confirmReject = (property) => {
    propertyToReject.value = property
    showRejectDialog.value = true
}

const handleReject = () => {
    if (propertyToReject.value) {
        emit('reject', propertyToReject.value.id)
    }
    showRejectDialog.value = false
    propertyToReject.value = null
}

const confirmDelete = (property) => {
    propertyToDelete.value = property
    showDeleteDialog.value = true
}

const handleDelete = () => {
    if (propertyToDelete.value) {
        emit('delete', propertyToDelete.value.id)
    }
    showDeleteDialog.value = false
    propertyToDelete.value = null
}
</script>

<template>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        {{ $t('admin.properties.table.title') }}
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        {{ $t('admin.properties.table.owner') }}
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        {{ $t('admin.properties.table.location') }}
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        {{ $t('admin.properties.table.price') }}
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        {{ $t('admin.properties.table.type') }}
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        {{ $t('admin.properties.table.status') }}
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        {{ $t('admin.properties.table.created') }}
                    </th>
                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                        {{ $t('admin.properties.table.actions') }}
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <tr v-if="properties.length === 0">
                    <td colspan="8" class="px-6 py-12 text-center text-gray-500">
                        <svg class="mx-auto h-12 w-12 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                        </svg>
                        <p class="mt-2 text-sm">{{ $t('admin.properties.empty') }}</p>
                        <p class="text-xs text-gray-400">{{ $t('admin.properties.empty_subtitle') }}</p>
                    </td>
                </tr>
                <tr v-for="property in properties" :key="property.id" class="hover:bg-gray-50">
                    <td class="px-6 py-4">
                        <div class="flex items-center">
                            <div class="h-16 w-24 flex-shrink-0 bg-gray-100 rounded overflow-hidden">
                                <img
                                    v-if="property.thumbnail_url"
                                    :src="property.thumbnail_url"
                                    class="h-full w-full object-cover"
                                    alt=""
                                />
                                <div v-else class="h-full w-full flex items-center justify-center bg-gray-200">
                                    <svg class="h-8 w-8 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-4">
                                <div class="text-sm font-medium text-gray-900 line-clamp-1">{{ property.title }}</div>
                                <div class="text-xs text-gray-500">
                                    {{ property.bedrooms }} {{ $t('property.bedrooms') }} •
                                    {{ property.max_guests }} {{ $t('property.guests') }}
                                </div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900">{{ property.owner?.name || '-' }}</div>
                        <div class="text-xs text-gray-500">{{ property.owner?.email || '' }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ property.wilaya }}<span v-if="property.commune">, {{ property.commune }}</span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                        {{ formatPrice(property.price_per_night_dzd) }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-700">
                            {{ property.property_type?.name || '-' }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex flex-col gap-1">
                            <StatusBadge
                                :status="property.is_verified ? 'verified' : 'pending'"
                                type="property"
                            />
                            <StatusBadge
                                :status="property.is_active ? 'active' : 'inactive'"
                                type="property"
                            />
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ formatDate(property.created_at) }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <div class="flex items-center justify-end gap-2">
                            <Link
                                :href="route('properties.show', property.id)"
                                target="_blank"
                                class="text-orange-600 hover:text-orange-900"
                                :title="$t('admin.properties.table.view')"
                            >
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                            </Link>
                            <button
                                v-if="!property.is_verified"
                                @click="confirmApprove(property)"
                                class="text-green-600 hover:text-green-900"
                                :title="$t('admin.properties.table.approve')"
                            >
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                            </button>
                            <button
                                v-if="!property.is_verified"
                                @click="confirmReject(property)"
                                class="text-red-600 hover:text-red-900"
                                :title="$t('admin.properties.table.reject')"
                            >
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                            <button
                                @click="confirmDelete(property)"
                                class="text-red-600 hover:text-red-900"
                                :title="$t('admin.properties.table.delete')"
                            >
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                            </button>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>

        <!-- Delete Confirmation Dialog -->
        <ConfirmDialog
            :show="showDeleteDialog"
            :title="$t('admin.properties.confirm_delete')"
            :message="$t('admin.properties.confirm_delete').replace('this property', propertyToDelete?.title || 'this property')"
            @confirm="handleDelete"
            @cancel="showDeleteDialog = false"
            @close="showDeleteDialog = false"
        />

        <!-- Approve Confirmation Dialog -->
        <ConfirmDialog
            :show="showApproveDialog"
            :title="$t('admin.properties.confirm_approve')"
            :message="$t('admin.properties.confirm_approve').replace('this property', propertyToApprove?.title || 'this property')"
            :danger="false"
            @confirm="handleApprove"
            @cancel="showApproveDialog = false"
            @close="showApproveDialog = false"
        />

        <!-- Reject Confirmation Dialog -->
        <ConfirmDialog
            :show="showRejectDialog"
            :title="$t('admin.properties.confirm_reject')"
            :message="$t('admin.properties.confirm_reject').replace('this property', propertyToReject?.title || 'this property')"
            @confirm="handleReject"
            @cancel="showRejectDialog = false"
            @close="showRejectDialog = false"
        />
    </div>
</template>
