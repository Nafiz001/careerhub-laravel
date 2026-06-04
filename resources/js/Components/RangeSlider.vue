<script setup>
import { computed } from 'vue';

// Dual-handle range slider (two stacked native inputs) styled to the palette.
// v-model is [min, max].
const model = defineModel({ type: Array, default: () => [0, 0] });
const props = defineProps({
    min: { type: Number, default: 0 },
    max: { type: Number, default: 100 },
    step: { type: Number, default: 1 },
});

const low = computed({
    get: () => model.value[0] ?? props.min,
    set: (v) => {
        const val = Math.min(Number(v), model.value[1] ?? props.max);
        model.value = [val, model.value[1] ?? props.max];
    },
});
const high = computed({
    get: () => model.value[1] ?? props.max,
    set: (v) => {
        const val = Math.max(Number(v), model.value[0] ?? props.min);
        model.value = [model.value[0] ?? props.min, val];
    },
});

const span = computed(() => props.max - props.min || 1);
const leftPct = computed(() => ((low.value - props.min) / span.value) * 100);
const rightPct = computed(() => ((high.value - props.min) / span.value) * 100);
</script>

<template>
    <div class="range-slider relative h-5 select-none">
        <!-- track -->
        <div class="absolute top-1/2 h-1 w-full -translate-y-1/2 rounded-full bg-hairline" />
        <!-- active range -->
        <div
            class="absolute top-1/2 h-1 -translate-y-1/2 rounded-full bg-clay"
            :style="{ left: `${leftPct}%`, right: `${100 - rightPct}%` }"
        />
        <input
            v-model.number="low"
            type="range"
            :min="min"
            :max="max"
            :step="step"
            class="thumb pointer-events-none absolute top-1/2 h-5 w-full -translate-y-1/2 appearance-none bg-transparent"
        />
        <input
            v-model.number="high"
            type="range"
            :min="min"
            :max="max"
            :step="step"
            class="thumb pointer-events-none absolute top-1/2 h-5 w-full -translate-y-1/2 appearance-none bg-transparent"
        />
    </div>
</template>

<style scoped>
.thumb::-webkit-slider-thumb {
    pointer-events: auto;
    -webkit-appearance: none;
    height: 16px;
    width: 16px;
    border-radius: 9999px;
    background: #fffdf8;
    border: 2px solid #c9785c;
    box-shadow: 0 1px 2px rgba(20, 20, 18, 0.15);
    cursor: pointer;
}
.thumb::-moz-range-thumb {
    pointer-events: auto;
    height: 14px;
    width: 14px;
    border-radius: 9999px;
    background: #fffdf8;
    border: 2px solid #c9785c;
    box-shadow: 0 1px 2px rgba(20, 20, 18, 0.15);
    cursor: pointer;
}
.thumb::-moz-range-track {
    background: transparent;
}
</style>
