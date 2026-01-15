<script setup>
import { ref, computed } from 'vue'
import { Link, useForm } from '@inertiajs/vue3'
import OwnerLayout from '@/Layouts/OwnerLayout.vue'
import ImageUploader from '@/Components/Property/ImageUploader.vue'
import AmenitySelector from '@/Components/Property/AmenitySelector.vue'
import WilayaCommuneSelect from '@/Components/Forms/WilayaCommuneSelect.vue'
import PriceInput from '@/Components/Forms/PriceInput.vue'

const props = defineProps({
    wilayas: {
        type: Array,
        default: () => [],
    },
    propertyTypes: {
        type: Array,
        default: () => [],
    },
    propertyCategories: {
        type: Array,
        default: () => [],
    },
    amenities: {
        type: Array,
        default: () => [],
    },
    exchangeRates: {
        type: Object,
        default: () => ({}),
    },
})

const currentStep = ref(1)
const totalSteps = 5
const imageUploaderRef = ref(null)

const form = useForm({
    // Step 1: Basic Info
    property_type_id: '',
    property_category_id: '',
    title: '',
    description: '',

    // Step 2: Location
    wilaya_id: '',
    commune_id: '',
    address: '',
    latitude: '',
    longitude: '',

    // Step 3: Details
    surface_area: '',
    bedrooms: '',
    bathrooms: '',
    max_guests: '',
    rooms: '',

    // Step 4: Amenities
    amenities: [],

    // Step 5: Photos
    images: [],

    // Pricing
    price_per_night_dzd: '',
})

const steps = [
    { id: 1, name: 'Basic Info', icon: 'document' },
    { id: 2, name: 'Location', icon: 'map' },
    { id: 3, name: 'Details', icon: 'home' },
    { id: 4, name: 'Amenities', icon: 'star' },
    { id: 5, name: 'Photos', icon: 'photo' },
]

const stepIcons = {
    document: 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
    map: 'M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z M15 11a3 3 0 11-6 0 3 3 0 016 0z',
    home: 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6',
    star: 'M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.921-.755 1.688-1.54 1.118l-3.976-2.888a1 1 0 00-1.175 0l-3.976 2.888c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.363-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.07-3.292z',
    photo: 'M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z',
}

const canProceed = computed(() => {
    switch (currentStep.value) {
        case 1:
            return form.property_type_id && form.property_category_id && form.title && form.description
        case 2:
            return form.wilaya_id && form.commune_id
        case 3:
            return form.surface_area && form.bedrooms && form.max_guests
        case 4:
            return true
        case 5:
            return true
        default:
            return true
    }
})

const nextStep = () => {
    if (canProceed.value && currentStep.value < totalSteps) {
        currentStep.value++
    }
}

const previousStep = () => {
    if (currentStep.value > 1) {
        currentStep.value--
    }
}

const goToStep = (step) => {
    if (step < currentStep.value || canProceed.value) {
        currentStep.value = step
    }
}

const submit = () => {
    // Collect images from the image uploader
    if (imageUploaderRef.value) {
        form.images = imageUploaderRef.value.getImagesForSubmit()
    }

    form.post('/owner/properties', {
        forceFormData: true,
        onSuccess: () => {
            currentStep.value = 1
        },
    })
}

const getLocation = () => {
    if (navigator.geolocation && form.address) {
        navigator.geolocation.getCurrentPosition(
            (position) => {
                form.latitude = position.coords.latitude
                form.longitude = position.coords.longitude
            },
            (error) => {
                console.error('Geolocation error:', error)
            }
        )
    }
}
</script>

