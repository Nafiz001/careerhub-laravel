<script setup>
import { watch } from 'vue';
import { usePage } from '@inertiajs/vue3';
import { useToast } from '@/Composables/useToast';
import Icon from '@/Components/Icon.vue';

// Renders global toasts and bridges Laravel flash messages into them,
// so any redirect-with-flash surfaces a tasteful, auto-dismissing toast.
const { toasts, dismiss, success, error } = useToast();
const page = usePage();

watch(
    () => page.props.flash,
    (flash) => {
        if (!flash) return;
        if (flash.success) success(flash.success);
        if (flash.error) error(flash.error);
    },
    { deep: true, immediate: true },
);

const accent = {
    success: 'text-positive',
    error: 'text-danger',
    info: 'text-clay',
};
const iconName = {
    success: 'check-circle',
    error: 'alert-circle',
    info: 'sparkles',
};
</script>

<template>
    <div class="pointer-events-none fixed inset-x-0 top-4 z-[60] flex flex-col items-center gap-2 px-4">
        <TransitionGroup
            enter-active-class="transition duration-200 ease-out"
            enter-from-class="-translate-y-2 opacity-0"
            enter-to-class="translate-y-0 opacity-100"
            leave-active-class="transition duration-150 ease-in"
            leave-from-class="translate-y-0 opacity-100"
            leave-to-class="-translate-y-1 opacity-0"
            move-class="transition duration-200"
        >
            <div
                v-for="t in toasts"
                :key="t.id"
                class="pointer-events-auto flex w-full max-w-md items-start gap-3 rounded-card border border-hairline bg-surface px-4 py-3 shadow-lift"
            >
                <span :class="accent[t.type]" class="mt-0.5">
                    <Icon :name="iconName[t.type]" :size="18" />
                </span>
                <p class="flex-1 text-sm leading-snug text-ink">{{ t.message }}</p>
                <button
                    class="rounded-md p-0.5 text-muted transition hover:bg-surface-2 hover:text-ink"
                    aria-label="Dismiss"
                    @click="dismiss(t.id)"
                >
                    <Icon name="x" :size="16" />
                </button>
            </div>
        </TransitionGroup>
    </div>
</template>
