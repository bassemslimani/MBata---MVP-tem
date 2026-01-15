<script setup>
import { ref, computed, watch } from 'vue'

const props = defineProps({
    modelValue: {
        type: Object,
        default: () => ({ start: '', end: '' }),
    },
    minDate: {
        type: String,
        default: '',
    },
    maxDate: {
        type: String,
        default: '',
    },
    disabled: {
        type: Boolean,
        default: false,
    },
    error: {
        type: String,
        default: '',
    },
})

const emit = defineEmits(['update:modelValue'])

const startDate = ref(props.modelValue.start)
const endDate = ref(props.modelValue.end)

const today = new Date()
const minDateFormatted = props.minDate || new Date(today.getFullYear(), today.getMonth(), today.getDate() + 1).toISOString().split('T')[0]

const nightsCount = computed(() => {
    if (!startDate.value || !endDate.value) return 0
    const start = new Date(startDate.value)
    const end = new Date(endDate.value)
    const diffTime = end - start
    return Math.max(0, Math.ceil(diffTime / (1000 * 60 * 60 * 24)))
})

const dateRangeText = computed(() => {
    if (startDate.value && endDate.value) {
        const start = new Date(startDate.value)
        const end = new Date(endDate.value)
        const options = { month: 'short', day: 'numeric' }
        return `${start.toLocaleDateString('en-US', options)} - ${end.toLocaleDateString('en-US', options)}`
    }
    if (startDate.value) {
        const start = new Date(startDate.value)
        return `${start.toLocaleDateString('en-US', { month: 'short', day: 'numeric' })} - ?`
    }
    return 'Add dates'
})

const updateDates = () => {
    emit('update:modelValue', {
        start: startDate.value,
        end: endDate.value,
    })
}

watch(startDate, (newVal) => {
    if (newVal && endDate.value && new Date(newVal) > new Date(endDate.value)) {
        endDate.value = newVal
    }
    updateDates()
})

watch(endDate, () => {
    updateDates()
})

watch(() => props.modelValue, (newVal) => {
    startDate.value = newVal.start
    endDate.value = newVal.end
})

const clearDates = () => {
    startDate.value = ''
    endDate.value = ''
    updateDates()
}
</script>

<template>
    <div class="space-y-3">
        <!-- Date Range Input -->
        <div class="relative">
            <button
                type="button"
                :disabled="disabled"
                class="w-full flex items-center justify-between px-4 py-3 border rounded-xl text-left transition-colors"
                :class="[
                    error ? 'border-red-500 focus:border-red-500 focus:ring-red-500' : 'border-gray-300 focus:border-orange-500 focus:ring-orange-500',
                    disabled ? 'bg-gray-100 cursor-not-allowed' : 'bg-white hover:border-gray-400'
                ]"
                @click="!disabled && $refs.startDate.focus()"
            >
                <div class="flex items-center gap-3">
                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    <div class="text-sm">
                        <p class="text-gray-900 font-medium">{{ dateRangeText }}</p>
                        <p v-if="nightsCount > 0" class="text-gray-500 text-xs">{{ nightsCount }} night{{ nightsCount > 1 ? 's' : '' }}</p>
                    </div>
                </div>
                <svg v-if="startDate || endDate" @click.stop="clearDates" class="h-5 w-5 text-gray-400 hover:text-gray-600 cursor-pointer" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <!-- Date Inputs (Hidden but functional for native pickers) -->
        <div class="grid grid-cols-2 gap-3">
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Check-in</label>
                <input
                    ref="startDate"
                    v-model="startDate"
                    type="date"
                    :min="minDateFormatted"
                    :max="maxDate"
                    :disabled="disabled"
                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 text-sm"
                    :class="{ 'bg-gray-100 cursor-not-allowed': disabled }"
                />
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Check-Out</label>
                <input
                    v-model="endDate"
                    type="date"
                    :min="startDate || minDateFormatted"
                    :max="maxDate"
                    :disabled="disabled || !startDate"
                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 text-sm"
                    :class="{ 'bg-gray-100 cursor-not-allowed': disabled || !startDate }"
                />
            </div>
        </div>

        <!-- Error Message -->
        <p v-if="error" class="text-xs text-red-600">{{ error }}</p>
    </div>
</template>
