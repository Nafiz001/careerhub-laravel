<script setup>
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';
import Badge from '@/Components/Badge.vue';
import Icon from '@/Components/Icon.vue';
import Avatar from '@/Components/Avatar.vue';
import SaveButton from '@/Components/SaveButton.vue';

const props = defineProps({
    job: { type: Object, required: true },
    saved: { type: Boolean, default: false },
    canSave: { type: Boolean, default: false },
});

// Prefer the structured salary range; fall back to the free-text field.
const salary = computed(() => {
    const fmt = (n) => '$' + Number(n).toLocaleString();
    const { salary_min: min, salary_max: max, salary_range: text } = props.job;
    if (min && max) return `${fmt(min)} – ${fmt(max)}`;
    if (min) return `From ${fmt(min)}`;
    if (max) return `Up to ${fmt(max)}`;
    return text || null;
});

const skills = computed(() => (Array.isArray(props.job.skills) ? props.job.skills : []));
const companyName = computed(() => props.job.employer?.company_name || props.job.company);
</script>

<template>
    <div
        class="group relative flex h-full flex-col rounded-card border bg-surface p-5 shadow-soft transition duration-200 hover:-translate-y-0.5 hover:shadow-lift"
        :class="job.is_featured ? 'border-clay/30' : 'border-hairline hover:border-clay/30'"
    >
        <!-- Featured accent rule -->
        <div v-if="job.is_featured" class="absolute inset-x-0 top-0 h-0.5 rounded-t-card bg-gradient-to-r from-clay/0 via-clay to-clay/0" />

        <div class="flex items-start justify-between gap-3">
            <div class="flex min-w-0 items-start gap-3">
                <Avatar
                    :name="companyName"
                    :src="job.employer?.logo ? `/storage/${job.employer.logo}` : null"
                    :size="40"
                    square
                />
                <div class="min-w-0">
                    <Link :href="route('jobs.show', job.id)" class="block">
                        <h3 class="truncate font-serif text-base font-medium text-ink transition group-hover:text-clay-strong">
                            {{ job.title }}
                        </h3>
                    </Link>
                    <p class="mt-0.5 truncate text-sm text-muted">{{ companyName }}</p>
                </div>
            </div>
            <SaveButton v-if="canSave" :job-id="job.id" :saved="saved" />
        </div>

        <!-- Meta -->
        <div class="mt-3.5 flex flex-wrap items-center gap-x-4 gap-y-1.5 text-sm text-muted">
            <span class="inline-flex items-center gap-1.5">
                <Icon name="map-pin" :size="15" class="text-muted/70" />
                {{ job.location }}
            </span>
            <span v-if="salary" class="inline-flex items-center gap-1.5 font-mono text-[13px] tabular-nums">
                <Icon name="dollar-sign" :size="15" class="text-muted/70" />
                {{ salary }}
            </span>
        </div>

        <!-- Skills -->
        <div v-if="skills.length" class="mt-3.5 flex flex-wrap gap-1.5">
            <span v-for="s in skills.slice(0, 4)" :key="s" class="chip">{{ s }}</span>
            <span v-if="skills.length > 4" class="chip">+{{ skills.length - 4 }}</span>
        </div>

        <p v-else class="mt-3.5 line-clamp-2 flex-1 text-sm leading-relaxed text-muted">{{ job.description }}</p>

        <!-- Footer -->
        <div class="mt-4 flex items-center justify-between gap-2 border-t border-hairline pt-3.5">
            <div class="flex flex-wrap items-center gap-1.5">
                <Badge :label="job.type" :variant="job.type" />
                <Badge v-if="job.remote && job.type !== 'remote'" label="Remote" variant="remote" />
                <Badge v-if="job.is_featured" label="Featured" variant="featured" />
            </div>
            <Link
                :href="route('jobs.show', job.id)"
                class="inline-flex items-center gap-1 text-sm font-medium text-clay transition group-hover:gap-1.5 group-hover:text-clay-strong"
            >
                View
                <Icon name="arrow-right" :size="15" />
            </Link>
        </div>
    </div>
</template>
