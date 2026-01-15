<script setup>
import { ref, computed } from 'vue'

const props = defineProps({
    images: {
        type: Array,
        default: () => [],
    },
    title: {
        type: String,
        default: 'Property',
    },
})

const currentIndex = ref(0)
const lightboxOpen = ref(false)

const currentImage = computed(() => props.images[currentIndex.value])
const hasImages = computed(() => props.images.length > 0)

const nextImage = () => {
    if (currentIndex.value < props.images.length - 1) {
        currentIndex.value++
    } else {
        currentIndex.value = 0
    }
}

const previousImage = () => {
    if (currentIndex.value > 0) {
        currentIndex.value--
    } else {
        currentIndex.value = props.images.length - 1
    }
}

const openLightbox = (index) => {
    currentIndex.value = index
    lightboxOpen.value = true
}

const closeLightbox = () => {
    lightboxOpen.value = false
}

const handleKeydown = (e) => {
    if (!lightboxOpen.value) return
    if (e.key === 'Escape') closeLightbox()
    if (e.key === 'ArrowRight') nextImage()
    if (e.key === 'ArrowLeft') previousImage()
}

// Close lightbox on escape key
if (typeof window !== 'undefined') {
    window.addEventListener('keydown', handleKeydown)
}
</script>

<template>
    <div class="space-y-4">
        <!-- Main Gallery -->
        <div v-if="hasImages" class="space-y-4">
            <!-- Main Image -->
            <div class="relative aspect-video rounded-2xl overflow-hidden bg-gray-100 group cursor-pointer" @click="openLightbox(0)">
                <img
                    :src="currentImage?.image_path || currentImage?.url"
                    :alt="`${title} - Photo ${currentIndex + 1}`"
                    class="h-full w-full object-cover transition-transform duration-300 group-hover:scale-105"
                />
                <div class="absolute inset-0 bg-black/0 group-hover:bg-black/10 transition-colors" />
                <div class="absolute inset-0 flex items-center justify-between opacity-0 group-hover:opacity-100 transition-opacity px-4">
                    <button
                        v-if="images.length > 1"
                        @click.stop="previousImage"
                        class="p-2 bg-white/90 hover:bg-white rounded-full shadow-lg transition-colors"
                    >
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                    </button>
                    <button
                        v-if="images.length > 1"
                        @click.stop="nextImage"
                        class="p-2 bg-white/90 hover:bg-white rounded-full shadow-lg transition-colors"
                    >
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </button>
                </div>
                <button
                    @click.stop="openLightbox(currentIndex)"
                    class="absolute bottom-4 right-4 p-2 bg-white/90 hover:bg-white rounded-full shadow-lg transition-colors"
                >
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l-5-5m11 5l-5-5m5 5v-4m0 4h-4" />
                    </svg>
                </button>
                <span class="absolute top-4 left-4 px-3 py-1 bg-black/50 text-white text-sm rounded-full">
                    {{ currentIndex + 1 }} / {{ images.length }}
                </span>
            </div>

            <!-- Thumbnail Strip -->
            <div v-if="images.length > 1" class="flex gap-2 overflow-x-auto pb-2">
                <button
                    v-for="(image, index) in images"
                    :key="image.id || index"
                    @click="currentIndex = index"
                    class="flex-shrink-0 w-20 h-20 rounded-lg overflow-hidden border-2 transition-colors"
                    :class="currentIndex === index ? 'border-orange-500' : 'border-transparent hover:border-gray-300'"
                >
                    <img
                        :src="image.image_path || image.url"
                        :alt="`${title} - Photo ${index + 1}`"
                        class="h-full w-full object-cover"
                    />
                </button>
            </div>
        </div>

        <!-- Empty State -->
        <div v-else class="aspect-video rounded-2xl bg-gray-100 flex items-center justify-center">
            <svg class="h-16 w-16 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
            </svg>
        </div>

        <!-- Lightbox Modal -->
        <div v-if="lightboxOpen" class="fixed inset-0 z-50 flex items-center justify-center" @click.self="closeLightbox">
            <div class="absolute inset-0 bg-black/90" @click="closeLightbox"></div>

            <button
                @click="closeLightbox"
                class="absolute top-4 right-4 z-10 p-2 text-white hover:text-gray-300 transition-colors"
            >
                <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>

            <div class="relative z-10 w-full h-full flex items-center justify-center p-4">
                <img
                    :src="currentImage?.image_path || currentImage?.url"
                    :alt="`${title} - Photo ${currentIndex + 1}`"
                    class="max-h-full max-w-full object-contain"
                />
            </div>

            <!-- Navigation Arrows -->
            <button
                v-if="images.length > 1"
                @click="previousImage"
                class="absolute left-4 top-1/2 -translate-y-1/2 z-10 p-3 bg-white/10 hover:bg-white/20 text-white rounded-full transition-colors"
            >
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
            </button>
            <button
                v-if="images.length > 1"
                @click="nextImage"
                class="absolute right-4 top-1/2 -translate-y-1/2 z-10 p-3 bg-white/10 hover:bg-white/20 text-white rounded-full transition-colors"
            >
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
            </button>

            <!-- Counter -->
            <div v-if="images.length > 1" class="absolute bottom-4 left-1/2 -translate-x-1/2 px-4 py-2 bg-black/50 text-white text-sm rounded-full">
                {{ currentIndex + 1 }} / {{ images.length }}
            </div>
        </div>
    </div>
</template>
