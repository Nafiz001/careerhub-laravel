<script setup>
import { computed } from 'vue';

// Thin line icons (Lucide-style, 1.5px stroke) referenced by name.
// Kept as inline path data so we ship no icon-font / extra dependency.
const props = defineProps({
    name: { type: String, required: true },
    size: { type: [Number, String], default: 18 },
    strokeWidth: { type: [Number, String], default: 1.5 },
});

const paths = {
    search: ['<circle cx="11" cy="11" r="7"/>', '<path d="m20 20-3.5-3.5"/>'],
    'map-pin': ['<path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/>', '<circle cx="12" cy="10" r="3"/>'],
    briefcase: ['<rect x="2" y="7" width="20" height="14" rx="2"/>', '<path d="M16 7V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v2"/>', '<path d="M2 13h20"/>'],
    'dollar-sign': ['<line x1="12" y1="2" x2="12" y2="22"/>', '<path d="M17 6.5C17 4.6 14.8 3.5 12 3.5S7 4.8 7 6.8s2 3.2 5 3.2 5 1.2 5 3.2-2.2 3.3-5 3.3-5-1.1-5-3"/>'],
    users: ['<path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/>', '<circle cx="9" cy="7" r="4"/>', '<path d="M22 21v-2a4 4 0 0 0-3-3.87"/>', '<path d="M16 3.13a4 4 0 0 1 0 7.75"/>'],
    eye: ['<path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7-10-7-10-7Z"/>', '<circle cx="12" cy="12" r="3"/>'],
    heart: ['<path d="M19 5.5a5 5 0 0 0-7 0l-.9.9-.9-.9a5 5 0 1 0-7 7l.9.9 7 7 7-7 .9-.9a5 5 0 0 0 0-7Z"/>'],
    bell: ['<path d="M6 8a6 6 0 0 1 12 0c0 7 3 9 3 9H3s3-2 3-9"/>', '<path d="M10.3 21a1.94 1.94 0 0 0 3.4 0"/>'],
    bookmark: ['<path d="M19 21l-7-5-7 5V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2Z"/>'],
    check: ['<path d="M20 6 9 17l-5-5"/>'],
    'check-circle': ['<path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>', '<path d="m22 4-10 10.01-3-3"/>'],
    x: ['<path d="M18 6 6 18"/>', '<path d="m6 6 12 12"/>'],
    'arrow-right': ['<path d="M5 12h14"/>', '<path d="m12 5 7 7-7 7"/>'],
    'arrow-left': ['<path d="M19 12H5"/>', '<path d="m12 19-7-7 7-7"/>'],
    'chevron-down': ['<path d="m6 9 6 6 6-6"/>'],
    'chevron-right': ['<path d="m9 18 6-6-6-6"/>'],
    plus: ['<path d="M12 5v14"/>', '<path d="M5 12h14"/>'],
    menu: ['<path d="M4 6h16"/>', '<path d="M4 12h16"/>', '<path d="M4 18h16"/>'],
    'sliders-horizontal': ['<line x1="21" y1="6" x2="9" y2="6"/>', '<line x1="5" y1="6" x2="3" y2="6"/>', '<circle cx="7" cy="6" r="2"/>', '<line x1="21" y1="12" x2="13" y2="12"/>', '<line x1="9" y1="12" x2="3" y2="12"/>', '<circle cx="11" cy="12" r="2"/>', '<line x1="21" y1="18" x2="17" y2="18"/>', '<line x1="13" y1="18" x2="3" y2="18"/>', '<circle cx="15" cy="18" r="2"/>'],
    globe: ['<circle cx="12" cy="12" r="10"/>', '<path d="M2 12h20"/>', '<path d="M12 2a15.3 15.3 0 0 1 0 20 15.3 15.3 0 0 1 0-20Z"/>'],
    'file-text': ['<path d="M14 3v4a1 1 0 0 0 1 1h4"/>', '<path d="M17 21H7a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h7l5 5v11a2 2 0 0 1-2 2Z"/>', '<path d="M9 13h6"/>', '<path d="M9 17h4"/>'],
    upload: ['<path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>', '<path d="m17 8-5-5-5 5"/>', '<path d="M12 3v12"/>'],
    download: ['<path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>', '<path d="m7 10 5 5 5-5"/>', '<path d="M12 15V3"/>'],
    mail: ['<rect x="2" y="4" width="20" height="16" rx="2"/>', '<path d="m22 7-10 6L2 7"/>'],
    star: ['<path d="M12 2.5l2.9 6 6.6.9-4.8 4.6 1.2 6.5L12 17.8 6.1 20.5l1.2-6.5L2.5 9.4l6.6-.9Z"/>'],
    sparkles: ['<path d="M12 3l1.6 4.4L18 9l-4.4 1.6L12 15l-1.6-4.4L6 9l4.4-1.6Z"/>', '<path d="M19 14l.8 2.2L22 17l-2.2.8L19 20l-.8-2.2L16 17l2.2-.8Z"/>'],
    'trending-up': ['<path d="M22 7 13.5 15.5 8.5 10.5 2 17"/>', '<path d="M16 7h6v6"/>'],
    clock: ['<circle cx="12" cy="12" r="9"/>', '<path d="M12 7v5l3 2"/>'],
    calendar: ['<rect x="3" y="4" width="18" height="18" rx="2"/>', '<path d="M16 2v4"/>', '<path d="M8 2v4"/>', '<path d="M3 10h18"/>'],
    'log-out': ['<path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>', '<path d="m16 17 5-5-5-5"/>', '<path d="M21 12H9"/>'],
    user: ['<path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/>', '<circle cx="12" cy="7" r="4"/>'],
    'layout-dashboard': ['<rect x="3" y="3" width="7" height="9" rx="1"/>', '<rect x="14" y="3" width="7" height="5" rx="1"/>', '<rect x="14" y="12" width="7" height="9" rx="1"/>', '<rect x="3" y="16" width="7" height="5" rx="1"/>'],
    inbox: ['<path d="M22 12h-6l-2 3h-4l-2-3H2"/>', '<path d="M5.5 5.5 2 12v6a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2v-6l-3.5-6.5A2 2 0 0 0 16.8 4H7.2a2 2 0 0 0-1.7 1.5Z"/>'],
    'external-link': ['<path d="M15 3h6v6"/>', '<path d="M10 14 21 3"/>', '<path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/>'],
    'alert-circle': ['<circle cx="12" cy="12" r="10"/>', '<line x1="12" y1="8" x2="12" y2="12"/>', '<line x1="12" y1="16" x2="12.01" y2="16"/>'],
    building: ['<rect x="4" y="2" width="16" height="20" rx="2"/>', '<path d="M9 22v-4h6v4"/>', '<path d="M8 6h.01"/>', '<path d="M16 6h.01"/>', '<path d="M8 10h.01"/>', '<path d="M16 10h.01"/>', '<path d="M8 14h.01"/>', '<path d="M16 14h.01"/>'],
    phone: ['<path d="M22 16.9v3a2 2 0 0 1-2.2 2 19.8 19.8 0 0 1-8.6-3 19.5 19.5 0 0 1-6-6 19.8 19.8 0 0 1-3-8.6A2 2 0 0 1 4.1 2h3a2 2 0 0 1 2 1.7c.1.9.3 1.8.6 2.7a2 2 0 0 1-.5 2.1L8.1 9.9a16 16 0 0 0 6 6l1.4-1.1a2 2 0 0 1 2.1-.5c.9.3 1.8.5 2.7.6a2 2 0 0 1 1.7 2Z"/>'],
    'graduation-cap': ['<path d="M22 10 12 5 2 10l10 5 10-5Z"/>', '<path d="M6 12v5c0 1 2.7 2 6 2s6-1 6-2v-5"/>'],
};

const sizePx = computed(() => `${props.size}px`);
const iconPaths = computed(() => paths[props.name] ?? paths['alert-circle']);
</script>

<template>
    <svg
        :width="sizePx"
        :height="sizePx"
        viewBox="0 0 24 24"
        fill="none"
        stroke="currentColor"
        :stroke-width="strokeWidth"
        stroke-linecap="round"
        stroke-linejoin="round"
        aria-hidden="true"
        class="shrink-0"
        v-html="iconPaths.join('')"
    />
</template>
