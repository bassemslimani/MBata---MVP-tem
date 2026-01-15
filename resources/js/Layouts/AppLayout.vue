<script setup>
import { computed } from 'vue'
import { Link, usePage } from '@inertiajs/vue3'
import AppHeader from '@/Components/Layout/AppHeader.vue'
import AppFooter from '@/Components/Layout/AppFooter.vue'

const page = usePage()
const locale = computed(() => page.props.locale || 'fr')
const isRtl = computed(() => locale.value === 'ar')
</script>

<template>
    <div
        class="min-h-screen flex flex-col bg-gray-50"
        :dir="isRtl ? 'rtl' : 'ltr'"
        :class="{ 'rtl': isRtl }"
    >
        <AppHeader />

        <main class="flex-1">
            <slot />
        </main>

        <AppFooter />
    </div>
</template>
