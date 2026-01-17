<script setup>
import { ref } from 'vue'
import { router, useForm } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import StatusBadge from '@/Components/Admin/StatusBadge.vue'

const props = defineProps({
    admins: {
        type: Array,
        default: () => []
    }
})

const loading = ref(false)
const showCreateForm = ref(false)
const successMessage = ref('')

const createForm = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
    role: 'admin'
})

const formatDate = (date) => {
    if (!date) return '-'
    return new Date(date).toLocaleString()
}

const openCreateForm = () => {
    showCreateForm.value = true
    successMessage.value = ''
}

const closeCreateForm = () => {
    showCreateForm.value = false
    createForm.reset()
}

const submit = () => {
    loading.value = true
    createForm.post(route('super-admin.admins.store'), {
        onSuccess: () => {
            successMessage.value = 'Admin created successfully'
            closeCreateForm()
            setTimeout(() => successMessage.value = '', 3000)
            loading.value = false
        },
        onError: () => {
            loading.value = false
        }
    })
}

const deleteAdmin = (adminId) => {
    if (!confirm('Are you sure you want to remove admin privileges from this user?')) {
        return
    }

    loading.value = true
    router.delete(route('super-admin.admins.destroy', adminId), {
        onSuccess: () => {
            loading.value = false
        },
        onFinish: () => loading.value = false
    })
}

const getRoleLabel = (role) => {
    return role === 'super_admin' ? 'admin.admins.roles.super_admin' : 'admin.admins.roles.admin'
}
</script>

