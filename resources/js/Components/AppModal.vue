<script setup>
import { watch, onMounted, onBeforeUnmount } from 'vue';
import Icon from '@/Components/Icon.vue';

// A clean, accessible modal in the design idiom — hairline border, soft lift,
// warm scrim, editorial header.
const props = defineProps({
    show: { type: Boolean, default: false },
    title: { type: String, default: '' },
    maxWidth: { type: String, default: 'lg' }, // sm | md | lg | xl
});
const emit = defineEmits(['close']);

const widths = {
    sm: 'sm:max-w-sm',
    md: 'sm:max-w-md',
    lg: 'sm:max-w-lg',
    xl: 'sm:max-w-2xl',
};

function close() {
    emit('close');
}
function onEsc(e) {
    if (e.key === 'Escape' && props.show) close();
}

watch(
    () => props.show,
    (v) => {
        document.body.style.overflow = v ? 'hidden' : '';
    },
);

onMounted(() => document.addEventListener('keydown', onEsc));
onBeforeUnmount(() => {
    document.removeEventListener('keydown', onEsc);
    document.body.style.overflow = '';
});
</script>

<template>
    <Teleport to="body">
        <Transition
            enter-active-class="transition duration-200 ease-out"
            enter-from-class="opacity-0"
            enter-to-class="opacity-100"
            leave-active-class="transition duration-150 ease-in"
            leave-from-class="opacity-100"
            leave-to-class="opacity-0"
        >
            <div v-if="show" class="fixed inset-0 z-50 overflow-y-auto">
                <div class="fixed inset-0 bg-ink/30 backdrop-blur-sm" @click="close" />
                <div class="flex min-h-full items-end justify-center p-4 sm:items-center">
                    <Transition
                        enter-active-class="transition duration-200 ease-out"
                        enter-from-class="translate-y-3 opacity-0 sm:scale-95"
                        enter-to-class="translate-y-0 opacity-100 sm:scale-100"
                        leave-active-class="transition duration-150 ease-in"
                        leave-from-class="translate-y-0 opacity-100 sm:scale-100"
                        leave-to-class="translate-y-3 opacity-0 sm:scale-95"
                    >
                        <div
                            v-if="show"
                            class="relative w-full overflow-hidden rounded-card border border-hairline bg-surface shadow-lift-lg"
                            :class="widths[maxWidth]"
                            role="dialog"
                            aria-modal="true"
                        >
                            <div class="h-1 w-full bg-gradient-to-r from-clay/0 via-clay to-clay/0" />
                            <div v-if="title || $slots.header" class="flex items-start justify-between gap-4 border-b border-hairline px-6 py-4">
                                <div>
                                    <slot name="header">
                                        <h2 class="font-serif text-lg font-medium text-ink">{{ title }}</h2>
                                    </slot>
                                </div>
                                <button
                                    class="-mr-1 flex h-8 w-8 items-center justify-center rounded-full text-muted transition hover:bg-surface-2 hover:text-ink"
                                    aria-label="Close"
                                    @click="close"
                                >
                                    <Icon name="x" :size="18" />
                                </button>
                            </div>
                            <div class="px-6 py-5">
                                <slot />
                            </div>
                        </div>
                    </Transition>
                </div>
            </div>
        </Transition>
    </Teleport>
</template>
