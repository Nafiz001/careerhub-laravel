<script setup>
import { Head } from '@inertiajs/vue3';
import PublicLayout from '@/Layouts/PublicLayout.vue';
import JobCard from '@/Components/JobCard.vue';
import Pagination from '@/Components/Pagination.vue';
import EmptyState from '@/Components/EmptyState.vue';
import AppButton from '@/Components/AppButton.vue';
import Icon from '@/Components/Icon.vue';

defineProps({
    jobs: { type: Object, default: () => ({ data: [] }) },
});
</script>

<template>
    <Head title="Saved Jobs" />

    <PublicLayout>
        <div class="mx-auto max-w-5xl px-4 py-8 sm:px-6 lg:px-8">
            <div class="flex items-center gap-3">
                <span class="flex h-10 w-10 items-center justify-center rounded-card bg-sage-soft text-sage">
                    <Icon name="bookmark" :size="20" />
                </span>
                <div>
                    <h1 class="font-serif text-3xl font-medium tracking-tight text-ink">Saved jobs</h1>
                    <p class="mt-0.5 text-sm text-muted">
                        {{ jobs.total ?? jobs.data.length }} bookmarked role{{ (jobs.total ?? jobs.data.length) === 1 ? '' : 's' }}.
                    </p>
                </div>
            </div>

            <div v-if="jobs.data.length" class="mt-7 grid grid-cols-1 gap-4 sm:grid-cols-2">
                <JobCard
                    v-for="job in jobs.data"
                    :key="job.id"
                    :job="job"
                    :can-save="true"
                    :saved="true"
                    class="animate-fade-up"
                />
            </div>

            <div v-else class="mt-7 rounded-card border border-dashed border-hairline bg-surface">
                <EmptyState
                    icon="bookmark"
                    title="No saved jobs yet"
                    description="Bookmark roles you're interested in and they'll show up here for easy access."
                >
                    <template #action>
                        <AppButton :href="route('jobs.index')" variant="primary"><Icon name="search" :size="16" /> Browse jobs</AppButton>
                    </template>
                </EmptyState>
            </div>

            <div v-if="jobs.data.length" class="mt-8">
                <Pagination :links="jobs.links" />
            </div>
        </div>
    </PublicLayout>
</template>
