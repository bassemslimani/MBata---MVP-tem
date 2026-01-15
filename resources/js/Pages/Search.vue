<script setup>
import { ref, computed, watch } from 'vue'
import { router } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import PropertyCard from '@/Components/Property/PropertyCard.vue'

const props = defineProps({
    properties: {
        type: Array,
        default: () => [],
    },
    filters: {
        type: Object,
        default: () => ({}),
    },
    wilayas: {
        type: Array,
        default: () => [],
    },
    propertyTypes: {
        type: Array,
        default: () => [],
    },
    pagination: {
        type: Object,
        default: () => ({ total: 0, per_page: 12, current_page: 1, last_page: 1 }),
    },
    Amenities: {
        type: Array,
        default: () => [],
    },
})

const filterForm = ref({
    destination: props.filters.destination || '',
    wilaya_id: props.filters.wilaya_id || '',
    commune_id: props.filters.commune_id || '',
    check_in: props.filters.check_in || '',
    check_out: props.filters.check_out || '',
    guests: props.filters.guests || 1,
    property_type_id: props.filters.property_type_id || '',
    min_price: props.filters.min_price || '',
    max_price: props.filters.max_price || '',
    bedrooms: props.filters.bedrooms || '',
    amenities: props.filters.amenities || [],
    sort_by: props.filters.sort_by || 'recommended',
})

const showMobileFilters = ref(false)
const searching = ref(false)
selectedAmenities = ref([])

const selectedWilaya = computed(() => {
    return props.wilayas.find(w => w.id === Number(filterForm.value.wilaya_id))
})

const communes = computed(() => {
    return selectedWilaya.value?.communes || []
})

const hasActiveFilters = computed(() => {
    return filterForm.value.wilaya_id ||
        filterForm.value.property_type_id ||
        filterForm.value.min_price ||
        filterForm.value.max_price ||
        filterForm.value.bedrooms !== '' ||
        selectedAmenities.value.length > 0
})

const resultsCount = computed(() => props.pagination.total || props.properties.length)

const activeFilterCount = computed(() => {
    let count = 0
    if (filterForm.value.property_type_id) count++
    if (filterForm.value.min_price) count++
    if (filterForm.value.max_price) count++
    if (filterForm.value.bedrooms !== '') count++
    if (selectedAmenities.value.length > 0) count++
    return count
})

const search = () => {
    searching.value = true

    const params = new URLSearchParams()

    if (filterForm.value.destination) params.append('destination', filterForm.value.destination)
    if (filterForm.value.wilaya_id) params.append('wilaya_id', filterForm.value.wilaya_id)
    if (filterForm.value.commune_id) params.append('commune_id', filterForm.value.commune_id)
    if (filterForm.value.check_in) params.append('check_in', filterForm.value.check_in)
    if (filterForm.value.check_out) params.append('check_out', filterForm.value.check_out)
    if (filterForm.value.guests > 1) params.append('guests', filterForm.value.guests)
    if (filterForm.value.property_type_id) params.append('property_type_id', filterForm.value.property_type_id)
    if (filterForm.value.min_price) params.append('min_price', filterForm.value.min_price)
    if (filterForm.value.max_price) params.append('max_price', filterForm.value.max_price)
    if (filterForm.value.bedrooms !== '') params.append('bedrooms', filterForm.value.bedrooms)
    selectedAmenities.value.forEach(a => params.append('amenities[]', a))
    if (filterForm.value.sort_by !== 'recommended') params.append('sort_by', filterForm.value.sort_by)

    router.get('/search', Object.fromEntries(params), {
        preserveState: true,
        onFinish: () => {
            searching.value = false
            showMobileFilters.value = false
        }
    })
}

const clearFilters = () => {
    filterForm.value = {
        destination: filterForm.value.destination, // Keep destination
        wilaya_id: '',
        commune_id: '',
        check_in: filterForm.value.check_in, // Keep dates
        check_out: filterForm.value.check_out,
        guests: filterForm.value.guests,
        property_type_id: '',
        min_price: '',
        max_price: '',
        bedrooms: '',
        amenities: [],
        sort_by: 'recommended',
    }
    selectedAmenities.value = []
    search()
}

