<script setup>
import { ref } from 'vue';
import Icon from '@/Components/Icon.vue';

// Chip-style skills editor. v-model is an array of strings.
const model = defineModel({ type: Array, default: () => [] });
const props = defineProps({
    placeholder: { type: String, default: 'Add a skill and press Enter' },
    suggestions: { type: Array, default: () => [] },
});

const draft = ref('');

function add(value) {
    const v = (value ?? draft.value).trim();
    if (!v) return;
    const exists = model.value.some((s) => s.toLowerCase() === v.toLowerCase());
    if (!exists) model.value = [...model.value, v];
    draft.value = '';
}

function remove(i) {
    model.value = model.value.filter((_, idx) => idx !== i);
}

function onKeydown(e) {
    if (e.key === 'Enter' || e.key === ',') {
        e.preventDefault();
        add();
    } else if (e.key === 'Backspace' && !draft.value && model.value.length) {
        remove(model.value.length - 1);
    }
}
</script>

<template>
    <div>
        <div
            class="flex flex-wrap items-center gap-1.5 rounded-input border border-hairline bg-surface px-2 py-2 focus-within:border-clay"
        >
            <span
                v-for="(skill, i) in model"
                :key="skill + i"
                class="inline-flex items-center gap-1 rounded-full bg-surface-2 py-0.5 pl-2.5 pr-1 text-xs font-medium text-ink"
            >
                {{ skill }}
                <button
                    type="button"
                    class="flex h-4 w-4 items-center justify-center rounded-full text-muted transition hover:bg-hairline hover:text-ink"
                    @click="remove(i)"
                    aria-label="Remove skill"
                >
                    <Icon name="x" :size="12" />
                </button>
            </span>
            <input
                v-model="draft"
                type="text"
                :placeholder="model.length ? '' : placeholder"
                class="min-w-[8rem] flex-1 border-0 bg-transparent p-1 text-sm text-ink placeholder:text-muted focus:outline-none focus:ring-0"
                @keydown="onKeydown"
                @blur="add()"
            />
        </div>
        <div v-if="suggestions.length" class="mt-2 flex flex-wrap gap-1.5">
            <button
                v-for="s in suggestions"
                :key="s"
                type="button"
                class="rounded-full border border-hairline bg-surface px-2.5 py-0.5 text-xs text-muted transition hover:border-clay/40 hover:text-clay"
                @click="add(s)"
            >
                + {{ s }}
            </button>
        </div>
    </div>
</template>
