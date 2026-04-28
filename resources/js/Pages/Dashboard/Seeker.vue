<script setup>
import { Head, Link } from '@inertiajs/vue3';
import PublicLayout from '@/Layouts/PublicLayout.vue';
import ApplicationRow from '@/Components/ApplicationRow.vue';

defineProps({
    applications: { type: Array, default: () => [] },
    stats: { type: Object, default: () => ({}) },
});
</script>

<template>
    <Head title="My Dashboard" />

    <PublicLayout>
        <div class="mx-auto max-w-5xl px-4 py-8 sm:px-6 lg:px-8">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Your dashboard</h1>
                    <p class="mt-1 text-sm text-gray-500">Track the jobs you have applied to.</p>
                </div>
                <Link :href="route('jobs.index')" class="inline-flex items-center gap-2 rounded-lg bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-500">
                    Browse jobs
                </Link>
            </div>

            <div class="mt-6 grid grid-cols-1 gap-4 sm:grid-cols-3">
                <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
                    <p class="text-sm text-gray-500">Applications</p>
                    <p class="mt-1 text-3xl font-bold text-gray-900">{{ stats.applications }}</p>
                </div>
                <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
                    <p class="text-sm text-gray-500">Pending</p>
                    <p class="mt-1 text-3xl font-bold text-amber-600">{{ stats.pending }}</p>
                </div>
                <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
                    <p class="text-sm text-gray-500">Accepted</p>
                    <p class="mt-1 text-3xl font-bold text-emerald-600">{{ stats.accepted }}</p>
                </div>
            </div>

            <div class="mt-8 overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
                <div class="flex items-center justify-between border-b border-gray-200 px-5 py-4">
                    <h2 class="font-semibold text-gray-900">Recent applications</h2>
                    <Link :href="route('applications.index')" class="text-sm font-medium text-indigo-600 hover:underline">View all</Link>
                </div>
                <div v-if="applications.length" class="divide-y divide-gray-100">
                    <ApplicationRow v-for="app in applications.slice(0, 5)" :key="app.id" :application="app" />
                </div>
                <div v-else class="px-5 py-16 text-center">
                    <p class="text-base font-medium text-gray-900">No applications yet</p>
                    <p class="mt-1 text-sm text-gray-500">Browse jobs and apply to get started.</p>
                </div>
            </div>
        </div>
    </PublicLayout>
</template>
