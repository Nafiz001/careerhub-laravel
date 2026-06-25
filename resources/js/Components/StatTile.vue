<script setup>
import Icon from '@/Components/Icon.vue';

// Editorial stat display: big mono figure, quiet label, hairline card,
// thin clay top-rule signature.
defineProps({
    label: { type: String, required: true },
    value: { type: [Number, String], required: true },
    icon: { type: String, default: null },
    accent: { type: String, default: 'ink' }, // ink | clay | sage | positive | caution
    hint: { type: String, default: '' },
});

const accentText = {
    ink: 'text-ink',
    clay: 'text-clay-strong',
    sage: 'text-sage',
    positive: 'text-positive',
    caution: 'text-caution',
};
</script>

<template>
    <div class="relative overflow-hidden rounded-card border border-hairline bg-surface p-5 shadow-soft">
        <div class="absolute inset-x-0 top-0 h-px bg-gradient-to-r from-clay/0 via-clay/40 to-clay/0" />
        <div class="flex items-start justify-between">
            <p class="text-xs font-medium uppercase tracking-wide text-muted">{{ label }}</p>
            <span v-if="icon" class="text-muted/70">
                <Icon :name="icon" :size="16" />
            </span>
        </div>
        <p class="mt-3 font-mono text-3xl font-medium tabular-nums" :class="accentText[accent]">
            {{ value }}
        </p>
        <p v-if="hint" class="mt-1 text-xs text-muted">{{ hint }}</p>
    </div>
</template>
