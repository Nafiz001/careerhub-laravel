<script setup>
import { ref, watch } from 'vue';
import { router } from '@inertiajs/vue3';
import Icon from '@/Components/Icon.vue';

// Heart toggle for bookmarking a job. Optimistic; reconciles on server reload.
const props = defineProps({
    jobId: { type: [Number, String], required: true },
    saved: { type: Boolean, default: false },
    variant: { type: String, default: 'icon' }, // icon | labelled
});

const isSaved = ref(props.saved);
const pending = ref(false);

watch(
    () => props.saved,
    (v) => (isSaved.value = v),
);

function toggle() {
    if (pending.value) return;
    pending.value = true;
    isSaved.value = !isSaved.value; // optimistic
    router.post(
        route('saved-jobs.toggle', props.jobId),
        {},
        {
            preserveScroll: true,
            preserveState: true,
            onError: () => (isSaved.value = !isSaved.value),
            onFinish: () => (pending.value = false),
        },
    );
}
</script>

<template>
    <button
        v-if="variant === 'icon'"
        type="button"
        :aria-pressed="isSaved"
        :aria-label="isSaved ? 'Remove from saved' : 'Save job'"
        class="flex h-9 w-9 items-center justify-center rounded-full border transition duration-150"
        :class="
            isSaved
                ? 'border-clay/30 bg-clay-soft text-clay-strong'
                : 'border-hairline bg-surface text-muted hover:border-clay/40 hover:text-clay'
        "
        @click.stop.prevent="toggle"
    >
        <Icon name="heart" :size="17" :class="isSaved ? 'fill-current' : ''" />
    </button>

    <button
        v-else
        type="button"
        :aria-pressed="isSaved"
        class="inline-flex items-center gap-2 rounded-btn border px-4 py-2.5 text-sm font-medium transition duration-150"
        :class="
            isSaved
                ? 'border-clay/30 bg-clay-soft text-clay-strong'
                : 'border-hairline bg-surface text-ink hover:bg-surface-2'
        "
        @click.stop.prevent="toggle"
    >
        <Icon name="heart" :size="17" :class="isSaved ? 'fill-current' : ''" />
        {{ isSaved ? 'Saved' : 'Save job' }}
    </button>
</template>
