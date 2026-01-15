<script setup>
import { ref, computed, watch, onMounted } from 'vue'
import axios from 'axios'

const props = defineProps({
    propertyId: {
        type: [String, Number],
        required: true,
    },
    existingAvailabilities: {
        type: Array,
        default: () => [],
    },
    editable: {
        type: Boolean,
        default: true,
    },
})

const emit = defineEmits(['update:availabilities'])

const today = new Date()
const currentMonth = ref(new Date(today.getFullYear(), today.getMonth(), 1))
const availabilities = ref([])
const loading = ref(false)
const saving = ref(false)

// Selection state
const selectionStart = ref(null)
const selectionEnd = ref(null)
const isSelecting = ref(false)
const selectionMode = ref('available') // 'available' or 'unavailable'

const daysInMonth = computed(() => {
    const year = currentMonth.value.getFullYear()
    const month = currentMonth.value.getMonth()
    const firstDay = new Date(year, month, 1)
    const lastDay = new Date(year, month + 1, 0)
    const days = []

    // Add empty cells for days before the first day of the month
    const startDayOfWeek = firstDay.getDay()
    for (let i = 0; i < startDayOfWeek; i++) {
        days.push({ type: 'empty', date: null })
    }

    // Add days of the month
    for (let d = 1; d <= lastDay.getDate(); d++) {
        const date = new Date(year, month, d)
        const dateStr = formatDate(date)
        const availability = availabilities.value.find(a => a.date === dateStr)

        days.push({
            type: 'day',
            date: date,
            dateStr: dateStr,
            status: availability?.status || 'available', // available, unavailable, booked
            price: availability?.price_override,
            isPast: date < new Date(today.getFullYear(), today.getMonth(), today.getDate()),
            isToday: date.toDateString() === today.toDateString(),
            isInSelection: isInSelection(date),
            isSelectionStart: selectionStart.value?.toDateString() === date.toDateString(),
            isSelectionEnd: selectionEnd.value?.toDateString() === date.toDateString(),
        })
    }

    return days
})

const monthNames = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December']
const dayNames = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat']

const formatDate = (date) => {
    return date.toISOString().split('T')[0]
}

const parseDate = (dateStr) => {
    return new Date(dateStr + 'T00:00:00')
}

const isInSelection = (date) => {
    if (!selectionStart.value || !selectionEnd.value) return false
    return date >= selectionStart.value && date <= selectionEnd.value
}

const fetchAvailabilities = async () => {
    loading.value = true
    try {
        const start = new Date(currentMonth.value.getFullYear(), currentMonth.value.getMonth(), 1)
        const end = new Date(currentMonth.value.getFullYear(), currentMonth.value.getMonth() + 3, 0)

        const response = await axios.get(`/api/owner/properties/${props.propertyId}/availabilities`, {
            params: {
                start_date: formatDate(start),
                end_date: formatDate(end),
            },
        })

        availabilities.value = response.data.data || response.data || []
    } catch (error) {
        console.error('Failed to fetch availabilities:', error)
    } finally {
        loading.value = false
    }
}

const handleDayClick = (day) => {
    if (!props.editable || day.isPast || day.type === 'empty') return

    if (!isSelecting.value) {
        // Start selection
        selectionStart.value = day.date
        selectionEnd.value = day.date
        isSelecting.value = true
        // Set mode based on existing status
        selectionMode.value = day.status === 'unavailable' ? 'available' : 'unavailable'
    } else {
        // Complete selection
        if (day.date < selectionStart.value) {
            selectionEnd.value = selectionStart.value
            selectionStart.value = day.date
        } else {
            selectionEnd.value = day.date
        }
        applySelection()
    }
}

const handleDayMouseEnter = (day) => {
    if (!isSelecting.value || !props.editable || day.isPast || day.type === 'empty') return

    if (day.date < selectionStart.value) {
        selectionEnd.value = selectionStart.value
        selectionStart.value = day.date
    } else {
        selectionEnd.value = day.date
    }
}

const applySelection = async () => {
    if (!selectionStart.value || !selectionEnd.value) return

    saving.value = true
    const dates = []
    let current = new Date(selectionStart.value)
    const end = new Date(selectionEnd.value)

    while (current <= end) {
        const dateStr = formatDate(current)
        dates.push({
            start_date: dateStr,
            end_date: dateStr,
            is_available: selectionMode.value === 'available',
        })
        current.setDate(current.getDate() + 1)
    }

    try {
        await axios.post(`/api/owner/properties/${props.propertyId}/availabilities`, {
            ranges: dates,
        })

        // Update local availabilities
        dates.forEach(range => {
            const existingIndex = availabilities.value.findIndex(a => a.date === range.start_date)
            if (existingIndex > -1) {
                availabilities.value[existingIndex].status = range.is_available ? 'available' : 'unavailable'
            } else {
                availabilities.value.push({
                    date: range.start_date,
                    status: range.is_available ? 'available' : 'unavailable',
                })
            }
        })

        emit('update:availabilities', availabilities.value)
    } catch (error) {
        console.error('Failed to save availability:', error)
    } finally {
        saving.value = false
        isSelecting.value = false
        selectionStart.value = null
        selectionEnd.value = null
    }
}

const cancelSelection = () => {
    isSelecting.value = false
    selectionStart.value = null
    selectionEnd.value = null
}

const previousMonth = () => {
    currentMonth.value = new Date(currentMonth.value.getFullYear(), currentMonth.value.getMonth() - 1, 1)
}

const nextMonth = () => {
    currentMonth.value = new Date(currentMonth.value.getFullYear(), currentMonth.value.getMonth() + 1, 1)
}

