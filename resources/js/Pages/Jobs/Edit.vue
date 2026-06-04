<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import PublicLayout from '@/Layouts/PublicLayout.vue';
import JobForm from '@/Components/JobForm.vue';
import Icon from '@/Components/Icon.vue';

const props = defineProps({
    job: { type: Object, required: true },
    types: { type: Array, required: true },
    categories: { type: Array, default: () => [] },
    experienceLevels: { type: Array, default: () => [] },
});

const toDateInput = (value) => (value ? String(value).slice(0, 10) : '');

const form = useForm({
    title: props.job.title,
    company: props.job.company,
    location: props.job.location,
    type: props.job.type,
    category: props.job.category ?? '',
    experience_level: props.job.experience_level ?? '',
    salary_range: props.job.salary_range ?? '',
    salary_min: props.job.salary_min ?? '',
    salary_max: props.job.salary_max ?? '',
    description: props.job.description,
    skills: Array.isArray(props.job.skills) ? props.job.skills : [],
    remote: !!props.job.remote,
    application_deadline: toDateInput(props.job.application_deadline),
    is_featured: !!props.job.is_featured,
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
            <Link :href="route('dashboard')" class="inline-flex items-center gap-1.5 text-sm font-medium text-muted transition hover:text-ink">
                <Icon name="arrow-left" :size="16" /> Back to dashboard
            </Link>
            <h1 class="mt-4 font-serif text-3xl font-medium tracking-tight text-ink">Edit job</h1>
            <p class="mt-0.5 text-sm text-muted">Update your listing details.</p>

            <div class="mt-7 rounded-card border border-hairline bg-surface p-6 shadow-soft sm:p-8">
                <JobForm
                    :form="form"
                    :types="types"
                    :categories="categories"
                    :experience-levels="experienceLevels"
                    submit-label="Save changes"
                    @submit="submit"
                >
                    <template #actions>
                        <Link :href="route('jobs.applicants', job.id)" class="text-sm font-medium text-clay transition hover:text-clay-strong">View applicants</Link>
                        <button type="button" class="ml-auto text-sm font-medium text-danger transition hover:underline" @click="destroy">Delete job</button>
                    </template>
                </JobForm>
            </div>
        </div>
    </PublicLayout>
</template>
