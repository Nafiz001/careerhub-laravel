<script setup>
import { Link } from '@inertiajs/vue3';

defineProps({
    // Laravel paginator "links" array.
    links: { type: Array, required: true },
});

// Render «/» arrows as glyphs instead of "Previous"/"Next" labels.
function clean(label) {
    return label
        .replace('&laquo; Previous', '‹')
        .replace('Next &raquo;', '›')
        .replace('pagination.previous', '‹')
        .replace('pagination.next', '›');
}
</script>

<template>
    <nav v-if="links.length > 3" class="flex flex-wrap items-center justify-center gap-1.5">
        <template v-for="(link, i) in links" :key="i">
            <span
                v-if="link.url === null"
                class="flex h-9 min-w-9 cursor-not-allowed items-center justify-center rounded-btn px-3 font-mono text-sm text-muted/50"
                v-html="clean(link.label)"
            />
            <Link
                v-else
                :href="link.url"
                class="flex h-9 min-w-9 items-center justify-center rounded-btn px-3 font-mono text-sm transition"
                :class="
                    link.active
                        ? 'bg-ink text-[#FFFDF8]'
                        : 'border border-hairline bg-surface text-muted hover:border-clay/40 hover:text-ink'
                "
                v-html="clean(link.label)"
                preserve-scroll
            />
        </template>
    </nav>
</template>
