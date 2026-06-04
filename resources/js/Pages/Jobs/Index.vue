<script setup>
import { reactive, ref, watch, computed } from 'vue';
import { Head, router, usePage } from '@inertiajs/vue3';
import { debounce } from 'lodash';
import PublicLayout from '@/Layouts/PublicLayout.vue';
import JobCard from '@/Components/JobCard.vue';
import Pagination from '@/Components/Pagination.vue';
import EmptyState from '@/Components/EmptyState.vue';
import RangeSlider from '@/Components/RangeSlider.vue';
import Icon from '@/Components/Icon.vue';

const props = defineProps({
    jobs: { type: Object, required: true },
    filters: { type: Object, default: () => ({}) },
    types: { type: Array, default: () => [] },
    categories: { type: Array, default: () => [] },
    experienceLevels: { type: Array, default: () => [] },
    savedJobIds: { type: Array, default: () => [] },
    locations: { type: Array, default: () => [] },
});

const page = usePage();
const user = computed(() => page.props.auth.user);
const canSave = computed(() => user.value?.role === 'seeker');
const savedSet = computed(() => new Set((props.savedJobIds ?? []).map(Number)));

const SALARY_MAX = 400000;

const initialSkills = () => {
    const s = props.filters.skills;
    if (Array.isArray(s)) return s;
    if (typeof s === 'string' && s.trim()) return s.split(',').map((x) => x.trim()).filter(Boolean);
    return [];
};

const form = reactive({
    search: props.filters.search ?? '',
    type: props.filters.type ?? '',
    location: props.filters.location ?? '',
    category: props.filters.category ?? '',
    experience_level: props.filters.experience_level ?? '',
    remote: !!props.filters.remote,
    skills: initialSkills(),
});

const salary = ref([
    props.filters.salary_min ? Number(props.filters.salary_min) : 0,
    props.filters.salary_max ? Number(props.filters.salary_max) : SALARY_MAX,
]);

const mobileFiltersOpen = ref(false);

const activeCount = computed(() => {
    let n = 0;
    if (form.type) n++;
    if (form.location) n++;
    if (form.category) n++;
    if (form.experience_level) n++;
    if (form.remote) n++;
    if (form.skills.length) n++;
    if (salary.value[0] > 0 || salary.value[1] < SALARY_MAX) n++;
    return n;
});

const apply = debounce(() => {
    const query = {};
    if (form.search) query.search = form.search;
    if (form.type) query.type = form.type;
    if (form.location) query.location = form.location;
    if (form.category) query.category = form.category;
    if (form.experience_level) query.experience_level = form.experience_level;
    if (form.remote) query.remote = 1;
    if (form.skills.length) query.skills = form.skills.join(',');
    if (salary.value[0] > 0) query.salary_min = salary.value[0];
    if (salary.value[1] < SALARY_MAX) query.salary_max = salary.value[1];

    router.get(route('jobs.index'), query, {
        preserveState: true,
        preserveScroll: true,
        replace: true,
    });
}, 350);

watch([form, salary], apply, { deep: true });

const reset = () => {
    form.search = '';
    form.type = '';
    form.location = '';
    form.category = '';
    form.experience_level = '';
    form.remote = false;
    form.skills = [];
    salary.value = [0, SALARY_MAX];
};

const fmtK = (n) => (n >= 1000 ? `$${Math.round(n / 1000)}k` : `$${n}`);

// Skill suggestions sourced from the seeded jobs' common stacks.
const skillSuggestions = ['Vue', 'React', 'Laravel', 'PHP', 'Python', 'TypeScript', 'Figma', 'SQL', 'AWS', 'Node.js'];
function toggleSkill(s) {
    const i = form.skills.findIndex((x) => x.toLowerCase() === s.toLowerCase());
    if (i === -1) form.skills = [...form.skills, s];
    else form.skills = form.skills.filter((_, idx) => idx !== i);
}
</script>

