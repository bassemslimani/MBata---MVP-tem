<script setup>
import { ref, onMounted } from 'vue'
import { router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import UserTable from '@/Components/Admin/UserTable.vue'
import StatusBadge from '@/Components/Admin/StatusBadge.vue'

const props = defineProps({
    users: {
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
const roleFilter = ref('')
const statusFilter = ref('')
const verifiedFilter = ref('')

const roles = ['user', 'owner', 'admin', 'super_admin']
const statuses = ['active', 'suspended', 'pending', 'inactive']

const applyFilters = () => {
    loading.value = true
    const params = new URLSearchParams()
    if (searchQuery.value) params.append('search', searchQuery.value)
    if (roleFilter.value) params.append('role', roleFilter.value)
    if (statusFilter.value) params.append('status', statusFilter.value)
    if (verifiedFilter.value) params.append('verified', verifiedFilter.value)

    router.get(route('admin.users'), Object.fromEntries(params), {
        onFinish: () => loading.value = false
    })
}

const clearFilters = () => {
    searchQuery.value = ''
    roleFilter.value = ''
    statusFilter.value = ''
    verifiedFilter.value = ''
    router.get(route('admin.users'))
}

const toggleUserStatus = (userId) => {
    loading.value = true
    router.post(route('admin.users.toggle-status', userId), {}, {
        onFinish: () => loading.value = false
    })
}

const deleteUser = (userId) => {
    loading.value = true
    router.delete(route('admin.users.destroy', userId), {
        onFinish: () => loading.value = false
    })
}
</script>

<template>
    <AdminLayout>
        <div class="mb-8">
            <h1 class="text-2xl font-bold text-gray-900">{{ $t('admin.users.title') }}</h1>
            <p class="text-gray-600 mt-1">{{ $t('admin.users.subtitle') }}</p>
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
                        :placeholder="$t('admin.users.search_placeholder')"
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 sm:text-sm"
                        @keyup.enter="applyFilters"
                    />
                </div>

                <!-- Role Filter -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        {{ $t('admin.users.filter_role') }}
                    </label>
                    <select
                        v-model="roleFilter"
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 sm:text-sm"
                        @change="applyFilters"
                    >
                        <option value="">{{ $t('common.any') }}</option>
                        <option v-for="role in roles" :key="role" :value="role">
                            {{ $t(`admin.users.roles.${role}`) }}
                        </option>
                    </select>
                </div>

                <!-- Status Filter -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        {{ $t('admin.users.filter_status') }}
                    </label>
                    <select
                        v-model="statusFilter"
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 sm:text-sm"
                        @change="applyFilters"
                    >
                        <option value="">{{ $t('common.any') }}</option>
                        <option v-for="status in statuses" :key="status" :value="status">
                            {{ $t(`admin.users.statuses.${status}`) }}
                        </option>
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

        <!-- Users Table -->
        <div class="bg-white rounded-xl shadow-sm overflow-hidden">
            <UserTable
                :users="users"
                @toggle-status="toggleUserStatus"
                @delete="deleteUser"
            />
        </div>
    </AdminLayout>
</template>
