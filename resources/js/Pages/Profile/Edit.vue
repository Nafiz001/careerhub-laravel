<script setup>
import { ref, computed } from 'vue';
import { Head, usePage } from '@inertiajs/vue3';
import PublicLayout from '@/Layouts/PublicLayout.vue';
import Icon from '@/Components/Icon.vue';
import UpdateProfileInformationForm from './Partials/UpdateProfileInformationForm.vue';
import UpdatePasswordForm from './Partials/UpdatePasswordForm.vue';
import DeleteUserForm from './Partials/DeleteUserForm.vue';

const props = defineProps({
    mustVerifyEmail: { type: Boolean },
    status: { type: String },
    profile: { type: Object, default: () => ({}) },
    experienceLevels: { type: Array, default: () => [] },
});

const page = usePage();
const isEmployer = computed(() => page.props.auth.user?.role === 'employer');

const tabs = [
    { key: 'profile', label: 'Profile', icon: 'user' },
    { key: 'security', label: 'Security', icon: 'alert-circle' },
];
const active = ref('profile');
</script>

<template>
    <Head title="Profile" />

    <PublicLayout>
        <div class="mx-auto max-w-4xl px-4 py-8 sm:px-6 lg:px-8">
            <div class="flex items-center gap-3">
                <span class="flex h-10 w-10 items-center justify-center rounded-card bg-clay-soft text-clay">
                    <Icon :name="isEmployer ? 'building' : 'user'" :size="20" />
                </span>
                <div>
                    <h1 class="font-serif text-3xl font-medium tracking-tight text-ink">
                        {{ isEmployer ? 'Company profile' : 'Your profile' }}
                    </h1>
                    <p class="mt-0.5 text-sm text-muted">Manage how you appear across CareerHub.</p>
                </div>
            </div>

            <!-- Tabs -->
            <div class="mt-7 flex gap-1 border-b border-hairline">
                <button
                    v-for="t in tabs"
                    :key="t.key"
                    class="relative -mb-px flex items-center gap-2 border-b-2 px-4 py-2.5 text-sm font-medium transition"
                    :class="active === t.key
                        ? 'border-clay text-ink'
                        : 'border-transparent text-muted hover:text-ink'"
                    @click="active = t.key"
                >
                    <Icon :name="t.icon" :size="16" /> {{ t.label }}
                </button>
            </div>

            <!-- Profile tab -->
            <div v-show="active === 'profile'" class="mt-6">
                <div class="rounded-card border border-hairline bg-surface p-6 shadow-soft sm:p-8">
                    <UpdateProfileInformationForm
                        :must-verify-email="mustVerifyEmail"
                        :status="status"
                        :profile="profile"
                        :experience-levels="experienceLevels"
                        :is-employer="isEmployer"
                    />
                </div>
            </div>

            <!-- Security tab -->
            <div v-show="active === 'security'" class="mt-6 space-y-6">
                <div class="rounded-card border border-hairline bg-surface p-6 shadow-soft sm:p-8">
                    <UpdatePasswordForm />
                </div>
                <div class="rounded-card border border-danger/20 bg-surface p-6 shadow-soft sm:p-8">
                    <DeleteUserForm />
                </div>
            </div>
        </div>
    </PublicLayout>
</template>
