<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import AppButton from '@/Components/AppButton.vue';

const props = defineProps({
    email: { type: String, required: true },
    token: { type: String, required: true },
});

const form = useForm({
    token: props.token,
    email: props.email,
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post(route('password.store'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};

const field =
    'mt-1.5 w-full rounded-input border-hairline bg-surface text-sm text-ink focus:border-clay focus:ring-clay';
const label = 'block text-sm font-medium text-ink';
</script>

<template>
    <GuestLayout title="Choose a new password" subtitle="Set a fresh password for your account.">
        <Head title="Reset Password" />

        <form class="space-y-5" @submit.prevent="submit">
            <div>
                <label :class="label">Email</label>
                <input v-model="form.email" type="email" :class="field" required autofocus autocomplete="username" />
                <InputError class="mt-1" :message="form.errors.email" />
            </div>
            <div>
                <label :class="label">New password</label>
                <input v-model="form.password" type="password" :class="field" required autocomplete="new-password" />
                <InputError class="mt-1" :message="form.errors.password" />
            </div>
            <div>
                <label :class="label">Confirm password</label>
                <input v-model="form.password_confirmation" type="password" :class="field" required autocomplete="new-password" />
                <InputError class="mt-1" :message="form.errors.password_confirmation" />
            </div>

            <AppButton variant="primary" type="submit" block :disabled="form.processing">Reset password</AppButton>
        </form>
    </GuestLayout>
</template>
