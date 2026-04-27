<script setup>
import { Head, Link } from '@inertiajs/vue3';
import PublicLayout from '@/Layouts/PublicLayout.vue';
import ApplicationRow from '@/Components/ApplicationRow.vue';

defineProps({
    applications: { type: Array, default: () => [] },
});
</script>

<template>
    <Head title="My Applications" />

    <PublicLayout>
        <div class="mx-auto max-w-4xl px-4 py-8 sm:px-6 lg:px-8">
            <h1 class="text-2xl font-bold text-gray-900">My applications</h1>
            <p class="mt-1 text-sm text-gray-500">{{ applications.length }} application{{ applications.length === 1 ? '' : 's' }} submitted.</p>

            <div class="mt-6 overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
                <div v-if="applications.length" class="divide-y divide-gray-100">
                    <ApplicationRow v-for="app in applications" :key="app.id" :application="app" />
                </div>
                <div v-else class="px-5 py-16 text-center">
                    <p class="text-base font-medium text-gray-900">No applications yet</p>
                    <p class="mt-1 text-sm text-gray-500">
                        <Link :href="route('jobs.index')" class="font-medium text-indigo-600 hover:underline">Browse jobs</Link>
                        and apply to get started.
                    </p>
                </div>
            </div>
        </div>
    </PublicLayout>
</template>
