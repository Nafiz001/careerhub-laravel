<script setup>
import { computed } from 'vue';

const props = defineProps({
    label: { type: String, required: true },
    // color key: type-based or status-based
    variant: { type: String, default: 'gray' },
});

const styles = {
    'full-time': 'bg-emerald-100 text-emerald-800 ring-emerald-200',
    'part-time': 'bg-amber-100 text-amber-800 ring-amber-200',
    remote: 'bg-sky-100 text-sky-800 ring-sky-200',
    internship: 'bg-violet-100 text-violet-800 ring-violet-200',
    pending: 'bg-amber-100 text-amber-800 ring-amber-200',
    reviewed: 'bg-sky-100 text-sky-800 ring-sky-200',
    accepted: 'bg-emerald-100 text-emerald-800 ring-emerald-200',
    rejected: 'bg-rose-100 text-rose-800 ring-rose-200',
    open: 'bg-emerald-100 text-emerald-800 ring-emerald-200',
    closed: 'bg-gray-200 text-gray-700 ring-gray-300',
    gray: 'bg-gray-100 text-gray-700 ring-gray-200',
};

const classes = computed(() => styles[props.variant] ?? styles.gray);
</script>

<template>
    <span
        class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium capitalize ring-1 ring-inset"
        :class="classes"
    >
        {{ label }}
    </span>
</template>
