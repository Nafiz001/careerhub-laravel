<script setup>
import { computed } from 'vue';

const props = defineProps({
    label: { type: String, required: true },
    // color key: type-based or status-based
    variant: { type: String, default: 'gray' },
    // optional: render a small leading dot
    dot: { type: Boolean, default: false },
});

// Warm, restrained badge palette — pill chips with hairline rings.
const styles = {
    'full-time': 'bg-sage-soft text-[#465038] ring-sage/30',
    'part-time': 'bg-clay-soft text-clay-strong ring-clay/25',
    remote: 'bg-[#E6EEF1] text-[#3C5A66] ring-[#3C5A66]/20',
    internship: 'bg-[#EFE9DA] text-[#7A6438] ring-[#7A6438]/20',
    pending: 'bg-[#F4ECDB] text-caution ring-caution/25',
    reviewed: 'bg-[#E6EEF1] text-[#3C5A66] ring-[#3C5A66]/20',
    accepted: 'bg-sage-soft text-positive ring-positive/25',
    rejected: 'bg-[#F3E1DC] text-danger ring-danger/25',
    open: 'bg-sage-soft text-positive ring-positive/25',
    closed: 'bg-surface-2 text-muted ring-hairline',
    featured: 'bg-clay-soft text-clay-strong ring-clay/30',
    gray: 'bg-surface-2 text-muted ring-hairline',
};

const dotColors = {
    pending: 'bg-caution',
    reviewed: 'bg-[#3C5A66]',
    accepted: 'bg-positive',
    rejected: 'bg-danger',
    open: 'bg-positive',
    closed: 'bg-muted',
};

const classes = computed(() => styles[props.variant] ?? styles.gray);
const dotClass = computed(() => dotColors[props.variant] ?? 'bg-muted');
</script>

<template>
    <span
        class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-0.5 text-xs font-medium capitalize ring-1 ring-inset"
        :class="classes"
    >
        <span v-if="dot" class="h-1.5 w-1.5 rounded-full" :class="dotClass" />
        {{ label }}
    </span>
</template>
