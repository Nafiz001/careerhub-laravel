<script setup>
import Icon from '@/Components/Icon.vue';

// A refined, editorial empty state — not a sad grey box.
defineProps({
    icon: { type: String, default: 'inbox' },
    title: { type: String, required: true },
    description: { type: String, default: '' },
});
</script>

<template>
    <div class="flex flex-col items-center px-6 py-16 text-center">
        <div class="relative mb-5">
            <div class="absolute inset-0 -z-10 scale-150 rounded-full bg-clay-soft/50 blur-xl" />
            <div class="flex h-14 w-14 items-center justify-center rounded-full border border-hairline bg-surface text-clay">
                <Icon :name="icon" :size="24" />
            </div>
        </div>
        <h3 class="font-serif text-lg font-medium text-ink">{{ title }}</h3>
        <p v-if="description" class="mt-1.5 max-w-sm text-sm text-muted">{{ description }}</p>
        <div v-if="$slots.action" class="mt-5">
            <slot name="action" />
        </div>
    </div>
</template>
