<script setup>
import { ref, computed, watch } from 'vue'

const props = defineProps({
    modelValue: {
        type: Array,
        default: () => [],
    },
    amenities: {
        type: Array,
        default: () => [],
    },
    disabled: {
        type: Boolean,
        default: false,
    },
})

const emit = defineEmits(['update:modelValue'])

const selectedAmenities = ref([...props.modelValue])

// Common amenity icons (can be overridden by backend data)
const defaultIcons = {
    wifi: 'M8.111 16.404a5.5 5.5 0 017.778 0M12 20h.01m-7.08-7.071c3.904-3.905 10.236-3.905 14.141 0M1.394 9.393c5.857-5.857 15.355-5.857 21.213 0',
    'air-conditioning': 'M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z',
    'swimming-pool': 'M5.22 17.2c.97.55 2.15.55 3.12 0 .97-.55 2.15-.55 3.12 0 .97.55 2.15.55 3.12 0 .97-.55 2.15-.55 3.12 0M5.22 10.4c.97.55 2.15.55 3.12 0 .97-.55 2.15-.55 3.12 0 .97.55 2.15.55 3.12 0 .97-.55 2.15-.55 3.12 0M5.22 3.6c.97.55 2.15.55 3.12 0 .97-.55 2.15-.55 3.12 0 .97.55 2.15.55 3.12 0 .97-.55 2.15-.55 3.12 0M14.22 17.2c.97.55 2.15.55 3.12 0 .97-.55 2.15-.55 3.12 0 .97.55 2.15.55 3.12 0 .97-.55 2.15-.55 3.12 0M14.22 10.4c.97.55 2.15.55 3.12 0 .97-.55 2.15-.55 3.12 0 .97.55 2.15.55 3.12 0 .97-.55 2.15-.55 3.12 0M14.22 3.6c.97.55 2.15.55 3.12 0 .97-.55 2.15-.55 3.12 0 .97.55 2.15.55 3.12 0 .97-.55 2.15-.55 3.12 0',
    parking: 'M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z M9 13V7m6 6V7M5 21h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v14a2 2 0 002 2z',
    kitchen: 'M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7',
    heater: 'M17.657 18.657A8 8 0 016.343 7.343S7 9 9 10c0-2 .5-5 2.986-7C14 5 16.09 5.777 17.656 7.343A7.975 7.975 0 0120 13a7.975 7.975 0 01-2.343 5.657z M9.879 16.121A3 3 0 1012.015 11L11 14H9c0 .768.293 1.536.879 2.121z',
    television: 'M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z',
    washer: 'M4 4h16v12H4z M4 8h16M8 12h8',
    elevator: 'M5 3v18M19 3v18M9 9h6M9 15h6 M12 3v6 M12 15v6',
    gym: 'M6 6l12 12M18 6L6 18 M4 4h4M4 20h4M16 4h4M16 20h4',
    'pet-friendly': 'M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z',
    smoking: 'M17.657 18.657A8 8 0 016.343 7.343S7 9 9 10c0-2 .5-5 2.986-7C14 5 16.09 5.777 17.656 7.343A7.975 7.975 0 0120 13a7.975 7.975 0 01-2.343 5.657z M9.879 16.121A3 3 0 1012.015 11L11 14H9c0 .768.293 1.536.879 2.121z',
}

const getIcon = (amenity) => {
    if (amenity.icon) return amenity.icon
    return defaultIcons[amenity.slug] || defaultIcons['wifi']
}

const toggleAmenity = (amenityId) => {
    const index = selectedAmenities.value.indexOf(amenityId)
    if (index > -1) {
        selectedAmenities.value.splice(index, 1)
    } else {
        selectedAmenities.value.push(amenityId)
    }
    emit('update:modelValue', selectedAmenities.value)
}

const isSelected = (amenityId) => {
    return selectedAmenities.value.includes(amenityId)
}

watch(() => props.modelValue, (newVal) => {
    selectedAmenities.value = [...newVal]
})
</script>

<template>
    <div class="space-y-4">
        <p class="text-sm text-gray-600">Select the amenities available at your property</p>

        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3">
            <button
                v-for="amenity in amenities"
                :key="amenity.id"
                type="button"
                :disabled="disabled"
                @click="toggleAmenity(amenity.id)"
                class="flex flex-col items-center gap-2 p-4 border-2 rounded-xl transition-all duration-200"
                :class="[
                    isSelected(amenity.id)
                        ? 'border-orange-500 bg-orange-50 text-orange-700'
                        : 'border-gray-200 bg-white text-gray-600 hover:border-gray-300',
                    disabled ? 'opacity-50 cursor-not-allowed' : 'cursor-pointer'
                ]"
            >
                <svg class="h-6 w-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="getIcon(amenity)" />
                </svg>
                <span class="text-xs font-medium text-center leading-tight">{{ amenity.name || amenity.name_en }}</span>

                <!-- Check indicator -->
                <div
                    v-if="isSelected(amenity.id)"
                    class="absolute top-2 right-2 h-5 w-5 bg-orange-500 rounded-full flex items-center justify-center"
                >
                    <svg class="h-3 w-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                    </svg>
                </div>
            </button>
        </div>

        <p v-if="selectedAmenities.length > 0" class="text-xs text-gray-500">
            {{ selectedAmenities.length }} amenity{{ selectedAmenities.length > 1 ? 'ies' : '' }} selected
        </p>
    </div>
</template>
