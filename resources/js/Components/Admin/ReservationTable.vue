<script setup>
import { ref, computed } from 'vue'
import { Link } from '@inertiajs/vue3'
import StatusBadge from './StatusBadge.vue'

const props = defineProps({
    reservations: {
        type: Array,
        default: () => []
    }
})

const emit = defineEmits(['view-details'])

const selectedReservation = ref(null)
const showDetailsDialog = ref(false)

const viewDetails = (reservation) => {
    selectedReservation.value = reservation
    showDetailsDialog.value = true
    emit('view-details', reservation)
}

const closeDetails = () => {
    showDetailsDialog.value = false
    selectedReservation.value = null
}

const formatDate = (date) => {
    if (!date) return '-'
    return new Date(date).toLocaleDateString()
}

const formatPrice = (price) => {
    if (!price) return '-'
    return new Intl.NumberFormat('fr-DZ').format(price) + ' DA'
}

const getPaymentMethodLabel = (method) => {
    const methods = {
        card: 'admin.reservations.payment_methods.card',
        ccp: 'admin.reservations.payment_methods.ccp',
        cash: 'admin.reservations.payment_methods.cash'
    }
    return methods[method] || method
}

const getNightsCount = (checkIn, checkOut) => {
    if (!checkIn || !checkOut) return 0
    const start = new Date(checkIn)
    const end = new Date(checkOut)
    return Math.ceil((end - start) / (1000 * 60 * 60 * 24))
}
</script>

