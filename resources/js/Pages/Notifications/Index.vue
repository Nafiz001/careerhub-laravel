<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import PublicLayout from '@/Layouts/PublicLayout.vue';
import Pagination from '@/Components/Pagination.vue';
import EmptyState from '@/Components/EmptyState.vue';
import Icon from '@/Components/Icon.vue';

defineProps({
    notifications: { type: Object, default: () => ({ data: [] }) },
    unreadCount: { type: Number, default: 0 },
});

function markRead(id) {
    router.post(route('notifications.read', id), {}, { preserveScroll: true });
}
function markAllRead() {
    router.post(route('notifications.readAll'), {}, { preserveScroll: true });
}

function timeAgo(value) {
    if (!value) return '';
    const mins = Math.floor((Date.now() - new Date(value).getTime()) / 60000);
    if (mins < 1) return 'just now';
    if (mins < 60) return `${mins}m ago`;
    const hrs = Math.floor(mins / 60);
    if (hrs < 24) return `${hrs}h ago`;
    return new Date(value).toLocaleDateString(undefined, { month: 'short', day: 'numeric' });
}

const iconFor = (type) =>
    ({
        new_application: 'inbox',
        application_status: 'check-circle',
    })[type] ?? 'bell';
</script>

<template>
    <Head title="Notifications" />

    <PublicLayout>
        <div class="mx-auto max-w-3xl px-4 py-8 sm:px-6 lg:px-8">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <div class="flex items-center gap-3">
                    <span class="flex h-10 w-10 items-center justify-center rounded-card bg-clay-soft text-clay">
                        <Icon name="bell" :size="20" />
                    </span>
                    <div>
                        <h1 class="font-serif text-3xl font-medium tracking-tight text-ink">Notifications</h1>
                        <p class="mt-0.5 text-sm text-muted">
                            {{ unreadCount }} unread.
                        </p>
                    </div>
                </div>
                <button
                    v-if="unreadCount > 0"
                    type="button"
                    class="inline-flex items-center gap-1.5 rounded-btn border border-hairline bg-surface px-3.5 py-2 text-sm font-medium text-ink transition hover:bg-surface-2"
                    @click="markAllRead"
                >
                    <Icon name="check" :size="15" /> Mark all read
                </button>
            </div>

            <div v-if="notifications.data.length" class="mt-7 space-y-2.5">
                <div
                    v-for="note in notifications.data"
                    :key="note.id"
                    class="group flex items-start gap-3.5 rounded-card border bg-surface p-4 shadow-soft transition"
                    :class="note.read_at ? 'border-hairline' : 'border-clay/25 bg-clay-soft/20'"
                >
                    <span
                        class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full"
                        :class="note.read_at ? 'bg-surface-2 text-muted' : 'bg-clay-soft text-clay'"
                    >
                        <Icon :name="iconFor(note.data.type)" :size="17" />
                    </span>
                    <div class="min-w-0 flex-1">
                        <div class="flex items-start justify-between gap-3">
                            <p class="text-sm font-medium text-ink">{{ note.data.title }}</p>
                            <span class="shrink-0 font-mono text-[11px] text-muted/80">{{ timeAgo(note.created_at) }}</span>
                        </div>
                        <p class="mt-0.5 text-sm text-muted">{{ note.data.message }}</p>
                        <div class="mt-2 flex items-center gap-4">
                            <Link
                                v-if="note.data.url"
                                :href="note.data.url"
                                class="inline-flex items-center gap-1 text-sm font-medium text-clay transition hover:text-clay-strong"
                            >
                                View <Icon name="arrow-right" :size="13" />
                            </Link>
                            <button
                                v-if="!note.read_at"
                                type="button"
                                class="text-sm font-medium text-muted transition hover:text-ink"
                                @click="markRead(note.id)"
                            >
                                Mark read
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div v-else class="mt-7 rounded-card border border-dashed border-hairline bg-surface">
                <EmptyState icon="bell" title="You're all caught up" description="New notifications about your applications and listings will appear here." />
            </div>

            <div v-if="notifications.data.length" class="mt-8">
                <Pagination :links="notifications.links" />
            </div>
        </div>
    </PublicLayout>
</template>