<template>
    <Head title="Browse Jobs" />

    <PublicLayout>
        <!-- Editorial hero -->
        <section class="border-b border-hairline bg-surface-2/60">
            <div class="mx-auto max-w-7xl px-4 py-14 sm:px-6 lg:px-8 lg:py-20">
                <div class="max-w-2xl">
                    <span class="inline-flex items-center gap-2 rounded-full border border-hairline bg-surface px-3 py-1 text-xs font-medium text-muted">
                        <span class="h-1.5 w-1.5 rounded-full bg-clay" />
                        {{ jobs.total }} open role{{ jobs.total === 1 ? '' : 's' }} hiring now
                    </span>
                    <h1 class="mt-5 font-serif text-4xl font-medium leading-[1.05] tracking-tight text-ink sm:text-5xl">
                        Work that<br class="hidden sm:block" /> fits your craft.
                    </h1>
                    <p class="mt-4 max-w-lg text-base leading-relaxed text-muted">
                        A curated board of engineering, design, and product roles. Filter by salary, skills, and experience — apply in minutes.
                    </p>
                </div>

                <!-- Prominent search -->
                <div class="mt-8 max-w-2xl">
                    <div class="relative">
                        <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4 text-muted">
                            <Icon name="search" :size="19" />
                        </span>
                        <input
                            v-model="form.search"
                            type="text"
                            placeholder="Search by title, company, or keyword…"
                            class="w-full rounded-card border-hairline bg-surface py-3.5 pl-12 pr-4 text-base text-ink shadow-soft placeholder:text-muted focus:border-clay focus:ring-clay"
                        />
                    </div>
                </div>
            </div>
        </section>

        <div class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
            <!-- Mobile filter toggle -->
            <div class="mb-4 flex items-center justify-between lg:hidden">
                <button
                    class="inline-flex items-center gap-2 rounded-btn border border-hairline bg-surface px-3.5 py-2 text-sm font-medium text-ink shadow-soft"
                    @click="mobileFiltersOpen = !mobileFiltersOpen"
                >
                    <Icon name="sliders-horizontal" :size="16" />
                    Filters
                    <span v-if="activeCount" class="ml-1 flex h-5 w-5 items-center justify-center rounded-full bg-clay font-mono text-xs text-[#FFFDF8]">{{ activeCount }}</span>
                </button>
                <p class="font-mono text-sm tabular-nums text-muted">{{ jobs.total }} results</p>
            </div>

            <div class="grid grid-cols-1 gap-8 lg:grid-cols-[280px_1fr]">
                <!-- Filter sidebar -->
                <aside :class="mobileFiltersOpen ? 'block' : 'hidden lg:block'">
                    <div class="sticky top-20 space-y-6 rounded-card border border-hairline bg-surface p-5 shadow-soft">
                        <div class="flex items-center justify-between">
                            <h2 class="font-serif text-lg font-medium text-ink">Filters</h2>
                            <button
                                v-if="activeCount"
                                class="text-xs font-medium text-clay transition hover:text-clay-strong"
                                @click="reset"
                            >
                                Clear all
                            </button>
                        </div>

                        <!-- Salary range -->
                        <div>
                            <div class="mb-3 flex items-center justify-between">
                                <label class="text-sm font-medium text-ink">Salary range</label>
                                <span class="font-mono text-xs tabular-nums text-muted">
                                    {{ fmtK(salary[0]) }} – {{ salary[1] >= SALARY_MAX ? `${fmtK(SALARY_MAX)}+` : fmtK(salary[1]) }}
                                </span>
                            </div>
                            <RangeSlider v-model="salary" :min="0" :max="SALARY_MAX" :step="5000" />
                        </div>

                        <div class="accent-rule" />

                        <!-- Experience level -->
                        <div>
                            <label class="mb-2.5 block text-sm font-medium text-ink">Experience level</label>
                            <div class="flex flex-wrap gap-1.5">
                                <button
                                    v-for="lvl in experienceLevels"
                                    :key="lvl"
                                    type="button"
                                    class="rounded-full border px-3 py-1 text-xs font-medium capitalize transition"
                                    :class="form.experience_level === lvl
                                        ? 'border-clay bg-clay-soft text-clay-strong'
                                        : 'border-hairline bg-surface text-muted hover:border-clay/40 hover:text-ink'"
                                    @click="form.experience_level = form.experience_level === lvl ? '' : lvl"
                                >
                                    {{ lvl }}
                                </button>
                            </div>
                        </div>

                        <div class="accent-rule" />

                        <!-- Type -->
                        <div>
                            <label class="mb-2 block text-sm font-medium text-ink">Employment type</label>
                            <select v-model="form.type" class="w-full rounded-input border-hairline bg-surface text-sm capitalize text-ink focus:border-clay focus:ring-clay">
                                <option value="">Any type</option>
                                <option v-for="t in types" :key="t" :value="t" class="capitalize">{{ t }}</option>
                            </select>
                        </div>

                        <!-- Category -->
                        <div>
                            <label class="mb-2 block text-sm font-medium text-ink">Category</label>
                            <select v-model="form.category" class="w-full rounded-input border-hairline bg-surface text-sm text-ink focus:border-clay focus:ring-clay">
                                <option value="">All categories</option>
                                <option v-for="c in categories" :key="c" :value="c">{{ c }}</option>
                            </select>
                        </div>

                        <!-- Location -->
                        <div>
                            <label class="mb-2 block text-sm font-medium text-ink">Location</label>
                            <select v-model="form.location" class="w-full rounded-input border-hairline bg-surface text-sm text-ink focus:border-clay focus:ring-clay">
                                <option value="">All locations</option>
                                <option v-for="loc in locations" :key="loc" :value="loc">{{ loc }}</option>
                            </select>
                        </div>

                        <div class="accent-rule" />

                        <!-- Remote toggle -->
                        <label class="flex cursor-pointer items-center justify-between">
                            <span class="text-sm font-medium text-ink">Remote only</span>
                            <span
                                class="relative inline-flex h-6 w-11 items-center rounded-full transition"
                                :class="form.remote ? 'bg-clay' : 'bg-hairline'"
                                @click.prevent="form.remote = !form.remote"
                            >
                                <span class="inline-block h-4 w-4 transform rounded-full bg-surface shadow-soft transition" :class="form.remote ? 'translate-x-6' : 'translate-x-1'" />
                            </span>
                        </label>

                        <div class="accent-rule" />

                        <!-- Skills chips -->
                        <div>
                            <label class="mb-2.5 block text-sm font-medium text-ink">Skills</label>
                            <div class="flex flex-wrap gap-1.5">
                                <button
                                    v-for="s in skillSuggestions"
                                    :key="s"
                                    type="button"
                                    class="rounded-full border px-3 py-1 text-xs font-medium transition"
                                    :class="form.skills.some((x) => x.toLowerCase() === s.toLowerCase())
                                        ? 'border-sage bg-sage-soft text-[#465038]'
                                        : 'border-hairline bg-surface text-muted hover:border-sage/50 hover:text-ink'"
                                    @click="toggleSkill(s)"
                                >
                                    {{ s }}
                                </button>
                            </div>
                        </div>
                    </div>
                </aside>

                <!-- Results -->
                <div>
                    <div class="mb-4 hidden items-center justify-between lg:flex">
                        <p class="text-sm text-muted">
                            Showing <span class="font-mono tabular-nums text-ink">{{ jobs.data.length }}</span> of
                            <span class="font-mono tabular-nums text-ink">{{ jobs.total }}</span> roles
                        </p>
                    </div>

                    <div v-if="jobs.data.length" class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-2">
                        <JobCard
                            v-for="job in jobs.data"
                            :key="job.id"
                            :job="job"
                            :can-save="canSave"
                            :saved="savedSet.has(Number(job.id))"
                            class="animate-fade-up"
                        />
                    </div>

                    <div v-else class="rounded-card border border-dashed border-hairline bg-surface">
                        <EmptyState
                            icon="search"
                            title="No roles match those filters"
                            description="Try widening your salary range, removing a skill, or clearing filters to see more."
                        >
                            <template #action>
                                <button class="rounded-btn bg-clay px-4 py-2.5 text-sm font-medium text-[#FFFDF8] transition hover:bg-clay-strong" @click="reset">
                                    Clear all filters
                                </button>
                            </template>
                        </EmptyState>
                    </div>

                    <div v-if="jobs.data.length" class="mt-8">
                        <Pagination :links="jobs.links" />
                    </div>
                </div>
            </div>
        </div>
    </PublicLayout>
</template>
