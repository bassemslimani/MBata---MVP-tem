<script setup>
import { computed } from 'vue'

const props = defineProps({
    status: {
        type: String,
        required: true
    },
    type: {
        type: String,
        default: 'default',
        validator: (value) => ['default', 'user', 'property', 'reservation', 'review', 'payment'].includes(value)
    }
})

const statusConfig = computed(() => {
    const status = props.status?.toLowerCase() || ''

    const configs = {
        // Positive statuses
        active: { color: 'green', label: 'admin.shared.status_badge.active' },
        verified: { color: 'green', label: 'admin.shared.status_badge.verified' },
        approved: { color: 'green', label: 'admin.shared.status_badge.approved' },
        confirmed: { color: 'green', label: 'admin.shared.status_badge.confirmed' },
        completed: { color: 'green', label: 'admin.shared.status_badge.completed' },
        visible: { color: 'green', label: 'common.yes' },

        // Warning/pending statuses
        pending: { color: 'yellow', label: 'admin.shared.status_badge.pending' },
        inactive: { color: 'gray', label: 'admin.shared.status_badge.inactive' },

        // Negative statuses
        suspended: { color: 'red', label: 'admin.shared.status_badge.suspended' },
        rejected: { color: 'red', label: 'admin.shared.status_badge.rejected' },
        cancelled: { color: 'red', label: 'admin.shared.status_badge.cancelled' },
        hidden: { color: 'red', label: 'admin.shared.status_badge.hidden' },
        deleted: { color: 'red', label: 'common.delete' },
    }

    return configs[status] || { color: 'gray', label: status }
})

const colorClasses = computed(() => {
    const colors = {
        green: 'bg-green-100 text-green-700 border-green-200',
        yellow: 'bg-yellow-100 text-yellow-700 border-yellow-200',
        red: 'bg-red-100 text-red-700 border-red-200',
        gray: 'bg-gray-100 text-gray-700 border-gray-200',
        blue: 'bg-blue-100 text-blue-700 border-blue-200',
    }
    return colors[statusConfig.value.color] || colors.gray
})
</script>

<template>
    <span
        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border"
        :class="colorClasses"
    >
        {{ $t(statusConfig.label) }}
    </span>
</template>
