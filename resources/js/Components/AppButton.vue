<script setup>
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';

// One button to rule them all: variants follow the design DNA.
// Renders an Inertia <Link> when given `href`, otherwise a native <button>.
const props = defineProps({
    variant: { type: String, default: 'primary' }, // primary | secondary | ghost | danger
    size: { type: String, default: 'md' }, // sm | md | lg
    href: { type: String, default: null },
    type: { type: String, default: 'button' },
    block: { type: Boolean, default: false },
    disabled: { type: Boolean, default: false },
});

const base =
    'group inline-flex items-center justify-center gap-2 rounded-btn font-medium transition duration-150 ease-out focus:outline-none focus-visible:ring-2 focus-visible:ring-clay focus-visible:ring-offset-2 focus-visible:ring-offset-canvas disabled:cursor-not-allowed disabled:opacity-50';

const variants = {
    primary:
        'bg-clay text-[#FFFDF8] shadow-soft hover:bg-clay-strong active:translate-y-px',
    secondary:
        'border border-hairline bg-surface text-ink hover:bg-surface-2 active:translate-y-px',
    ghost: 'text-muted hover:bg-surface-2 hover:text-ink',
    danger: 'border border-danger/30 bg-danger/5 text-danger hover:bg-danger/10',
};

const sizes = {
    sm: 'px-3 py-1.5 text-sm',
    md: 'px-4 py-2.5 text-sm',
    lg: 'px-5 py-3 text-base',
};

const classes = computed(() => [
    base,
    variants[props.variant] ?? variants.primary,
    sizes[props.size] ?? sizes.md,
    props.block ? 'w-full' : '',
]);
</script>

<template>
    <Link v-if="href && !disabled" :href="href" :class="classes">
        <slot />
    </Link>
    <button v-else :type="type" :disabled="disabled" :class="classes">
        <slot />
    </button>
</template>
