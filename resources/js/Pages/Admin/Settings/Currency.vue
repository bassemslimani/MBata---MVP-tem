<script setup>
import { ref } from 'vue'
import { router, useForm } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

const props = defineProps({
    currencies: {
        type: Object,
        default: () => ({
            base_currency: 'DZD',
            rates: {
                EUR: null,
                USD: null
            },
            last_updated: null
        })
    }
})

const loading = ref(false)
const successMessage = ref('')

const form = useForm({
    eur_rate: props.currencies?.rates?.EUR || '',
    usd_rate: props.currencies?.rates?.USD || '',
    auto_update: false
})

const formatDate = (date) => {
    if (!date) return '-'
    return new Date(date).toLocaleString()
}

const submit = () => {
    loading.value = true
    successMessage.value = ''

    form.put(route('admin.settings.currency.update'), {
        onSuccess: () => {
            successMessage.value = 'admin.settings.currency.saved'
            setTimeout(() => successMessage.value = '', 3000)
            loading.value = false
        },
        onError: () => {
            loading.value = false
        }
    })
}

const updateFromAPI = () => {
    loading.value = true
    router.post(route('admin.settings.currency.update'), {
        data: { auto_update: true },
        onSuccess: () => {
            successMessage.value = 'Exchange rates updated from API'
            setTimeout(() => successMessage.value = '', 3000)
            loading.value = false
        },
        onFinish: () => loading.value = false
    })
}
</script>

<template>
    <AdminLayout>
        <div class="mb-8">
            <h1 class="text-2xl font-bold text-gray-900">{{ $t('admin.settings.title') }}</h1>
            <p class="text-gray-600 mt-1">{{ $t('admin.settings.subtitle') }}</p>
        </div>

        <div class="max-w-3xl">
            <!-- Success Message -->
            <div v-if="successMessage" class="mb-6 bg-green-50 border border-green-200 rounded-lg p-4">
                <div class="flex items-center">
                    <svg class="h-5 w-5 text-green-600" fill="none" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <p class="ml-3 text-sm text-green-700">{{ $t(successMessage) }}</p>
                </div>
            </div>

            <!-- Currency Settings Card -->
            <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                <div class="p-6 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">{{ $t('admin.settings.currency.title') }}</h2>
                    <p class="text-sm text-gray-500 mt-1">{{ $t('admin.settings.currency.subtitle') }}</p>
                    <p v-if="currencies.last_updated" class="text-xs text-gray-400 mt-2">
                        {{ $t('admin.settings.currency.rates.last_updated').replace('{date}', formatDate(currencies.last_updated)) }}
                    </p>
                </div>

                <form @submit.prevent="submit" class="p-6 space-y-6">
                    <!-- Base Currency -->
                    <div class="bg-gray-50 rounded-lg p-4">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-700">{{ $t('admin.settings.currency.base_currency') }}</p>
                                <p class="text-xs text-gray-500">Algerian Dinar (DZD)</p>
                            </div>
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-gray-200 text-gray-700">
                                DZD
                            </span>
                        </div>
                    </div>

                    <!-- EUR Rate -->
                    <div>
                        <label for="eur_rate" class="block text-sm font-medium text-gray-700 mb-2">
                            {{ $t('admin.settings.currency.rates.eur') }}
                        </label>
                        <div class="relative rounded-md shadow-sm">
                            <input
                                id="eur_rate"
                                v-model.number="form.eur_rate"
                                type="number"
                                step="0.0001"
                                min="0"
                                required
                                class="block w-full rounded-md border-gray-300 pr-12 focus:border-orange-500 focus:ring-orange-500 sm:text-sm"
                                placeholder="0.01"
                            />
                            <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                <span class="text-gray-500 sm:text-sm">DA</span>
                            </div>
                        </div>
                        <p class="mt-1 text-xs text-gray-500">1 EUR = X DA</p>
                    </div>

                    <!-- USD Rate -->
                    <div>
                        <label for="usd_rate" class="block text-sm font-medium text-gray-700 mb-2">
                            {{ $t('admin.settings.currency.rates.usd') }}
                        </label>
                        <div class="relative rounded-md shadow-sm">
                            <input
                                id="usd_rate"
                                v-model.number="form.usd_rate"
                                type="number"
                                step="0.0001"
                                min="0"
                                required
                                class="block w-full rounded-md border-gray-300 pr-12 focus:border-orange-500 focus:ring-orange-500 sm:text-sm"
                                placeholder="0.01"
                            />
                            <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                <span class="text-gray-500 sm:text-sm">DA</span>
                            </div>
                        </div>
                        <p class="mt-1 text-xs text-gray-500">1 USD = X DA</p>
                    </div>

                    <!-- Actions -->
                    <div class="flex items-center justify-between pt-6 border-t border-gray-200">
                        <button
                            type="button"
                            @click="updateFromAPI"
                            :disabled="loading"
                            class="inline-flex items-center gap-2 text-sm text-gray-600 hover:text-gray-900"
                        >
                            <svg v-if="loading" class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <svg v-else class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                            </svg>
                            {{ $t('admin.settings.currency.update') }}
                        </button>

                        <button
                            type="submit"
                            :disabled="form.processing || loading"
                            class="inline-flex items-center justify-center rounded-md border border-transparent bg-orange-600 px-6 py-2 text-sm font-medium text-white hover:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:ring-offset-2 disabled:opacity-50"
                        >
                            <svg v-if="form.processing" class="animate-spin -ml-1 mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            {{ $t('admin.settings.currency.save') }}
                        </button>
                    </div>
                </form>
            </div>

            <!-- General Settings Card -->
            <div class="bg-white rounded-xl shadow-sm overflow-hidden mt-6">
                <div class="p-6 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">{{ $t('admin.settings.general.title') }}</h2>
                </div>
                <div class="p-6 space-y-4">
                    <div class="flex items-center justify-between py-3 border-b border-gray-100">
                        <div>
                            <p class="text-sm font-medium text-gray-900">{{ $t('admin.settings.general.site_name') }}</p>
                            <p class="text-xs text-gray-500">Mbata</p>
                        </div>
                        <span class="text-sm text-gray-500">-</span>
                    </div>
                    <div class="flex items-center justify-between py-3 border-b border-gray-100">
                        <div>
                            <p class="text-sm font-medium text-gray-900">{{ $t('admin.settings.general.site_email') }}</p>
                            <p class="text-xs text-gray-500">contact@mbata.dz</p>
                        </div>
                        <span class="text-sm text-gray-500">-</span>
                    </div>
                    <div class="flex items-center justify-between py-3">
                        <div>
                            <p class="text-sm font-medium text-gray-900">{{ $t('admin.settings.general.maintenance_mode') }}</p>
                            <p class="text-xs text-gray-500">Temporarily disable the site</p>
                        </div>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                            Off
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
