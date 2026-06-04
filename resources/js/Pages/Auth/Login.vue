<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import AppButton from '@/Components/AppButton.vue';

defineProps({
    canResetPassword: { type: Boolean },
    status: { type: String },
});

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};

const field =
    'mt-1.5 w-full rounded-input border-hairline bg-surface text-sm text-ink placeholder:text-muted focus:border-clay focus:ring-clay';
const label = 'block text-sm font-medium text-ink';
</script>

<template>
    <GuestLayout title="Welcome back" subtitle="Sign in to continue your job search or manage your listings.">
        <Head title="Log in" />

        <div v-if="status" class="mb-4 rounded-input border border-positive/25 bg-sage-soft px-4 py-2.5 text-sm text-positive">
            {{ status }}
        </div>

        <form class="space-y-5" @submit.prevent="submit">
            <div>
                <label :class="label">Email</label>
                <input v-model="form.email" type="email" :class="field" required autofocus autocomplete="username" placeholder="you@example.com" />
                <InputError class="mt-1" :message="form.errors.email" />
            </div>

            <div>
                <div class="flex items-center justify-between">
                    <label :class="label">Password</label>
                    <Link v-if="canResetPassword" :href="route('password.request')" class="text-xs font-medium text-clay transition hover:text-clay-strong">
                        Forgot password?
                    </Link>
                </div>
                <input v-model="form.password" type="password" :class="field" required autocomplete="current-password" placeholder="••••••••" />
                <InputError class="mt-1" :message="form.errors.password" />
            </div>

            <label class="flex cursor-pointer items-center gap-2">
                <input v-model="form.remember" type="checkbox" class="rounded border-hairline text-clay focus:ring-clay" />
                <span class="text-sm text-muted">Remember me</span>
            </label>

            <AppButton variant="primary" type="submit" block :disabled="form.processing">
                {{ form.processing ? 'Signing in…' : 'Sign in' }}
            </AppButton>

            <p class="text-center text-sm text-muted">
                New to CareerHub?
                <Link :href="route('register')" class="font-medium text-clay transition hover:text-clay-strong">Create an account</Link>
            </p>
        </form>
    </GuestLayout>
</template>
