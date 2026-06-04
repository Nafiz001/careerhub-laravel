<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import AppButton from '@/Components/AppButton.vue';

defineProps({
    status: { type: String },
});

const form = useForm({ email: '' });

const submit = () => form.post(route('password.email'));

const field =
    'mt-1.5 w-full rounded-input border-hairline bg-surface text-sm text-ink placeholder:text-muted focus:border-clay focus:ring-clay';
</script>

<template>
    <GuestLayout title="Reset your password" subtitle="Enter your email and we'll send you a reset link.">
        <Head title="Forgot Password" />

        <div v-if="status" class="mb-4 rounded-input border border-positive/25 bg-sage-soft px-4 py-2.5 text-sm text-positive">
            {{ status }}
        </div>

        <form class="space-y-5" @submit.prevent="submit">
            <div>
                <label class="block text-sm font-medium text-ink">Email</label>
                <input v-model="form.email" type="email" :class="field" required autofocus autocomplete="username" placeholder="you@example.com" />
                <InputError class="mt-1" :message="form.errors.email" />
            </div>

            <AppButton variant="primary" type="submit" block :disabled="form.processing">
                Email password reset link
            </AppButton>

            <p class="text-center text-sm text-muted">
                <Link :href="route('login')" class="font-medium text-clay transition hover:text-clay-strong">Back to sign in</Link>
            </p>
        </form>
    </GuestLayout>
</template>
