<script setup>
import { computed } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import AppButton from '@/Components/AppButton.vue';

const props = defineProps({
    status: { type: String },
});

const form = useForm({});
const submit = () => form.post(route('verification.send'));
const verificationLinkSent = computed(() => props.status === 'verification-link-sent');
</script>

<template>
    <GuestLayout title="Verify your email" subtitle="We've sent a verification link to your inbox.">
        <Head title="Email Verification" />

        <p class="text-sm leading-relaxed text-muted">
            Thanks for signing up! Click the link in the email we just sent to verify your address. Didn't get it? We'll gladly send another.
        </p>

        <div v-if="verificationLinkSent" class="mt-4 rounded-input border border-positive/25 bg-sage-soft px-4 py-2.5 text-sm text-positive">
            A new verification link has been sent to your email address.
        </div>

        <form class="mt-6 flex items-center justify-between" @submit.prevent="submit">
            <AppButton variant="primary" type="submit" :disabled="form.processing">Resend verification email</AppButton>
            <Link :href="route('logout')" method="post" as="button" class="text-sm font-medium text-muted transition hover:text-ink">Log out</Link>
        </form>
    </GuestLayout>
</template>
