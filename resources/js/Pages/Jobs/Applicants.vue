<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import PublicLayout from '@/Layouts/PublicLayout.vue';
import Badge from '@/Components/Badge.vue';

const props = defineProps({
    job: { type: Object, required: true },
    applicants: { type: Array, default: () => [] },
    statuses: { type: Array, default: () => [] },
});

const updateStatus = (application, status) => {
    router.patch(route('applications.status', application.id), { status }, {
        preserveScroll: true,
    });
};
</script>

<template>
    <Head :title="`Applicants - ${job.title}`" />

    <PublicLayout>
        <div class="mx-auto max-w-4xl px-4 py-8 sm:px-6 lg:px-8">
            <Link :href="route('dashboard')" class="inline-flex items-center gap-1 text-sm font-medium text-gray-500 hover:text-gray-700">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" /></svg>
                Back to dashboard
            </Link>

            <div class="mt-4 flex flex-wrap items-center justify-between gap-3">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">{{ job.title }}</h1>
                    <p class="text-sm text-gray-500">{{ job.company }} - {{ job.location }}</p>
                </div>
                <Badge :label="job.type" :variant="job.type" />
            </div>

            <p class="mt-2 text-sm text-gray-500">{{ applicants.length }} applicant{{ applicants.length === 1 ? '' : 's' }}</p>

            <div v-if="applicants.length" class="mt-4 space-y-4">
                <div v-for="app in applicants" :key="app.id" class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
                    <div class="flex flex-wrap items-start justify-between gap-3">
                        <div>
                            <p class="font-semibold text-gray-900">{{ app.seeker.name }}</p>
                            <a :href="`mailto:${app.seeker.email}`" class="text-sm text-indigo-600 hover:underline">{{ app.seeker.email }}</a>
                        </div>
                        <div class="flex items-center gap-2">
                            <Badge :label="app.status" :variant="app.status" />
                            <select
                                :value="app.status"
                                @change="updateStatus(app, $event.target.value)"
                                class="rounded-lg border-gray-300 text-xs capitalize focus:border-indigo-500 focus:ring-indigo-500"
                            >
                                <option v-for="s in statuses" :key="s" :value="s" class="capitalize">{{ s }}</option>
                            </select>
                        </div>
                    </div>
                    <p class="mt-3 whitespace-pre-line border-t border-gray-100 pt-3 text-sm text-gray-600">{{ app.cover_letter }}</p>
                </div>
            </div>
            <div v-else class="mt-6 rounded-xl border border-dashed border-gray-300 bg-white py-16 text-center">
                <p class="text-base font-medium text-gray-900">No applicants yet</p>
                <p class="mt-1 text-sm text-gray-500">Share your listing to attract candidates.</p>
            </div>
        </div>
    </PublicLayout>
</template>
