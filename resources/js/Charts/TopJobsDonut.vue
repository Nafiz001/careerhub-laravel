<script setup>
import { computed } from 'vue';
import { Doughnut } from 'vue-chartjs';
import { palette, tooltipStyle } from './theme';

// Top jobs by applicant share.
const props = defineProps({
    jobs: { type: Array, default: () => [] }, // [{title, applicants}]
});

// Earthy, monochromatic-leaning ramp (clay → sage → muted), no rainbow.
const ramp = ['#C9785C', '#6F7A63', '#B5823A', '#3C5A66', '#9A8C76'];

const filtered = computed(() => props.jobs.filter((j) => j.applicants > 0));

const data = computed(() => ({
    labels: filtered.value.map((j) => j.title),
    datasets: [
        {
            data: filtered.value.map((j) => j.applicants),
            backgroundColor: filtered.value.map((_, i) => ramp[i % ramp.length]),
            borderColor: palette.surface,
            borderWidth: 3,
            hoverOffset: 6,
        },
    ],
}));

const total = computed(() => filtered.value.reduce((s, j) => s + j.applicants, 0));

const options = {
    responsive: true,
    maintainAspectRatio: false,
    cutout: '66%',
    plugins: {
        legend: { display: false },
        tooltip: {
            ...tooltipStyle,
            callbacks: {
                label: (item) => `${item.label}: ${item.parsed}`,
            },
        },
    },
};
</script>

<template>
    <div v-if="filtered.length" class="grid grid-cols-1 items-center gap-4 sm:grid-cols-[180px_1fr]">
        <div class="relative mx-auto h-44 w-44">
            <Doughnut :data="data" :options="options" />
            <div class="pointer-events-none absolute inset-0 flex flex-col items-center justify-center">
                <span class="font-mono text-2xl font-medium text-ink">{{ total }}</span>
                <span class="text-xs text-muted">applicants</span>
            </div>
        </div>
        <ul class="space-y-2">
            <li v-for="(j, i) in filtered" :key="i" class="flex items-center gap-2.5 text-sm">
                <span class="h-2.5 w-2.5 shrink-0 rounded-full" :style="{ background: ramp[i % ramp.length] }" />
                <span class="min-w-0 flex-1 truncate text-ink">{{ j.title }}</span>
                <span class="font-mono tabular-nums text-muted">{{ j.applicants }}</span>
            </li>
        </ul>
    </div>
    <div v-else class="flex h-44 items-center justify-center text-sm text-muted">
        No applicants to chart yet.
    </div>
</template>
