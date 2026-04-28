<script setup>
import { ref, computed } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import FlashMessages from '@/Components/FlashMessages.vue';

const page = usePage();
const user = computed(() => page.props.auth.user);
const isEmployer = computed(() => user.value?.role === 'employer');

const mobileOpen = ref(false);
</script>

<template>
    <div class="min-h-screen bg-gray-50">
        <header class="sticky top-0 z-30 border-b border-gray-200 bg-white/90 backdrop-blur">
            <div class="mx-auto flex h-16 max-w-7xl items-center justify-between px-4 sm:px-6 lg:px-8">
                <!-- Brand -->
                <Link :href="route('jobs.index')" class="flex items-center gap-2">
                    <span class="flex h-9 w-9 items-center justify-center rounded-lg bg-indigo-600 text-lg font-bold text-white">C</span>
                    <span class="text-lg font-bold tracking-tight text-gray-900">CareerHub</span>
                </Link>

                <!-- Desktop nav -->
                <nav class="hidden items-center gap-1 sm:flex">
                    <Link
                        :href="route('jobs.index')"
                        class="rounded-md px-3 py-2 text-sm font-medium text-gray-600 transition hover:bg-gray-100 hover:text-gray-900"
                    >
                        Browse Jobs
                    </Link>

                    <template v-if="user">
                        <Link
                            :href="route('dashboard')"
                            class="rounded-md px-3 py-2 text-sm font-medium text-gray-600 transition hover:bg-gray-100 hover:text-gray-900"
                        >
                            Dashboard
                        </Link>
                        <Link
                            v-if="isEmployer"
                            :href="route('jobs.create')"
                            class="rounded-md px-3 py-2 text-sm font-medium text-gray-600 transition hover:bg-gray-100 hover:text-gray-900"
                        >
                            Post a Job
                        </Link>
                        <Link
                            v-else
                            :href="route('applications.index')"
                            class="rounded-md px-3 py-2 text-sm font-medium text-gray-600 transition hover:bg-gray-100 hover:text-gray-900"
                        >
                            My Applications
                        </Link>
                        <span class="mx-1 hidden text-sm text-gray-400 lg:inline">|</span>
                        <Link
                            :href="route('profile.edit')"
                            class="hidden rounded-md px-3 py-2 text-sm font-medium text-gray-600 transition hover:bg-gray-100 hover:text-gray-900 lg:inline-block"
                        >
                            {{ user.name }}
                        </Link>
                        <Link
                            :href="route('logout')"
                            method="post"
                            as="button"
                            class="rounded-md px-3 py-2 text-sm font-medium text-gray-600 transition hover:bg-gray-100 hover:text-gray-900"
                        >
                            Log Out
                        </Link>
                    </template>
                    <template v-else>
                        <Link
                            :href="route('login')"
                            class="rounded-md px-3 py-2 text-sm font-medium text-gray-600 transition hover:bg-gray-100 hover:text-gray-900"
                        >
                            Log In
                        </Link>
                        <Link
                            :href="route('register')"
                            class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-500"
                        >
                            Sign Up
                        </Link>
                    </template>
                </nav>

                <!-- Mobile toggle -->
                <button
                    class="rounded-md p-2 text-gray-500 hover:bg-gray-100 sm:hidden"
                    @click="mobileOpen = !mobileOpen"
                    aria-label="Toggle menu"
                >
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path v-if="!mobileOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <!-- Mobile menu -->
            <div v-show="mobileOpen" class="space-y-1 border-t border-gray-200 px-4 py-3 sm:hidden">
                <Link :href="route('jobs.index')" class="block rounded-md px-3 py-2 text-base font-medium text-gray-700 hover:bg-gray-100">Browse Jobs</Link>
                <template v-if="user">
                    <Link :href="route('dashboard')" class="block rounded-md px-3 py-2 text-base font-medium text-gray-700 hover:bg-gray-100">Dashboard</Link>
                    <Link v-if="isEmployer" :href="route('jobs.create')" class="block rounded-md px-3 py-2 text-base font-medium text-gray-700 hover:bg-gray-100">Post a Job</Link>
                    <Link v-else :href="route('applications.index')" class="block rounded-md px-3 py-2 text-base font-medium text-gray-700 hover:bg-gray-100">My Applications</Link>
                    <Link :href="route('profile.edit')" class="block rounded-md px-3 py-2 text-base font-medium text-gray-700 hover:bg-gray-100">Profile</Link>
                    <Link :href="route('logout')" method="post" as="button" class="block w-full rounded-md px-3 py-2 text-left text-base font-medium text-gray-700 hover:bg-gray-100">Log Out</Link>
                </template>
                <template v-else>
                    <Link :href="route('login')" class="block rounded-md px-3 py-2 text-base font-medium text-gray-700 hover:bg-gray-100">Log In</Link>
                    <Link :href="route('register')" class="block rounded-md px-3 py-2 text-base font-medium text-indigo-600 hover:bg-gray-100">Sign Up</Link>
                </template>
            </div>
        </header>

        <FlashMessages />

        <main>
            <slot />
        </main>

        <footer class="mt-16 border-t border-gray-200 bg-white">
            <div class="mx-auto max-w-7xl px-4 py-8 text-center text-sm text-gray-500 sm:px-6 lg:px-8">
                CareerHub - a Laravel + Inertia + Vue demo by Md. Nafiz Ahmed.
            </div>
        </footer>
    </div>
</template>
