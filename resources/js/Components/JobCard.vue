<script setup>
import { Link } from '@inertiajs/vue3';
import Badge from '@/Components/Badge.vue';

defineProps({
    job: { type: Object, required: true },
});
</script>

<template>
    <Link
        :href="route('jobs.show', job.id)"
        class="group flex h-full flex-col rounded-xl border border-gray-200 bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:border-indigo-300 hover:shadow-md"
    >
        <div class="flex items-start justify-between gap-3">
            <div>
                <h3 class="text-base font-semibold text-gray-900 group-hover:text-indigo-600">
                    {{ job.title }}
                </h3>
                <p class="mt-0.5 text-sm font-medium text-gray-600">{{ job.company }}</p>
            </div>
            <Badge :label="job.type" :variant="job.type" />
        </div>

        <div class="mt-3 flex flex-wrap items-center gap-x-4 gap-y-1 text-sm text-gray-500">
            <span class="inline-flex items-center gap-1">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a2 2 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                {{ job.location }}
            </span>
            <span v-if="job.salary_range" class="inline-flex items-center gap-1">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1" /></svg>
                {{ job.salary_range }}
            </span>
        </div>

        <p class="mt-3 line-clamp-2 flex-1 text-sm text-gray-600">{{ job.description }}</p>

        <div class="mt-4 flex items-center justify-between border-t border-gray-100 pt-3">
            <span class="text-xs text-gray-400">
                {{ job.applications_count ?? 0 }} applicant{{ (job.applications_count ?? 0) === 1 ? '' : 's' }}
            </span>
            <span class="text-sm font-medium text-indigo-600 group-hover:underline">View details &rarr;</span>
        </div>
    </Link>
</template>
