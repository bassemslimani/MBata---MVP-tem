<script setup>
import { ref, computed } from 'vue'
import { useForm } from '@inertiajs/vue3'

const props = defineProps({
    propertyId: {
        type: [String, Number],
        default: null,
    },
    existingImages: {
        type: Array,
        default: () => [],
    },
    maxImages: {
        type: Number,
        default: 10,
    },
})

const emit = defineEmits(['update:images', 'image-uploaded', 'image-deleted'])

const images = ref([...props.existingImages])
const isDragging = ref(false)
const uploadProgress = ref({})
const uploadingCount = ref(0)

const remainingSlots = computed(() => props.maxImages - images.value.length)

const handleDragEnter = (e) => {
    e.preventDefault()
    isDragging.value = true
}

const handleDragLeave = (e) => {
    e.preventDefault()
    if (e.target === e.currentTarget) {
        isDragging.value = false
    }
}

const handleDragOver = (e) => {
    e.preventDefault()
}

const handleDrop = (e) => {
    e.preventDefault()
    isDragging.value = false
    const files = Array.from(e.dataTransfer.files).filter(f => f.type.startsWith('image/'))
    uploadFiles(files)
}

const handleFileSelect = (e) => {
    const files = Array.from(e.target.files)
    uploadFiles(files)
}

const uploadFiles = async (files) => {
    const availableSlots = remainingSlots.value
    if (availableSlots <= 0) {
        alert('Maximum number of images reached')
        return
    }

    const filesToUpload = files.slice(0, availableSlots)

    for (const file of filesToUpload) {
        await uploadSingleFile(file)
    }
}

const uploadSingleFile = async (file) => {
    uploadingCount.value++
    const fileId = Date.now() + Math.random()

    const form = useForm({
        image: file,
        property_id: props.propertyId,
    })

    // For existing properties, upload to server
    if (props.propertyId) {
        try {
            await form.post(`/owner/properties/${props.propertyId}/images`, {
                onSuccess: (page) => {
                    const newImage = page.props.images?.find(i => i.temp_id === fileId)
                    if (newImage) {
                        images.value.push(newImage)
                    }
                    emit('image-uploaded', newImage)
                    delete uploadProgress.value[fileId]
                    uploadingCount.value--
                },
                onProgress: (progress) => {
                    uploadProgress.value[fileId] = Math.round(progress.progress)
                },
                onError: () => {
                    delete uploadProgress.value[fileId]
                    uploadingCount.value--
                },
            })
        } catch (error) {
            delete uploadProgress.value[fileId]
            uploadingCount.value--
        }
    } else {
        // For new properties, create preview URL
        const reader = new FileReader()
        reader.onload = (e) => {
            const newImage = {
                id: fileId,
                temp_id: fileId,
                image_path: e.target.result,
                is_primary: images.value.length === 0,
                order: images.value.length,
                is_new: true,
                file: file,
            }
            images.value.push(newImage)
            emit('image-uploaded', newImage)
            delete uploadProgress.value[fileId]
            uploadingCount.value--
        }
        reader.readAsDataURL(file)
    }
}

const setPrimary = (image) => {
    images.value = images.value.map(img => ({
        ...img,
        is_primary: img.id === image.id || img.temp_id === image.temp_id,
    }))
    emit('update:images', images.value)

    if (props.propertyId && !image.is_new) {
        useForm({}).put(`/owner/properties/${props.propertyId}/images/${image.id}/primary`, {
            preserveScroll: true,
        })
    }
}

const deleteImage = (image) => {
    if (!confirm('Are you sure you want to delete this image?')) return

    images.value = images.value.filter(
        img => img.id !== image.id && img.temp_id !== image.temp_id
    )

    // If deleted image was primary, set first image as primary
    if (image.is_primary && images.value.length > 0) {
        images.value[0].is_primary = true
    }

    emit('update:images', images.value)
    emit('image-deleted', image)

    if (props.propertyId && !image.is_new) {
        useForm({}).delete(`/owner/properties/${props.propertyId}/images/${image.id}`, {
            preserveScroll: true,
        })
    }
}