<template>
    <AdminLayout>
        <div class="mb-8 flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">{{ $t('admin.admins.title') }}</h1>
                <p class="text-gray-600 mt-1">{{ $t('admin.admins.subtitle') }}</p>
            </div>
            <button
                @click="openCreateForm"
                class="inline-flex items-center gap-2 px-4 py-2 bg-orange-600 text-white rounded-lg font-medium hover:bg-orange-700 transition-colors"
            >
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                {{ $t('admin.admins.add_admin') }}
            </button>
        </div>

        <!-- Success Message -->
        <div v-if="successMessage" class="mb-6 bg-green-50 border border-green-200 rounded-lg p-4">
            <div class="flex items-center">
                <svg class="h-5 w-5 text-green-600" fill="none" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <p class="ml-3 text-sm text-green-700">{{ successMessage }}</p>
            </div>
        </div>

        <!-- Admins Table -->
        <div class="bg-white rounded-xl shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                {{ $t('admin.admins.table.name') }}
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                {{ $t('admin.admins.table.email') }}
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                {{ $t('admin.users.table.role') }}
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                {{ $t('admin.admins.table.status') }}
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                {{ $t('admin.admins.table.created') }}
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                {{ $t('admin.admins.table.last_login') }}
                            </th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                {{ $t('admin.admins.table.actions') }}
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <tr v-if="admins.length === 0">
                            <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                                <svg class="mx-auto h-12 w-12 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                                <p class="mt-2 text-sm">{{ $t('admin.admins.empty') }}</p>
                                <p class="text-xs text-gray-400">{{ $t('admin.admins.empty_subtitle') }}</p>
                            </td>
                        </tr>
                        <tr v-for="admin in admins" :key="admin.id" class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="h-10 w-10 flex-shrink-0">
                                        <div class="h-10 w-10 rounded-full bg-indigo-100 flex items-center justify-center">
                                            <span class="text-indigo-600 font-medium">
                                                {{ admin.name?.charAt(0).toUpperCase() || '?' }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">{{ admin.name }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ admin.email }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                                    :class="{
                                        'bg-red-100 text-red-700': admin.role === 'super_admin',
                                        'bg-purple-100 text-purple-700': admin.role === 'admin'
                                    }">
                                    {{ $t(getRoleLabel(admin.role)) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <StatusBadge
                                    :status="admin.is_active ? 'active' : 'inactive'"
                                    type="user"
                                />
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ formatDate(admin.created_at) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ admin.last_login_at ? formatDate(admin.last_login_at) : '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <button
                                    @click="deleteAdmin(admin.id)"
                                    class="text-red-600 hover:text-red-900"
                                    :title="$t('admin.admins.table.delete')"
                                >
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Create Admin Modal -->
        <Teleport to="body">
            <Transition
                enter-active-class="transition ease-out duration-300"
                enter-from-class="opacity-0"
                enter-to-class="opacity-100"
                leave-active-class="transition ease-in duration-200"
                leave-from-class="opacity-100"
                leave-to-class="opacity-0"
            >
                <div
                    v-if="showCreateForm"
                    class="fixed inset-0 z-50 overflow-y-auto"
                    role="dialog"
                    aria-modal="true"
                >
                    <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="closeCreateForm"></div>

                    <div class="flex min-h-full items-center justify-center p-4">
                        <Transition
                            enter-active-class="transition ease-out duration-300"
                            enter-from-class="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                            enter-to-class="opacity-100 translate-y-0 sm:scale-100"
                            leave-active-class="transition ease-in duration-200"
                            leave-from-class="opacity-100 translate-y-0 sm:scale-100"
                            leave-to-class="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                        >
                            <div class="relative transform overflow-hidden rounded-lg bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg">
                                <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                                    <div class="flex items-start justify-between mb-4">
                                        <h3 class="text-lg font-semibold text-gray-900">{{ $t('admin.admins.add_admin') }}</h3>
                                        <button @click="closeCreateForm" class="text-gray-400 hover:text-gray-500">
                                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                        </button>
                                    </div>

                                    <form @submit.prevent="submit" class="space-y-4">
                                        <div>
                                            <label for="name" class="block text-sm font-medium text-gray-700 mb-1">
                                                {{ $t('admin.admins.form.name') }}
                                            </label>
                                            <input
                                                id="name"
                                                v-model="createForm.name"
                                                type="text"
                                                required
                                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 sm:text-sm"
                                            />
                                            <p v-if="createForm.errors.name" class="mt-1 text-sm text-red-600">{{ createForm.errors.name }}</p>
                                        </div>

                                        <div>
                                            <label for="email" class="block text-sm font-medium text-gray-700 mb-1">
                                                {{ $t('admin.admins.form.email') }}
                                            </label>
                                            <input
                                                id="email"
                                                v-model="createForm.email"
                                                type="email"
                                                required
                                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 sm:text-sm"
                                            />
                                            <p v-if="createForm.errors.email" class="mt-1 text-sm text-red-600">{{ createForm.errors.email }}</p>
                                        </div>

                                        <div>
                                            <label for="password" class="block text-sm font-medium text-gray-700 mb-1">
                                                {{ $t('admin.admins.form.password') }}
                                            </label>
                                            <input
                                                id="password"
                                                v-model="createForm.password"
                                                type="password"
                                                required
                                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 sm:text-sm"
                                            />
                                            <p v-if="createForm.errors.password" class="mt-1 text-sm text-red-600">{{ createForm.errors.password }}</p>
                                        </div>

                                        <div>
                                            <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">
                                                {{ $t('auth.register.password_confirmation') }}
                                            </label>
                                            <input
                                                id="password_confirmation"
                                                v-model="createForm.password_confirmation"
                                                type="password"
                                                required
                                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 sm:text-sm"
                                            />
                                            <p v-if="createForm.errors.password_confirmation" class="mt-1 text-sm text-red-600">{{ createForm.errors.password_confirmation }}</p>
                                        </div>

                                        <div>
                                            <label for="role" class="block text-sm font-medium text-gray-700 mb-1">
                                                {{ $t('admin.admins.form.role') }}
                                            </label>
                                            <select
                                                id="role"
                                                v-model="createForm.role"
                                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 sm:text-sm"
                                            >
                                                <option value="admin">{{ $t('admin.admins.roles.admin') }}</option>
                                                <option value="super_admin">{{ $t('admin.admins.roles.super_admin') }}</option>
                                            </select>
                                        </div>
                                    </form>
                                </div>

                                <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6">
                                    <button
                                        type="button"
                                        @click="submit"
                                        :disabled="createForm.processing || loading"
                                        class="inline-flex w-full justify-center rounded-md border border-transparent bg-orange-600 px-4 py-2 text-base font-medium text-white hover:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:ring-offset-2 sm:ml-3 sm:w-auto sm:text-sm"
                                    >
                                        <svg v-if="createForm.processing" class="animate-spin -ml-1 mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                        {{ $t('admin.admins.form.create') }}
                                    </button>
                                    <button
                                        type="button"
                                        @click="closeCreateForm"
                                        class="mt-3 inline-flex w-full justify-center rounded-md bg-white px-4 py-2 text-base font-medium text-gray-700 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:mt-0 sm:w-auto sm:text-sm"
                                    >
                                        {{ $t('admin.admins.form.cancel') }}
                                    </button>
                                </div>
                            </div>
                        </Transition>
                    </div>
                </div>
            </Transition>
        </Teleport>
    </AdminLayout>
</template>
