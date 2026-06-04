<script setup>
import InputError from '@/Components/InputError.vue';
import SkillsInput from '@/Components/SkillsInput.vue';
import AppButton from '@/Components/AppButton.vue';

defineProps({
    form: { type: Object, required: true },
    types: { type: Array, required: true },
    categories: { type: Array, default: () => [] },
    experienceLevels: { type: Array, default: () => [] },
    submitLabel: { type: String, default: 'Save' },
});

const emit = defineEmits(['submit']);

const field =
    'mt-1.5 w-full rounded-input border-hairline bg-surface text-sm text-ink placeholder:text-muted focus:border-clay focus:ring-clay';
const label = 'block text-sm font-medium text-ink';
</script>

<template>
    <form class="space-y-6" @submit.prevent="emit('submit')">
        <!-- Basics -->
        <section class="space-y-5">
            <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                <div>
                    <label :class="label">Job title</label>
                    <input v-model="form.title" type="text" placeholder="e.g. Senior Frontend Engineer" :class="field" />
                    <InputError class="mt-1" :message="form.errors.title" />
                </div>
                <div>
                    <label :class="label">Company</label>
                    <input v-model="form.company" type="text" :class="field" />
                    <InputError class="mt-1" :message="form.errors.company" />
                </div>
                <div>
                    <label :class="label">Location</label>
                    <input v-model="form.location" type="text" placeholder="e.g. Dhaka, Bangladesh or Remote" :class="field" />
                    <InputError class="mt-1" :message="form.errors.location" />
                </div>
                <div>
                    <label :class="label">Employment type</label>
                    <select v-model="form.type" :class="[field, 'capitalize']">
                        <option value="" disabled>Select a type</option>
                        <option v-for="t in types" :key="t" :value="t" class="capitalize">{{ t }}</option>
                    </select>
                    <InputError class="mt-1" :message="form.errors.type" />
                </div>
                <div v-if="categories.length">
                    <label :class="label">Category <span class="font-normal text-muted">(optional)</span></label>
                    <select v-model="form.category" :class="field">
                        <option value="">Select a category</option>
                        <option v-for="c in categories" :key="c" :value="c">{{ c }}</option>
                    </select>
                    <InputError class="mt-1" :message="form.errors.category" />
                </div>
                <div v-if="experienceLevels.length">
                    <label :class="label">Experience level <span class="font-normal text-muted">(optional)</span></label>
                    <select v-model="form.experience_level" :class="[field, 'capitalize']">
                        <option value="">Any level</option>
                        <option v-for="l in experienceLevels" :key="l" :value="l" class="capitalize">{{ l }}</option>
                    </select>
                    <InputError class="mt-1" :message="form.errors.experience_level" />
                </div>
            </div>
        </section>

        <div class="accent-rule" />

        <!-- Compensation -->
        <section>
            <h3 class="font-serif text-base font-medium text-ink">Compensation</h3>
            <div class="mt-3 grid grid-cols-1 gap-5 sm:grid-cols-2">
                <div>
                    <label :class="label">Minimum salary <span class="font-normal text-muted">(USD / year)</span></label>
                    <input v-model="form.salary_min" type="number" min="0" placeholder="60000" :class="[field, 'font-mono']" />
                    <InputError class="mt-1" :message="form.errors.salary_min" />
                </div>
                <div>
                    <label :class="label">Maximum salary</label>
                    <input v-model="form.salary_max" type="number" min="0" placeholder="90000" :class="[field, 'font-mono']" />
                    <InputError class="mt-1" :message="form.errors.salary_max" />
                </div>
            </div>
            <div class="mt-5">
                <label :class="label">Display salary text <span class="font-normal text-muted">(optional)</span></label>
                <input v-model="form.salary_range" type="text" placeholder="e.g. $60k – $90k + equity" :class="field" />
                <InputError class="mt-1" :message="form.errors.salary_range" />
            </div>
        </section>

        <div class="accent-rule" />

        <!-- Details -->
        <section class="space-y-5">
            <div>
                <label :class="label">Description</label>
                <textarea v-model="form.description" rows="7" placeholder="Describe the role, responsibilities, and requirements (min. 20 characters)…" :class="field" />
                <InputError class="mt-1" :message="form.errors.description" />
            </div>

            <div>
                <label :class="label">Required skills</label>
                <SkillsInput v-model="form.skills" class="mt-1.5" :suggestions="['Vue', 'React', 'Laravel', 'TypeScript', 'Figma', 'SQL']" />
                <InputError class="mt-1" :message="form.errors.skills" />
            </div>

            <div>
                <label :class="label">Application deadline <span class="font-normal text-muted">(optional)</span></label>
                <input v-model="form.application_deadline" type="date" :class="field" />
                <InputError class="mt-1" :message="form.errors.application_deadline" />
            </div>
        </section>

        <div class="accent-rule" />

        <!-- Flags -->
        <section class="flex flex-col gap-3 sm:flex-row sm:gap-6">
            <label class="flex items-center gap-2.5">
                <input v-model="form.remote" type="checkbox" class="rounded border-hairline text-clay focus:ring-clay" />
                <span class="text-sm text-ink">Remote-friendly</span>
            </label>
            <label class="flex items-center gap-2.5">
                <input v-model="form.is_featured" type="checkbox" class="rounded border-hairline text-clay focus:ring-clay" />
                <span class="text-sm text-ink">Feature this listing</span>
            </label>
            <label class="flex items-center gap-2.5">
                <input v-model="form.is_open" type="checkbox" class="rounded border-hairline text-clay focus:ring-clay" />
                <span class="text-sm text-ink">Open for applications</span>
            </label>
        </section>

        <div class="flex flex-wrap items-center gap-4 border-t border-hairline pt-5">
            <AppButton variant="primary" type="submit" :disabled="form.processing">{{ submitLabel }}</AppButton>
            <slot name="actions" />
        </div>
    </form>
</template>
