<script setup>
import { computed, ref, watch } from 'vue';
import { usePage } from '@inertiajs/vue3';

const page = usePage();
const visible = ref(true);

const flash = computed(() => page.props.flash ?? {});

// Re-show the banner whenever a new flash arrives.
watch(
    () => [flash.value.success, flash.value.error],
    () => { visible.value = true; }
);
</script>

<template>
    <div v-if="visible && (flash.success || flash.error)" class="mx-auto max-w-7xl px-4 pt-4 sm:px-6 lg:px-8">
        <div
            v-if="flash.success"
            class="flex items-center justify-between rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800"
        >
            <span>{{ flash.success }}</span>
            <button class="ml-4 text-emerald-600 hover:text-emerald-800" @click="visible = false">&times;</button>
        </div>
        <div
            v-if="flash.error"
            class="flex items-center justify-between rounded-lg border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-800"
        >
            <span>{{ flash.error }}</span>
            <button class="ml-4 text-rose-600 hover:text-rose-800" @click="visible = false">&times;</button>
        </div>
    </div>
</template>
