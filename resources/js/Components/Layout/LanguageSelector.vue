<script setup>
import { ref, computed } from 'vue'
import { usePage } from '@inertiajs/vue3'

const page = usePage()
const currentLocale = computed(() => page.props.locale || 'fr')

const open = ref(false)

const locales = [
    { code: 'fr', name: 'Français', flag: '🇫🇷' },
    { code: 'ar', name: 'العربية', flag: '🇩🇿' },
    { code: 'en', name: 'English', flag: '🇬🇧' },
]

const currentLocaleData = computed(() =>
    locales.find(l => l.code === currentLocale.value) || locales[0]
)

const changeLocale = (localeCode) => {
    open.value = false
    // This will make a request to change locale
    // The backend should handle this and redirect back
    window.location.href = `/locale/${localeCode}`
}
</script>

<template>
    <div class="relative" v-click-away="() => open = false">
        <button
            type="button"
            @click="open = !open"
            class="inline-flex items-center gap-1 rounded-md border border-gray-300 bg-white px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
        >
            <span class="text-lg">{{ currentLocaleData.flag }}</span>
            <span class="hidden sm:inline">{{ currentLocaleData.code.toUpperCase() }}</span>
            <svg
                class="h-4 w-4"
                :class="{ 'rotate-180': open }"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
            >
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
            </svg>
        </button>

        <div
            v-if="open"
            class="absolute right-0 z-50 mt-2 w-40 origin-top-right rounded-md bg-white shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none"
            :class="{ 'left-0 right-auto': currentLocale === 'ar' }"
        >
            <div class="py-1">
                <button
                    v-for="locale in locales"
                    :key="locale.code"
                    type="button"
                    @click="changeLocale(locale.code)"
                    class="flex w-full items-center gap-2 px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                    :class="{ 'bg-indigo-50 text-indigo-600': locale.code === currentLocale }"
                >
                    <span class="text-lg">{{ locale.flag }}</span>
                    <span>{{ locale.name }}</span>
                </button>
            </div>
        </div>
    </div>
</template>
