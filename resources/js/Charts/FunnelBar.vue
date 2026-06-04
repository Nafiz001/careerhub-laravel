<script setup>
import { computed } from 'vue';
import { Bar } from 'vue-chartjs';
import { palette, tooltipStyle, gridStyle } from './theme';

// Application status funnel: pending / reviewed / accepted / rejected.
const props = defineProps({
    funnel: { type: Array, default: () => [] }, // [{status, count}]
});

const colorFor = {
    pending: palette.caution,
    reviewed: '#3C5A66',
    accepted: palette.positive,
    rejected: palette.danger,
};

const data = computed(() => ({
    labels: props.funnel.map((f) => f.status),
    datasets: [
        {
            data: props.funnel.map((f) => f.count),
            backgroundColor: props.funnel.map((f) => colorFor[f.status] ?? palette.clay),
            borderRadius: 6,
            borderSkipped: false,
            maxBarThickness: 56,
        },
    ],
}));

const options = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        legend: { display: false },
        tooltip: {
            ...tooltipStyle,
            callbacks: {
                title: (items) => items[0].label,
                label: (item) => `${item.parsed.y} applicant${item.parsed.y === 1 ? '' : 's'}`,
            },
        },
    },
    scales: {
        x: {
            grid: { display: false },
            border: { display: false },
            ticks: { font: { weight: '500' }, color: palette.ink },
        },
        y: {
            beginAtZero: true,
            grid: gridStyle,
            border: { display: false },
            ticks: { precision: 0, maxTicksLimit: 5 },
        },
    },
};
</script>

<template>
    <div class="h-56">
        <Bar :data="data" :options="options" />
    </div>
</template>
