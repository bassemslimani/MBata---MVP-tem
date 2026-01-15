<script setup>
import { ref, computed, watch, onMounted } from 'vue'
import axios from 'axios'

const props = defineProps({
    modelValue: {
        type: [String, Number],
        default: '',
    },
    label: {
        type: String,
        default: 'Price per night',
    },
    required: {
        type: Boolean,
        default: false,
    },
    disabled: {
        type: Boolean,
        default: false,
    },
    error: {
        type: String,
        default: '',
    },
    showConversion: {
        type: Boolean,
        default: true,
    },
})

const emit = defineEmits(['update:modelValue'])

const localValue = ref(props.modelValue)
const exchangeRates = ref({
    eur: 0.007,
    usd: 0.0075,
})
const loading = ref(false)

const formattedDzd = computed(() => {
    return Number(localValue.value || 0).toLocaleString('fr-DZ')
})

const convertedEur = computed(() => {
    const dzd = Number(localValue.value || 0)
    return (dzd * exchangeRates.value.eur).toFixed(2)
})

const convertedUsd = computed(() => {
    const dzd = Number(localValue.value || 0)
    return (dzd * exchangeRates.value.usd).toFixed(2)
})

const fetchExchangeRates = async () => {
    loading.value = true
    try {
        const response = await axios.get('/api/currencies/exchange-rates')
        exchangeRates.value = response.data
    } catch (error) {
        console.error('Failed to fetch exchange rates:', error)
        // Use default rates
    } finally {
        loading.value = false
    }
}

onMounted(() => {
    fetchExchangeRates()
})

watch(localValue, (newVal) => {
    emit('update:modelValue', newVal)
})

watch(() => props.modelValue, (newVal) => {
    localValue.value = newVal
})
</script>

<template>
    <div class="space-y-3">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">
                {{ label }}
                <span v-if="required" class="text-red-500">*</span>
            </label>
            <div class="relative rounded-md shadow-sm">
                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                    <span class="text-gray-500 sm:text-sm">DA</span>
                </div>
                <input
                    v-model="localValue"
                    type="number"
                    :disabled="disabled"
                    :required="required"
                    min="0"
                    step="100"
                    class="block w-full rounded-md border-gray-300 pl-12 pr-12 focus:border-orange-500 focus:ring-orange-500 sm:text-sm"
                    :class="{
                        'border-red-500 focus:border-red-500 focus:ring-red-500': error,
                        'bg-gray-100 cursor-not-allowed': disabled
                    }"
                    placeholder="0"
                />
                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3">
                    <span class="text-gray-400 sm:text-sm">/ night</span>
                </div>
            </div>
            <p v-if="error" class="mt-1 text-xs text-red-600">{{ error }}</p>
        </div>

        <!-- Currency Conversion Preview -->
        <div
            v-if="showConversion && localValue && Number(localValue) > 0"
            class="flex flex-wrap gap-3 text-xs"
        >
            <div class="flex items-center gap-1 text-gray-500">
                <span class="font-medium">{{ formattedDzd }} DA</span>
                <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                </svg>
                <span class="font-medium text-green-600">≈ {{ convertedEur }} €</span>
            </div>
            <div class="flex items-center gap-1 text-gray-500">
                <span class="font-medium">{{ formattedDzd }} DA</span>
                <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                </svg>
                <span class="font-medium text-green-600">≈ {{ convertedUsd }} $</span>
            </div>
            <div v-if="loading" class="flex items-center gap-1 text-gray-400">
                <svg class="h-3 w-3 animate-spin" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span>Updating rates...</span>
            </div>
        </div>

        <!-- Price Suggestion Chips -->
        <div class="flex flex-wrap gap-2">
            <button
                type="button"
                :disabled="disabled"
                @click="$emit('update:modelValue', 5000)"
                class="px-3 py-1 text-xs rounded-full border border-gray-200 hover:border-orange-300 hover:bg-orange-50 transition-colors"
                :class="{ 'bg-gray-100 cursor-not-allowed': disabled }"
            >
                Budget (5K DA)
            </button>
            <button
                type="button"
                :disabled="disabled"
                @click="$emit('update:modelValue', 10000)"
                class="px-3 py-1 text-xs rounded-full border border-gray-200 hover:border-orange-300 hover:bg-orange-50 transition-colors"
                :class="{ 'bg-gray-100 cursor-not-allowed': disabled }"
            >
                Standard (10K DA)
            </button>
            <button
                type="button"
                :disabled="disabled"
                @click="$emit('update:modelValue', 25000)"
                class="px-3 py-1 text-xs rounded-full border border-gray-200 hover:border-orange-300 hover:bg-orange-50 transition-colors"
                :class="{ 'bg-gray-100 cursor-not-allowed': disabled }"
            >
                Premium (25K DA)
            </button>
        </div>
    </div>
</template>
