<script setup>
import { ref, computed } from 'vue'
import { useForm, Link } from '@inertiajs/vue3'
import ClientLayout from '@/Layouts/ClientLayout.vue'

const props = defineProps({
    user: {
        type: Object,
        required: true,
    },
})

const activeTab = ref('profile')
const message = ref('')
const messageType = ref('') // 'success' or 'error'

const tabs = [
    { id: 'profile', label: 'Profile', icon: 'user' },
    { id: 'security', label: 'Security', icon: 'lock' },
    { id: 'preferences', label: 'Preferences', icon: 'settings' },
]

// Profile Form
const profileForm = useForm({
    first_name: props.user.first_name || '',
    last_name: props.user.last_name || '',
    email: props.user.email || '',
    phone: props.user.phone || '',
    bio: props.user.bio || '',
    date_of_birth: props.user.date_of_birth || '',
})

// Password Form
const passwordForm = useForm({
    current_password: '',
    password: '',
    password_confirmation: '',
})

// Preferences Form
const preferencesForm = useForm({
    language: props.user.language || 'fr',
    currency: props.user.currency || 'DZD',
    email_notifications: props.user.email_notifications ?? true,
    promotional_emails: props.user.promotional_emails ?? false,
})

const languages = [
    { code: 'fr', label: 'Français' },
    { code: 'ar', label: 'العربية' },
    { code: 'en', label: 'English' },
]

const currencies = [
    { code: 'DZD', label: 'Dinar Algérien (DA)' },
    { code: 'EUR', label: 'Euro (€)' },
    { code: 'USD', label: 'US Dollar ($)' },
]

const updateProfile = () => {
    profileForm.put('/client/profile', {
        onSuccess: () => {
            message.value = 'Profile updated successfully!'
            messageType.value = 'success'
            setTimeout(() => message.value = '', 3000)
        },
        onError: () => {
            message.value = 'Failed to update profile. Please check your inputs.'
            messageType.value = 'error'
        },
    })
}

const updatePassword = () => {
    passwordForm.put('/client/password', {
        onSuccess: () => {
            message.value = 'Password updated successfully!'
            messageType.value = 'success'
            passwordForm.reset()
            setTimeout(() => message.value = '', 3000)
        },
        onError: () => {
            message.value = 'Failed to update password. Please check your inputs.'
            messageType.value = 'error'
        },
    })
}

const updatePreferences = () => {
    preferencesForm.put('/client/preferences', {
        onSuccess: () => {
            message.value = 'Preferences updated successfully!'
            messageType.value = 'success'
            setTimeout(() => message.value = '', 3000)
        },
        onError: () => {
            message.value = 'Failed to update preferences.'
            messageType.value = 'error'
        },
    })
}

const deleteAccount = () => {
    if (confirm('Are you sure you want to delete your account? This action cannot be undone.')) {
        window.location.href = '/client/profile/delete'
    }
}

const avatarInitials = computed(() => {
    const first = profileForm.first_name.charAt(0).toUpperCase()
    const last = profileForm.last_name.charAt(0).toUpperCase()
    return first + last
})
</script>

