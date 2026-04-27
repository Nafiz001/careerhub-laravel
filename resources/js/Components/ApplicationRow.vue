<script setup>
import { Link } from '@inertiajs/vue3';
import Badge from '@/Components/Badge.vue';

defineProps({
    application: { type: Object, required: true },
});
</script>

<template>
    <div class="flex flex-wrap items-center justify-between gap-3 px-5 py-4">
        <div class="min-w-0">
            <div class="flex items-center gap-2">
                <Link :href="route('jobs.show', application.job.id)" class="font-medium text-gray-900 hover:text-indigo-600">
                    {{ application.job.title }}
                </Link>
                <Badge :label="application.job.type" :variant="application.job.type" />
                <Badge v-if="!application.job.is_open" label="closed" variant="closed" />
            </div>
            <p class="mt-0.5 text-sm text-gray-500">{{ application.job.company }} - {{ application.job.location }}</p>
        </div>
        <Badge :label="application.status" :variant="application.status" />
    </div>
</template>
