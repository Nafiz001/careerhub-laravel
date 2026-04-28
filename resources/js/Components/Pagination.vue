<script setup>
import { Link } from '@inertiajs/vue3';

defineProps({
    // Laravel paginator "links" array.
    links: { type: Array, required: true },
});
</script>

<template>
    <nav v-if="links.length > 3" class="flex flex-wrap items-center justify-center gap-1">
        <template v-for="(link, i) in links" :key="i">
            <span
                v-if="link.url === null"
                class="cursor-not-allowed rounded-md px-3 py-2 text-sm text-gray-400"
                v-html="link.label"
            />
            <Link
                v-else
                :href="link.url"
                class="rounded-md px-3 py-2 text-sm transition"
                :class="
                    link.active
                        ? 'bg-indigo-600 text-white'
                        : 'bg-white text-gray-600 ring-1 ring-gray-200 hover:bg-gray-50'
                "
                v-html="link.label"
                preserve-scroll
            />
        </template>
    </nav>
</template>
