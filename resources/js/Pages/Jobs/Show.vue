<script setup>
import { computed, ref } from 'vue';
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';
import PublicLayout from '@/Layouts/PublicLayout.vue';
import Badge from '@/Components/Badge.vue';
import Icon from '@/Components/Icon.vue';
import Avatar from '@/Components/Avatar.vue';
import InputError from '@/Components/InputError.vue';
import SaveButton from '@/Components/SaveButton.vue';
import AppButton from '@/Components/AppButton.vue';
import AppModal from '@/Components/AppModal.vue';

const props = defineProps({
    job: { type: Object, required: true },
    hasApplied: { type: Boolean, default: false },
    canApply: { type: Boolean, default: false },
    isSaved: { type: Boolean, default: false },
    applicationsCount: { type: Number, default: 0 },
});

const page = usePage();
const user = computed(() => page.props.auth.user);
const isEmployer = computed(() => user.value?.role === 'employer');
const isSeeker = computed(() => user.value?.role === 'seeker');
const ownsJob = computed(() => user.value?.id === props.job.employer_id);

const skills = computed(() => (Array.isArray(props.job.skills) ? props.job.skills : []));
const employer = computed(() => props.job.employer ?? {});
const companyName = computed(() => employer.value.company_name || props.job.company);

const salary = computed(() => {
    const fmt = (n) => '$' + Number(n).toLocaleString();
    const { salary_min: min, salary_max: max, salary_range: text } = props.job;
    if (min && max) return `${fmt(min)} – ${fmt(max)}`;
    if (min) return `From ${fmt(min)}`;
    if (max) return `Up to ${fmt(max)}`;
    return text || null;
});

const deadline = computed(() => {
    if (!props.job.application_deadline) return null;
    return new Date(props.job.application_deadline).toLocaleDateString(undefined, {
        month: 'long', day: 'numeric', year: 'numeric',
    });
});

// Apply modal + multipart form.
const showApply = ref(false);
const resumeName = ref('');
const form = useForm({
    cover_letter: '',
    expected_salary: '',
    resume: null,
});

function onFile(e) {
    const file = e.target.files?.[0] ?? null;
    form.resume = file;
    resumeName.value = file?.name ?? '';
}

function submit() {
    form
        .transform((data) => {
            // Fold expected salary into the cover letter so it reaches the employer,
            // since the backend stores cover_letter + resume only.
            const note = data.expected_salary
                ? `\n\nExpected salary: ${data.expected_salary}`
                : '';
            return {
                cover_letter: (data.cover_letter ?? '') + note,
                resume: data.resume,
            };
        })
        .post(route('applications.store', props.job.id), {
            preserveScroll: true,
            forceFormData: true,
            onSuccess: () => {
                showApply.value = false;
                form.reset();
                resumeName.value = '';
            },
        });
}
</script>

