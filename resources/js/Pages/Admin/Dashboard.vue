<script setup>
import { Link } from '@inertiajs/vue3'
import { ref, onMounted } from 'vue'
import AdminLayout from '@/Layouts/AdminLayout.vue'

const props = defineProps({
    stats: {
        type: Object,
        default: () => ({
            totalUsers: 0,
            totalProperties: 0,
            pendingProperties: 0,
            totalReservations: 0,
            pendingPayments: 0,
            totalRevenue: 0,
            currentMonthRevenue: 0,
        }),
    },
    recentActivities: {
        type: Array,
        default: () => [],
    },
    pendingVerifications: {
        type: Array,
        default: () => [],
    },
})

const loading = ref(false)

const formatCurrency = (amount) => {
    if (!amount) return '0 DA'
    return new Intl.NumberFormat('fr-DZ').format(amount) + ' DA'
}

const getActivityIcon = (type) => {
    const icons = {
        property_created: 'home',
        property_updated: 'home',
        reservation_created: 'calendar',
        payment_completed: 'currency',
        user_registered: 'users',
    }
    return icons[type] || 'circle'
}

const getActivityColor = (type) => {
    const colors = {
        property_created: 'bg-indigo-100 text-indigo-600',
        property_updated: 'bg-blue-100 text-blue-600',
        reservation_created: 'bg-green-100 text-green-600',
        payment_completed: 'bg-yellow-100 text-yellow-600',
        user_registered: 'bg-purple-100 text-purple-600',
    }
    return colors[type] || 'bg-gray-100 text-gray-600'
}
</script>

