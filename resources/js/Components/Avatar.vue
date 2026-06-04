<script setup>
import { computed } from 'vue';

// Initials-based avatar with optional image. Warm tinted fallback.
const props = defineProps({
    name: { type: String, default: '' },
    src: { type: String, default: null },
    size: { type: Number, default: 36 },
    square: { type: Boolean, default: false },
});

const initials = computed(() => {
    const parts = (props.name || '').trim().split(/\s+/).filter(Boolean);
    if (!parts.length) return '?';
    return (parts[0][0] + (parts.length > 1 ? parts[parts.length - 1][0] : '')).toUpperCase();
});

const style = computed(() => ({ width: `${props.size}px`, height: `${props.size}px` }));
const fontSize = computed(() => `${Math.round(props.size * 0.4)}px`);
</script>

<template>
    <span
        class="inline-flex shrink-0 items-center justify-center overflow-hidden border border-hairline bg-clay-soft text-clay-strong"
        :class="square ? 'rounded-card' : 'rounded-full'"
        :style="style"
    >
        <img v-if="src" :src="src" :alt="name" class="h-full w-full object-cover" />
        <span v-else class="font-semibold" :style="{ fontSize }">{{ initials }}</span>
    </span>
</template>
