<script setup>
import { Head, Link } from '@inertiajs/vue3';
import PublicLayout from '@/Layouts/PublicLayout.vue';
import Badge from '@/Components/Badge.vue';

defineProps({
    jobs: { type: Array, default: () => [] },
    stats: { type: Object, default: () => ({}) },
});
</script>

<template>
    <Head title="Employer Dashboard" />

    <PublicLayout>
        <div class="mx-auto max-w-6xl px-4 py-8 sm:px-6 lg:px-8">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Employer dashboard</h1>
                    <p class="mt-1 text-sm text-gray-500">Manage your listings and review applicants.</p>
                </div>
                <Link :href="route('jobs.create')" class="inline-flex items-center gap-2 rounded-lg bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-500">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                    Post a job
                </Link>
            </div>

            <!-- Stats -->
            <div class="mt-6 grid grid-cols-1 gap-4 sm:grid-cols-3">
                <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
                    <p class="text-sm text-gray-500">Total jobs</p>
                    <p class="mt-1 text-3xl font-bold text-gray-900">{{ stats.jobs }}</p>
                </div>
                <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
                    <p class="text-sm text-gray-500">Open positions</p>
                    <p class="mt-1 text-3xl font-bold text-emerald-600">{{ stats.open }}</p>
                </div>
                <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
                    <p class="text-sm text-gray-500">Total applicants</p>
                    <p class="mt-1 text-3xl font-bold text-indigo-600">{{ stats.applicants }}</p>
                </div>
            </div>

            <!-- Jobs table -->
            <div class="mt-8 overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
                <div class="border-b border-gray-200 px-5 py-4">
                    <h2 class="font-semibold text-gray-900">Your job listings</h2>
                </div>
                <div v-if="jobs.length" class="divide-y divide-gray-100">
                    <div v-for="job in jobs" :key="job.id" class="flex flex-wrap items-center justify-between gap-3 px-5 py-4">
                        <div class="min-w-0">
                            <div class="flex items-center gap-2">
                                <Link :href="route('jobs.show', job.id)" class="font-medium text-gray-900 hover:text-indigo-600">{{ job.title }}</Link>
                                <Badge :label="job.type" :variant="job.type" />
                                <Badge v-if="!job.is_open" label="closed" variant="closed" />
                            </div>
                            <p class="mt-0.5 text-sm text-gray-500">{{ job.location }}</p>
                        </div>
                        <div class="flex items-center gap-4">
                            <Link :href="route('jobs.applicants', job.id)" class="text-sm font-medium text-gray-700 hover:text-indigo-600">
                                {{ job.applications_count }} applicant{{ job.applications_count === 1 ? '' : 's' }}
                            </Link>
                            <Link :href="route('jobs.edit', job.id)" class="text-sm font-medium text-indigo-600 hover:underline">Edit</Link>
                        </div>
                    </div>
                </div>
                <div v-else class="px-5 py-16 text-center">
                    <p class="text-base font-medium text-gray-900">No jobs yet</p>
                    <p class="mt-1 text-sm text-gray-500">Post your first job to start receiving applications.</p>
                </div>
            </div>
        </div>
    </PublicLayout>
</template>
