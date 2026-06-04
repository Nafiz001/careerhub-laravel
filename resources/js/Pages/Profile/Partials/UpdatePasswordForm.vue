<script setup>
import { ref } from 'vue';
import { useForm } from '@inertiajs/vue3';
import InputError from '@/Components/InputError.vue';
import AppButton from '@/Components/AppButton.vue';
import Icon from '@/Components/Icon.vue';

const passwordInput = ref(null);
const currentPasswordInput = ref(null);

const form = useForm({
    current_password: '',
    password: '',
    password_confirmation: '',
});

const updatePassword = () => {
    form.put(route('password.update'), {
        preserveScroll: true,
        onSuccess: () => form.reset(),
        onError: () => {
            if (form.errors.password) {
                form.reset('password', 'password_confirmation');
                passwordInput.value?.focus();
            }
            if (form.errors.current_password) {
                form.reset('current_password');
                currentPasswordInput.value?.focus();
            }
        },
    });
};

const field =
    'mt-1.5 w-full rounded-input border-hairline bg-surface text-sm text-ink focus:border-clay focus:ring-clay';
const label = 'block text-sm font-medium text-ink';
</script>

<template>
    <section>
        <header>
            <h2 class="font-serif text-lg font-medium text-ink">Update password</h2>
            <p class="mt-1 text-sm text-muted">Use a long, random password to keep your account secure.</p>
        </header>

        <form class="mt-6 max-w-lg space-y-5" @submit.prevent="updatePassword">
            <div>
                <label :class="label">Current password</label>
                <input ref="currentPasswordInput" v-model="form.current_password" type="password" :class="field" autocomplete="current-password" />
                <InputError :message="form.errors.current_password" class="mt-1" />
            </div>
            <div>
                <label :class="label">New password</label>
                <input ref="passwordInput" v-model="form.password" type="password" :class="field" autocomplete="new-password" />
                <InputError :message="form.errors.password" class="mt-1" />
            </div>
            <div>
                <label :class="label">Confirm password</label>
                <input v-model="form.password_confirmation" type="password" :class="field" autocomplete="new-password" />
                <InputError :message="form.errors.password_confirmation" class="mt-1" />
            </div>

            <div class="flex items-center gap-4">
                <AppButton variant="primary" type="submit" :disabled="form.processing">Save password</AppButton>
                <Transition
                    enter-active-class="transition ease-out"
                    enter-from-class="opacity-0"
                    leave-active-class="transition ease-in"
                    leave-to-class="opacity-0"
                >
                    <p v-if="form.recentlySuccessful" class="flex items-center gap-1.5 text-sm text-positive">
                        <Icon name="check" :size="15" /> Saved
                    </p>
                </Transition>
            </div>
        </form>
    </section>
</template>
