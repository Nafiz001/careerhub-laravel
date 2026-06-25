<script setup>
import { computed, ref, onMounted, onBeforeUnmount } from 'vue';
import { Link, router, usePage } from '@inertiajs/vue3';
import Icon from '@/Components/Icon.vue';

// Navbar bell with an unread badge + dropdown of recent notifications.
// Reads the shared Inertia props populated by HandleInertiaRequests.
const page = usePage();
const open = ref(false);
const root = ref(null);

const unread = computed(() => page.props.unreadNotifications ?? 0);
const recent = computed(() => page.props.recentNotifications ?? []);

function timeAgo(value) {
    if (!value) return '';
    const then = new Date(value).getTime();
    const diff = Math.max(0, Date.now() - then);
    const mins = Math.floor(diff / 60000);
    if (mins < 1) return 'just now';
    if (mins < 60) return `${mins}m ago`;
    const hrs = Math.floor(mins / 60);
    if (hrs < 24) return `${hrs}h ago`;
    const days = Math.floor(hrs / 24);
    if (days < 7) return `${days}d ago`;
    return new Date(value).toLocaleDateString(undefined, { month: 'short', day: 'numeric' });
}

function markAllRead() {
    router.post(route('notifications.readAll'), {}, { preserveScroll: true });
}

function onClickOutside(e) {
    if (root.value && !root.value.contains(e.target)) open.value = false;
}
function onEsc(e) {
    if (e.key === 'Escape') open.value = false;
}

onMounted(() => {
    document.addEventListener('click', onClickOutside);
    document.addEventListener('keydown', onEsc);
});
onBeforeUnmount(() => {
    document.removeEventListener('click', onClickOutside);
    document.removeEventListener('keydown', onEsc);
});
</script>

<template>
    <div ref="root" class="relative">
        <button
            type="button"
            class="relative flex h-9 w-9 items-center justify-center rounded-full text-muted transition hover:bg-surface-2 hover:text-ink"
            :aria-expanded="open"
            aria-label="Notifications"
            @click="open = !open"
        >
            <Icon name="bell" :size="19" />
            <span
                v-if="unread > 0"
                class="absolute -right-0.5 -top-0.5 flex h-4 min-w-4 items-center justify-center rounded-full bg-clay px-1 font-mono text-[10px] font-medium leading-none text-[#FFFDF8] ring-2 ring-canvas"
            >
                {{ unread > 9 ? '9+' : unread }}
            </span>
        </button>

        <Transition
            enter-active-class="transition duration-150 ease-out"
            enter-from-class="-translate-y-1 opacity-0"
            enter-to-class="translate-y-0 opacity-100"
            leave-active-class="transition duration-100 ease-in"
            leave-from-class="translate-y-0 opacity-100"
            leave-to-class="-translate-y-1 opacity-0"
        >
            <div
                v-if="open"
                class="absolute right-0 z-50 mt-2 w-80 origin-top-right overflow-hidden rounded-card border border-hairline bg-surface shadow-lift"
            >
                <div class="flex items-center justify-between border-b border-hairline px-4 py-3">
                    <p class="font-serif text-sm font-medium text-ink">Notifications</p>
                    <button
                        v-if="unread > 0"
                        class="text-xs font-medium text-clay hover:text-clay-strong"
                        @click="markAllRead"
                    >
                        Mark all read
                    </button>
                </div>

                <div v-if="recent.length" class="max-h-80 divide-y divide-hairline overflow-y-auto">
                    <Link
                        v-for="n in recent"
                        :key="n.id"
                        :href="n.data.url || route('notifications.index')"
                        class="block px-4 py-3 transition hover:bg-surface-2"
                        :class="{ 'bg-clay-soft/30': !n.read_at }"
                        @click="open = false"
                    >
                        <div class="flex items-start gap-2.5">
                            <span
                                class="mt-1.5 h-1.5 w-1.5 shrink-0 rounded-full"
                                :class="n.read_at ? 'bg-transparent' : 'bg-clay'"
                            />
                            <div class="min-w-0">
                                <p class="truncate text-sm font-medium text-ink">{{ n.data.title }}</p>
                                <p class="mt-0.5 line-clamp-2 text-xs text-muted">{{ n.data.message }}</p>
                                <p class="mt-1 font-mono text-[11px] text-muted/80">{{ timeAgo(n.created_at) }}</p>
                            </div>
                        </div>
                    </Link>
                </div>
                <div v-else class="px-4 py-10 text-center">
                    <p class="text-sm text-muted">You're all caught up.</p>
                </div>

                <Link
                    :href="route('notifications.index')"
                    class="block border-t border-hairline px-4 py-2.5 text-center text-xs font-medium text-clay transition hover:bg-surface-2"
                    @click="open = false"
                >
                    View all notifications
                </Link>
            </div>
        </Transition>
    </div>
</template>