const getImagesForSubmit = () => {
    return images.value.map((img, index) => ({
        ...img,
        order: index,
    }))
}

defineExpose({
    getImagesForSubmit,
})
</script>

<template>
    <div class="space-y-4">
        <!-- Upload Area -->
        <div v-if="remainingSlots > 0" class="relative">
            <div
                class="border-2 border-dashed rounded-xl p-8 text-center transition-colors cursor-pointer"
                :class="[
                    isDragging
                        ? 'border-orange-500 bg-orange-50'
                        : 'border-gray-300 hover:border-gray-400'
                ]"
                @dragenter="handleDragEnter"
                @dragleave="handleDragLeave"
                @dragover="handleDragOver"
                @drop="handleDrop"
                @click="$refs.fileInput.click()"
            >
                <input
                    ref="fileInput"
                    type="file"
                    multiple
                    accept="image/*"
                    class="hidden"
                    @change="handleFileSelect"
                />
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                <p class="mt-4 text-sm text-gray-600">
                    <span class="font-medium text-orange-600">Click to upload</span>
                    or drag and drop
                </p>
                <p class="mt-1 text-xs text-gray-500">PNG, JPG up to 5MB (max {{ maxImages }} images)</p>
            </div>
        </div>

        <p v-else class="text-sm text-gray-500 text-center">Maximum number of images reached</p>

        <!-- Upload Progress -->
        <div v-if="uploadingCount > 0" class="space-y-2">
            <div
                v-for="(progress, fileId) in uploadProgress"
                :key="fileId"
                class="bg-gray-100 rounded-full h-2 overflow-hidden"
            >
                <div
                    class="bg-orange-500 h-full transition-all duration-300"
                    :style="{ width: progress + '%' }"
                />
            </div>
            <p class="text-xs text-gray-500">Uploading {{ uploadingCount }} image(s)... {{ Object.values(uploadProgress)[0] }}%</p>
        </div>

        <!-- Image Grid -->
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
            <div
                v-for="(image, index) in images"
                :key="image.id || image.temp_id"
                class="relative aspect-square rounded-lg overflow-hidden group"
            >
                <img
                    :src="image.image_path"
                    :alt="`Property image ${index + 1}`"
                    class="h-full w-full object-cover"
                />

                <!-- Primary Badge -->
                <div
                    v-if="image.is_primary"
                    class="absolute top-2 left-2 bg-orange-500 text-white text-xs px-2 py-1 rounded-full font-medium"
                >
                    Primary
                </div>

                <!-- Actions Overlay -->
                <div class="absolute inset-0 bg-black/50 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center gap-2">
                    <button
                        v-if="!image.is_primary"
                        @click="setPrimary(image)"
                        class="p-2 bg-white rounded-full hover:bg-gray-100 transition-colors"
                        title="Set as primary"
                    >
                        <svg class="h-4 w-4 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.921-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.07-3.292z" />
                        </svg>
                    </button>
                    <button
                        @click="deleteImage(image)"
                        class="p-2 bg-red-500 rounded-full hover:bg-red-600 transition-colors"
                        title="Delete"
                    >
                        <svg class="h-4 w-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Empty State -->
        <div v-if="images.length === 0" class="text-center py-12 bg-gray-50 rounded-xl border-2 border-dashed border-gray-200">
            <svg class="mx-auto h-12 w-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
            </svg>
            <p class="mt-4 text-sm text-gray-500">No images uploaded yet</p>
            <p class="text-xs text-gray-400 mt-1">Add at least one image to showcase your property</p>
        </div>

        <p class="text-xs text-gray-500 text-center">
            {{ images.length }} / {{ maxImages }} images
        </p>
    </div>
</template>
