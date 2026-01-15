<script setup>
import { computed } from 'vue'

const props = defineProps({
    amenities: {
        type: Array,
        default: () => [],
    },
    limit: {
        type: Number,
        default: null,
    },
    columns: {
        type: Number,
        default: 2,
    },
})

const defaultIcons = {
    wifi: 'M8.111 16.404a5.5 5.5 0 017.778 0M12 20h.01m-7.08-7.071c3.904-3.905 10.236-3.905 14.141 0M1.394 9.393c5.857-5.857 15.355-5.857 21.213 0M5.22 17.2c.97.55 2.15.55 3.12 0 .97-.55 2.15-.55 3.12 0 .97.55 2.15.55 3.12 0 .97-.55 2.15-.55 3.12 0 .97-.55 2.15-.55 3.12 0M5.22 10.4c.97.55 2.15.55 3.12 0 .97-.55 2.15-.55 3.12 0 .97.55 2.15.55 3.12 0 .97-.55 2.15-.55 3.12 0M5.22 3.6c.97.55 2.15.55 3.12 0 .97-.55 2.15-.55 3.12 0 .97.55 2.15.55 3.12 0 .97-.55 2.15-.55 3.12 0M14.22 17.2c.97.55 2.15.55 3.12 0 .97-.55 2.15-.55 3.12 0 .97.55 2.15.55 3.12 0 .97-.55 2.15-.55 3.12 0M14.22 10.4c.97.55 2.15.55 3.12 0 .97-.55 2.15-.55 3.12 0 .97.55 2.15.55 3.12 0 .97-.55 2.15-.55 3.12 0M14.22 3.6c.97.55 2.15.55 3.12 0 .97-.55 2.15-.55 3.12 0 .97.55 2.15.55 3.12 0 .97-.55 2.15-.55 3.12 0',
    'air-conditioning': 'M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z',
    'swimming-pool': 'M5.22 17.2c.97.55 2.15.55 3.12 0 .97-.55 2.15-.55 3.12 0 .97.55 2.15.55 3.12 0 .97-.55 2.15-.55 3.12 0M5.22 10.4c.97.55 2.15.55 3.12 0 .97-.55 2.15-.55 3.12 0 .97.55 2.15.55 3.12 0 .97-.55 2.15-.55 3.12 0M5.22 3.6c.97.55 2.15.55 3.12 0 .97-.55 2.15-.55 3.12 0 .97.55 2.15.55 3.12 0 .97-.55 2.15-.55 3.12 0M14.22 17.2c.97.55 2.15.55 3.12 0 .97-.55 2.15-.55 3.12 0 .97.55 2.15.55 3.12 0 .97-.55 2.15-.55 3.12 0M14.22 10.4c.97.55 2.15.55 3.12 0 .97-.55 2.15-.55 3.12 0 .97.55 2.15.55 3.12 0 .97-.55 2.15-.55 3.12 0M14.22 3.6c.97.55 2.15.55 3.12 0 .97-.55 2.15-.55 3.12 0 .97.55 2.15.55 3.12 0 .97-.55 2.15-.55 3.12 0',
    parking: 'M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z M9 13V7m6 6V7M9 17V7m0 0V5.5A2.5 2.5 0 119.5 8H12m-7 4h14M5 21h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v14a2 2 0 002 2z',
    kitchen: 'M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7',
    heater: 'M17.657 18.657A8 8 0 016.343 7.343S7 9 9 10c0-2 .5-5 2.986-7C14 5 16.09 5.777 17.656 7.343A7.975 7.975 0 0120 13a7.975 7.975 0 01-2.343 5.657z M9.879 16.121A3 3 0 1012.015 11L11 14H9c0 .768.293 1.536.879 2.121z',
    television: 'M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z',
    washer: 'M4 4h16v12H4z M4 8h16M9 9h6',
    elevator: 'M5 3v18M19 3v18M9 9h6M9 15h6 M12 3v6 M12 15v6',
    gym: 'M6 6l12 12M18 6L6 18 M4 4h4M4 20h4M16 4h4M16 20h4',
    'pet-friendly': 'M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z',
    wifi_fast: 'M8.111 16.404a5.5 5.5 0 017.778 0M12 20h.01m-7.08-7.071c3.904-3.905 10.236-3.905 14.141 0M1.394 9.393c5.857-5.857 15.355-5.857 21.213 0',
    breakfast: 'M3 3h18v3H3V3zm0 4h18v3H3V7zm0 4h18v3H3v-3zm0 4h18v3H3v-3z',
    workspace: 'M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z',
}

const displayAmenities = computed(() => {
    let list = [...props.amenities]
    if (props.limit) {
        list = list.slice(0, props.limit)
    }
    return list
})

const getIcon = (amenity) => {
    if (amenity.icon) return amenity.icon
    return defaultIcons[amenity.slug] || defaultIcons.wifi
}
</script>

<template>
    <div class="space-y-4">
        <!-- Grid Layout -->
        <div
            class="grid gap-3"
            :class="{
                'grid-cols-1': columns === 1,
                'grid-cols-2': columns === 2,
                'grid-cols-3': columns === 3,
                'grid-cols-4': columns === 4,
            }"
        >
            <div
                v-for="amenity in displayAmenities"
                :key="amenity.id"
                class="flex items-center gap-3 p-3 bg-gray-50 rounded-xl"
            >
                <div class="h-10 w-10 flex-shrink-0 flex items-center justify-center bg-white rounded-lg shadow-sm">
                    <svg class="h-5 w-5 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="getIcon(amenity)" />
                    </svg>
                </div>
                <span class="text-sm text-gray-700">{{ amenity.name || amenity.name_en }}</span>
            </div>
        </div>

        <!-- Show More Indicator -->
        <div v-if="limit && amenities.length > limit" class="text-center">
            <span class="text-sm text-gray-500">+{{ amenities.length - limit }} more amenities</span>
        </div>
    </div>
</template>
