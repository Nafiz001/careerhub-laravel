<script setup>
import { Link } from '@inertiajs/vue3';
import Icon from '@/Components/Icon.vue';

defineProps({
    title: { type: String, default: '' },
    subtitle: { type: String, default: '' },
});
</script>

<template>
    <div class="grid min-h-screen lg:grid-cols-2">
        <!-- Editorial brand panel -->
        <div class="relative hidden flex-col justify-between overflow-hidden bg-ink p-12 lg:flex">
            <div class="pointer-events-none absolute -right-24 -top-24 h-96 w-96 rounded-full bg-clay/20 blur-3xl" />
            <div class="pointer-events-none absolute -bottom-24 -left-16 h-80 w-80 rounded-full bg-sage/10 blur-3xl" />

            <Link href="/" class="relative flex items-center gap-2.5">
                <span class="flex h-8 w-8 items-center justify-center rounded-[9px] bg-clay text-[#FFFDF8]">
                    <Icon name="briefcase" :size="17" />
                </span>
                <span class="font-serif text-xl font-medium text-[#FFFDF8]">CareerHub</span>
            </Link>

            <div class="relative max-w-md">
                <p class="font-serif text-3xl font-medium leading-snug text-[#FFFDF8]">
                    Where careful work finds the people who value it.
                </p>
                <p class="mt-4 text-sm leading-relaxed text-[#C9C3B4]">
                    A curated board for engineering, design, and product roles. Thoughtful filters, real companies, and a calm place to apply.
                </p>
                <div class="mt-8 flex items-center gap-6">
                    <div>
                        <p class="font-mono text-2xl font-medium text-clay">1.2k+</p>
                        <p class="text-xs text-[#9C9686]">curated roles</p>
                    </div>
                    <div class="h-8 w-px bg-ink-border" />
                    <div>
                        <p class="font-mono text-2xl font-medium text-clay">300+</p>
                        <p class="text-xs text-[#9C9686]">hiring teams</p>
                    </div>
                </div>
            </div>

            <p class="relative text-xs text-[#7A7568]">A Laravel + Inertia + Vue demo by Md. Nafiz Ahmed.</p>
        </div>

        <!-- Form panel -->
        <div class="flex flex-col items-center justify-center bg-canvas px-4 py-10 sm:px-8">
            <div class="w-full max-w-sm">
                <Link href="/" class="mb-8 flex items-center gap-2.5 lg:hidden">
                    <span class="flex h-8 w-8 items-center justify-center rounded-[9px] bg-ink text-[#FFFDF8]">
                        <Icon name="briefcase" :size="17" />
                    </span>
                    <span class="font-serif text-xl font-medium text-ink">CareerHub</span>
                </Link>

                <div v-if="title" class="mb-7">
                    <h1 class="font-serif text-2xl font-medium tracking-tight text-ink">{{ title }}</h1>
                    <p v-if="subtitle" class="mt-1.5 text-sm text-muted">{{ subtitle }}</p>
                </div>

                <slot />
            </div>
        </div>
    </div>
</template>
