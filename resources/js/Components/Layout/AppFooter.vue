<script setup>
import { Link } from '@inertiajs/vue3'
</script>

<template>
    <footer class="bg-gray-900 text-gray-300">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-12">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <!-- Brand -->
                <div class="col-span-1 md:col-span-1">
                    <Link :href="route('home')" class="flex items-center gap-2">
                        <svg class="h-8 w-8 text-indigo-400" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z" />
                        </svg>
                        <span class="text-xl font-bold text-white">Mbata</span>
                    </Link>
                    <p class="mt-4 text-sm text-gray-400">
                        Your gateway to the best vacation rentals across Algeria.
                    </p>
                </div>

                <!-- Quick Links -->
                <div>
                    <h3 class="text-sm font-semibold text-white tracking-wider uppercase mb-4">
                        Quick Links
                    </h3>
                    <ul class="space-y-3">
                        <li>
                            <Link :href="route('home')" class="text-sm hover:text-white transition-colors">
                                Home
                            </Link>
                        </li>
                        <li>
                            <Link href="/search" class="text-sm hover:text-white transition-colors">
                                Properties
                            </Link>
                        </li>
                        <li>
                            <Link href="/promotions" class="text-sm hover:text-white transition-colors">
                                Real Estate
                            </Link>
                        </li>
                    </ul>
                </div>

                <!-- For Owners -->
                <div>
                    <h3 class="text-sm font-semibold text-white tracking-wider uppercase mb-4">
                        For Owners
                    </h3>
                    <ul class="space-y-3">
                        <li>
                            <Link href="/owner/register" class="text-sm hover:text-white transition-colors">
                                List Your Property
                            </Link>
                        </li>
                        <li>
                            <Link href="/owner/dashboard" class="text-sm hover:text-white transition-colors">
                                Owner Dashboard
                            </Link>
                        </li>
                    </ul>
                </div>

                <!-- Support -->
                <div>
                    <h3 class="text-sm font-semibold text-white tracking-wider uppercase mb-4">
                        Support
                    </h3>
                    <ul class="space-y-3">
                        <li>
                            <Link href="/contact" class="text-sm hover:text-white transition-colors">
                                Contact Us
                            </Link>
                        </li>
                        <li>
                            <Link href="/privacy" class="text-sm hover:text-white transition-colors">
                                Privacy Policy
                            </Link>
                        </li>
                        <li>
                            <Link href="/terms" class="text-sm hover:text-white transition-colors">
                                Terms of Service
                            </Link>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="mt-8 pt-8 border-t border-gray-800">
                <p class="text-sm text-gray-400 text-center">
                    &copy; {{ new Date().getFullYear() }} Mbata. All rights reserved.
                </p>
            </div>
        </div>
    </footer>
</template>