<template>
    <AdminLayout>
        <div class="mb-8 flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">{{ $t('admin.dashboard.title') }}</h1>
                <p class="text-gray-600 mt-1">{{ $t('admin.dashboard.subtitle') }}</p>
            </div>
        </div>

        <!-- Stats Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- Total Users -->
            <div class="bg-white rounded-xl shadow-sm p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">{{ $t('admin.dashboard.total_users') }}</p>
                        <p class="text-3xl font-bold text-gray-900 mt-2">{{ stats.totalUsers }}</p>
                    </div>
                    <div class="h-12 w-12 bg-purple-100 rounded-lg flex items-center justify-center">
                        <svg class="h-6 w-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                    </div>
                </div>
                <div class="mt-4">
                    <Link :href="route('admin.users')" class="text-sm text-orange-600 hover:text-orange-700 font-medium">
                        {{ $t('admin.dashboard.manage_users') }} &rarr;
                    </Link>
                </div>
            </div>

            <!-- Total Properties -->
            <div class="bg-white rounded-xl shadow-sm p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">{{ $t('owner.dashboard.total_properties') }}</p>
                        <p class="text-3xl font-bold text-gray-900 mt-2">{{ stats.totalProperties }}</p>
                    </div>
                    <div class="h-12 w-12 bg-indigo-100 rounded-lg flex items-center justify-center">
                        <svg class="h-6 w-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                        </svg>
                    </div>
                </div>
                <div class="mt-4 flex items-center text-sm">
                    <span class="text-orange-600 font-medium">{{ stats.pendingProperties }}</span>
                    <span class="text-gray-400 ml-2">{{ $t('admin.dashboard.pending_verifications') }}</span>
                </div>
            </div>

            <!-- Total Reservations -->
            <div class="bg-white rounded-xl shadow-sm p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">{{ $t('owner.dashboard.total_reservations') }}</p>
                        <p class="text-3xl font-bold text-gray-900 mt-2">{{ stats.totalReservations }}</p>
                    </div>
                    <div class="h-12 w-12 bg-green-100 rounded-lg flex items-center justify-center">
                        <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                </div>
                <div class="mt-4">
                    <Link :href="route('admin.reservations')" class="text-sm text-orange-600 hover:text-orange-700 font-medium">
                        {{ $t('common.view_all') }} &rarr;
                    </Link>
                </div>
            </div>

            <!-- Total Revenue -->
            <div class="bg-white rounded-xl shadow-sm p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">{{ $t('owner.dashboard.total_earnings') }}</p>
                        <p class="text-3xl font-bold text-gray-900 mt-2">{{ formatCurrency(stats.totalRevenue) }}</p>
                    </div>
                    <div class="h-12 w-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                        <svg class="h-6 w-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
                <div class="mt-4 flex items-center text-sm">
                    <span class="text-green-600 font-medium">{{ formatCurrency(stats.currentMonthRevenue) }}</span>
                    <span class="text-gray-400 ml-2">{{ $t('owner.dashboard.this_month') }}</span>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Pending Property Verifications -->
            <div class="bg-white rounded-xl shadow-sm">
                <div class="p-6 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <h2 class="text-lg font-semibold text-gray-900">{{ $t('admin.dashboard.pending_verifications') }}</h2>
                        <Link
                            v-if="pendingVerifications.length > 0"
                            :href="route('admin.properties')"
                            class="text-sm text-orange-600 hover:text-orange-700 font-medium"
                        >
                            {{ $t('common.view_all') }}
                        </Link>
                    </div>
                </div>

                <div v-if="pendingVerifications.length > 0" class="divide-y divide-gray-200">
                    <div
                        v-for="property in pendingVerifications"
                        :key="property.id"
                        class="p-6 hover:bg-gray-50 transition-colors"
                    >
                        <div class="flex items-start justify-between">
                            <div class="flex items-start gap-4">
                                <div class="h-14 w-14 bg-gray-100 rounded-lg overflow-hidden flex-shrink-0">
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
                                <div>
                                    <h3 class="font-medium text-gray-900">{{ property.title }}</h3>
                                    <p class="text-sm text-gray-600 mt-1">
                                        {{ property.wilaya }}<span v-if="property.commune">, {{ property.commune }}</span>
                                    </p>
                                    <p class="text-sm text-gray-500 mt-1">
                                        {{ $t('owner.properties.form.basic.property_type') }}: {{ property.owner?.name || '-' }}
                                    </p>
                                </div>
                            </div>
                            <div class="flex items-center gap-2">
                                <Link
                                    :href="route('admin.properties.show', property.id)"
                                    class="text-sm text-orange-600 hover:text-orange-700 font-medium"
                                >
                                    {{ $t('admin.shared.actions.view') }}
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>

                <div v-else class="p-12 text-center">
                    <svg class="mx-auto h-16 w-16 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <h3 class="mt-4 text-lg font-medium text-gray-900">{{ $t('admin.dashboard.all_caught_up') }}</h3>
                    <p class="mt-2 text-gray-500">{{ $t('admin.properties.empty_subtitle') }}</p>
                </div>
            </div>

            <!-- Recent Activities -->
            <div class="bg-white rounded-xl shadow-sm">
                <div class="p-6 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <h2 class="text-lg font-semibold text-gray-900">{{ $t('admin.dashboard.recent_activity') }}</h2>
                    </div>
                </div>

                <div v-if="recentActivities.length > 0" class="divide-y divide-gray-200">
                    <div
                        v-for="activity in recentActivities"
                        :key="activity.id"
                        class="p-6 hover:bg-gray-50 transition-colors"
                    >
                        <div class="flex items-start gap-4">
                            <div class="h-10 w-10 rounded-full flex items-center justify-center flex-shrink-0" :class="getActivityColor(activity.type)">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm text-gray-900">{{ activity.description }}</p>
                                <p class="text-xs text-gray-500 mt-1">{{ new Date(activity.created_at).toLocaleString() }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div v-else class="p-12 text-center">
                    <svg class="mx-auto h-16 w-16 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <h3 class="mt-4 text-lg font-medium text-gray-900">{{ $t('admin.shared.empty_states.no_results') }}</h3>
                    <p class="mt-2 text-gray-500">{{ $t('admin.dashboard.recent_activity') }}</p>
                </div>
            </div>
        </div>

        <!-- Pending Payments Alert -->
        <div v-if="stats.pendingPayments > 0" class="mt-8 bg-orange-50 border border-orange-200 rounded-xl p-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <div class="h-12 w-12 bg-orange-100 rounded-lg flex items-center justify-center">
                        <svg class="h-6 w-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">{{ stats.pendingPayments }} {{ $t('admin.dashboard.pending_payments') }}</h3>
                        <p class="text-gray-600 mt-1">Payment verifications awaiting your review.</p>
                    </div>
                </div>
                <Link
                    :href="route('admin.reservations')"
                    class="inline-flex items-center gap-2 px-4 py-2 bg-orange-600 text-white rounded-lg font-medium hover:bg-orange-700 transition-colors"
                >
                    Review Payments
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                    </svg>
                </Link>
            </div>
        </div>
    </AdminLayout>
</template>