<template>
    <OwnerLayout>
        <div class="mb-8">
            <h1 class="text-2xl font-bold text-gray-900">Add New Property</h1>
            <p class="text-gray-600 mt-1">List your vacation rental on Mbata</p>
        </div>

        <!-- Progress Steps -->
        <div class="mb-8">
            <nav class="flex justify-between" aria-label="Progress">
                <ol class="flex items-center w-full">
                    <li
                        v-for="(step, index) in steps"
                        :key="step.id"
                        class="flex items-center"
                        :class="{ 'flex-1': index < steps.length - 1 }"
                    >
                        <div class="flex flex-col items-center">
                            <button
                                @click="goToStep(step.id)"
                                :disabled="step.id > currentStep && !canProceed"
                                class="flex items-center justify-center w-10 h-10 rounded-full border-2 font-medium transition-colors"
                                :class="[
                                    step.id < currentStep
                                        ? 'bg-orange-500 border-orange-500 text-white'
                                        : step.id === currentStep
                                            ? 'border-orange-500 text-orange-500'
                                            : 'border-gray-300 text-gray-400',
                                    step.id > currentStep && !canProceed ? 'cursor-not-allowed' : 'cursor-pointer'
                                ]"
                            >
                                <svg v-if="step.id < currentStep" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                <span v-else>{{ step.id }}</span>
                            </button>
                            <span class="mt-2 text-xs font-medium hidden sm:block"
                                :class="step.id === currentStep ? 'text-orange-600' : 'text-gray-500'"
                            >
                                {{ step.name }}
                            </span>
                        </div>
                        <div
                            v-if="index < steps.length - 1"
                            class="flex-1 h-0.5 mx-2 sm:mx-4 bg-gray-200"
                            :class="{ 'bg-orange-500': step.id < currentStep }"
                        ></div>
                    </li>
                </ol>
            </nav>
        </div>

        <form @submit.prevent="submit" class="bg-white rounded-xl shadow-sm p-6">
            <!-- Step 1: Basic Info -->
            <div v-show="currentStep === 1" class="space-y-6">
                <h2 class="text-xl font-semibold text-gray-900">Basic Information</h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Property Type <span class="text-red-500">*</span>
                        </label>
                        <select
                            v-model="form.property_type_id"
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500"
                            required
                        >
                            <option value="">Select type</option>
                            <option v-for="type in propertyTypes" :key="type.id" :value="type.id">
                                {{ type.name }}
                            </option>
                        </select>
                        <p v-if="form.errors.property_type_id" class="mt-1 text-xs text-red-600">{{ form.errors.property_type_id }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Category <span class="text-red-500">*</span>
                        </label>
                        <select
                            v-model="form.property_category_id"
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500"
                            required
                        >
                            <option value="">Select category</option>
                            <option v-for="cat in propertyCategories" :key="cat.id" :value="cat.id">
                                {{ cat.name }}
                            </option>
                        </select>
                        <p v-if="form.errors.property_category_id" class="mt-1 text-xs text-red-600">{{ form.errors.property_category_id }}</p>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Property Title <span class="text-red-500">*</span>
                    </label>
                    <input
                        v-model="form.title"
                        type="text"
                        maxlength="100"
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500"
                        placeholder="e.g., Beautiful Villa with Sea View"
                        required
                    />
                    <p class="mt-1 text-xs text-gray-500">{{ form.title.length }}/100 characters</p>
                    <p v-if="form.errors.title" class="mt-1 text-xs text-red-600">{{ form.errors.title }}</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Description <span class="text-red-500">*</span>
                    </label>
                    <textarea
                        v-model="form.description"
                        rows="5"
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500"
                        placeholder="Describe your property, its features, and what makes it special..."
                        required
                    ></textarea>
                    <p v-if="form.errors.description" class="mt-1 text-xs text-red-600">{{ form.errors.description }}</p>
                </div>
            </div>

            <!-- Step 2: Location -->
            <div v-show="currentStep === 2" class="space-y-6">
                <h2 class="text-xl font-semibold text-gray-900">Location</h2>

                <WilayaCommuneSelect
                    v-model:wilayaId="form.wilaya_id"
                    v-model:communeId="form.commune_id"
                    :wilayas="wilayas"
                    :errors="form.errors"
                />

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Address <span class="text-red-500">*</span>
                    </label>
                    <input
                        v-model="form.address"
                        type="text"
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500"
                        placeholder="Street address, building, floor..."
                        required
                    />
                    <p v-if="form.errors.address" class="mt-1 text-xs text-red-600">{{ form.errors.address }}</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Latitude</label>
                        <input
                            v-model="form.latitude"
                            type="text"
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500"
                            placeholder="Optional"
                        />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Longitude</label>
                        <input
                            v-model="form.longitude"
                            type="text"
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500"
                            placeholder="Optional"
                        />
                    </div>
                </div>

                <button
                    type="button"
                    @click="getLocation"
                    class="flex items-center gap-2 text-sm text-orange-600 hover:text-orange-700"
                >
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                    </svg>
                    Use my current location
                </button>
            </div>

            <!-- Step 3: Details -->
            <div v-show="currentStep === 3" class="space-y-6">
                <h2 class="text-xl font-semibold text-gray-900">Property Details</h2>

                <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Surface Area (m²) <span class="text-red-500">*</span>
                        </label>
                        <input
                            v-model="form.surface_area"
                            type="number"
                            min="1"
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500"
                            required
                        />
                        <p v-if="form.errors.surface_area" class="mt-1 text-xs text-red-600">{{ form.errors.surface_area }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Bedrooms <span class="text-red-500">*</span>
                        </label>
                        <input
                            v-model="form.bedrooms"
                            type="number"
                            min="1"
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500"
                            required
                        />
                        <p v-if="form.errors.bedrooms" class="mt-1 text-xs text-red-600">{{ form.errors.bedrooms }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Bathrooms</label>
                        <input
                            v-model="form.bathrooms"
                            type="number"
                            min="1"
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500"
                        />
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Rooms</label>
                        <input
                            v-model="form.rooms"
                            type="number"
                            min="1"
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500"
                        />
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Max Guests <span class="text-red-500">*</span>
                    </label>
                    <select
                        v-model="form.max_guests"
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500"
                        required
                    >
                        <option value="">Select max guests</option>
                        <option v-for="n in 16" :key="n" :value="n">{{ n }} guest{{ n > 1 ? 's' : '' }}</option>
                    </select>
                    <p v-if="form.errors.max_guests" class="mt-1 text-xs text-red-600">{{ form.errors.max_guests }}</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Price per Night <span class="text-red-500">*</span>
                    </label>
                    <PriceInput
                        v-model="form.price_per_night_dzd"
                        :error="form.errors.price_per_night_dzd"
                        required
                    />
                    <p v-if="form.errors.price_per_night_dzd" class="mt-1 text-xs text-red-600">{{ form.errors.price_per_night_dzd }}</p>
                </div>
            </div>

            <!-- Step 4: Amenities -->
            <div v-show="currentStep === 4" class="space-y-6">
                <h2 class="text-xl font-semibold text-gray-900">Amenities</h2>

                <AmenitySelector
                    v-model="form.amenities"
                    :amenities="amenities"
                />
            </div>

            <!-- Step 5: Photos -->
            <div v-show="currentStep === 5" class="space-y-6">
                <h2 class="text-xl font-semibold text-gray-900">Photos</h2>
                <p class="text-gray-600">Add at least one photo to showcase your property. The first photo will be the primary image.</p>

                <ImageUploader
                    ref="imageUploaderRef"
                    :existing-images="[]"
                />
            </div>

            <!-- Navigation Buttons -->
            <div class="mt-8 flex items-center justify-between pt-6 border-t border-gray-200">
                <button
                    v-if="currentStep > 1"
                    type="button"
                    @click="previousStep"
                    class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors"
                >
                    Previous
                </button>

                <button
                    v-if="currentStep < totalSteps"
                    type="button"
                    @click="nextStep"
                    :disabled="!canProceed"
                    class="ml-auto px-6 py-2 bg-orange-500 text-white rounded-lg hover:bg-orange-600 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                >
                    Next Step
                </button>

                <button
                    v-if="currentStep === totalSteps"
                    type="submit"
                    :disabled="form.processing"
                    class="ml-auto px-6 py-2 bg-orange-500 text-white rounded-lg hover:bg-orange-600 transition-colors disabled:opacity-50"
                >
                    {{ form.processing ? 'Creating...' : 'Create Property' }}
                </button>
            </div>
        </form>
    </OwnerLayout>
</template>
