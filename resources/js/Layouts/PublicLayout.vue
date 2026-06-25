<script setup>
import { ref, computed, onMounted, onBeforeUnmount } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import Icon from '@/Components/Icon.vue';
import Avatar from '@/Components/Avatar.vue';
import NotificationBell from '@/Components/NotificationBell.vue';
import Toaster from '@/Components/Toaster.vue';

const page = usePage();
const user = computed(() => page.props.auth.user);
const isEmployer = computed(() => user.value?.role === 'employer');
const savedCount = computed(() => page.props.savedCount ?? 0);

const mobileOpen = ref(false);
const profileOpen = ref(false);
const profileRoot = ref(null);

const isActive = (name) => route().current(name);

function onClickOutside(e) {
    if (profileRoot.value && !profileRoot.value.contains(e.target)) profileOpen.value = false;
}
onMounted(() => document.addEventListener('click', onClickOutside));
onBeforeUnmount(() => document.removeEventListener('click', onClickOutside));
</script>

<template>
    <div class="flex min-h-screen flex-col bg-canvas">
        <Toaster />

        <header class="sticky top-0 z-40 border-b border-hairline bg-canvas/80 backdrop-blur-md">
            <div class="mx-auto flex h-16 max-w-7xl items-center justify-between gap-4 px-4 sm:px-6 lg:px-8">
                <!-- Wordmark -->
                <Link :href="route('jobs.index')" class="group flex items-center gap-2.5">
                    <span class="flex h-8 w-8 items-center justify-center rounded-[9px] bg-ink text-[#FFFDF8] transition group-hover:bg-clay">
                        <Icon name="briefcase" :size="17" />
                    </span>
                    <span class="font-serif text-xl font-medium tracking-tight text-ink">CareerHub</span>
                </Link>

                <!-- Desktop nav -->
                <nav class="hidden items-center gap-1 md:flex">
                    <Link
                        :href="route('jobs.index')"
                        class="rounded-btn px-3 py-2 text-sm font-medium transition"
                        :class="isActive('jobs.index') || isActive('home') ? 'text-ink' : 'text-muted hover:text-ink'"
                    >
                        Browse jobs
                    </Link>

                    <template v-if="user">
                        <Link
                            :href="route('dashboard')"
                            class="rounded-btn px-3 py-2 text-sm font-medium transition"
                            :class="isActive('dashboard') ? 'text-ink' : 'text-muted hover:text-ink'"
                        >
                            Dashboard
                        </Link>
                        <Link
                            v-if="isEmployer"
                            :href="route('jobs.create')"
                            class="rounded-btn px-3 py-2 text-sm font-medium text-muted transition hover:text-ink"
                        >
                            Post a job
                        </Link>
                        <Link
                            v-else
                            :href="route('applications.index')"
                            class="rounded-btn px-3 py-2 text-sm font-medium transition"
                            :class="isActive('applications.index') ? 'text-ink' : 'text-muted hover:text-ink'"
                        >
                            Applications
                        </Link>
                    </template>
                </nav>

                <!-- Right cluster -->
                <div class="flex items-center gap-1.5">
                    <template v-if="user">
                        <!-- Saved jobs (seekers) -->
                        <Link
                            v-if="!isEmployer"
                            :href="route('saved-jobs.index')"
                            class="relative flex h-9 w-9 items-center justify-center rounded-full text-muted transition hover:bg-surface-2 hover:text-ink"
                            aria-label="Saved jobs"
                        >
                            <Icon name="bookmark" :size="18" />
                            <span
                                v-if="savedCount > 0"
                                class="absolute -right-0.5 -top-0.5 flex h-4 min-w-4 items-center justify-center rounded-full bg-sage px-1 font-mono text-[10px] font-medium leading-none text-[#FFFDF8] ring-2 ring-canvas"
                            >
                                {{ savedCount > 9 ? '9+' : savedCount }}
                            </span>
                        </Link>

                        <NotificationBell />

                        <!-- Profile menu -->
                        <div ref="profileRoot" class="relative ml-1">
                            <button
                                type="button"
                                class="flex items-center gap-2 rounded-full border border-hairline bg-surface py-1 pl-1 pr-2.5 transition hover:bg-surface-2"
                                :aria-expanded="profileOpen"
                                @click="profileOpen = !profileOpen"
                            >
                                <Avatar :name="user.name" :src="user.avatar ? `/storage/${user.avatar}` : (isEmployer && user.logo ? `/storage/${user.logo}` : null)" :size="28" />
                                <span class="hidden max-w-[8rem] truncate text-sm font-medium text-ink lg:block">{{ user.name }}</span>
                                <Icon name="chevron-down" :size="15" class="text-muted" />
                            </button>

                            <Transition
                                enter-active-class="transition duration-150 ease-out"
                                enter-from-class="-translate-y-1 opacity-0"
                                enter-to-class="translate-y-0 opacity-100"
                                leave-active-class="transition duration-100 ease-in"
                                leave-from-class="translate-y-0 opacity-100"
                                leave-to-class="-translate-y-1 opacity-0"
                            >
                                <div
                                    v-if="profileOpen"
                                    class="absolute right-0 z-50 mt-2 w-60 origin-top-right overflow-hidden rounded-card border border-hairline bg-surface shadow-lift"
                                >
                                    <div class="border-b border-hairline px-4 py-3">
                                        <p class="truncate text-sm font-medium text-ink">{{ user.name }}</p>
                                        <p class="truncate text-xs text-muted">{{ user.email }}</p>
                                        <span class="mt-2 inline-flex items-center gap-1 rounded-full bg-clay-soft px-2 py-0.5 text-[11px] font-medium capitalize text-clay-strong">
                                            {{ isEmployer ? 'Employer' : 'Job seeker' }}
                                        </span>
                                    </div>
                                    <div class="py-1">
                                        <Link :href="route('dashboard')" class="flex items-center gap-2.5 px-4 py-2 text-sm text-muted transition hover:bg-surface-2 hover:text-ink" @click="profileOpen = false">
                                            <Icon name="layout-dashboard" :size="16" /> Dashboard
                                        </Link>
                                        <Link :href="route('profile.edit')" class="flex items-center gap-2.5 px-4 py-2 text-sm text-muted transition hover:bg-surface-2 hover:text-ink" @click="profileOpen = false">
                                            <Icon name="user" :size="16" /> Profile
                                        </Link>
                                        <Link v-if="!isEmployer" :href="route('saved-jobs.index')" class="flex items-center gap-2.5 px-4 py-2 text-sm text-muted transition hover:bg-surface-2 hover:text-ink" @click="profileOpen = false">
                                            <Icon name="bookmark" :size="16" /> Saved jobs
                                        </Link>
                                    </div>
                                    <div class="border-t border-hairline py-1">
                                        <Link :href="route('logout')" method="post" as="button" class="flex w-full items-center gap-2.5 px-4 py-2 text-left text-sm text-muted transition hover:bg-surface-2 hover:text-ink">
                                            <Icon name="log-out" :size="16" /> Log out
                                        </Link>
                                    </div>
                                </div>
                            </Transition>
                        </div>
                    </template>

                    <template v-else>
                        <Link :href="route('login')" class="hidden rounded-btn px-3 py-2 text-sm font-medium text-muted transition hover:text-ink sm:block">
                            Log in
                        </Link>
                        <Link :href="route('register')" class="rounded-btn bg-clay px-4 py-2 text-sm font-medium text-[#FFFDF8] shadow-soft transition hover:bg-clay-strong">
                            Sign up
                        </Link>
                    </template>

                    <!-- Mobile toggle -->
                    <button
                        class="ml-1 flex h-9 w-9 items-center justify-center rounded-full text-muted transition hover:bg-surface-2 md:hidden"
                        @click="mobileOpen = !mobileOpen"
                        aria-label="Toggle menu"
                    >
                        <Icon :name="mobileOpen ? 'x' : 'menu'" :size="20" />
                    </button>
                </div>
            </div>

            <!-- Mobile menu -->
            <Transition
                enter-active-class="transition duration-150 ease-out"
                enter-from-class="opacity-0"
                enter-to-class="opacity-100"
                leave-active-class="transition duration-100 ease-in"
                leave-from-class="opacity-100"
                leave-to-class="opacity-0"
            >
                <div v-show="mobileOpen" class="space-y-1 border-t border-hairline bg-surface px-4 py-3 md:hidden">
                    <Link :href="route('jobs.index')" class="block rounded-btn px-3 py-2 text-sm font-medium text-muted hover:bg-surface-2 hover:text-ink">Browse jobs</Link>
                    <template v-if="user">
                        <Link :href="route('dashboard')" class="block rounded-btn px-3 py-2 text-sm font-medium text-muted hover:bg-surface-2 hover:text-ink">Dashboard</Link>
                        <Link v-if="isEmployer" :href="route('jobs.create')" class="block rounded-btn px-3 py-2 text-sm font-medium text-muted hover:bg-surface-2 hover:text-ink">Post a job</Link>
                        <Link v-else :href="route('applications.index')" class="block rounded-btn px-3 py-2 text-sm font-medium text-muted hover:bg-surface-2 hover:text-ink">My applications</Link>
                        <Link v-if="!isEmployer" :href="route('saved-jobs.index')" class="block rounded-btn px-3 py-2 text-sm font-medium text-muted hover:bg-surface-2 hover:text-ink">Saved jobs</Link>
                        <Link :href="route('notifications.index')" class="block rounded-btn px-3 py-2 text-sm font-medium text-muted hover:bg-surface-2 hover:text-ink">Notifications</Link>
                        <Link :href="route('profile.edit')" class="block rounded-btn px-3 py-2 text-sm font-medium text-muted hover:bg-surface-2 hover:text-ink">Profile</Link>
                        <Link :href="route('logout')" method="post" as="button" class="block w-full rounded-btn px-3 py-2 text-left text-sm font-medium text-muted hover:bg-surface-2 hover:text-ink">Log out</Link>
                    </template>
                    <template v-else>
                        <Link :href="route('login')" class="block rounded-btn px-3 py-2 text-sm font-medium text-muted hover:bg-surface-2 hover:text-ink">Log in</Link>
                        <Link :href="route('register')" class="block rounded-btn px-3 py-2 text-sm font-medium text-clay hover:bg-surface-2">Sign up</Link>
                    </template>
                </div>
            </Transition>
        </header>

        <main class="flex-1">
            <slot />
        </main>

        <footer class="mt-20 border-t border-hairline bg-surface">
            <div class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
                <div class="flex flex-col items-center justify-between gap-4 sm:flex-row">
                    <div class="flex items-center gap-2.5">
                        <span class="flex h-7 w-7 items-center justify-center rounded-[8px] bg-ink text-[#FFFDF8]">
                            <Icon name="briefcase" :size="15" />
                        </span>
                        <span class="font-serif text-base font-medium text-ink">CareerHub</span>
                    </div>
                    <p class="text-sm text-muted">
                        A Laravel + Inertia + Vue demo by Md. Nafiz Ahmed.
                    </p>
                </div>
            </div>
        </footer>
    </div>
</template>
