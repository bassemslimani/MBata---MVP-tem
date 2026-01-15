<script setup>
import { Link, usePage } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'

const page = usePage()

const featuredProperties = [
    {
        id: 1,
        title: 'Villa Dar El Bayda',
        location: 'Algiers, Hydra',
        price: 15000,
        rating: 4.8,
        image: 'https://images.unsplash.com/photo-1613490493576-7fde63acd811?w=800&h=600&fit=crop',
    },
    {
        id: 2,
        title: 'Appartement Oran Center',
        location: 'Oran, Front de Mer',
        price: 8500,
        rating: 4.5,
        image: 'https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?w=800&h=600&fit=crop',
    },
    {
        id: 3,
        title: 'Villa Annaba Beach',
        location: 'Annaba, Plage}',
        price: 12000,
        rating: 4.9,
        image: 'https://images.unsplash.com/photo-1582268611958-ebfd161ef9cf?w=800&h=600&fit=crop',
    },
]

const searchParams = {
    destination: '',
    checkIn: '',
    checkOut: '',
    guests: 1,
}

const searchProperties = () => {
    // Will be implemented with Inertia visit to /search
}
</script>

<template>
    <AppLayout>
        <!-- Hero Section -->
        <section class="relative bg-gray-900 h-[600px] flex items-center">
            <div class="absolute inset-0 overflow-hidden">
                <img
                    src="https://images.unsplash.com/photo-1600596542815-ffad4c1539a9?w=1920&h=1080&fit=crop"
                    alt="Beautiful Algerian villa"
                    class="h-full w-full object-cover opacity-50"
                />
            </div>
            <div class="relative mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-24 text-center">
                <h1 class="text-4xl font-bold tracking-tight text-white sm:text-5xl md:text-6xl">
                    Find Your Perfect Stay in Algeria
                </h1>
                <p class="mt-6 text-lg text-gray-300 max-w-2xl mx-auto">
                    Discover unique homes, villas, and apartments for your next vacation.
                    From the beaches of Oran to the mountains of Kabylie.
                </p>

                <!-- Search Box -->
                <div class="mt-10 bg-white rounded-xl shadow-2xl p-4 max-w-4xl mx-auto">
                    <form @submit.prevent="searchProperties" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div class="md:col-span-1">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Destination</label>
                            <input
                                v-model="searchParams.destination"
                                type="text"
                                placeholder="Where are you going?"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Check-in</label>
                            <input
                                v-model="searchParams.checkIn"
                                type="date"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Check-out</label>
                            <input
                                v-model="searchParams.checkOut"
                                type="date"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            />
                        </div>
                        <div class="flex items-end">
                            <button
                                type="submit"
                                class="w-full bg-indigo-600 text-white px-6 py-2.5 rounded-md font-medium hover:bg-indigo-700 transition-colors"
                            >
                                Search
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </section>

        <!-- Featured Properties -->
        <section class="py-16 bg-gray-50">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-12">
                    <h2 class="text-3xl font-bold text-gray-900">Featured Properties</h2>
                    <p class="mt-4 text-gray-600">Hand-picked accommodations for your perfect stay</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    <div
                        v-for="property in featuredProperties"
                        :key="property.id"
                        class="bg-white rounded-xl shadow-md overflow-hidden hover:shadow-xl transition-shadow"
                    >
                        <Link :href="`/properties/${property.id}`" class="block">
                            <div class="relative h-56">
                                <img
                                    :src="property.image"
                                    :alt="property.title"
                                    class="h-full w-full object-cover"
                                />
                                <div class="absolute top-4 right-4 bg-white px-2 py-1 rounded-md text-sm font-medium text-gray-900 shadow">
                                    {{ property.price.toLocaleString() }} DA/night
                                </div>
                            </div>
                            <div class="p-5">
                                <div class="flex items-start justify-between">
                                    <h3 class="text-lg font-semibold text-gray-900">{{ property.title }}</h3>
                                    <div class="flex items-center gap-1 text-sm">
                                        <svg class="h-4 w-4 text-yellow-400 fill-current" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                        </svg>
                                        <span class="text-gray-600">{{ property.rating }}</span>
                                    </div>
                                </div>
                                <p class="mt-2 text-gray-600 flex items-center gap-1">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                    {{ property.location }}
                                </p>
                            </div>
                        </Link>
                    </div>
                </div>

                <div class="text-center mt-12">
                    <Link
                        href="/search"
                        class="inline-flex items-center gap-2 bg-indigo-600 text-white px-6 py-3 rounded-lg font-medium hover:bg-indigo-700 transition-colors"
                    >
                        View All Properties
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                        </svg>
                    </Link>
                </div>
            </div>
        </section>

        <!-- How It Works -->
        <section class="py-16 bg-white">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-12">
                    <h2 class="text-3xl font-bold text-gray-900">How It Works</h2>
                    <p class="mt-4 text-gray-600">Book your perfect vacation in 3 simple steps</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <div class="text-center">
                        <div class="inline-flex items-center justify-center w-16 h-16 bg-indigo-100 rounded-full text-indigo-600 mb-4">
                            <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">Search</h3>
                        <p class="text-gray-600">Find the perfect property by location, dates, and amenities</p>
                    </div>
                    <div class="text-center">
                        <div class="inline-flex items-center justify-center w-16 h-16 bg-indigo-100 rounded-full text-indigo-600 mb-4">
                            <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">Book</h3>
                        <p class="text-gray-600">Choose your dates and book securely online</p>
                    </div>
                    <div class="text-center">
                        <div class="inline-flex items-center justify-center w-16 h-16 bg-indigo-100 rounded-full text-indigo-600 mb-4">
                            <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">Enjoy</h3>
                        <p class="text-gray-600">Arrive at your destination and enjoy your stay</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- CTA Section -->
        <section class="py-16 bg-indigo-600">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 text-center">
                <h2 class="text-3xl font-bold text-white">List Your Property on Mbata</h2>
                <p class="mt-4 text-indigo-100 max-w-2xl mx-auto">
                    Are you a property owner? Join thousands of hosts who are earning money by renting their homes on Mbata.
                </p>
                <div class="mt-8">
                    <Link
                        href="/owner/register"
                        class="inline-block bg-white text-indigo-600 px-8 py-3 rounded-lg font-medium hover:bg-gray-100 transition-colors"
                    >
                        Become a Host
                    </Link>
                </div>
            </div>
        </section>
    </AppLayout>
</template>
