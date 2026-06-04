<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import AppButton from '@/Components/AppButton.vue';

const form = useForm({ password: '' });

const submit = () => {
    form.post(route('password.confirm'), {
        onFinish: () => form.reset(),
    });
};

const field =
    'mt-1.5 w-full rounded-input border-hairline bg-surface text-sm text-ink focus:border-clay focus:ring-clay';
</script>

<template>
    <GuestLayout title="Confirm your password" subtitle="This is a secure area — please confirm your password to continue.">
        <Head title="Confirm Password" />

        <form class="space-y-5" @submit.prevent="submit">
            <div>
                <label class="block text-sm font-medium text-ink">Password</label>
                <input v-model="form.password" type="password" :class="field" required autocomplete="current-password" autofocus />
                <InputError class="mt-1" :message="form.errors.password" />
            </div>
            <AppButton variant="primary" type="submit" block :disabled="form.processing">Confirm</AppButton>
        </form>
    </GuestLayout>
</template>
