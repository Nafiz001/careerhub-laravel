<script setup>
import { computed } from 'vue';
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';
import PublicLayout from '@/Layouts/PublicLayout.vue';
import Badge from '@/Components/Badge.vue';
import InputError from '@/Components/InputError.vue';

const props = defineProps({
    job: { type: Object, required: true },
    hasApplied: { type: Boolean, default: false },
    canApply: { type: Boolean, default: false },
    applicationsCount: { type: Number, default: 0 },
});

const page = usePage();
const user = computed(() => page.props.auth.user);
const isEmployer = computed(() => user.value?.role === 'employer');
const ownsJob = computed(() => user.value?.id === props.job.employer_id);

const form = useForm({ cover_letter: '' });

const submit = () => {
    form.post(route('applications.store', props.job.id), {
        preserveScroll: true,
        onSuccess: () => form.reset('cover_letter'),
    });
};
</script>

<template>
    <Head :title="job.title" />

    <PublicLayout>
        <div class="mx-auto max-w-4xl px-4 py-8 sm:px-6 lg:px-8">
            <Link :href="route('jobs.index')" class="inline-flex items-center gap-1 text-sm font-medium text-gray-500 hover:text-gray-700">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" /></svg>
                Back to jobs
            </Link>

            <!-- Header card -->
            <div class="mt-4 rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
                <div class="flex flex-wrap items-start justify-between gap-3">
                    <div>
                        <div class="flex items-center gap-2">
                            <h1 class="text-2xl font-bold text-gray-900">{{ job.title }}</h1>
                            <Badge v-if="!job.is_open" label="closed" variant="closed" />
                        </div>
                        <p class="mt-1 text-base font-medium text-gray-600">{{ job.company }}</p>
                    </div>
                    <Badge :label="job.type" :variant="job.type" />
                </div>

                <div class="mt-4 flex flex-wrap gap-x-6 gap-y-2 text-sm text-gray-600">
                    <span class="inline-flex items-center gap-1.5">
                        <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a2 2 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                        {{ job.location }}
                    </span>
                    <span v-if="job.salary_range" class="inline-flex items-center gap-1.5">
                        <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1" /></svg>
                        {{ job.salary_range }}
                    </span>
                    <span class="inline-flex items-center gap-1.5">
                        <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a4 4 0 00-3-3.87M9 20H4v-2a4 4 0 013-3.87m6-9a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
                        {{ applicationsCount }} applicant{{ applicationsCount === 1 ? '' : 's' }}
                    </span>
                </div>
            </div>

            <!-- Description -->
            <div class="mt-6 rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
                <h2 class="text-lg font-semibold text-gray-900">Job description</h2>
                <p class="mt-3 whitespace-pre-line text-sm leading-relaxed text-gray-700">{{ job.description }}</p>
            </div>

            <!-- Apply section -->
            <div class="mt-6 rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
                <!-- Already applied -->
                <div v-if="hasApplied" class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
                    You have already applied to this job. Track its status in
                    <Link :href="route('applications.index')" class="font-semibold underline">My Applications</Link>.
                </div>

                <!-- Closed -->
                <div v-else-if="!job.is_open" class="rounded-lg border border-gray-200 bg-gray-50 px-4 py-3 text-sm text-gray-600">
                    This position is no longer accepting applications.
                </div>

                <!-- Employer viewing -->
                <div v-else-if="isEmployer" class="rounded-lg border border-gray-200 bg-gray-50 px-4 py-3 text-sm text-gray-600">
                    <span v-if="ownsJob">
                        This is your listing.
                        <Link :href="route('jobs.applicants', job.id)" class="font-semibold text-indigo-600 underline">View applicants</Link>.
                    </span>
                    <span v-else>Employer accounts cannot apply to jobs.</span>
                </div>

                <!-- Guest -->
                <div v-else-if="!user" class="rounded-lg border border-indigo-200 bg-indigo-50 px-4 py-3 text-sm text-indigo-800">
                    <Link :href="route('login')" class="font-semibold underline">Log in</Link>
                    or
                    <Link :href="route('register')" class="font-semibold underline">create a seeker account</Link>
                    to apply for this job.
                </div>

                <!-- Apply form (seeker) -->
                <form v-else-if="canApply" @submit.prevent="submit">
                    <h2 class="text-lg font-semibold text-gray-900">Apply for this role</h2>
                    <p class="mt-1 text-sm text-gray-500">Write a short cover letter to introduce yourself.</p>
                    <textarea
                        v-model="form.cover_letter"
                        rows="6"
                        placeholder="Tell the employer why you are a great fit (min. 20 characters)..."
                        class="mt-3 w-full rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500"
                    ></textarea>
                    <InputError class="mt-1" :message="form.errors.cover_letter" />
                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="mt-3 inline-flex items-center rounded-lg bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-500 disabled:opacity-50"
                    >
                        Submit application
                    </button>
                </form>
            </div>
        </div>
    </PublicLayout>
</template>