const goToToday = () => {
    currentMonth.value = new Date(today.getFullYear(), today.getMonth(), 1)
}

watch(currentMonth, () => {
    fetchAvailabilities()
}, { immediate: true })

watch(() => props.existingAvailabilities, (newVal) => {
    availabilities.value = [...newVal]
}, { immediate: true })

onMounted(() => {
    fetchAvailabilities()
})
</script>

<template>
    <div class="bg-white rounded-xl shadow-sm">
        <!-- Header -->
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200">
            <div class="flex items-center gap-4">
                <button
                    @click="previousMonth"
                    class="p-2 hover:bg-gray-100 rounded-lg transition-colors"
                    :disabled="loading"
                >
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </button>
                <h3 class="text-lg font-semibold text-gray-900">
                    {{ monthNames[currentMonth.getMonth()] }} {{ currentMonth.getFullYear() }}
                </h3>
                <button
                    @click="nextMonth"
                    class="p-2 hover:bg-gray-100 rounded-lg transition-colors"
                    :disabled="loading"
                >
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </button>
                <button
                    @click="goToToday"
                    class="text-sm text-orange-600 hover:text-orange-700 font-medium"
                >
                    Today
                </button>
            </div>

            <div v-if="isSelecting" class="flex items-center gap-3">
                <span class="text-sm text-gray-600">
                    Selecting: {{ selectionStart?.toLocaleDateString() }} - {{ selectionEnd?.toLocaleDateString() }}
                </span>
                <button
                    @click="cancelSelection"
                    class="px-3 py-1.5 text-sm border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors"
                >
                    Cancel
                </button>
                <button
                    @click="applySelection"
                    :disabled="saving"
                    class="px-3 py-1.5 text-sm bg-orange-500 text-white rounded-lg hover:bg-orange-600 transition-colors disabled:opacity-50"
                >
                    {{ saving ? 'Saving...' : `Set as ${selectionMode}` }}
                </button>
            </div>
        </div>

        <!-- Loading State -->
        <div v-if="loading && availabilities.length === 0" class="p-12 text-center">
            <svg class="mx-auto h-8 w-8 animate-spin text-gray-400" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <p class="mt-4 text-gray-500">Loading availability...</p>
        </div>

        <!-- Calendar Grid -->
        <div v-else class="p-6">
            <!-- Day Headers -->
            <div class="grid grid-cols-7 gap-1 mb-2">
                <div
                    v-for="day in dayNames"
                    :key="day"
                    class="text-center text-xs font-medium text-gray-500 py-2"
                >
                    {{ day }}
                </div>
            </div>

            <!-- Days -->
            <div class="grid grid-cols-7 gap-1">
                <div
                    v-for="(day, index) in daysInMonth"
                    :key="index"
                    class="aspect-square rounded-lg flex items-center justify-center text-sm relative transition-all duration-150"
                    :class="{
                        'bg-gray-50 text-gray-400': day.type === 'empty',
                        'text-gray-400 bg-gray-100 cursor-not-allowed': day.isPast && day.type !== 'empty',
                        'bg-orange-100 text-orange-700 ring-2 ring-orange-500': day.isToday && !day.isPast,
                        'cursor-pointer hover:bg-gray-100': day.type === 'day' && !day.isPast && editable,
                        'bg-gray-200': day.status === 'unavailable' && !day.isPast && !day.isInSelection,
                        'bg-red-100 text-red-700': day.status === 'booked' && !day.isPast,
                        'bg-orange-500 text-white': day.isInSelection && !day.isPast,
                        'ring-2 ring-orange-300': day.isSelectionStart || day.isSelectionEnd,
                    }"
                    @click="handleDayClick(day)"
                    @mouseenter="handleDayMouseEnter(day)"
                    @mouseleave="handleDayMouseLeave(day)"
                >
                    <span v-if="day.type === 'day'">{{ day.date.getDate() }}</span>

                    <!-- Status Indicator -->
                    <div
                        v-if="day.type === 'day' && !day.isPast && day.status !== 'available' && !day.isInSelection"
                        class="absolute bottom-1 left-1/2 transform -translate-x-1/2 w-1.5 h-1.5 rounded-full"
                        :class="{
                            'bg-gray-400': day.status === 'unavailable',
                            'bg-red-500': day.status === 'booked',
                        }"
                    />
                </div>
            </div>

            <!-- Legend -->
            <div class="flex flex-wrap items-center gap-4 mt-6 pt-4 border-t border-gray-200 text-xs text-gray-600">
                <div class="flex items-center gap-1.5">
                    <div class="w-4 h-4 rounded bg-gray-200"></div>
                    <span>Unavailable</span>
                </div>
                <div class="flex items-center gap-1.5">
                    <div class="w-4 h-4 rounded bg-white border border-gray-300"></div>
                    <span>Available</span>
                </div>
                <div class="flex items-center gap-1.5">
                    <div class="w-4 h-4 rounded bg-red-100"></div>
                    <span>Booked</span>
                </div>
                <div class="flex items-center gap-1.5">
                    <div class="w-4 h-4 rounded bg-orange-500"></div>
                    <span>Selected</span>
                </div>
            </div>

            <!-- Instructions -->
            <div v-if="editable" class="mt-4 p-3 bg-gray-50 rounded-lg text-sm text-gray-600">
                <p class="font-medium">How to use:</p>
                <ul class="mt-1 space-y-1 text-xs">
                    <li>• Click a day to start selection, then click another day to select a range</li>
                    <li>• First click determines the mode (set available or unavailable)</li>
                    <li>• Click on unavailable dates to make them available</li>
                    <li>• Click on available dates to make them unavailable</li>
                </ul>
            </div>
        </div>
    </div>
</template>
