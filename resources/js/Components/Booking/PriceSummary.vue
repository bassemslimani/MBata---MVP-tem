<script setup>
import { computed } from 'vue'

const props = defineProps({
    pricePerNight: {
        type: Number,
        required: true,
    },
    nights: {
        type: Number,
        default: 1,
    },
    cleaningFee: {
        type: Number,
        default: 0,
    },
    serviceFee: {
        type: Number,
        default: 0,
    },
    currency: {
        type: String,
        default: 'DA',
    },
    showDetails: {
        type: Boolean,
        default: true,
    },
    loading: {
        type: Boolean,
        default: false,
    },
}

const formatCurrency = (amount) => {
    return new Intl.NumberFormat('fr-DZ').format(amount) + ' ' + props.currency
}

const basePrice = computed(() => props.pricePerNight * props.nights)

const totalBeforeFees = computed(() => basePrice.value + props.cleaningFee)

const total = computed(() => totalBeforeFees.value + props.serviceFee)

const breakdown = computed(() => [
    {
        label: `${props.pricePerNight.toLocaleString()} ${props.currency} × ${props.nights} night${props.nights > 1 ? 's' : ''}`,
        amount: basePrice.value,
    },
    {
        label: 'Cleaning fee',
        amount: props.cleaningFee,
    },
    {
        label: 'Service fee',
        amount: props.serviceFee,
    },
])
</script>

<template>
    <div class="space-y-3">
        <div v-if="loading" class="flex items-center justify-center py-4">
            <svg class="h-6 w-6 animate-spin text-gray-400" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
        </div>

        <div v-else>
            <!-- Price Breakdown -->
            <div v-if="showDetails" class="space-y-2 pb-3 border-b border-gray-200">
                <div
                    v-for="item in breakdown"
                    :key="item.label"
                    class="flex justify-between text-sm"
                >
                    <span class="text-gray-600">{{ item.label }}</span>
                    <span class="font-medium">{{ formatCurrency(item.amount) }}</span>
                </div>
            </div>

            <!-- Total -->
            <div class="flex justify-between items-center pt-3">
                <span class="text-lg font-semibold text-gray-900">Total</span>
                <span class="text-2xl font-bold text-gray-900">{{ formatCurrency(total) }}</span>
            </div>

            <!-- Note -->
            <p v-if="showDetails" class="text-xs text-gray-500 text-center">
                Taxes included. No hidden fees.
            </p>
        </div>
    </div>
</template>
