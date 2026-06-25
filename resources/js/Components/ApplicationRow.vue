<script setup>
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';
import Badge from '@/Components/Badge.vue';
import Icon from '@/Components/Icon.vue';

const props = defineProps({
    application: { type: Object, required: true },
});

const submitted = computed(() => {
    if (!props.application.created_at) return '';
    return new Date(props.application.created_at).toLocaleDateString(undefined, {
        month: 'short', day: 'numeric', year: 'numeric',
    });
});
</script>

<template>
    <div class="flex flex-wrap items-center justify-between gap-3 px-5 py-4 transition hover:bg-surface-2/50">
        <div class="min-w-0">
            <div class="flex items-center gap-2">
                <Link :href="route('jobs.show', application.job.id)" class="truncate font-medium text-ink transition hover:text-clay">
                    {{ application.job.title }}
                </Link>
                <Badge v-if="!application.job.is_open" label="closed" variant="closed" />
            </div>
            <p class="mt-0.5 flex flex-wrap items-center gap-1.5 text-xs text-muted">
                <span>{{ application.job.company }}</span>
                <span class="text-hairline">·</span>
                <Icon name="map-pin" :size="12" /> {{ application.job.location }}
                <template v-if="submitted">
                    <span class="text-hairline">·</span>
                    <Icon name="clock" :size="12" /> {{ submitted }}
                </template>
            </p>
        </div>
        <Badge :label="application.status" :variant="application.status" dot />
    </div>
</template>
