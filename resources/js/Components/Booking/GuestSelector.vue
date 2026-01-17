<script setup>
import { ref, computed, watch } from 'vue'

const props = defineProps({
    modelValue: {
        type: Number,
        default: 1,
    },
    maxGuests: {
        type: Number,
        default: 16,
    },
    disabled: {
        type: Boolean,
        default: false,
    },
    label: {
        type: String,
        default: 'Guests',
    },
})

const emit = defineEmits(['update:modelValue'])

const localValue = ref(props.modelValue)
const isOpen = ref(false)

const guestText = computed(() => {
    return `${localValue.value} guest${localValue.value > 1 ? 's' : ''}`
})

const increment = () => {
    if (localValue.value < props.maxGuests) {
        localValue.value++
        emit('update:modelValue', localValue.value)
    }
}

const decrement = () => {
    if (localValue.value > 1) {
        localValue.value--
        emit('update:modelValue', localValue.value)
    }
}

const setValue = (val) => {
    localValue.value = val
    emit('update:modelValue', val)
    isOpen.value = false
}

watch(() => props.modelValue, (newVal) => {
    localValue.value = newVal
})

// Close dropdown when clicking outside
const closeOnClickOutside = (e) => {
    if (isOpen.value && !e.target.closest('.guest-selector')) {
        isOpen.value = false
    }
}

if (typeof window !== 'undefined') {
    window.addEventListener('click', closeOnClickOutside)
}
</script>

<template>
    <div class="guest-selector relative">
        <label v-if="label" class="block text-sm font-medium text-gray-700 mb-1">
            {{ label }}
        </label>

        <!-- Dropdown Button -->
        <button
            type="button"
            :disabled="disabled"
            @click="isOpen = !isOpen"
            class="w-full flex items-center justify-between px-4 py-3 border rounded-xl text-left transition-colors guest-selector"
            :class="[
                disabled ? 'bg-gray-100 cursor-not-allowed border-gray-300' : 'bg-white border-gray-300 hover:border-gray-400'
            ]"
        >
            <div class="flex items-center gap-3">
                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2a2 2 0 01-2-2v-6a2 2 0 012-2h5l2 2h5l2-2h5a2 2 0 012 2v6a2 2 0 01-2 2z" />
                </svg>
                <span class="text-sm font-medium text-gray-900">{{ guestText }}</span>
            </div>
            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="isOpen ? 'M19 9l-7 7-7-7' : 'M9 5l7 7-7 7'" />
            </svg>
        </button>

        <!-- Dropdown Menu -->
        <div
            v-if="isOpen"
            class="absolute z-50 mt-2 w-full bg-white rounded-xl shadow-xl border border-gray-200 py-4"
        >
            <!-- Current Value with Controls -->
            <div class="flex items-center justify-between px-4 pb-4 border-b border-gray-100">
                <button
                    type="button"
                    :disabled="localValue <= 1"
                    @click="decrement"
                    class="h-10 w-10 flex items-center justify-center rounded-full border transition-colors"
                    :class="[
                        localValue <= 1
                            ? 'border-gray-200 text-gray-300 cursor-not-allowed'
                            : 'border-gray-300 text-gray-600 hover:border-orange-500 hover:text-orange-500'
                    ]"
                >
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4" />
                    </svg>
                </button>

                <span class="text-lg font-semibold w-12 text-center">{{ localValue }}</span>

                <button
                    type="button"
                    :disabled="localValue >= maxGuests"
                    @click="increment"
                    class="h-10 w-10 flex items-center justify-center rounded-full border transition-colors"
                    :class="[
                        localValue >= maxGuests
                            ? 'border-gray-200 text-gray-300 cursor-not-allowed'
                            : 'border-gray-300 text-gray-600 hover:border-orange-500 hover:text-orange-500'
                    ]"
                >
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                </button>
            </div>

            <!-- Quick Select Buttons -->
            <div class="px-4 pt-2">
                <p class="text-xs text-gray-500 mb-2">Quick select</p>
                <div class="grid grid-cols-4 gap-2">
                    <button
                        v-for="n in [1, 2, 3, 4]"
                        :key="n"
                        type="button"
                        @click="setValue(n)"
                        class="py-2 text-sm font-medium rounded-lg border transition-colors"
                        :class="[
                            localValue === n
                                ? 'bg-orange-500 text-white border-orange-500'
                                : 'border-gray-200 text-gray-700 hover:border-orange-300 hover:bg-orange-50'
                        ]"
                    >
                        {{ n }}
                    </button>
                </div>
                <p v-if="maxGuests > 4" class="text-xs text-gray-400 mt-2">
                    Up to {{ maxGuests }} guests
                </p>
            </div>
        </div>
    </div>
</template>
