<script setup>
import { ref } from 'vue'
import { router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import ReservationTable from '@/Components/Admin/ReservationTable.vue'

const props = defineProps({
    reservations: {
        type: Array,
        default: () => []
    },
    filters: {
        type: Object,
        default: () => ({})
    }
})

const loading = ref(false)
const searchQuery = ref('')
const statusFilter = ref('')
const dateFromFilter = ref('')
const dateToFilter = ref('')

const statuses = [
    { value: 'pending', label: 'admin.shared.status_badge.pending' },
    { value: 'confirmed', label: 'admin.shared.status_badge.confirmed' },
    { value: 'completed', label: 'admin.shared.status_badge.completed' },
    { value: 'cancelled', label: 'admin.shared.status_badge.cancelled' }
]

const applyFilters = () => {
    loading.value = true
    const params = new URLSearchParams()
    if (searchQuery.value) params.append('search', searchQuery.value)
    if (statusFilter.value) params.append('status', statusFilter.value)
    if (dateFromFilter.value) params.append('date_from', dateFromFilter.value)
    if (dateToFilter.value) params.append('date_to', dateToFilter.value)

    router.get(route('admin.reservations'), Object.fromEntries(params), {
        onFinish: () => loading.value = false
    })
}

const clearFilters = () => {
    searchQuery.value = ''
    statusFilter.value = ''
    dateFromFilter.value = ''
    dateToFilter.value = ''
    router.get(route('admin.reservations'))
}
</script>

<template>
    <AdminLayout>
        <div class="mb-8">
            <h1 class="text-2xl font-bold text-gray-900">{{ $t('admin.reservations.title') }}</h1>
            <p class="text-gray-600 mt-1">{{ $t('admin.reservations.subtitle') }}</p>
        </div>

        <!-- Filters -->
        <div class="bg-white rounded-xl shadow-sm p-6 mb-6">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <!-- Search -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        {{ $t('admin.shared.filters.search') }}
                    </label>
                    <input
                        v-model="searchQuery"
                        type="text"
                        :placeholder="$t('admin.reservations.search_placeholder')"
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 sm:text-sm"
                        @keyup.enter="applyFilters"
                    />
                </div>

                <!-- Status Filter -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        {{ $t('admin.reservations.filter_status') }}
                    </label>
                    <select
                        v-model="statusFilter"
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 sm:text-sm"
                        @change="applyFilters"
                    >
                        <option value="">{{ $t('common.any') }}</option>
                        <option v-for="status in statuses" :key="status.value" :value="status.value">
                            {{ $t(status.label) }}
                        </option>
                    </select>
                </div>

                <!-- Date From -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        {{ $t('admin.reservations.filter_date') }} - {{ $t('admin.shared.filters.date_from') }}
                    </label>
                    <input
                        v-model="dateFromFilter"
                        type="date"
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 sm:text-sm"
                        @change="applyFilters"
                    />
                </div>

                <!-- Date To -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        {{ $t('admin.shared.filters.date_to') }}
                    </label>
                    <input
                        v-model="dateToFilter"
                        type="date"
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 sm:text-sm"
                        @change="applyFilters"
                    />
                </div>

                <!-- Actions -->
                <div class="flex items-end gap-2 md:col-span-4">
                    <button
                        @click="applyFilters"
                        :disabled="loading"
                        class="inline-flex items-center justify-center rounded-md border border-transparent bg-orange-600 px-4 py-2 text-sm font-medium text-white hover:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:ring-offset-2"
                    >
                        <svg v-if="loading" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        {{ $t('admin.shared.filters.apply') }}
                    </button>
                    <button
                        @click="clearFilters"
                        class="inline-flex items-center justify-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50"
                    >
                        {{ $t('admin.shared.filters.clear') }}
                    </button>
                </div>
            </div>
        </div>

        <!-- Reservations Table -->
        <div class="bg-white rounded-xl shadow-sm overflow-hidden">
            <ReservationTable :reservations="reservations" />
        </div>
    </AdminLayout>
</template>
