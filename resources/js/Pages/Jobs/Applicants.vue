<script setup>
import { computed } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import PublicLayout from '@/Layouts/PublicLayout.vue';
import Badge from '@/Components/Badge.vue';
import Avatar from '@/Components/Avatar.vue';
import Icon from '@/Components/Icon.vue';
import EmptyState from '@/Components/EmptyState.vue';

const props = defineProps({
    job: { type: Object, required: true },
    applicants: { type: Array, default: () => [] },
    statuses: { type: Array, default: () => [] },
});

const updateStatus = (application, status) => {
    router.patch(route('applications.status', application.id), { status }, { preserveScroll: true });
};

const counts = computed(() => {
    const c = {};
    props.statuses.forEach((s) => (c[s] = 0));
    props.applicants.forEach((a) => (c[a.status] = (c[a.status] ?? 0) + 1));
    return c;
});

const submitted = (value) =>
    value
        ? new Date(value).toLocaleDateString(undefined, { month: 'short', day: 'numeric', year: 'numeric' })
        : '';
</script>

<template>
    <Head :title="`Applicants — ${job.title}`" />

    <PublicLayout>
        <div class="mx-auto max-w-4xl px-4 py-8 sm:px-6 lg:px-8">
            <Link :href="route('dashboard')" class="inline-flex items-center gap-1.5 text-sm font-medium text-muted transition hover:text-ink">
                <Icon name="arrow-left" :size="16" /> Back to dashboard
            </Link>

            <div class="mt-4 flex flex-wrap items-start justify-between gap-3">
                <div>
                    <h1 class="font-serif text-3xl font-medium tracking-tight text-ink">{{ job.title }}</h1>
                    <p class="mt-1 flex items-center gap-1.5 text-sm text-muted">
                        {{ job.company }} <span class="text-hairline">·</span>
                        <Icon name="map-pin" :size="13" /> {{ job.location }}
                    </p>
                </div>
                <div class="flex items-center gap-1.5">
                    <Badge :label="job.type" :variant="job.type" />
                    <Badge v-if="!job.is_open" label="closed" variant="closed" />
                </div>
            </div>

            <!-- Status strip -->
            <div v-if="applicants.length" class="mt-6 grid grid-cols-4 gap-3">
                <div v-for="s in statuses" :key="s" class="rounded-card border border-hairline bg-surface px-4 py-3 text-center shadow-soft">
                    <p class="font-mono text-2xl font-medium tabular-nums text-ink">{{ counts[s] }}</p>
                    <p class="mt-0.5 text-xs capitalize text-muted">{{ s }}</p>
                </div>
            </div>

            <div v-if="applicants.length" class="mt-6 space-y-4">
                <div v-for="app in applicants" :key="app.id" class="rounded-card border border-hairline bg-surface p-5 shadow-soft">
                    <div class="flex flex-wrap items-start justify-between gap-4">
                        <div class="flex items-start gap-3">
                            <Avatar :name="app.seeker.name" :size="44" />
                            <div>
                                <p class="font-medium text-ink">{{ app.seeker.name }}</p>
                                <p v-if="app.seeker.headline" class="text-sm text-muted">{{ app.seeker.headline }}</p>
                                <div class="mt-1.5 flex flex-wrap items-center gap-x-3 gap-y-1 text-xs text-muted">
                                    <a :href="`mailto:${app.seeker.email}`" class="inline-flex items-center gap-1 text-clay transition hover:text-clay-strong">
                                        <Icon name="mail" :size="13" /> {{ app.seeker.email }}
                                    </a>
                                    <span v-if="app.seeker.location" class="inline-flex items-center gap-1"><Icon name="map-pin" :size="13" /> {{ app.seeker.location }}</span>
                                    <span v-if="app.seeker.experience_level" class="inline-flex items-center gap-1 capitalize"><Icon name="graduation-cap" :size="13" /> {{ app.seeker.experience_level }}</span>
                                    <span class="inline-flex items-center gap-1"><Icon name="clock" :size="13" /> {{ submitted(app.created_at) }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            <Badge :label="app.status" :variant="app.status" dot />
                            <select
                                :value="app.status"
                                class="rounded-input border-hairline bg-surface text-xs capitalize text-ink focus:border-clay focus:ring-clay"
                                @change="updateStatus(app, $event.target.value)"
                            >
                                <option v-for="s in statuses" :key="s" :value="s" class="capitalize">{{ s }}</option>
                            </select>
                        </div>
                    </div>

                    <p class="mt-4 whitespace-pre-line border-t border-hairline pt-4 text-sm leading-relaxed text-muted">{{ app.cover_letter }}</p>

                    <div v-if="app.resume_url" class="mt-4">
                        <a
                            :href="route('applications.resume', app.id)"
                            class="inline-flex items-center gap-2 rounded-btn border border-hairline bg-surface px-3.5 py-2 text-sm font-medium text-ink transition hover:bg-surface-2"
                        >
                            <Icon name="download" :size="15" />
                            {{ app.resume_name || 'Download résumé' }}
                        </a>
                    </div>
                </div>
            </div>

            <div v-else class="mt-6 rounded-card border border-dashed border-hairline bg-surface">
                <EmptyState icon="users" title="No applicants yet" description="Share your listing to attract qualified candidates." />
            </div>
        </div>
    </PublicLayout>
</template>
