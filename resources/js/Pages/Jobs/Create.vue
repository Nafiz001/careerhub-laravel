<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import PublicLayout from '@/Layouts/PublicLayout.vue';
import JobForm from '@/Components/JobForm.vue';
import Icon from '@/Components/Icon.vue';

defineProps({
    types: { type: Array, required: true },
    categories: { type: Array, default: () => [] },
    experienceLevels: { type: Array, default: () => [] },
});

const form = useForm({
    title: '',
    company: '',
    location: '',
    type: '',
    category: '',
    experience_level: '',
    salary_range: '',
    salary_min: '',
    salary_max: '',
    description: '',
    skills: [],
    remote: false,
    application_deadline: '',
    is_featured: false,
    is_open: true,
});

const submit = () => form.post(route('jobs.store'));
</script>

<template>
    <Head title="Post a Job" />

    <PublicLayout>
        <div class="mx-auto max-w-3xl px-4 py-8 sm:px-6 lg:px-8">
            <div class="flex items-center gap-3">
                <span class="flex h-10 w-10 items-center justify-center rounded-card bg-clay-soft text-clay">
                    <Icon name="briefcase" :size="20" />
                </span>
                <div>
                    <h1 class="font-serif text-3xl font-medium tracking-tight text-ink">Post a new job</h1>
                    <p class="mt-0.5 text-sm text-muted">Fill in the details below to publish your listing.</p>
                </div>
            </div>

            <div class="mt-7 rounded-card border border-hairline bg-surface p-6 shadow-soft sm:p-8">
                <JobForm
                    :form="form"
                    :types="types"
                    :categories="categories"
                    :experience-levels="experienceLevels"
                    submit-label="Publish job"
                    @submit="submit"
                />
            </div>
        </div>
    </PublicLayout>
</template>