<template>
    <ClientLayout>
        <div class="space-y-6">
            <!-- Header -->
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Account Settings</h1>
                <p class="text-gray-600 mt-1">Manage your profile and preferences</p>
            </div>

            <!-- Alert Message -->
            <div
                v-if="message"
                class="p-4 rounded-xl"
                :class="messageType === 'success' ? 'bg-green-50 text-green-700' : 'bg-red-50 text-red-700'"
            >
                <div class="flex items-center gap-2">
                    <svg
                        v-if="messageType === 'success'"
                        class="h-5 w-5"
                        fill="currentColor"
                        viewBox="0 0 20 20"
                    >
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                    </svg>
                    <svg
                        v-else
                        class="h-5 w-5"
                        fill="currentColor"
                        viewBox="0 0 20 20"
                    >
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                    </svg>
                    <span>{{ message }}</span>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
                <!-- Sidebar Navigation -->
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-xl border border-gray-200 p-2">
                        <button
                            v-for="tab in tabs"
                            :key="tab.id"
                            @click="activeTab = tab.id"
                            class="w-full flex items-center gap-3 px-4 py-3 rounded-lg text-left transition-colors"
                            :class="activeTab === tab.id
                                ? 'bg-orange-50 text-orange-600 font-medium'
                                : 'text-gray-600 hover:bg-gray-50'"
                        >
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path v-if="tab.icon === 'user'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                <path v-else-if="tab.icon === 'lock'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                <path v-if="tab.icon === 'settings'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            {{ tab.label }}
                        </button>
                    </div>

                    <!-- Danger Zone -->
                    <div class="mt-4 bg-white rounded-xl border border-red-200 p-4">
                        <h3 class="font-medium text-red-900 mb-2">Danger Zone</h3>
                        <p class="text-sm text-red-700 mb-3">Once you delete your account, there is no going back.</p>
                        <button
                            @click="deleteAccount"
                            class="w-full px-4 py-2 border border-red-300 text-red-600 text-sm font-medium rounded-lg hover:bg-red-50 transition-colors"
                        >
                            Delete Account
                        </button>
                    </div>
                </div>

                <!-- Content Area -->
                <div class="lg:col-span-3">
                    <!-- Profile Tab -->
                    <div v-if="activeTab === 'profile'" class="bg-white rounded-xl border border-gray-200 p-6">
                        <h2 class="text-lg font-semibold text-gray-900 mb-6">Profile Information</h2>

                        <!-- Avatar Section -->
                        <div class="flex items-center gap-6 mb-8 pb-8 border-b border-gray-200">
                            <div class="h-20 w-20 bg-gradient-to-br from-orange-400 to-orange-600 rounded-full flex items-center justify-center">
                                <span class="text-white font-bold text-2xl">{{ avatarInitials }}</span>
                            </div>
                            <div>
                                <h3 class="font-semibold text-gray-900">{{ profileForm.first_name }} {{ profileForm.last_name }}</h3>
                                <p class="text-sm text-gray-600">{{ profileForm.email }}</p>
                                <button class="mt-2 text-sm text-orange-600 hover:underline">Change photo</button>
                            </div>
                        </div>

                        <form @submit.prevent="updateProfile" class="space-y-6">
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">First Name</label>
                                    <input
                                        v-model="profileForm.first_name"
                                        type="text"
                                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500"
                                        :class="{ 'border-red-500': profileForm.errors.first_name }"
                                    />
                                    <p v-if="profileForm.errors.first_name" class="text-xs text-red-600 mt-1">{{ profileForm.errors.first_name }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Last Name</label>
                                    <input
                                        v-model="profileForm.last_name"
                                        type="text"
                                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500"
                                        :class="{ 'border-red-500': profileForm.errors.last_name }"
                                    />
                                    <p v-if="profileForm.errors.last_name" class="text-xs text-red-600 mt-1">{{ profileForm.errors.last_name }}</p>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                                    <input
                                        v-model="profileForm.email"
                                        type="email"
                                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500"
                                        :class="{ 'border-red-500': profileForm.errors.email }"
                                    />
                                    <p v-if="profileForm.errors.email" class="text-xs text-red-600 mt-1">{{ profileForm.errors.email }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Phone Number</label>
                                    <input
                                        v-model="profileForm.phone"
                                        type="tel"
                                        placeholder="+213 XXX XXX XXX"
                                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500"
                                    />
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Date of Birth</label>
                                <input
                                    v-model="profileForm.date_of_birth"
                                    type="date"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500"
                                />
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Bio</label>
                                <textarea
                                    v-model="profileForm.bio"
                                    rows="3"
                                    placeholder="Tell us a little about yourself..."
                                    class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 resize-none"
                                ></textarea>
                            </div>

                            <div class="flex justify-end">
                                <button
                                    type="submit"
                                    :disabled="profileForm.processing"
                                    class="px-6 py-3 bg-orange-500 text-white font-medium rounded-xl hover:bg-orange-600 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                                >
                                    <span v-if="profileForm.processing">Saving...</span>
                                    <span v-else>Save Changes</span>
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Security Tab -->
                    <div v-if="activeTab === 'security'" class="bg-white rounded-xl border border-gray-200 p-6">
                        <h2 class="text-lg font-semibold text-gray-900 mb-6">Change Password</h2>

                        <form @submit.prevent="updatePassword" class="space-y-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Current Password</label>
                                <input
                                    v-model="passwordForm.current_password"
                                    type="password"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500"
                                    :class="{ 'border-red-500': passwordForm.errors.current_password }"
                                />
                                <p v-if="passwordForm.errors.current_password" class="text-xs text-red-600 mt-1">{{ passwordForm.errors.current_password }}</p>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">New Password</label>
                                    <input
                                        v-model="passwordForm.password"
                                        type="password"
                                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500"
                                        :class="{ 'border-red-500': passwordForm.errors.password }"
                                    />
                                    <p v-if="passwordForm.errors.password" class="text-xs text-red-600 mt-1">{{ passwordForm.errors.password }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Confirm New Password</label>
                                    <input
                                        v-model="passwordForm.password_confirmation"
                                        type="password"
                                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500"
                                    />
                                </div>
                            </div>

                            <div class="bg-blue-50 rounded-xl p-4">
                                <div class="flex items-start gap-3">
                                    <svg class="h-5 w-5 text-blue-600 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                                    </svg>
                                    <div class="text-sm text-blue-800">
                                        <p class="font-medium">Password requirements:</p>
                                        <ul class="mt-1 space-y-1 text-blue-700">
                                            <li>• At least 8 characters long</li>
                                            <li>• Contains uppercase and lowercase letters</li>
                                            <li>• Contains at least one number</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>

                            <div class="flex justify-end">
                                <button
                                    type="submit"
                                    :disabled="passwordForm.processing"
                                    class="px-6 py-3 bg-orange-500 text-white font-medium rounded-xl hover:bg-orange-600 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                                >
                                    <span v-if="passwordForm.processing">Updating...</span>
                                    <span v-else>Update Password</span>
                                </button>
                            </div>
                        </form>

                        <!-- Login Sessions -->
                        <div class="mt-8 pt-8 border-t border-gray-200">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Login Sessions</h3>
                            <div class="space-y-3">
                                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-xl">
                                    <div class="flex items-center gap-3">
                                        <svg class="h-5 w-5 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                        </svg>
                                        <div>
                                            <p class="font-medium text-gray-900">Current session</p>
                                            <p class="text-sm text-gray-500">{{ $page.props.browser }} on {{ $page.props.platform }}</p>
                                        </div>
                                    </div>
                                    <span class="text-xs text-green-600 font-medium">Active now</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Preferences Tab -->
                    <div v-if="activeTab === 'preferences'" class="bg-white rounded-xl border border-gray-200 p-6">
                        <h2 class="text-lg font-semibold text-gray-900 mb-6">Preferences</h2>

                        <form @submit.prevent="updatePreferences" class="space-y-6">
                            <!-- Language -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Language</label>
                                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                                    <button
                                        v-for="lang in languages"
                                        :key="lang.code"
                                        type="button"
                                        @click="preferencesForm.language = lang.code"
                                        class="p-4 border rounded-xl text-left transition-colors"
                                        :class="preferencesForm.language === lang.code
                                            ? 'border-orange-500 bg-orange-50 ring-1 ring-orange-500'
                                            : 'border-gray-300 hover:border-gray-400'"
                                    >
                                        <p class="font-medium" :class="preferencesForm.language === lang.code ? 'text-orange-600' : 'text-gray-900'">
                                            {{ lang.label }}
                                        </p>
                                    </button>
                                </div>
                            </div>

                            <!-- Currency -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Currency</label>
                                <select
                                    v-model="preferencesForm.currency"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500"
                                >
                                    <option v-for="curr in currencies" :key="curr.code" :value="curr.code">
                                        {{ curr.label }}
                                    </option>
                                </select>
                            </div>

                            <!-- Notifications -->
                            <div class="space-y-4">
                                <h3 class="font-medium text-gray-900">Email Notifications</h3>

                                <label class="flex items-center justify-between p-4 bg-gray-50 rounded-xl cursor-pointer">
                                    <div>
                                        <p class="font-medium text-gray-900">Booking Updates</p>
                                        <p class="text-sm text-gray-500">Receive emails about your bookings</p>
                                    </div>
                                    <div class="relative">
                                        <input
                                            v-model="preferencesForm.email_notifications"
                                            type="checkbox"
                                            class="sr-only peer"
                                        />
                                        <div class="w-11 h-6 bg-gray-300 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-orange-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-orange-500"></div>
                                    </div>
                                </label>

                                <label class="flex items-center justify-between p-4 bg-gray-50 rounded-xl cursor-pointer">
                                    <div>
                                        <p class="font-medium text-gray-900">Promotional Emails</p>
                                        <p class="text-sm text-gray-500">Receive special offers and recommendations</p>
                                    </div>
                                    <div class="relative">
                                        <input
                                            v-model="preferencesForm.promotional_emails"
                                            type="checkbox"
                                            class="sr-only peer"
                                        />
                                        <div class="w-11 h-6 bg-gray-300 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-orange-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-orange-500"></div>
                                    </div>
                                </label>
                            </div>

                            <div class="flex justify-end">
                                <button
                                    type="submit"
                                    :disabled="preferencesForm.processing"
                                    class="px-6 py-3 bg-orange-500 text-white font-medium rounded-xl hover:bg-orange-600 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                                >
                                    <span v-if="preferencesForm.processing">Saving...</span>
                                    <span v-else>Save Preferences</span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </ClientLayout>
</template>
