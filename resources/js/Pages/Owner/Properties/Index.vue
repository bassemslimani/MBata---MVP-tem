<script setup>
import { ref, computed } from 'vue'
import { Link, useForm } from '@inertiajs/vue3'
import OwnerLayout from '@/Layouts/OwnerLayout.vue'
import PropertyCard from '@/Components/Property/PropertyCard.vue'

const props = defineProps({
    properties: {
        type: Array,
        default: () => [],
    },
    filters: {
        type: Object,
        default: () => ({}),
    },
})

const filterForm = useForm({
    status: props.filters.status || 'all',
    property_type_id: props.filters.property_type_id || '',
})

const deleteForm = useForm({})

const selectedProperties = ref([])
const showDeleteModal = ref(false)
const propertyToDelete = ref(null)

const filteredProperties = computed(() => {
    let filtered = [...props.properties]

    if (filterForm.status !== 'all') {
        filtered = filtered.filter(p => {
            if (filterForm.status === 'active') return p.is_active
            if (filterForm.status === 'inactive') return !p.is_active
            if (filterForm.status === 'verified') return p.is_verified
            if (filterForm.status === 'pending') return !p.is_verified
            return true
        })
    }

    return filtered
})

const stats = computed(() => ({
    total: props.properties.length,
    active: props.properties.filter(p => p.is_active).length,
    verified: props.properties.filter(p => p.is_verified).length,
    pending: props.properties.filter(p => !p.is_verified).length,
}))

const confirmDelete = (property) => {
    propertyToDelete.value = property
    showDeleteModal.value = true
}

const deleteProperty = () => {
    if (!propertyToDelete.value) return

    deleteForm.delete(`/owner/properties/${propertyToDelete.value.id}`, {
        onSuccess: () => {
            showDeleteModal.value = false
            propertyToDelete.value = null
        },
    })
}

const toggleStatus = (property) => {
    useForm({
        is_active: !property.is_active,
    }).put(`/owner/properties/${property.id}`, {
        preserveScroll: true,
    })
}

const applyFilters = () => {
    filterForm.get('/owner/properties', {
        preserveScroll: true,
    })
}

const clearFilters = () => {
    filterForm.status = 'all'
    filterForm.property_type_id = ''
    applyFilters()
}
</script>