<template>
    <Head :title="job.title" />

    <PublicLayout>
        <div class="mx-auto max-w-5xl px-4 py-8 sm:px-6 lg:px-8">
            <Link :href="route('jobs.index')" class="inline-flex items-center gap-1.5 text-sm font-medium text-muted transition hover:text-ink">
                <Icon name="arrow-left" :size="16" />
                Back to jobs
            </Link>

            <div class="mt-5 grid grid-cols-1 gap-6 lg:grid-cols-[1fr_320px]">
                <!-- Main column -->
                <div class="space-y-6">
                    <!-- Header card -->
                    <div class="relative overflow-hidden rounded-card border border-hairline bg-surface p-6 shadow-soft">
                        <div v-if="job.is_featured" class="absolute inset-x-0 top-0 h-1 bg-gradient-to-r from-clay/0 via-clay to-clay/0" />
                        <div class="flex items-start gap-4">
                            <Avatar :name="companyName" :src="employer.logo ? `/storage/${employer.logo}` : null" :size="56" square />
                            <div class="min-w-0 flex-1">
                                <div class="flex flex-wrap items-center gap-2">
                                    <h1 class="font-serif text-2xl font-medium leading-tight text-ink">{{ job.title }}</h1>
                                    <Badge v-if="!job.is_open" label="closed" variant="closed" />
                                </div>
                                <p class="mt-1 text-base text-muted">{{ companyName }}</p>
                                <div class="mt-3 flex flex-wrap items-center gap-1.5">
                                    <Badge :label="job.type" :variant="job.type" />
                                    <Badge v-if="job.remote && job.type !== 'remote'" label="Remote" variant="remote" />
                                    <Badge v-if="job.experience_level" :label="job.experience_level" variant="gray" />
                                    <Badge v-if="job.category" :label="job.category" variant="gray" />
                                    <Badge v-if="job.is_featured" label="Featured" variant="featured" />
                                </div>
                            </div>
                        </div>

                        <div class="mt-5 grid grid-cols-2 gap-4 border-t border-hairline pt-5 sm:grid-cols-4">
                            <div>
                                <p class="text-xs uppercase tracking-wide text-muted">Location</p>
                                <p class="mt-1 flex items-center gap-1.5 text-sm font-medium text-ink"><Icon name="map-pin" :size="14" class="text-muted" /> {{ job.location }}</p>
                            </div>
                            <div v-if="salary">
                                <p class="text-xs uppercase tracking-wide text-muted">Salary</p>
                                <p class="mt-1 font-mono text-sm font-medium tabular-nums text-ink">{{ salary }}</p>
                            </div>
                            <div>
                                <p class="text-xs uppercase tracking-wide text-muted">Applicants</p>
                                <p class="mt-1 font-mono text-sm font-medium tabular-nums text-ink">{{ applicationsCount }}</p>
                            </div>
                            <div v-if="deadline">
                                <p class="text-xs uppercase tracking-wide text-muted">Closes</p>
                                <p class="mt-1 text-sm font-medium text-ink">{{ deadline }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Description -->
                    <div class="rounded-card border border-hairline bg-surface p-6 shadow-soft">
                        <h2 class="font-serif text-lg font-medium text-ink">About this role</h2>
                        <p class="mt-3 whitespace-pre-line text-sm leading-relaxed text-muted">{{ job.description }}</p>

                        <div v-if="skills.length" class="mt-6 border-t border-hairline pt-5">
                            <h3 class="text-sm font-medium text-ink">Skills</h3>
                            <div class="mt-3 flex flex-wrap gap-1.5">
                                <span v-for="s in skills" :key="s" class="chip">{{ s }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- About company -->
                    <div v-if="employer.about" class="rounded-card border border-hairline bg-surface p-6 shadow-soft">
                        <h2 class="font-serif text-lg font-medium text-ink">About {{ companyName }}</h2>
                        <p class="mt-3 whitespace-pre-line text-sm leading-relaxed text-muted">{{ employer.about }}</p>
                        <a
                            v-if="employer.website"
                            :href="employer.website"
                            target="_blank"
                            rel="noopener"
                            class="mt-4 inline-flex items-center gap-1.5 text-sm font-medium text-clay transition hover:text-clay-strong"
                        >
                            <Icon name="globe" :size="15" /> Visit website
                            <Icon name="external-link" :size="13" />
                        </a>
                    </div>
                </div>

                <!-- Sticky apply rail -->
                <aside class="lg:sticky lg:top-20 lg:self-start">
                    <div class="rounded-card border border-hairline bg-surface p-5 shadow-soft">
                        <!-- Already applied -->
                        <div v-if="hasApplied" class="rounded-input border border-positive/25 bg-sage-soft px-4 py-3 text-sm text-positive">
                            <div class="flex items-center gap-2 font-medium"><Icon name="check-circle" :size="16" /> Application submitted</div>
                            <Link :href="route('applications.index')" class="mt-1 inline-block font-medium text-clay underline-offset-2 hover:underline">Track in My Applications →</Link>
                        </div>

                        <!-- Closed -->
                        <div v-else-if="!job.is_open" class="rounded-input border border-hairline bg-surface-2 px-4 py-3 text-sm text-muted">
                            This position is no longer accepting applications.
                        </div>

                        <!-- Employer -->
                        <div v-else-if="isEmployer">
                            <div v-if="ownsJob" class="space-y-3">
                                <p class="text-sm text-muted">This is your listing.</p>
                                <AppButton :href="route('jobs.applicants', job.id)" variant="primary" block>
                                    <Icon name="users" :size="16" /> View applicants
                                </AppButton>
                                <AppButton :href="route('jobs.edit', job.id)" variant="secondary" block>Edit listing</AppButton>
                            </div>
                            <p v-else class="rounded-input border border-hairline bg-surface-2 px-4 py-3 text-sm text-muted">Employer accounts cannot apply to jobs.</p>
                        </div>

                        <!-- Guest -->
                        <div v-else-if="!user" class="space-y-3">
                            <p class="text-sm text-muted">Sign in as a job seeker to apply for this role.</p>
                            <AppButton :href="route('login')" variant="primary" block>Log in to apply</AppButton>
                            <AppButton :href="route('register')" variant="secondary" block>Create an account</AppButton>
                        </div>

                        <!-- Seeker can apply -->
                        <div v-else-if="canApply" class="space-y-3">
                            <AppButton variant="primary" block @click="showApply = true">
                                <Icon name="check" :size="16" /> Apply now
                            </AppButton>
                            <SaveButton v-if="isSeeker" :job-id="job.id" :saved="isSaved" variant="labelled" class="w-full justify-center" />
                        </div>

                        <!-- Seeker who can't apply for some reason -->
                        <div v-else class="space-y-3">
                            <SaveButton v-if="isSeeker" :job-id="job.id" :saved="isSaved" variant="labelled" class="w-full justify-center" />
                        </div>

                        <p v-if="job.is_open && !hasApplied" class="mt-4 flex items-center gap-1.5 text-xs text-muted">
                            <Icon name="clock" :size="13" /> Typically responds within a week
                        </p>
                    </div>
                </aside>
            </div>
        </div>

        <!-- Apply modal -->
        <AppModal :show="showApply" title="Apply for this role" max-width="xl" @close="showApply = false">
            <template #header>
                <h2 class="font-serif text-lg font-medium text-ink">Apply — {{ job.title }}</h2>
                <p class="mt-0.5 text-sm text-muted">{{ companyName }} · {{ job.location }}</p>
            </template>

            <form class="space-y-5" @submit.prevent="submit">
                <!-- Cover letter -->
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-ink">Cover letter</label>
                    <textarea
                        v-model="form.cover_letter"
                        rows="6"
                        placeholder="Introduce yourself and explain why you're a strong fit (min. 20 characters)…"
                        class="w-full rounded-input border-hairline bg-surface text-sm text-ink placeholder:text-muted focus:border-clay focus:ring-clay"
                    />
                    <InputError class="mt-1" :message="form.errors.cover_letter" />
                </div>

                <!-- Expected salary -->
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-ink">Expected salary <span class="font-normal text-muted">(optional)</span></label>
                    <input
                        v-model="form.expected_salary"
                        type="text"
                        placeholder="e.g. $90,000 / year"
                        class="w-full rounded-input border-hairline bg-surface text-sm text-ink placeholder:text-muted focus:border-clay focus:ring-clay"
                    />
                </div>

                <!-- Resume upload -->
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-ink">Résumé <span class="font-normal text-muted">(PDF, DOC, DOCX — optional)</span></label>
                    <label
                        class="flex cursor-pointer items-center gap-3 rounded-input border border-dashed border-hairline bg-surface-2/50 px-4 py-3.5 text-sm transition hover:border-clay/40"
                    >
                        <span class="flex h-9 w-9 items-center justify-center rounded-full bg-surface text-clay">
                            <Icon name="upload" :size="17" />
                        </span>
                        <span v-if="resumeName" class="min-w-0 flex-1">
                            <span class="block truncate font-medium text-ink">{{ resumeName }}</span>
                            <span class="text-xs text-muted">Click to replace</span>
                        </span>
                        <span v-else class="flex-1 text-muted">Click to upload your résumé</span>
                        <input type="file" accept=".pdf,.doc,.docx" class="sr-only" @change="onFile" />
                    </label>
                    <InputError class="mt-1" :message="form.errors.resume" />
                </div>

                <div class="flex items-center justify-end gap-3 border-t border-hairline pt-4">
                    <AppButton variant="ghost" type="button" @click="showApply = false">Cancel</AppButton>
                    <AppButton variant="primary" type="submit" :disabled="form.processing">
                        {{ form.processing ? 'Submitting…' : 'Submit application' }}
                    </AppButton>
                </div>
            </form>
        </AppModal>
    </PublicLayout>
</template>
