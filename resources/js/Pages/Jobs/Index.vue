<script setup>
import { reactive, watch } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import { debounce } from 'lodash';
import PublicLayout from '@/Layouts/PublicLayout.vue';
import JobCard from '@/Components/JobCard.vue';
import Pagination from '@/Components/Pagination.vue';

const props = defineProps({
    jobs: { type: Object, required: true },
    filters: { type: Object, default: () => ({}) },
    types: { type: Array, default: () => [] },
    locations: { type: Array, default: () => [] },
});

const form = reactive({
    search: props.filters.search ?? '',
    type: props.filters.type ?? '',
    location: props.filters.location ?? '',
});

// Server-side search/filter: push query params, preserving scroll + state.
const applyFilters = debounce(() => {
    const query = {};
    if (form.search) query.search = form.search;
    if (form.type) query.type = form.type;
    if (form.location) query.location = form.location;

    router.get(route('jobs.index'), query, {
        preserveState: true,
        preserveScroll: true,
        replace: true,
    });
}, 350);

watch(form, applyFilters);

const reset = () => {
    form.search = '';
    form.type = '';
    form.location = '';
};
</script>

<template>
    <Head title="Browse Jobs" />

    <PublicLayout>
        <!-- Hero -->
        <section class="bg-gradient-to-br from-indigo-600 to-violet-600">
            <div class="mx-auto max-w-7xl px-4 py-14 text-center sm:px-6 lg:px-8">
                <h1 class="text-3xl font-extrabold tracking-tight text-white sm:text-4xl">
                    Find your next role in Bangladesh's tech scene
                </h1>
                <p class="mx-auto mt-3 max-w-2xl text-base text-indigo-100">
                    Browse openings from leading companies. Search, filter, and apply in minutes.
                </p>
            </div>
        </section>

        <div class="mx-auto -mt-8 max-w-7xl px-4 pb-16 sm:px-6 lg:px-8">
            <!-- Filter bar -->
            <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
                <div class="grid grid-cols-1 gap-3 md:grid-cols-12">
                    <div class="md:col-span-6">
                        <label class="sr-only" for="search">Search</label>
                        <div class="relative">
                            <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                            </span>
                            <input
                                id="search"
                                v-model="form.search"
                                type="text"
                                placeholder="Search by title or company..."
                                class="w-full rounded-lg border-gray-300 pl-10 text-sm focus:border-indigo-500 focus:ring-indigo-500"
                            />
                        </div>
                    </div>
                    <div class="md:col-span-3">
                        <select v-model="form.type" class="w-full rounded-lg border-gray-300 text-sm capitalize focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">All types</option>
                            <option v-for="t in types" :key="t" :value="t" class="capitalize">{{ t }}</option>
                        </select>
                    </div>
                    <div class="md:col-span-3">
                        <select v-model="form.location" class="w-full rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">All locations</option>
                            <option v-for="loc in locations" :key="loc" :value="loc">{{ loc }}</option>
                        </select>
                    </div>
                </div>
                <div class="mt-3 flex items-center justify-between">
                    <p class="text-sm text-gray-500">{{ jobs.total }} job{{ jobs.total === 1 ? '' : 's' }} found</p>
                    <button
                        v-if="form.search || form.type || form.location"
                        class="text-sm font-medium text-indigo-600 hover:underline"
                        @click="reset"
                    >
                        Clear filters
                    </button>
                </div>
            </div>

            <!-- Results -->
            <div v-if="jobs.data.length" class="mt-6 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                <JobCard v-for="job in jobs.data" :key="job.id" :job="job" />
            </div>
            <div v-else class="mt-10 rounded-xl border border-dashed border-gray-300 bg-white py-16 text-center">
                <p class="text-base font-medium text-gray-900">No jobs match your search</p>
                <p class="mt-1 text-sm text-gray-500">Try adjusting your filters or clearing them.</p>
            </div>

            <div class="mt-8">
                <Pagination :links="jobs.links" />
            </div>
        </div>
    </PublicLayout>
</template>
