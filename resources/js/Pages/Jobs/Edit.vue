<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import PublicLayout from '@/Layouts/PublicLayout.vue';
import JobForm from '@/Components/JobForm.vue';

const props = defineProps({
    job: { type: Object, required: true },
    types: { type: Array, required: true },
});

const form = useForm({
    title: props.job.title,
    company: props.job.company,
    location: props.job.location,
    type: props.job.type,
    salary_range: props.job.salary_range ?? '',
    description: props.job.description,
    is_open: props.job.is_open,
});

const submit = () => form.put(route('jobs.update', props.job.id));

const destroy = () => {
    if (confirm('Delete this job and all its applications? This cannot be undone.')) {
        form.delete(route('jobs.destroy', props.job.id));
    }
};
</script>

<template>
    <Head title="Edit Job" />

    <PublicLayout>
        <div class="mx-auto max-w-3xl px-4 py-8 sm:px-6 lg:px-8">
            <h1 class="text-2xl font-bold text-gray-900">Edit job</h1>
            <p class="mt-1 text-sm text-gray-500">Update your listing details.</p>

            <div class="mt-6 rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
                <JobForm :form="form" :types="types" submit-label="Save changes" @submit="submit">
                    <template #actions>
                        <Link :href="route('jobs.applicants', job.id)" class="text-sm font-medium text-indigo-600 hover:underline">View applicants</Link>
                        <button type="button" class="ml-auto text-sm font-medium text-rose-600 hover:underline" @click="destroy">Delete job</button>
                    </template>
                </JobForm>
            </div>
        </div>
    </PublicLayout>
</template>