const toggleAmenity = (amenityId) => {
    const index = selectedAmenities.value.indexOf(amenityId)
    if (index > -1) {
        selectedAmenities.value.splice(index, 1)
    } else {
        selectedAmenities.value.push(amenityId)
    }
    filterForm.value.amenities = selectedAmenities.value
}

const isAmenitySelected = (amenityId) => {
    return selectedAmenities.value.includes(amenityId)
}

const goToPage = (page) => {
    const params = new URLSearchParams(window.location.search)
    params.set('page', page)
    router.get('/search?' + params.toString())
}

const formatPrice = (price) => {
    return new Intl.NumberFormat('fr-DZ').format(price)
}

// Sync amenity selection from props
watch(() => props.filters.amenities, (newVal) => {
    if (Array.isArray(newVal)) {
        selectedAmenities.value = [...newVal]
    }
}, { immediate: true })
</script>

<template>
    <AppLayout>
        <!-- Search Header Bar -->
        <div class="bg-white shadow-sm border-b border-gray-200 sticky top-0 z-20">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
                <form @submit.prevent="search" class="flex flex-wrap items-center gap-3">
                    <!-- Destination -->
                    <div class="flex-1 min-w-[180px] relative">
                        <svg class="h-5 w-5 absolute left-3 top-1/2 -translate-y-1/2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        <input
                            v-model="filterForm.destination"
                            type="text"
                            placeholder="Where are you going?"
                            class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-xl focus:border-orange-500 focus:ring-orange-500"
                        />
                    </div>

                    <!-- Check-in -->
                    <div class="w-36 sm:w-44">
                        <input
                            v-model="filterForm.check_in"
                            type="date"
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:border-orange-500 focus:ring-orange-500"
                        />
                    </div>

                    <!-- Check-out -->
                    <div class="w-36 sm:w-44">
                        <input
                            v-model="filterForm.check_out"
                            type="date"
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:border-orange-500 focus:ring-orange-500"
                        />
                    </div>

                    <!-- Guests -->
                    <div class="w-32">
                        <select
                            v-model="filterForm.guests"
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:border-orange-500 focus:ring-orange-500"
                        >
                            <option v-for="n in 16" :key="n" :value="n">
                                {{ n }} guest{{ n > 1 ? 's' : '' }}
                            </option>
                        </select>
                    </div>

                    <!-- Search Button -->
                    <button
                        type="submit"
                        :disabled="searching"
                        class="px-6 py-2.5 bg-orange-500 text-white rounded-xl font-medium hover:bg-orange-600 transition-colors disabled:opacity-50 flex items-center gap-2"
                    >
                        <svg v-if="searching" class="h-5 w-5 animate-spin" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <svg v-else class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        <span>Search</span>
                    </button>
                </form>
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="flex gap-8">
                <!-- Filters Sidebar -->
                <aside class="hidden lg:block w-72 flex-shrink-0">
                    <div class="bg-white rounded-xl border border-gray-200 p-6 sticky top-24">
                        <div class="flex items-center justify-between mb-6">
                            <h2 class="text-lg font-semibold text-gray-900">Filters</h2>
                            <button
                                v-if="hasActiveFilters"
                                @click="clearFilters"
                                class="text-sm text-orange-600 hover:text-orange-700"
                            >
                                Clear all
                            </button>
                        </div>

                        <!-- Wilaya & Commune -->
                        <div class="mb-6">
                            <h3 class="text-sm font-medium text-gray-900 mb-3">Location</h3>
                            <select
                                v-model="filterForm.wilaya_id"
                                class="w-full mb-2 px-3 py-2 border border-gray-300 rounded-lg focus:border-orange-500 focus:ring-orange-500 text-sm"
                            >
                                <option value="">Select Wilaya</option>
                                <option v-for="wilaya in wilayas" :key="wilaya.id" :value="wilaya.id">
                                    {{ wilaya.name }}
                                </option>
                            </select>
                            <select
                                v-if="communes.length > 0"
                                v-model="filterForm.commune_id"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:border-orange-500 focus:ring-orange-500 text-sm"
                            >
                                <option value="">Select Commune</option>
                                <option v-for="commune in communes" :key="commune.id" :value="commune.id">
                                    {{ commune.name }}
                                </option>
                            </select>
                        </div>

                        <!-- Property Type -->
                        <div class="mb-6">
                            <h3 class="text-sm font-medium text-gray-900 mb-3">Property Type</h3>
                            <div class="space-y-2">
                                <label
                                    v-for="type in propertyTypes"
                                    :key="type.id"
                                    class="flex items-center cursor-pointer"
                                >
                                    <input
                                        v-model="filterForm.property_type_id"
                                        type="radio"
                                        :value="type.id"
                                        class="h-4 w-4 text-orange-600 focus:ring-orange-500 border-gray-300"
                                    />
                                    <span class="ml-2 text-sm text-gray-600">{{ type.name }}</span>
                                </label>
                            </div>
                        </div>

                        <!-- Price Range -->
                        <div class="mb-6">
                            <h3 class="text-sm font-medium text-gray-900 mb-3">Price per night (DA)</h3>
                            <div class="flex items-center gap-2">
                                <input
                                    v-model="filterForm.min_price"
                                    type="number"
                                    placeholder="Min"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:border-orange-500 focus:ring-orange-500 text-sm"
                                />
                                <span class="text-gray-400">-</span>
                                <input
                                    v-model="filterForm.max_price"
                                    type="number"
                                    placeholder="Max"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:border-orange-500 focus:ring-orange-500 text-sm"
                                />
                            </div>
                        </div>

                        <!-- Bedrooms -->
                        <div class="mb-6">
                            <h3 class="text-sm font-medium text-gray-900 mb-3">Bedrooms</h3>
                            <div class="flex gap-2">
                                <button
                                    v-for="n in ['Any', 1, 2, 3, 4, '5+']"
                                    :key="n"
                                    type="button"
                                    @click="filterForm.bedrooms = n === 'Any' ? '' : n"
                                    class="flex-1 py-2 text-sm border rounded-lg transition-colors"
                                    :class="(filterForm.bedrooms === '' && n === 'Any') || filterForm.bedrooms === n
                                        ? 'bg-orange-500 text-white border-orange-500'
                                        : 'border-gray-300 text-gray-600 hover:border-gray-400'"
                                >
                                    {{ n }}
                                </button>
                            </div>
                        </div>

                        <!-- Amenities -->
                        <div v-if="Amenities.length > 0" class="mb-6">
                            <h3 class="text-sm font-medium text-gray-900 mb-3">Amenities</h3>
                            <div class="grid grid-cols-2 gap-2 max-h-48 overflow-y-auto">
                                <label
                                    v-for="amenity in Amenities.slice(0, 10)"
                                    :key="amenity.id"
                                    class="flex items-center gap-2 text-sm text-gray-600 cursor-pointer"
                                >
                                    <input
                                        type="checkbox"
                                        :checked="isAmenitySelected(amenity.id)"
                                        @change="toggleAmenity(amenity.id)"
                                        class="h-4 w-4 text-orange-600 focus:ring-orange-500 border-gray-300 rounded"
                                    />
                                    <span class="truncate">{{ amenity.name }}</span>
                                </label>
                            </div>
                        </div>

                        <button
                            @click="search"
                            class="w-full py-3 bg-orange-500 text-white rounded-xl font-medium hover:bg-orange-600 transition-colors"
                        >
                            Apply Filters
                        </button>
                    </div>
                </aside>

                <!-- Results -->
                <div class="flex-1">
                    <!-- Results Header -->
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
                        <div>
                            <h1 class="text-2xl font-bold text-gray-900">
                                {{ resultsCount }} {{ resultsCount === 1 ? 'property' : 'properties' }} found
                            </h1>
                            <p v-if="filterForm.destination" class="text-sm text-gray-600 mt-1">
                                in "{{ filterForm.destination }}"
                            </p>
                        </div>

                        <div class="flex items-center gap-3">
                            <button
                                @click="showMobileFilters = true"
                                class="lg:hidden flex items-center gap-2 px-4 py-2 border border-gray-300 rounded-xl text-gray-700"
                            >
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                                </svg>
                                <span v-if="activeFilterCount > 0" class="bg-orange-500 text-white text-xs px-2 py-0.5 rounded-full">
                                    {{ activeFilterCount }}
                                </span>
                                Filters
                            </button>

                            <select
                                v-model="filterForm.sort_by"
                                @change="search"
                                class="px-4 py-2 border border-gray-300 rounded-xl focus:border-orange-500 focus:ring-orange-500 text-sm"
                            >
                                <option value="recommended">Recommended</option>
                                <option value="price_low">Price: Low to High</option>
                                <option value="price_high">Price: High to Low</option>
                                <option value="rating">Highest Rated</option>
                                <option value="newest">Newest</option>
                            </select>
                        </div>
                    </div>

                    <!-- Active Filters -->
                    <div v-if="hasActiveFilters" class="flex flex-wrap gap-2 mb-6">
                        <span
                            v-if="filterForm.property_type_id"
                            class="inline-flex items-center gap-1 px-3 py-1 bg-gray-100 text-gray-700 text-sm rounded-full"
                        >
                            {{ propertyTypes.find(t => t.id === Number(filterForm.property_type_id))?.name }}
                            <button @click="filterForm.property_type_id = ''; search()" class="hover:text-gray-900">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </span>
                        <span
                            v-if="filterForm.min_price || filterForm.max_price"
                            class="inline-flex items-center gap-1 px-3 py-1 bg-gray-100 text-gray-700 text-sm rounded-full"
                        >
                            {{ formatPrice(filterForm.min_price || 0) }} - {{ formatPrice(filterForm.max_price || 999999) }} DA
                            <button @click="filterForm.min_price = ''; filterForm.max_price = ''; search()" class="hover:text-gray-900">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </span>
                        <span
                            v-if="filterForm.bedrooms !== ''"
                            class="inline-flex items-center gap-1 px-3 py-1 bg-gray-100 text-gray-700 text-sm rounded-full"
                        >
                            {{ filterForm.bedrooms }} bedrooms
                            <button @click="filterForm.bedrooms = ''; search()" class="hover:text-gray-900">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </span>
                    </div>

                    <!-- Results Grid -->
                    <div v-if="properties.length > 0" class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
                        <PropertyCard
                            v-for="property in properties"
                            :key="property.id"
                            :property="property"
                        />
                    </div>

                    <!-- Empty State -->
                    <div v-else class="text-center py-16 bg-white rounded-xl border border-gray-200">
                        <svg class="mx-auto h-24 w-24 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        <h3 class="mt-4 text-lg font-medium text-gray-900">No properties found</h3>
                        <p class="mt-2 text-gray-500">Try adjusting your search or filter criteria</p>
                        <button
                            @click="clearFilters"
                            class="mt-6 px-6 py-3 bg-orange-500 text-white rounded-xl font-medium hover:bg-orange-600 transition-colors"
                        >
                            Clear all filters
                        </button>
                    </div>

                    <!-- Pagination -->
                    <div v-if="pagination.last_page > 1" class="flex items-center justify-center gap-2 mt-8">
                        <button
                            v-if="pagination.current_page > 1"
                            @click="goToPage(pagination.current_page - 1)"
                            class="p-2 border border-gray-300 rounded-lg hover:bg-gray-50"
                        >
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                            </svg>
                        </button>

                        <div class="flex items-center gap-1">
                            <button
                                v-for="page in Math.min(5, pagination.last_page)"
                                :key="page"
                                @click="goToPage(page)"
                                class="w-10 h-10 rounded-lg font-medium transition-colors"
                                :class="pagination.current_page === page
                                    ? 'bg-orange-500 text-white'
                                    : 'border border-gray-300 hover:bg-gray-50'"
                            >
                                {{ page }}
                            </button>
                            <span v-if="pagination.last_page > 5" class="px-2">...</span>
                            <button
                                v-if="pagination.last_page > 5"
                                @click="goToPage(pagination.last_page)"
                                class="w-10 h-10 rounded-lg font-medium border border-gray-300 hover:bg-gray-50"
                            >
                                {{ pagination.last_page }}
                            </button>
                        </div>

                        <button
                            v-if="pagination.current_page < pagination.last_page"
                            @click="goToPage(pagination.current_page + 1)"
                            class="p-2 border border-gray-300 rounded-lg hover:bg-gray-50"
                        >
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Mobile Filters Modal -->
        <div v-if="showMobileFilters" class="fixed inset-0 z-50 lg:hidden">
            <div class="fixed inset-0 bg-black/50" @click="showMobileFilters = false"></div>
            <div class="fixed inset-y-0 right-0 w-full max-w-sm bg-white shadow-xl overflow-y-auto">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-lg font-semibold text-gray-900">Filters</h2>
                        <button @click="showMobileFilters = false" class="text-gray-400 hover:text-gray-600">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <!-- Mobile filter content -->
                    <div class="mb-6">
                        <h3 class="text-sm font-medium text-gray-900 mb-3">Location</h3>
                        <select
                            v-model="filterForm.wilaya_id"
                            class="w-full mb-2 px-3 py-2 border border-gray-300 rounded-lg text-sm"
                        >
                            <option value="">Select Wilaya</option>
                            <option v-for="wilaya in wilayas" :key="wilaya.id" :value="wilaya.id">
                                {{ wilaya.name }}
                            </option>
                        </select>
                        <select
                            v-if="communes.length > 0"
                            v-model="filterForm.commune_id"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm"
                        >
                            <option value="">Select Commune</option>
                            <option v-for="commune in communes" :key="commune.id" :value="commune.id">
                                {{ commune.name }}
                            </option>
                        </select>
                    </div>

                    <div class="mb-6">
                        <h3 class="text-sm font-medium text-gray-900 mb-3">Property Type</h3>
                        <div class="space-y-2">
                            <label v-for="type in propertyTypes" :key="type.id" class="flex items-center">
                                <input
                                    v-model="filterForm.property_type_id"
                                    type="radio"
                                    :value="type.id"
                                    class="h-4 w-4 text-orange-600 border-gray-300"
                                />
                                <span class="ml-2 text-sm text-gray-600">{{ type.name }}</span>
                            </label>
                        </div>
                    </div>

                    <div class="mb-6">
                        <h3 class="text-sm font-medium text-gray-900 mb-3">Price per night (DA)</h3>
                        <div class="flex items-center gap-2">
                            <input
                                v-model="filterForm.min_price"
                                type="number"
                                placeholder="Min"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm"
                            />
                            <span class="text-gray-500">-</span>
                            <input
                                v-model="filterForm.max_price"
                                type="number"
                                placeholder="Max"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm"
                            />
                        </div>
                    </div>

                    <div class="mb-6">
                        <h3 class="text-sm font-medium text-gray-900 mb-3">Bedrooms</h3>
                        <div class="flex gap-2">
                            <button
                                v-for="n in ['Any', 1, 2, 3, 4, '5+']"
                                :key="n"
                                type="button"
                                @click="filterForm.bedrooms = n === 'Any' ? '' : n"
                                class="flex-1 py-2 text-sm border rounded-lg"
                                :class="(filterForm.bedrooms === '' && n === 'Any') || filterForm.bedrooms === n
                                    ? 'bg-orange-500 text-white border-orange-500'
                                    : 'border-gray-300'"
                            >
                                {{ n }}
                            </button>
                        </div>
                    </div>

                    <div class="flex gap-3">
                        <button
                            @click="search(); showMobileFilters = false"
                            class="flex-1 py-3 bg-orange-500 text-white rounded-xl font-medium hover:bg-orange-600"
                        >
                            Apply
                        </button>
                        <button
                            @click="clearFilters"
                            class="px-6 py-3 border border-gray-300 rounded-xl text-gray-700 hover:bg-gray-50"
                        >
                            Clear
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
