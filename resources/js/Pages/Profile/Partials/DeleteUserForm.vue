<script setup>
import { nextTick, ref } from 'vue';
import { useForm } from '@inertiajs/vue3';
import InputError from '@/Components/InputError.vue';
import AppModal from '@/Components/AppModal.vue';
import AppButton from '@/Components/AppButton.vue';
import Icon from '@/Components/Icon.vue';

const confirming = ref(false);
const passwordInput = ref(null);

const form = useForm({ password: '' });

const confirmDeletion = () => {
    confirming.value = true;
    nextTick(() => passwordInput.value?.focus());
};

const deleteUser = () => {
    form.delete(route('profile.destroy'), {
        preserveScroll: true,
        onSuccess: () => closeModal(),
        onError: () => passwordInput.value?.focus(),
        onFinish: () => form.reset(),
    });
};

const closeModal = () => {
    confirming.value = false;
    form.clearErrors();
    form.reset();
};
</script>

<template>
    <section>
        <header class="flex items-start gap-3">
            <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-danger/10 text-danger">
                <Icon name="alert-circle" :size="18" />
            </span>
            <div>
                <h2 class="font-serif text-lg font-medium text-ink">Delete account</h2>
                <p class="mt-1 text-sm text-muted">
                    Permanently delete your account and all associated data. This cannot be undone.
                </p>
            </div>
        </header>

        <div class="mt-5">
            <AppButton variant="danger" @click="confirmDeletion">Delete account</AppButton>
        </div>

        <AppModal :show="confirming" title="Delete your account?" max-width="md" @close="closeModal">
            <p class="text-sm text-muted">
                Once deleted, all of your data is permanently removed. Enter your password to confirm.
            </p>
            <div class="mt-4">
                <input
                    ref="passwordInput"
                    v-model="form.password"
                    type="password"
                    placeholder="Password"
                    class="w-full rounded-input border-hairline bg-surface text-sm text-ink focus:border-clay focus:ring-clay"
                    @keyup.enter="deleteUser"
                />
                <InputError :message="form.errors.password" class="mt-1" />
            </div>
            <div class="mt-5 flex justify-end gap-3">
                <AppButton variant="ghost" @click="closeModal">Cancel</AppButton>
                <AppButton variant="danger" :disabled="form.processing" @click="deleteUser">Delete account</AppButton>
            </div>
        </AppModal>
    </section>
</template>
