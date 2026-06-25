<script setup>
import { ref, computed } from 'vue';
import { Link, useForm } from '@inertiajs/vue3';
import InputError from '@/Components/InputError.vue';
import SkillsInput from '@/Components/SkillsInput.vue';
import Avatar from '@/Components/Avatar.vue';
import AppButton from '@/Components/AppButton.vue';
import Icon from '@/Components/Icon.vue';

const props = defineProps({
    mustVerifyEmail: { type: Boolean },
    status: { type: String },
    profile: { type: Object, default: () => ({}) },
    experienceLevels: { type: Array, default: () => [] },
    isEmployer: { type: Boolean, default: false },
});

const p = props.profile;

const form = useForm({
    _method: 'patch',
    name: p.name ?? '',
    email: p.email ?? '',
    location: p.location ?? '',
    phone: p.phone ?? '',
    // seeker
    headline: p.headline ?? '',
    bio: p.bio ?? '',
    skills: Array.isArray(p.skills) ? p.skills : [],
    experience_level: p.experience_level ?? '',
    // employer
    company_name: p.company_name ?? '',
    website: p.website ?? '',
    about: p.about ?? '',
    // files
    avatar: null,
    logo: null,
});

// Live previews for image uploads.
const avatarPreview = ref(p.avatar_url ?? null);
const logoPreview = ref(p.logo_url ?? null);

function onAvatar(e) {
    const file = e.target.files?.[0];
    if (!file) return;
    form.avatar = file;
    avatarPreview.value = URL.createObjectURL(file);
}
function onLogo(e) {
    const file = e.target.files?.[0];
    if (!file) return;
    form.logo = file;
    logoPreview.value = URL.createObjectURL(file);
}

const submit = () => {
    // POST with _method=patch so file uploads work as multipart.
    form.post(route('profile.update'), {
        preserveScroll: true,
        forceFormData: true,
    });
};

const field =
    'mt-1.5 w-full rounded-input border-hairline bg-surface text-sm text-ink placeholder:text-muted focus:border-clay focus:ring-clay';
const label = 'block text-sm font-medium text-ink';
</script>

<template>
    <form class="space-y-7" @submit.prevent="submit">
        <!-- Identity / avatar row -->
        <section class="flex flex-col gap-5 sm:flex-row sm:items-center">
            <div class="relative">
                <Avatar
                    :name="form.name"
                    :src="isEmployer ? logoPreview : avatarPreview"
                    :size="80"
                    :square="isEmployer"
                />
                <label
                    class="absolute -bottom-1 -right-1 flex h-8 w-8 cursor-pointer items-center justify-center rounded-full border border-hairline bg-surface text-clay shadow-soft transition hover:bg-surface-2"
                    :title="isEmployer ? 'Upload logo' : 'Upload avatar'"
                >
                    <Icon name="upload" :size="15" />
                    <input v-if="isEmployer" type="file" accept="image/*" class="sr-only" @change="onLogo" />
                    <input v-else type="file" accept="image/*" class="sr-only" @change="onAvatar" />
                </label>
            </div>
            <div>
                <p class="font-serif text-lg font-medium text-ink">{{ isEmployer ? 'Company logo' : 'Profile photo' }}</p>
                <p class="mt-0.5 text-sm text-muted">PNG, JPG up to 2 MB. {{ isEmployer ? 'Shown on your listings.' : 'Shown to employers.' }}</p>
                <InputError class="mt-1" :message="isEmployer ? form.errors.logo : form.errors.avatar" />
            </div>
        </section>

        <div class="accent-rule" />

        <!-- Account basics -->
        <section class="grid grid-cols-1 gap-5 sm:grid-cols-2">
            <div>
                <label :class="label">Full name</label>
                <input v-model="form.name" type="text" :class="field" autocomplete="name" />
                <InputError class="mt-1" :message="form.errors.name" />
            </div>
            <div>
                <label :class="label">Email</label>
                <input v-model="form.email" type="email" :class="field" autocomplete="username" />
                <InputError class="mt-1" :message="form.errors.email" />
            </div>
            <div>
                <label :class="label">Location</label>
                <input v-model="form.location" type="text" placeholder="e.g. Dhaka, Bangladesh" :class="field" />
                <InputError class="mt-1" :message="form.errors.location" />
            </div>
            <div>
                <label :class="label">Phone <span class="font-normal text-muted">(optional)</span></label>
                <input v-model="form.phone" type="text" :class="field" />
                <InputError class="mt-1" :message="form.errors.phone" />
            </div>
        </section>

        <div v-if="mustVerifyEmail && profile.email_verified_at === null" class="rounded-input border border-caution/25 bg-[#F4ECDB] px-4 py-3 text-sm text-caution">
            Your email address is unverified.
            <Link :href="route('verification.send')" method="post" as="button" class="font-medium underline">Re-send verification email.</Link>
            <span v-show="status === 'verification-link-sent'" class="mt-1 block font-medium text-positive">A new verification link has been sent.</span>
        </div>

        <div class="accent-rule" />

        <!-- Seeker section -->
        <section v-if="!isEmployer" class="space-y-5">
            <h3 class="font-serif text-base font-medium text-ink">Professional details</h3>
            <div>
                <label :class="label">Headline</label>
                <input v-model="form.headline" type="text" placeholder="e.g. Senior Frontend Engineer · Vue & TypeScript" :class="field" />
                <InputError class="mt-1" :message="form.errors.headline" />
            </div>
            <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                <div>
                    <label :class="label">Experience level</label>
                    <select v-model="form.experience_level" :class="[field, 'capitalize']">
                        <option value="">Select level</option>
                        <option v-for="l in experienceLevels" :key="l" :value="l" class="capitalize">{{ l }}</option>
                    </select>
                    <InputError class="mt-1" :message="form.errors.experience_level" />
                </div>
            </div>
            <div>
                <label :class="label">Skills</label>
                <SkillsInput v-model="form.skills" class="mt-1.5" :suggestions="['Vue', 'React', 'Laravel', 'TypeScript', 'Python', 'Figma']" />
                <InputError class="mt-1" :message="form.errors.skills" />
            </div>
            <div>
                <label :class="label">Bio</label>
                <textarea v-model="form.bio" rows="5" placeholder="A short summary of your experience and what you're looking for…" :class="field" />
                <InputError class="mt-1" :message="form.errors.bio" />
            </div>
        </section>

        <!-- Employer section -->
        <section v-else class="space-y-5">
            <h3 class="font-serif text-base font-medium text-ink">Company details</h3>
            <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                <div>
                    <label :class="label">Company name</label>
                    <input v-model="form.company_name" type="text" :class="field" />
                    <InputError class="mt-1" :message="form.errors.company_name" />
                </div>
                <div>
                    <label :class="label">Website</label>
                    <input v-model="form.website" type="url" placeholder="https://example.com" :class="field" />
                    <InputError class="mt-1" :message="form.errors.website" />
                </div>
            </div>
            <div>
                <label :class="label">About the company</label>
                <textarea v-model="form.about" rows="5" placeholder="Tell candidates about your company, mission, and culture…" :class="field" />
                <InputError class="mt-1" :message="form.errors.about" />
            </div>
        </section>

        <div class="flex items-center gap-4 border-t border-hairline pt-5">
            <AppButton variant="primary" type="submit" :disabled="form.processing">Save changes</AppButton>
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
</template>
