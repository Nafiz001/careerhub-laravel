<script setup>
import { computed } from 'vue';
import { Head } from '@inertiajs/vue3';
import PublicLayout from '@/Layouts/PublicLayout.vue';
import ApplicationRow from '@/Components/ApplicationRow.vue';
import EmptyState from '@/Components/EmptyState.vue';
import AppButton from '@/Components/AppButton.vue';
import Icon from '@/Components/Icon.vue';

const props = defineProps({
    applications: { type: Array, default: () => [] },
});

// Small status breakdown strip for a designed feel.
const counts = computed(() => {
    const c = { pending: 0, reviewed: 0, accepted: 0, rejected: 0 };
    props.applications.forEach((a) => (c[a.status] = (c[a.status] ?? 0) + 1));
    return c;
});
</script>

<template>
    <Head title="My Applications" />

    <PublicLayout>
        <div class="mx-auto max-w-4xl px-4 py-8 sm:px-6 lg:px-8">
            <div class="flex items-center gap-3">
                <span class="flex h-10 w-10 items-center justify-center rounded-card bg-clay-soft text-clay">
                    <Icon name="file-text" :size="20" />
                </span>
                <div>
                    <h1 class="font-serif text-3xl font-medium tracking-tight text-ink">My applications</h1>
                    <p class="mt-0.5 text-sm text-muted">{{ applications.length }} application{{ applications.length === 1 ? '' : 's' }} submitted.</p>
                </div>
            </div>

            <!-- Status strip -->
            <div v-if="applications.length" class="mt-6 grid grid-cols-4 gap-3">
                <div v-for="(n, status) in counts" :key="status" class="rounded-card border border-hairline bg-surface px-4 py-3 text-center shadow-soft">
                    <p class="font-mono text-2xl font-medium tabular-nums text-ink">{{ n }}</p>
                    <p class="mt-0.5 text-xs capitalize text-muted">{{ status }}</p>
                </div>
            </div>

            <div class="mt-6 overflow-hidden rounded-card border border-hairline bg-surface shadow-soft">
                <div v-if="applications.length" class="divide-y divide-hairline">
                    <ApplicationRow v-for="app in applications" :key="app.id" :application="app" />
                </div>
                <EmptyState
                    v-else
                    icon="file-text"
                    title="No applications yet"
                    description="Browse the board and apply to roles that fit your craft."
                >
                    <template #action>
                        <AppButton :href="route('jobs.index')" variant="primary">Browse jobs</AppButton>
                    </template>
                </EmptyState>
            </div>
        </div>
    </PublicLayout>
</template>
