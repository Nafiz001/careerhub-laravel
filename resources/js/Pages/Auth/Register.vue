<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import AppButton from '@/Components/AppButton.vue';
import Icon from '@/Components/Icon.vue';

defineProps({
    roles: { type: Array, default: () => ['seeker', 'employer'] },
});

const form = useForm({
    name: '',
    email: '',
    role: 'seeker',
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};

const roleMeta = {
    seeker: { label: 'Job seeker', icon: 'user', blurb: 'Browse and apply to roles.' },
    employer: { label: 'Employer', icon: 'building', blurb: 'Post jobs and review applicants.' },
};

const field =
    'mt-1.5 w-full rounded-input border-hairline bg-surface text-sm text-ink placeholder:text-muted focus:border-clay focus:ring-clay';
const label = 'block text-sm font-medium text-ink';
</script>

<template>
    <GuestLayout title="Create your account" subtitle="Join CareerHub in under a minute.">
        <Head title="Register" />

        <form class="space-y-5" @submit.prevent="submit">
            <!-- Role toggle -->
            <div>
                <label :class="label">I'm joining as a…</label>
                <div class="mt-2 grid grid-cols-2 gap-3">
                    <button
                        v-for="r in roles"
                        :key="r"
                        type="button"
                        class="flex flex-col items-start gap-1.5 rounded-card border p-3.5 text-left transition"
                        :class="form.role === r
                            ? 'border-clay bg-clay-soft/60 ring-1 ring-clay'
                            : 'border-hairline bg-surface hover:border-clay/40'"
                        @click="form.role = r"
                    >
                        <span
                            class="flex h-8 w-8 items-center justify-center rounded-full"
                            :class="form.role === r ? 'bg-clay text-[#FFFDF8]' : 'bg-surface-2 text-muted'"
                        >
                            <Icon :name="roleMeta[r].icon" :size="16" />
                        </span>
                        <span class="text-sm font-medium text-ink">{{ roleMeta[r].label }}</span>
                        <span class="text-xs text-muted">{{ roleMeta[r].blurb }}</span>
                    </button>
                </div>
                <InputError class="mt-1" :message="form.errors.role" />
            </div>

            <div>
                <label :class="label">Full name</label>
                <input v-model="form.name" type="text" :class="field" required autofocus autocomplete="name" />
                <InputError class="mt-1" :message="form.errors.name" />
            </div>

            <div>
                <label :class="label">Email</label>
                <input v-model="form.email" type="email" :class="field" required autocomplete="username" placeholder="you@example.com" />
                <InputError class="mt-1" :message="form.errors.email" />
            </div>

            <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                <div>
                    <label :class="label">Password</label>
                    <input v-model="form.password" type="password" :class="field" required autocomplete="new-password" />
                    <InputError class="mt-1" :message="form.errors.password" />
                </div>
                <div>
                    <label :class="label">Confirm</label>
                    <input v-model="form.password_confirmation" type="password" :class="field" required autocomplete="new-password" />
                    <InputError class="mt-1" :message="form.errors.password_confirmation" />
                </div>
            </div>

            <AppButton variant="primary" type="submit" block :disabled="form.processing">
                {{ form.processing ? 'Creating account…' : 'Create account' }}
            </AppButton>

            <p class="text-center text-sm text-muted">
                Already have an account?
                <Link :href="route('login')" class="font-medium text-clay transition hover:text-clay-strong">Sign in</Link>
            </p>
        </form>
    </GuestLayout>
</template>
