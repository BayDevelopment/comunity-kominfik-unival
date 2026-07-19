<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import logoSrc from '@/images/logo-kominfik.png';

const page = usePage();
const mobileOpen = ref(false);

const user = computed(() => {
    const auth = page.props.auth as { user?: { name: string; email?: string } | null } | undefined;

    return auth?.user ?? null;
});

const navItems = [
    { label: 'Beranda', id: 'beranda' },
    { label: 'Project', id: 'project' },
    { label: 'Anggota', id: 'anggota' },
    { label: 'Layanan', id: 'layanan' },
    { label: 'Kerjasama', id: 'kerjasama' },
];

// Deteksi apakah kita sedang berada di homepage (path "/", tanpa memandang hash)
const isHomePage = computed(() => page.url.split('#')[0] === '/');

// Navigasi ke section:
// - Kalau sudah di homepage -> smooth scroll di tempat (tetap SPA, tanpa reload)
// - Kalau di halaman lain (mis. /join/anggota) -> balik ke homepage dulu + hash,
//   supaya browser otomatis scroll ke section setelah halaman termuat
function goToSection(id: string) {
    mobileOpen.value = false;

    if (isHomePage.value) {
        document.getElementById(id)?.scrollIntoView({ behavior: 'smooth' });
        history.pushState(null, '', `#${id}`);
    } else {
        window.location.href = `/#${id}`;
    }
}
</script>

<template>
    <header
        class="sticky top-0 z-50 border-b border-orange-100/80 bg-white/85 shadow-[0_10px_40px_rgba(251,146,60,0.08)] backdrop-blur-xl"
    >
        <nav class="mx-auto flex max-w-7xl items-center justify-between px-5 py-3.5 lg:px-6">
            <!-- Brand -->
            <a href="#" class="group flex items-center gap-3" @click.prevent="goToSection('beranda')">
                <img :src="logoSrc" alt="Logo KOMINFIK" class="h-11 w-11" />

                <div class="leading-tight">
                    <h1 class="text-lg font-black tracking-tight text-slate-900">
                        KOMINFIK
                    </h1>
                    <p class="text-[11px] font-semibold uppercase tracking-[0.2em] text-orange-400">
                        Creative Tech
                    </p>
                </div>
            </a>

            <!-- Desktop Menu -->
            <div
                class="hidden items-center rounded-2xl border border-orange-100 bg-orange-50/80 p-1 shadow-inner shadow-orange-100/40 md:flex"
            >
                <a
                    v-for="item in navItems"
                    :key="item.id"
                    href="#"
                    class="rounded-xl px-4 py-2 text-sm font-bold text-slate-600 transition duration-300 hover:-translate-y-0.5 hover:bg-white hover:text-orange-600 hover:shadow-sm hover:shadow-orange-200/60"
                    @click.prevent="goToSection(item.id)"
                >
                    {{ item.label }}
                </a>
            </div>

            <!-- Desktop Action -->
            <div class="hidden items-center gap-3 md:flex">
                <template v-if="user">
                    <Link
                        href="/dashboard"
                        class="rounded-2xl border border-orange-100 bg-white px-5 py-2.5 text-sm font-bold text-slate-700 shadow-sm transition duration-300 hover:-translate-y-0.5 hover:border-orange-200 hover:bg-orange-50 hover:text-orange-700 hover:shadow-md hover:shadow-orange-100"
                    >
                        Dashboard
                    </Link>
                </template>

                <template v-else>
                    <Link
                        href="/login"
                        class="rounded-2xl px-4 py-2.5 text-sm font-bold text-slate-600 transition duration-300 hover:bg-orange-50 hover:text-orange-600"
                    >
                        Login
                    </Link>

                    <Link
                        href="/join"
                        class="rounded-2xl bg-gradient-to-r from-orange-500 to-amber-400 px-5 py-2.5 text-sm font-black text-white shadow-lg shadow-orange-500/25 transition duration-300 hover:-translate-y-0.5 hover:shadow-xl hover:shadow-orange-500/30"
                    >
                        Join
                    </Link>
                </template>
            </div>

            <!-- Mobile Button -->
            <button
                type="button"
                class="inline-flex h-11 w-11 items-center justify-center rounded-2xl border border-orange-100 bg-white text-slate-700 shadow-sm transition hover:bg-orange-50 hover:text-orange-600 md:hidden"
                :aria-expanded="mobileOpen"
                aria-label="Toggle navigation menu"
                @click="mobileOpen = !mobileOpen"
            >
                <svg
                    v-if="!mobileOpen"
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-5 w-5"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                    stroke-width="2"
                >
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 7h16M4 12h16M4 17h16" />
                </svg>

                <svg
                    v-else
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-5 w-5"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                    stroke-width="2"
                >
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                </svg>
            </button>
        </nav>

        <!-- Mobile Menu Smooth -->
        <Transition
            enter-active-class="transition duration-200 ease-out"
            enter-from-class="-translate-y-3 opacity-0"
            enter-to-class="translate-y-0 opacity-100"
            leave-active-class="transition duration-150 ease-in"
            leave-from-class="translate-y-0 opacity-100"
            leave-to-class="-translate-y-3 opacity-0"
        >
            <div
                v-if="mobileOpen"
                class="border-t border-orange-100 bg-white/95 px-5 py-4 shadow-xl shadow-orange-100/50 backdrop-blur-xl md:hidden"
            >
                <div class="space-y-2">
                    <a
                        v-for="item in navItems"
                        :key="item.id"
                        href="#"
                        class="block rounded-2xl px-4 py-3 text-sm font-bold text-slate-700 transition hover:bg-orange-50 hover:text-orange-700"
                        @click.prevent="goToSection(item.id)"
                    >
                        {{ item.label }}
                    </a>
                </div>

                <div class="mt-4 grid grid-cols-2 gap-3">
                    <template v-if="user">
                        <Link
                            href="/dashboard"
                            class="col-span-2 rounded-2xl border border-orange-100 px-4 py-3 text-center text-sm font-bold text-slate-700 transition hover:bg-orange-50 hover:text-orange-700"
                        >
                            Dashboard
                        </Link>
                    </template>

                    <template v-else>
                        <Link
                            href="/login"
                            class="rounded-2xl border border-orange-100 px-4 py-3 text-center text-sm font-bold text-slate-700 transition hover:bg-orange-50 hover:text-orange-700"
                        >
                            Login
                        </Link>

                        <Link
                            href="/join"
                            class="rounded-2xl bg-gradient-to-r from-orange-500 to-amber-400 px-4 py-3 text-center text-sm font-black text-white shadow-lg shadow-orange-500/25"
                        >
                            Join
                        </Link>
                    </template>
                </div>
            </div>
        </Transition>
    </header>
</template>