<template>
    <OwnerLayout>
        <div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">My Properties</h1>
                <p class="text-gray-600 mt-1">Manage your vacation rentals</p>
            </div>
            <Link
                href="/owner/properties/create"
                class="inline-flex items-center gap-2 px-4 py-2 bg-orange-500 text-white rounded-lg font-medium hover:bg-orange-600 transition-colors"
            >
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Add Property
            </Link>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
            <div class="bg-white rounded-xl shadow-sm p-5">
                <p class="text-sm font-medium text-gray-600">Total</p>
                <p class="text-2xl font-bold text-gray-900 mt-1">{{ stats.total }}</p>
            </div>
            <div class="bg-white rounded-xl shadow-sm p-5">
                <p class="text-sm font-medium text-gray-600">Active</p>
                <p class="text-2xl font-bold text-green-600 mt-1">{{ stats.active }}</p>
            </div>
            <div class="bg-white rounded-xl shadow-sm p-5">
                <p class="text-sm font-medium text-gray-600">Verified</p>
                <p class="text-2xl font-bold text-blue-600 mt-1">{{ stats.verified }}</p>
            </div>
            <div class="bg-white rounded-xl shadow-sm p-5">
                <p class="text-sm font-medium text-gray-600">Pending</p>
                <p class="text-2xl font-bold text-orange-600 mt-1">{{ stats.pending }}</p>
            </div>
        </div>

        <!-- Filters -->
        <div class="bg-white rounded-xl shadow-sm p-4 mb-6">
            <form @submit.prevent="applyFilters" class="flex flex-wrap gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                    <select
                        v-model="filterForm.status"
                        class="rounded-md border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500"
                    >
                        <option value="all">All Properties</option>
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                        <option value="verified">Verified</option>
                        <option value="pending">Pending Verification</option>
                    </select>
                </div>

                <button
                    type="submit"
                    class="self-end px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors"
                >
                    Apply Filters
                </button>

                <button
                    type="button"
                    @click="clearFilters"
                    class="self-end px-4 py-2 text-gray-600 hover:text-gray-800"
                >
                    Clear
                </button>
            </form>
        </div>

        <!-- Properties List -->
        <div v-if="filteredProperties.length > 0" class="space-y-4">
            <div
                v-for="property in filteredProperties"
                :key="property.id"
                class="bg-white rounded-xl shadow-sm overflow-hidden hover:shadow-md transition-shadow"
            >
                <div class="flex flex-col sm:flex-row">
                    <!-- Image -->
                    <div class="sm:w-48 sm:h-36 flex-shrink-0">
                        <img
                            :src="property.images?.find(i => i.is_primary)?.image_path || property.images?.[0]?.image_path || 'https://via.placeholder.com/300x200'"
                            :alt="property.title"
                            class="h-full w-full object-cover"
                        />
                    </div>

                    <!-- Content -->
                    <div class="flex-1 p-4">
                        <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
                            <div class="flex-1">
                                <div class="flex items-start gap-3">
                                    <h3 class="text-lg font-semibold text-gray-900">{{ property.title }}</h3>
                                    <span
                                        v-if="!property.is_verified"
                                        class="px-2 py-0.5 text-xs font-medium rounded-full bg-yellow-100 text-yellow-800"
                                    >
                                        Pending
                                    </span>
                                </div>

                                <p class="text-sm text-gray-600 mt-1 flex items-center gap-1">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                    {{ property.commune?.name }}, {{ property.wilaya?.name }}
                                </p>

                                <div class="flex flex-wrap items-center gap-4 mt-3 text-sm text-gray-500">
                                    <span>{{ property.price_per_night_dzd?.toLocaleString() }} DA/night</span>
                                    <span>{{ property.bedrooms }} beds</span>
                                    <span>{{ property.max_guests }} guests</span>
                                </div>

                                <div class="flex flex-wrap items-center gap-2 mt-3">
                                    <span
                                        class="px-2 py-1 text-xs font-medium rounded-full"
                                        :class="property.is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600'"
                                    >
                                        {{ property.is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                    <span class="px-2 py-1 text-xs font-medium rounded-full bg-blue-100 text-blue-700">
                                        {{ property.property_type?.name }}
                                    </span>
                                </div>
                            </div>

                            <!-- Actions -->
                            <div class="flex sm:flex-col items-center gap-2">
                                <Link
                                    :href="`/properties/${property.id}`"
                                    target="_blank"
                                    class="p-2 text-gray-400 hover:text-gray-600"
                                    title="View on site"
                                >
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                </Link>

                                <button
                                    @click="toggleStatus(property)"
                                    class="p-2"
                                    :class="property.is_active ? 'text-green-600 hover:text-green-700' : 'text-gray-400 hover:text-gray-600'"
                                    :title="property.is_active ? 'Deactivate' : 'Activate'"
                                >
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 11V7a4 4 0 118 0m-4 8v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z" />
                                    </svg>
                                </button>

                                <Link
                                    :href="`/owner/properties/${property.id}/edit`"
                                    class="p-2 text-gray-400 hover:text-blue-600"
                                    title="Edit"
                                >
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                </Link>

                                <Link
                                    :href="`/owner/properties/${property.id}/photos`"
                                    class="p-2 text-gray-400 hover:text-purple-600"
                                    title="Photos"
                                >
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                </Link>

                                <Link
                                    :href="`/owner/properties/${property.id}/availabilities`"
                                    class="p-2 text-gray-400 hover:text-orange-600"
                                    title="Availability"
                                >
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                </Link>

                                <button
                                    @click="confirmDelete(property)"
                                    class="p-2 text-gray-400 hover:text-red-600"
                                    title="Delete"
                                >
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Empty State -->
        <div v-else class="bg-white rounded-xl shadow-sm p-12 text-center">
            <svg class="mx-auto h-16 w-16 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
            </svg>
            <h3 class="mt-4 text-lg font-medium text-gray-900">No properties found</h3>
            <p class="mt-2 text-gray-500">Get started by adding your first property.</p>
            <Link
                href="/owner/properties/create"
                class="mt-6 inline-flex items-center gap-2 px-4 py-2 bg-orange-500 text-white rounded-lg font-medium hover:bg-orange-600 transition-colors"
            >
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Add Your First Property
            </Link>
        </div>

        <!-- Delete Modal -->
        <div v-if="showDeleteModal" class="fixed inset-0 z-50 flex items-center justify-center">
            <div class="absolute inset-0 bg-black/50" @click="showDeleteModal = false"></div>
            <div class="relative bg-white rounded-xl shadow-xl max-w-md w-full mx-4 p-6">
                <h3 class="text-lg font-semibold text-gray-900">Delete Property</h3>
                <p class="mt-2 text-gray-600">
                    Are you sure you want to delete "{{ propertyToDelete?.title }}"? This action cannot be undone.
                </p>
                <div class="mt-6 flex gap-3 justify-end">
                    <button
                        @click="showDeleteModal = false"
                        type="button"
                        class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50"
                    >
                        Cancel
                    </button>
                    <button
                        @click="deleteProperty"
                        :disabled="deleteForm.processing"
                        class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 disabled:opacity-50"
                    >
                        {{ deleteForm.processing ? 'Deleting...' : 'Delete' }}
                    </button>
                </div>
            </div>
        </div>
    </OwnerLayout>
</template>
