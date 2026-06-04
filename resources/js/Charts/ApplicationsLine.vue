<script setup>
import { computed } from 'vue';
import { Line } from 'vue-chartjs';
import { palette, tooltipStyle, gridStyle } from './theme';

// Applications received per day over the trailing 30 days.
const props = defineProps({
    series: { type: Array, default: () => [] }, // [{date, count}]
});

const data = computed(() => ({
    labels: props.series.map((d) => d.date),
    datasets: [
        {
            data: props.series.map((d) => d.count),
            borderColor: palette.clay,
            backgroundColor: palette.claySoft,
            borderWidth: 2,
            fill: true,
            tension: 0.35,
            pointRadius: 0,
            pointHoverRadius: 5,
            pointHoverBackgroundColor: palette.clay,
            pointHoverBorderColor: '#FFFDF8',
            pointHoverBorderWidth: 2,
        },
    ],
}));

const options = {
    responsive: true,
    maintainAspectRatio: false,
    interaction: { intersect: false, mode: 'index' },
    plugins: {
        legend: { display: false },
        tooltip: {
            ...tooltipStyle,
            callbacks: {
                title: (items) =>
                    new Date(items[0].label).toLocaleDateString(undefined, {
                        month: 'short',
                        day: 'numeric',
                    }),
                label: (item) => `${item.parsed.y} application${item.parsed.y === 1 ? '' : 's'}`,
            },
        },
    },
    scales: {
        x: {
            grid: { display: false },
            border: { display: false },
            ticks: {
                maxTicksLimit: 6,
                callback(value) {
                    const d = new Date(this.getLabelForValue(value));
                    return d.toLocaleDateString(undefined, { month: 'short', day: 'numeric' });
                },
            },
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
        <Line :data="data" :options="options" />
    </div>
</template>
