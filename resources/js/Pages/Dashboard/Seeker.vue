<script setup>
import { computed } from 'vue';
import { Head, Link, usePage } from '@inertiajs/vue3';
import PublicLayout from '@/Layouts/PublicLayout.vue';
import ApplicationRow from '@/Components/ApplicationRow.vue';
import Icon from '@/Components/Icon.vue';
import AppButton from '@/Components/AppButton.vue';
import StatTile from '@/Components/StatTile.vue';
import EmptyState from '@/Components/EmptyState.vue';

const props = defineProps({
    applications: { type: Array, default: () => [] },
    stats: { type: Object, default: () => ({}) },
});

const page = usePage();
const user = computed(() => page.props.auth.user);
const notifications = computed(() => page.props.recentNotifications ?? []);

const recent = computed(() => props.applications.slice(0, 6));

function timeAgo(value) {
    if (!value) return '';
    const mins = Math.floor((Date.now() - new Date(value).getTime()) / 60000);
    if (mins < 1) return 'just now';
    if (mins < 60) return `${mins}m ago`;
    const hrs = Math.floor(mins / 60);
    if (hrs < 24) return `${hrs}h ago`;
    return `${Math.floor(hrs / 24)}d ago`;
}
</script>

<template>
    <Head title="My Dashboard" />

    <PublicLayout>
        <div class="mx-auto max-w-6xl px-4 py-8 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="flex flex-wrap items-end justify-between gap-4">
                <div>
                    <p class="text-sm font-medium text-clay">Your workspace</p>
                    <h1 class="mt-1 font-serif text-3xl font-medium tracking-tight text-ink">
                        Hello, {{ user?.name?.split(' ')[0] }}
                    </h1>
                    <p class="mt-1.5 text-sm text-muted">Track your applications and saved roles.</p>
                </div>
                <AppButton :href="route('jobs.index')" variant="primary">
                    <Icon name="search" :size="16" /> Browse jobs
                </AppButton>
            </div>

            <!-- Stat tiles -->
            <div class="mt-7 grid grid-cols-2 gap-4 lg:grid-cols-4">
                <StatTile label="Applications" :value="stats.applications ?? 0" icon="file-text" accent="ink" />
                <StatTile label="Pending" :value="stats.pending ?? 0" icon="clock" accent="caution" />
                <StatTile label="Accepted" :value="stats.accepted ?? 0" icon="check-circle" accent="positive" />
                <StatTile label="Saved jobs" :value="stats.saved ?? 0" icon="bookmark" accent="sage" />
            </div>

            <div class="mt-6 grid grid-cols-1 gap-5 lg:grid-cols-3">
                <!-- Applications -->
                <div class="overflow-hidden rounded-card border border-hairline bg-surface shadow-soft lg:col-span-2">
                    <div class="flex items-center justify-between border-b border-hairline px-5 py-4">
                        <h2 class="font-serif text-lg font-medium text-ink">Recent applications</h2>
                        <Link :href="route('applications.index')" class="inline-flex items-center gap-1 text-sm font-medium text-clay transition hover:text-clay-strong">
                            View all <Icon name="arrow-right" :size="14" />
                        </Link>
                    </div>
                    <div v-if="recent.length" class="divide-y divide-hairline">
                        <ApplicationRow v-for="app in recent" :key="app.id" :application="app" />
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

                <!-- Notifications panel -->
                <div class="overflow-hidden rounded-card border border-hairline bg-surface shadow-soft">
                    <div class="flex items-center justify-between border-b border-hairline px-5 py-4">
                        <h2 class="font-serif text-lg font-medium text-ink">Activity</h2>
                        <Link :href="route('notifications.index')" class="text-sm font-medium text-clay transition hover:text-clay-strong">All</Link>
                    </div>
                    <div v-if="notifications.length" class="divide-y divide-hairline">
                        <Link
                            v-for="n in notifications"
                            :key="n.id"
                            :href="n.data.url || route('notifications.index')"
                            class="block px-5 py-3.5 transition hover:bg-surface-2/50"
                            :class="{ 'bg-clay-soft/25': !n.read_at }"
                        >
                            <div class="flex items-start gap-2.5">
                                <span class="mt-1.5 h-1.5 w-1.5 shrink-0 rounded-full" :class="n.read_at ? 'bg-hairline' : 'bg-clay'" />
                                <div class="min-w-0">
                                    <p class="truncate text-sm font-medium text-ink">{{ n.data.title }}</p>
                                    <p class="mt-0.5 line-clamp-2 text-xs text-muted">{{ n.data.message }}</p>
                                    <p class="mt-1 font-mono text-[11px] text-muted/80">{{ timeAgo(n.created_at) }}</p>
                                </div>
                            </div>
                        </Link>
                    </div>
                    <div v-else class="px-5 py-12 text-center">
                        <p class="text-sm text-muted">No recent activity.</p>
                    </div>
                </div>
            </div>
        </div>
    </PublicLayout>
</template>
