<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import PublicLayout from '@/Layouts/PublicLayout.vue';
import JobForm from '@/Components/JobForm.vue';

defineProps({
    types: { type: Array, required: true },
});

const form = useForm({
    title: '',
    company: '',
    location: '',
    type: '',
    salary_range: '',
    description: '',
    is_open: true,
});

const submit = () => form.post(route('jobs.store'));
</script>

<template>
    <Head title="Post a Job" />

    <PublicLayout>
        <div class="mx-auto max-w-3xl px-4 py-8 sm:px-6 lg:px-8">
            <h1 class="text-2xl font-bold text-gray-900">Post a new job</h1>
            <p class="mt-1 text-sm text-gray-500">Fill in the details below to publish your listing.</p>

            <div class="mt-6 rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
                <JobForm :form="form" :types="types" submit-label="Publish job" @submit="submit" />
            </div>
        </div>
    </PublicLayout>
</template>
