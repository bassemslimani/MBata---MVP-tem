<script setup>
import { ref } from 'vue'
import { Link, useForm } from '@inertiajs/vue3'
import OwnerLayout from '@/Layouts/OwnerLayout.vue'
import ImageUploader from '@/Components/Property/ImageUploader.vue'

const props = defineProps({
    property: {
        type: Object,
        required: true,
    },
})

const imageUploaderRef = ref(null)
const savingOrder = ref(false)

const reorderForm = useForm({
    images: [],
})

const handleImagesUpdated = (images) => {
    // Called when images are added or removed
    // We don't need to do anything special here as the component handles it
}

const handleImageDeleted = (image) => {
    // Image deletion is handled within the component
}

const saveOrder = async () => {
    if (!imageUploaderRef.value) return

    savingOrder.value = true
    const images = imageUploaderRef.value.getImagesForSubmit()

    reorderForm.images = images.map((img, index) => ({
        id: img.id,
        order: index,
        is_primary: img.is_primary,
    }))

    reorderForm.put(`/owner/properties/${props.property.id}/images/reorder`, {
        preserveScroll: true,
        onFinish: () => {
            savingOrder.value = false
        },
    })
}
</script>

<template>
    <OwnerLayout>
        <div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <div class="flex items-center gap-2">
                    <Link
                        href="/owner/properties"
                        class="text-gray-400 hover:text-gray-600"
                    >
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                    </Link>
                    <h1 class="text-2xl font-bold text-gray-900">Property Photos</h1>
                </div>
                <p class="text-gray-600 mt-1">{{ property.title }}</p>
            </div>

            <Link
                :href="`/owner/properties/${property.id}/edit`"
                class="inline-flex items-center gap-2 px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors"
            >
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
                Edit Property
            </Link>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Main Content -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Upload & Manage Photos</h2>

                    <ImageUploader
                        ref="imageUploaderRef"
                        :property-id="property.id"
                        :existing-images="property.images || []"
                        :max-images="15"
                        @update:images="handleImagesUpdated"
                        @image-uploaded="handleImagesUpdated"
                        @image-deleted="handleImageDeleted"
                    />
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Tips Card -->
                <div class="bg-orange-50 rounded-xl p-6">
                    <h3 class="font-semibold text-orange-900 flex items-center gap-2">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
                        </svg>
                        Photo Tips
                    </h3>
                    <ul class="mt-4 space-y-2 text-sm text-orange-800">
                        <li class="flex items-start gap-2">
                            <svg class="h-4 w-4 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                            </svg>
                            Use bright, natural lighting
                        </li>
                        <li class="flex items-start gap-2">
                            <svg class="h-4 w-4 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                            </svg>
                            Show all main rooms and spaces
                        </li>
                        <li class="flex items-start gap-2">
                            <svg class="h-4 w-4 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                            </svg>
                            Include exterior and views
                        </li>
                        <li class="flex items-start gap-2">
                            <svg class="h-4 w-4 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                            </svg>
                            First image becomes the cover photo
                        </li>
                        <li class="flex items-start gap-2">
                            <svg class="h-4 w-4 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                            </svg>
                            Recommended size: 1920x1080px
                        </li>
                    </ul>
                </div>

                <!-- Quick Actions -->
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <h3 class="font-semibold text-gray-900 mb-4">Quick Actions</h3>
                    <div class="space-y-3">
                        <Link
                            :href="`/owner/properties/${property.id}/availabilities`"
                            class="flex items-center gap-3 text-gray-700 hover:text-orange-600 transition-colors"
                        >
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            Manage Availability
                        </Link>
                        <Link
                            :href="`/properties/${property.id}`"
                            target="_blank"
                            class="flex items-center gap-3 text-gray-700 hover:text-orange-600 transition-colors"
                        >
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                            Preview Listing
                        </Link>
                        <Link
                            href="/owner/properties"
                            class="flex items-center gap-3 text-gray-700 hover:text-orange-600 transition-colors"
                        >
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                            </svg>
                            Back to Properties
                        </Link>
                    </div>
                </div>

                <!-- Stats -->
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <h3 class="font-semibold text-gray-900 mb-4">Photo Stats</h3>
                    <div class="space-y-3">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Total Photos</span>
                            <span class="font-medium">{{ property.images?.length || 0 }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Primary Photo</span>
                            <span class="font-medium">{{ property.images?.find(i => i.is_primary) ? 'Set' : 'Not set' }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </OwnerLayout>
</template>
