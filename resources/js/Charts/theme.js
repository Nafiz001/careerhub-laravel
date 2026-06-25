import {
    Chart as ChartJS,
    CategoryScale,
    LinearScale,
    PointElement,
    LineElement,
    BarElement,
    ArcElement,
    Filler,
    Tooltip,
    Legend,
} from 'chart.js';

// Register only what we use, once.
ChartJS.register(
    CategoryScale,
    LinearScale,
    PointElement,
    LineElement,
    BarElement,
    ArcElement,
    Filler,
    Tooltip,
    Legend,
);

// Earthy palette — clay / sage / ink / muted, never rainbow defaults.
export const palette = {
    clay: '#C9785C',
    claySoft: 'rgba(201, 120, 92, 0.14)',
    clayStrong: '#B4593B',
    sage: '#6F7A63',
    ink: '#1A1A18',
    muted: '#6E6A5E',
    hairline: '#E7E2D6',
    caution: '#B5823A',
    danger: '#AE4B3B',
    positive: '#4B7B5A',
    surface: '#FFFFFF',
};

// Shared, restrained chart defaults.
ChartJS.defaults.font.family =
    '"Hanken Grotesk", ui-sans-serif, system-ui, sans-serif';
ChartJS.defaults.font.size = 12;
ChartJS.defaults.color = palette.muted;

export const tooltipStyle = {
    backgroundColor: palette.ink,
    titleColor: '#FFFDF8',
    bodyColor: '#E7E2D6',
    padding: 10,
    cornerRadius: 8,
    displayColors: false,
    titleFont: { family: '"Hanken Grotesk"', weight: '600' },
    bodyFont: { family: '"JetBrains Mono"' },
};

export const gridStyle = {
    color: palette.hairline,
    drawTicks: false,
};