<template>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        {{ $t('admin.reservations.table.id') }}
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        {{ $t('admin.reservations.table.property') }}
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        {{ $t('admin.reservations.table.guest') }}
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        {{ $t('admin.reservations.table.check_in') }}
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        {{ $t('admin.reservations.table.check_out') }}
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        {{ $t('admin.reservations.table.guests') }}
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        {{ $t('admin.reservations.table.total') }}
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        {{ $t('admin.reservations.table.payment') }}
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        {{ $t('admin.reservations.table.status') }}
                    </th>
                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                        {{ $t('admin.reservations.table.actions') }}
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <tr v-if="reservations.length === 0">
                    <td colspan="10" class="px-6 py-12 text-center text-gray-500">
                        <svg class="mx-auto h-12 w-12 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        <p class="mt-2 text-sm">{{ $t('admin.reservations.empty') }}</p>
                        <p class="text-xs text-gray-400">{{ $t('admin.reservations.empty_subtitle') }}</p>
                    </td>
                </tr>
                <tr v-for="reservation in reservations" :key="reservation.id" class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-mono text-gray-900">
                        #{{ reservation.id }}
                    </td>
                    <td class="px-6 py-4">
                        <div class="text-sm font-medium text-gray-900 line-clamp-1">
                            {{ reservation.property?.title || '-' }}
                        </div>
                        <div class="text-xs text-gray-500">
                            {{ reservation.property?.wilaya || '' }}
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900">{{ reservation.user?.name || '-' }}</div>
                        <div class="text-xs text-gray-500">{{ reservation.user?.email || '' }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ formatDate(reservation.check_in) }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ formatDate(reservation.check_out) }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ reservation.guests || 1 }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                        {{ formatPrice(reservation.total_price) }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                            :class="{
                                'bg-blue-100 text-blue-700': reservation.payment_method === 'card',
                                'bg-green-100 text-green-700': reservation.payment_method === 'ccp',
                                'bg-yellow-100 text-yellow-700': reservation.payment_method === 'cash'
                            }">
                            {{ $t(getPaymentMethodLabel(reservation.payment_method)) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <StatusBadge
                            :status="reservation.status"
                            type="reservation"
                        />
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <button
                            @click="viewDetails(reservation)"
                            class="text-orange-600 hover:text-orange-900"
                        >
                            {{ $t('admin.reservations.table.view') }}
                        </button>
                    </td>
                </tr>
            </tbody>
        </table>

        <!-- Details Dialog -->
        <Teleport to="body">
            <Transition
                enter-active-class="transition ease-out duration-300"
                enter-from-class="opacity-0"
                enter-to-class="opacity-100"
                leave-active-class="transition ease-in duration-200"
                leave-from-class="opacity-100"
                leave-to-class="opacity-0"
            >
                <div
                    v-if="showDetailsDialog && selectedReservation"
                    class="fixed inset-0 z-50 overflow-y-auto"
                    role="dialog"
                    aria-modal="true"
                >
                    <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="closeDetails"></div>

                    <div class="flex min-h-full items-center justify-center p-4">
                        <Transition
                            enter-active-class="transition ease-out duration-300"
                            enter-from-class="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                            enter-to-class="opacity-100 translate-y-0 sm:scale-100"
                            leave-active-class="transition ease-in duration-200"
                            leave-from-class="opacity-100 translate-y-0 sm:scale-100"
                            leave-to-class="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                        >
                            <div class="relative transform overflow-hidden rounded-lg bg-white shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-2xl">
                                <div class="bg-white px-4 pb-4 pt-5 sm:p-6">
                                    <div class="flex items-start justify-between">
                                        <h3 class="text-lg font-semibold text-gray-900">
                                            {{ $t('admin.reservations.table.id') }} #{{ selectedReservation.id }}
                                        </h3>
                                        <button @click="closeDetails" class="text-gray-400 hover:text-gray-500">
                                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                        </button>
                                    </div>

                                    <div class="mt-6 grid grid-cols-2 gap-6">
                                        <!-- Property Info -->
                                        <div>
                                            <h4 class="text-sm font-medium text-gray-500 mb-2">{{ $t('admin.reservations.table.property') }}</h4>
                                            <p class="text-sm text-gray-900">{{ selectedReservation.property?.title }}</p>
                                            <p class="text-xs text-gray-500">{{ selectedReservation.property?.wilaya }}</p>
                                        </div>

                                        <!-- Guest Info -->
                                        <div>
                                            <h4 class="text-sm font-medium text-gray-500 mb-2">{{ $t('admin.reservations.table.guest') }}</h4>
                                            <p class="text-sm text-gray-900">{{ selectedReservation.user?.name }}</p>
                                            <p class="text-xs text-gray-500">{{ selectedReservation.user?.email }}</p>
                                        </div>

                                        <!-- Dates -->
                                        <div>
                                            <h4 class="text-sm font-medium text-gray-500 mb-2">{{ $t('property.booking.trip_details') }}</h4>
                                            <p class="text-sm text-gray-900">
                                                {{ formatDate(selectedReservation.check_in) }} - {{ formatDate(selectedReservation.check_out) }}
                                            </p>
                                            <p class="text-xs text-gray-500">
                                                {{ getNightsCount(selectedReservation.check_in, selectedReservation.check_out) }} {{ $t('common.nights') }}
                                            </p>
                                        </div>

                                        <!-- Payment -->
                                        <div>
                                            <h4 class="text-sm font-medium text-gray-500 mb-2">{{ $t('property.booking.pay_with') }}</h4>
                                            <p class="text-sm text-gray-900">{{ $t(getPaymentMethodLabel(selectedReservation.payment_method)) }}</p>
                                            <p class="text-sm font-medium text-orange-600">{{ formatPrice(selectedReservation.total_price) }}</p>
                                        </div>

                                        <!-- Status -->
                                        <div class="col-span-2">
                                            <h4 class="text-sm font-medium text-gray-500 mb-2">{{ $t('admin.reservations.table.status') }}</h4>
                                            <StatusBadge :status="selectedReservation.status" type="reservation" />
                                        </div>
                                    </div>
                                </div>

                                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                                    <Link
                                        :href="route('client.reservations.show', selectedReservation.id)"
                                        class="inline-flex w-full justify-center rounded-md bg-orange-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-orange-500 sm:ml-3 sm:w-auto"
                                    >
                                        {{ $t('admin.reservations.table.view') }}
                                    </Link>
                                    <button
                                        type="button"
                                        class="mt-3 inline-flex w-full justify-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:mt-0 sm:w-auto"
                                        @click="closeDetails"
                                    >
                                        {{ $t('common.close') }}
                                    </button>
                                </div>
                            </div>
                        </Transition>
                    </div>
                </div>
            </Transition>
        </Teleport>
    </div>
</template>
