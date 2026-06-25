<script setup>
import { computed } from 'vue';
import { Head, Link, usePage } from '@inertiajs/vue3';
import PublicLayout from '@/Layouts/PublicLayout.vue';
import Badge from '@/Components/Badge.vue';
import Icon from '@/Components/Icon.vue';
import AppButton from '@/Components/AppButton.vue';
import StatTile from '@/Components/StatTile.vue';
import EmptyState from '@/Components/EmptyState.vue';
import ApplicationsLine from '@/Charts/ApplicationsLine.vue';
import FunnelBar from '@/Charts/FunnelBar.vue';
import TopJobsDonut from '@/Charts/TopJobsDonut.vue';

const props = defineProps({
    jobs: { type: Array, default: () => [] },
    stats: { type: Object, default: () => ({}) },
    analytics: { type: Object, default: () => ({}) },
});

const page = usePage();
const user = computed(() => page.props.auth.user);

const recentApps = computed(() =>
    (props.analytics.applicationsPerDay ?? []).reduce((s, d) => s + d.count, 0),
);
</script>

<template>
    <Head title="Employer Dashboard" />

    <PublicLayout>
        <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="flex flex-wrap items-end justify-between gap-4">
                <div>
                    <p class="text-sm font-medium text-clay">Employer workspace</p>
                    <h1 class="mt-1 font-serif text-3xl font-medium tracking-tight text-ink">
                        Welcome back, {{ user?.name?.split(' ')[0] }}
                    </h1>
                    <p class="mt-1.5 text-sm text-muted">Here's how your listings are performing.</p>
                </div>
                <AppButton :href="route('jobs.create')" variant="primary">
                    <Icon name="plus" :size="16" /> Post a job
                </AppButton>
            </div>

            <!-- Stat tiles -->
            <div class="mt-7 grid grid-cols-2 gap-4 lg:grid-cols-4">
                <StatTile label="Active listings" :value="stats.open ?? 0" icon="briefcase" accent="ink" :hint="`${stats.jobs ?? 0} total`" />
                <StatTile label="Total applicants" :value="stats.applicants ?? 0" icon="users" accent="clay" />
                <StatTile label="Profile views" :value="(stats.views ?? 0).toLocaleString()" icon="eye" accent="sage" />
                <StatTile label="Last 30 days" :value="recentApps" icon="trending-up" accent="positive" hint="new applications" />
            </div>

            <!-- Charts row -->
            <div class="mt-6 grid grid-cols-1 gap-5 lg:grid-cols-3">
                <!-- Applications over time -->
                <div class="rounded-card border border-hairline bg-surface p-5 shadow-soft lg:col-span-2">
                    <div class="mb-4 flex items-center justify-between">
                        <div>
                            <h2 class="font-serif text-lg font-medium text-ink">Applications over time</h2>
                            <p class="text-xs text-muted">Trailing 30 days</p>
                        </div>
                        <Icon name="trending-up" :size="18" class="text-muted/70" />
                    </div>
                    <ApplicationsLine :series="analytics.applicationsPerDay ?? []" />
                </div>

                <!-- Status funnel -->
                <div class="rounded-card border border-hairline bg-surface p-5 shadow-soft">
                    <div class="mb-4 flex items-center justify-between">
                        <div>
                            <h2 class="font-serif text-lg font-medium text-ink">Pipeline</h2>
                            <p class="text-xs text-muted">By application status</p>
                        </div>
                        <Icon name="sliders-horizontal" :size="18" class="text-muted/70" />
                    </div>
                    <FunnelBar :funnel="analytics.funnel ?? []" />
                </div>
            </div>

            <!-- Top jobs donut + listings -->
            <div class="mt-5 grid grid-cols-1 gap-5 lg:grid-cols-3">
                <div class="rounded-card border border-hairline bg-surface p-5 shadow-soft">
                    <h2 class="mb-4 font-serif text-lg font-medium text-ink">Top jobs by applicants</h2>
                    <TopJobsDonut :jobs="analytics.topJobs ?? []" />
                </div>

                <!-- Listings table -->
                <div class="overflow-hidden rounded-card border border-hairline bg-surface shadow-soft lg:col-span-2">
                    <div class="flex items-center justify-between border-b border-hairline px-5 py-4">
                        <h2 class="font-serif text-lg font-medium text-ink">Your listings</h2>
                        <span class="font-mono text-xs tabular-nums text-muted">{{ jobs.length }} total</span>
                    </div>
                    <div v-if="jobs.length" class="divide-y divide-hairline">
                        <div v-for="job in jobs" :key="job.id" class="flex flex-wrap items-center justify-between gap-3 px-5 py-3.5 transition hover:bg-surface-2/50">
                            <div class="min-w-0">
                                <div class="flex items-center gap-2">
                                    <Link :href="route('jobs.show', job.id)" class="truncate font-medium text-ink transition hover:text-clay">{{ job.title }}</Link>
                                    <Badge v-if="!job.is_open" label="closed" variant="closed" />
                                    <Badge v-if="job.is_featured" label="Featured" variant="featured" />
                                </div>
                                <p class="mt-0.5 flex items-center gap-1.5 text-xs text-muted">
                                    <Icon name="map-pin" :size="12" /> {{ job.location }}
                                    <span class="text-hairline">·</span>
                                    <Icon name="eye" :size="12" /> <span class="font-mono">{{ (job.views_count ?? 0).toLocaleString() }}</span>
                                </p>
                            </div>
                            <div class="flex items-center gap-4">
                                <Link :href="route('jobs.applicants', job.id)" class="inline-flex items-center gap-1.5 text-sm text-muted transition hover:text-ink">
                                    <Icon name="users" :size="15" />
                                    <span class="font-mono tabular-nums">{{ job.applications_count }}</span>
                                </Link>
                                <Link :href="route('jobs.edit', job.id)" class="text-sm font-medium text-clay transition hover:text-clay-strong">Edit</Link>
                            </div>
                        </div>
                    </div>
                    <EmptyState
                        v-else
                        icon="briefcase"
                        title="No jobs yet"
                        description="Post your first listing to start receiving applications."
                    >
                        <template #action>
                            <AppButton :href="route('jobs.create')" variant="primary"><Icon name="plus" :size="16" /> Post a job</AppButton>
                        </template>
                    </EmptyState>
                </div>
            </div>
        </div>
    </PublicLayout>
</template>
