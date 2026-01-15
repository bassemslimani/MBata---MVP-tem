<script setup>
import { ref, computed, watch } from 'vue'
import axios from 'axios'

const props = defineProps({
    wilayaId: {
        type: [String, Number, null],
        default: null,
    },
    communeId: {
        type: [String, Number, null],
        default: null,
    },
    wilayas: {
        type: Array,
        default: () => [],
    },
    disabled: {
        type: Boolean,
        default: false,
    },
    errors: {
        type: Object,
        default: () => ({}),
    },
})

const emit = defineEmits(['update:wilayaId', 'update:communeId'])

const loading = ref(false)
const communes = ref([])

const selectedWilayaId = ref(props.wilayaId)
const selectedCommuneId = ref(props.communeId)

const selectedWilaya = computed(() => {
    return props.wilayas.find(w => w.id === Number(selectedWilayaId.value))
})

const hasWilayaError = computed(() => props.errors.wilaya_id || props.errors.wilayaId)
const hasCommuneError = computed(() => props.errors.commune_id || props.errors.communeId)

// Load communes when wilaya changes
watch(selectedWilayaId, async (newWilayaId) => {
    if (!newWilayaId) {
        communes.value = []
        selectedCommuneId.value = null
        emit('update:communeId', null)
        return
    }

    // Check if we already have communes from the wilaya data
    const wilaya = props.wilayas.find(w => w.id === Number(newWilayaId))
    if (wilaya?.communes?.length > 0) {
        communes.value = wilaya.communes
        return
    }

    // Otherwise fetch from API
    loading.value = true
    try {
        const response = await axios.get(`/api/wilayas/${newWilayaId}/communes`)
        communes.value = response.data.data || response.data
    } catch (error) {
        console.error('Failed to load communes:', error)
        communes.value = []
    } finally {
        loading.value = false
    }
}, { immediate: true })

// Emit changes
watch(selectedWilayaId, (newVal) => {
    emit('update:wilayaId', newVal)
})

watch(selectedCommuneId, (newVal) => {
    emit('update:communeId', newVal)
})

// Sync with props
watch(() => props.wilayaId, (newVal) => {
    selectedWilayaId.value = newVal
})

watch(() => props.communeId, (newVal) => {
    selectedCommuneId.value = newVal
})

const reset = () => {
    selectedWilayaId.value = null
    selectedCommuneId.value = null
    communes.value = []
}

defineExpose({
    reset,
})
</script>

<template>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <!-- Wilaya Select -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">
                Wilaya <span class="text-red-500">*</span>
            </label>
            <select
                v-model="selectedWilayaId"
                :disabled="disabled || wilayas.length === 0"
                class="w-full rounded-md border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500"
                :class="{
                    'border-red-500 focus:border-red-500 focus:ring-red-500': hasWilayaError,
                    'bg-gray-100 cursor-not-allowed': disabled
                }"
            >
                <option :value="null">Select a wilaya</option>
                <option
                    v-for="wilaya in wilayas"
                    :key="wilaya.id"
                    :value="wilaya.id"
                >
                    {{ wilaya.code }} - {{ wilaya.name }}
                </option>
            </select>
            <p v-if="hasWilayaError" class="mt-1 text-xs text-red-600">
                {{ hasWilayaError }}
            </p>
        </div>

        <!-- Commune Select -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">
                Commune <span class="text-red-500">*</span>
            </label>
            <div class="relative">
                <select
                    v-model="selectedCommuneId"
                    :disabled="disabled || !selectedWilayaId || loading"
                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 pr-10"
                    :class="{
                        'border-red-500 focus:border-red-500 focus:ring-red-500': hasCommuneError,
                        'bg-gray-100 cursor-not-allowed': disabled || !selectedWilayaId
                    }"
                >
                    <option :value="null">
                        {{ loading ? 'Loading...' : 'Select a commune' }}
                    </option>
                    <option
                        v-for="commune in communes"
                        :key="commune.id"
                        :value="commune.id"
                    >
                        {{ commune.name }} {{ commune.postal_code ? `(${commune.postal_code})` : '' }}
                    </option>
                </select>

                <!-- Loading Spinner -->
                <div
                    v-if="loading"
                    class="absolute inset-y-0 right-0 flex items-center pr-3"
                >
                    <svg class="h-5 w-5 animate-spin text-gray-400" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </div>
            </div>
            <p v-if="hasCommuneError" class="mt-1 text-xs text-red-600">
                {{ hasCommuneError }}
            </p>
        </div>
    </div>

    <!-- Location Info Display -->
    <div v-if="selectedWilaya" class="mt-3 flex items-center gap-2 text-sm text-gray-500">
        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
        </svg>
        <span v-if="selectedCommuneId">
            {{ selectedWilaya?.name }}, {{ communes.find(c => c.id === Number(selectedCommuneId))?.name }}
        </span>
        <span v-else>{{ selectedWilaya?.name }}</span>
    </div>
</template>
