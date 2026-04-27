<script setup>
import InputError from '@/Components/InputError.vue';

defineProps({
    form: { type: Object, required: true },
    types: { type: Array, required: true },
    submitLabel: { type: String, default: 'Save' },
});

const emit = defineEmits(['submit']);
</script>

<template>
    <form @submit.prevent="emit('submit')" class="space-y-5">
        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
            <div>
                <label class="block text-sm font-medium text-gray-700">Job title</label>
                <input v-model="form.title" type="text" class="mt-1 w-full rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500" />
                <InputError class="mt-1" :message="form.errors.title" />
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Company</label>
                <input v-model="form.company" type="text" class="mt-1 w-full rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500" />
                <InputError class="mt-1" :message="form.errors.company" />
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Location</label>
                <input v-model="form.location" type="text" placeholder="e.g. Dhaka, Bangladesh or Remote" class="mt-1 w-full rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500" />
                <InputError class="mt-1" :message="form.errors.location" />
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Employment type</label>
                <select v-model="form.type" class="mt-1 w-full rounded-lg border-gray-300 text-sm capitalize focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="" disabled>Select a type</option>
                    <option v-for="t in types" :key="t" :value="t" class="capitalize">{{ t }}</option>
                </select>
                <InputError class="mt-1" :message="form.errors.type" />
            </div>
            <div class="sm:col-span-2">
                <label class="block text-sm font-medium text-gray-700">Salary range <span class="text-gray-400">(optional)</span></label>
                <input v-model="form.salary_range" type="text" placeholder="e.g. BDT 60,000 - 90,000" class="mt-1 w-full rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500" />
                <InputError class="mt-1" :message="form.errors.salary_range" />
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700">Description</label>
            <textarea v-model="form.description" rows="7" placeholder="Describe the role, responsibilities, and requirements (min. 20 characters)..." class="mt-1 w-full rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>
            <InputError class="mt-1" :message="form.errors.description" />
        </div>

        <label class="flex items-center gap-2">
            <input v-model="form.is_open" type="checkbox" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500" />
            <span class="text-sm text-gray-700">Open for applications</span>
        </label>

        <div class="flex items-center gap-3">
            <button type="submit" :disabled="form.processing" class="inline-flex items-center rounded-lg bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-500 disabled:opacity-50">
                {{ submitLabel }}
            </button>
            <slot name="actions" />
        </div>
    </form>
</template>
