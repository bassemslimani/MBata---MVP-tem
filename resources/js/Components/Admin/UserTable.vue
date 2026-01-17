<script setup>
import { ref, computed } from 'vue'
import { Link } from '@inertiajs/vue3'
import StatusBadge from './StatusBadge.vue'
import ConfirmDialog from './ConfirmDialog.vue'

const props = defineProps({
    users: {
        type: Array,
        default: () => []
    }
})

const emit = defineEmits(['toggle-status', 'delete'])

const userToDelete = ref(null)
const showDeleteDialog = ref(false)
const userToToggle = ref(null)
const showToggleDialog = ref(false)

const confirmDelete = (user) => {
    userToDelete.value = user
    showDeleteDialog.value = true
}

const handleDelete = () => {
    if (userToDelete.value) {
        emit('delete', userToDelete.value.id)
    }
    showDeleteDialog.value = false
    userToDelete.value = null
}

const confirmToggle = (user) => {
    userToToggle.value = user
    showToggleDialog.value = true
}

const handleToggle = () => {
    if (userToToggle.value) {
        emit('toggle-status', userToToggle.value.id)
    }
    showToggleDialog.value = false
    userToToggle.value = null
}

const formatDate = (date) => {
    if (!date) return '-'
    return new Date(date).toLocaleDateString()
}

const getRoleLabel = (role) => {
    const roles = {
        user: 'admin.users.roles.user',
        owner: 'admin.users.roles.owner',
        admin: 'admin.users.roles.admin',
        super_admin: 'admin.users.roles.super_admin'
    }
    return roles[role] || role
}
</script>

<template>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        {{ $t('admin.users.table.name') }}
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        {{ $t('admin.users.table.email') }}
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        {{ $t('admin.users.table.role') }}
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        {{ $t('admin.users.table.status') }}
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        {{ $t('admin.users.table.joined') }}
                    </th>
                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                        {{ $t('admin.users.table.actions') }}
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <tr v-if="users.length === 0">
                    <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                        <svg class="mx-auto h-12 w-12 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                        <p class="mt-2 text-sm">{{ $t('admin.users.empty') }}</p>
                        <p class="text-xs text-gray-400">{{ $t('admin.users.empty_subtitle') }}</p>
                    </td>
                </tr>
                <tr v-for="user in users" :key="user.id" class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                            <div class="h-10 w-10 flex-shrink-0">
                                <div class="h-10 w-10 rounded-full bg-orange-100 flex items-center justify-center">
                                    <span class="text-orange-600 font-medium">
                                        {{ user.name?.charAt(0).toUpperCase() || '?' }}
                                    </span>
                                </div>
                            </div>
                            <div class="ml-4">
                                <div class="text-sm font-medium text-gray-900">{{ user.name }}</div>
                                <div v-if="user.properties_count !== undefined" class="text-xs text-gray-500">
                                    {{ user.properties_count }} {{ $t('admin.users.table.properties') }}
                                </div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900">{{ user.email }}</div>
                        <div v-if="user.phone" class="text-xs text-gray-500">{{ user.phone }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                            :class="{
                                'bg-purple-100 text-purple-700': user.role === 'admin' || user.role === 'super_admin',
                                'bg-blue-100 text-blue-700': user.role === 'owner',
                                'bg-gray-100 text-gray-700': user.role === 'user'
                            }">
                            {{ $t(getRoleLabel(user.role)) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <StatusBadge
                            :status="user.is_active ? 'active' : 'suspended'"
                            type="user"
                        />
                        <div v-if="user.email_verified_at === null" class="mt-1">
                            <span class="text-xs text-yellow-600">
                                {{ $t('admin.users.statuses.pending') }}
                            </span>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ formatDate(user.created_at) }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <div class="flex items-center justify-end gap-2">
                            <Link
                                :href="route('admin.users.show', user.id)"
                                class="text-orange-600 hover:text-orange-900"
                                :title="$t('admin.users.table.view')"
                            >
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                            </Link>
                            <button
                                @click="confirmToggle(user)"
                                class="text-blue-600 hover:text-blue-900"
                                :title="user.is_active ? $t('admin.users.table.suspend') : $t('admin.users.table.activate')"
                            >
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path v-if="user.is_active" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 9v6m4-6v6m7-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    <g v-else>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </g>
                                </svg>
                            </button>
                            <button
                                @click="confirmDelete(user)"
                                class="text-red-600 hover:text-red-900"
                                :title="$t('admin.users.table.delete')"
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
            :title="$t('admin.users.confirm_delete')"
            :message="$t('admin.users.confirm_delete').replace('this user', userToDelete?.name || 'this user')"
            @confirm="handleDelete"
            @cancel="showDeleteDialog = false"
            @close="showDeleteDialog = false"
        />

        <!-- Toggle Status Confirmation Dialog -->
        <ConfirmDialog
            :show="showToggleDialog"
            :title="userToToggle?.is_active ? $t('admin.users.confirm_suspend') : $t('admin.users.confirm_activate')"
            :message="(userToToggle?.is_active ? $t('admin.users.confirm_suspend') : $t('admin.users.confirm_activate')).replace('this user', userToToggle?.name || 'this user')"
            :danger="false"
            @confirm="handleToggle"
            @cancel="showToggleDialog = false"
            @close="showToggleDialog = false"
        />
    </div>
</template>
