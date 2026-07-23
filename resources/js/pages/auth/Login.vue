<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import {
    ArrowRight,
    Eye,
    EyeOff,
    LoaderCircle,
    LockKeyhole,
    Mail,
    ShieldCheck,
    Sparkles,
} from 'lucide-vue-next';
import { ref } from 'vue';

import AppAuthLogo from '@/components/AppAuthLogo.vue';

import { store } from '@/routes/login';
import { request } from '@/routes/password';

defineProps<{
    status?: string;
    canResetPassword?: boolean;
}>();

const showPassword = ref(false);

const form = useForm({
    email: '',
    password: '',
    remember: false as boolean,
});

const submit = () => {
    form.post(store().url, {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>

    <Head title="Login" />

    <main
        class="min-h-screen w-full overflow-x-hidden bg-gradient-to-br from-orange-50 via-white to-amber-50 font-sans text-slate-900">
        <section class="flex min-h-screen items-center justify-center px-3 py-6 sm:px-4 sm:py-8">
            <div
                class="grid w-full max-w-md overflow-hidden rounded-2xl border border-orange-100 bg-white shadow-2xl shadow-orange-900/10 sm:rounded-[2rem] lg:max-w-5xl lg:grid-cols-2">
                <!-- Left Panel (hidden on mobile & tablet, shown from lg) -->
                <div
                    class="hidden min-w-0 overflow-hidden bg-gradient-to-br from-orange-500 via-orange-500 to-amber-400 p-8 text-white lg:block">
                    <div class="flex h-full min-h-[520px] flex-col justify-between">
                        <div>
                            <div
                                class="inline-flex items-center gap-2 rounded-full border border-white/25 bg-white/15 px-4 py-2 text-xs font-bold text-white shadow-sm backdrop-blur">
                                <span class="h-2 w-2 rounded-full bg-white"></span>
                                KOMINFIK Platform
                            </div>

                            <h1 class="mt-8 max-w-sm text-3xl font-black leading-tight tracking-tight xl:text-4xl">
                                Masuk ke dashboard digital KOMINFIK.
                            </h1>

                            <p class="mt-5 max-w-sm text-sm leading-7 text-orange-50">
                                Kelola project, anggota, kerjasama, dan layanan komunitas dalam satu sistem modern.
                            </p>
                        </div>

                        <div class="space-y-4">
                            <div class="rounded-3xl border border-white/20 bg-white/15 p-5 shadow-xl backdrop-blur">
                                <div class="flex items-center gap-4">
                                    <div
                                        class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-white text-orange-600">
                                        <ShieldCheck class="h-6 w-6" stroke-width="2.5" />
                                    </div>

                                    <div class="min-w-0">
                                        <h3 class="font-black">Akses Aman</h3>
                                        <p class="mt-1 text-sm leading-6 text-orange-50">
                                            Sistem login dilindungi autentikasi Laravel.
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div class="rounded-3xl border border-white/20 bg-slate-950/15 p-5 shadow-xl backdrop-blur">
                                <div class="flex items-center gap-4">
                                    <div
                                        class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-white text-orange-600">
                                        <Sparkles class="h-6 w-6" stroke-width="2.5" />
                                    </div>

                                    <div class="min-w-0">
                                        <h3 class="font-black">Modern UI</h3>
                                        <p class="mt-1 text-sm leading-6 text-orange-50">
                                            Tampilan responsif, bersih, dan nyaman digunakan.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Form Panel -->
                <div class="flex min-w-0 items-center justify-center p-5 sm:p-8 lg:p-10">
                    <div class="w-full max-w-sm">
                        <div class="mb-6 text-center sm:mb-8">
                            <AppAuthLogo />

                            <h2 class="text-xl font-black tracking-tight text-slate-950 sm:text-2xl lg:text-3xl">
                                Selamat Datang
                            </h2>

                            <p class="mt-2 text-sm leading-6 text-slate-500 sm:mt-3">
                                Silakan login menggunakan email dan password akun kamu.
                            </p>
                        </div>

                        <div v-if="status"
                            class="mb-5 rounded-2xl border border-emerald-100 bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-700 sm:mb-6">
                            {{ status }}
                        </div>

                        <form class="space-y-4 sm:space-y-5" @submit.prevent="submit">
                            <!-- Email -->
                            <div>
                                <label for="email" class="mb-2 block text-sm font-black text-slate-800">
                                    Email
                                </label>

                                <div class="relative">
                                    <Mail
                                        class="pointer-events-none absolute left-4 top-1/2 h-5 w-5 -translate-y-1/2 text-slate-400"
                                        stroke-width="2.3" />

                                    <input id="email" v-model="form.email" type="email" required autofocus
                                        autocomplete="email" placeholder="Masukkan email"
                                        class="h-12 w-full rounded-2xl border border-orange-100 bg-white pl-12 pr-4 text-sm font-semibold text-slate-800 shadow-sm outline-none transition placeholder:text-slate-400 focus:border-orange-300 focus:ring-4 focus:ring-orange-100 sm:h-14" />
                                </div>

                                <p v-if="form.errors.email" class="mt-2 text-sm font-semibold text-red-600">
                                    {{ form.errors.email }}
                                </p>
                            </div>

                            <!-- Password -->
                            <div>
                                <div class="mb-2 flex flex-wrap items-center justify-between gap-x-3 gap-y-1">
                                    <label for="password" class="block text-sm font-black text-slate-800">
                                        Password
                                    </label>

                                    <Link v-if="canResetPassword" :href="request().url"
                                        class="shrink-0 text-xs font-black text-orange-600 transition hover:text-orange-700 hover:underline">
                                        Lupa password?
                                    </Link>
                                </div>

                                <div class="relative">
                                    <LockKeyhole
                                        class="pointer-events-none absolute left-4 top-1/2 h-5 w-5 -translate-y-1/2 text-slate-400"
                                        stroke-width="2.3" />

                                    <input id="password" v-model="form.password"
                                        :type="showPassword ? 'text' : 'password'" required
                                        autocomplete="current-password" placeholder="Masukkan password"
                                        class="h-12 w-full rounded-2xl border border-orange-100 bg-white pl-12 pr-12 text-sm font-semibold text-slate-800 shadow-sm outline-none transition placeholder:text-slate-400 focus:border-orange-300 focus:ring-4 focus:ring-orange-100 sm:h-14" />

                                    <button type="button"
                                        class="absolute right-3 top-1/2 flex h-9 w-9 -translate-y-1/2 items-center justify-center text-slate-400 transition hover:text-orange-600"
                                        @click="showPassword = !showPassword">
                                        <EyeOff v-if="showPassword" class="h-5 w-5" stroke-width="2.3" />
                                        <Eye v-else class="h-5 w-5" stroke-width="2.3" />
                                    </button>
                                </div>

                                <p v-if="form.errors.password" class="mt-2 text-sm font-semibold text-red-600">
                                    {{ form.errors.password }}
                                </p>
                            </div>

                            <!-- Remember -->
                            <label class="flex cursor-pointer items-center gap-3">
                                <input v-model="form.remember" type="checkbox"
                                    class="h-4 w-4 shrink-0 rounded border-orange-200 text-orange-600 focus:ring-orange-500" />

                                <span class="text-sm font-semibold text-slate-600">
                                    Ingat saya
                                </span>
                            </label>

                            <!-- Submit -->
                            <button type="submit" :disabled="form.processing"
                                class="group inline-flex h-12 w-full items-center justify-center gap-2 rounded-2xl bg-gradient-to-r from-orange-500 to-amber-400 px-6 text-sm font-black text-white shadow-xl shadow-orange-500/25 transition duration-300 hover:-translate-y-0.5 hover:shadow-2xl hover:shadow-orange-500/30 disabled:pointer-events-none disabled:opacity-70 sm:h-14">
                                <LoaderCircle v-if="form.processing" class="h-5 w-5 animate-spin" stroke-width="2.5" />

                                <span>
                                    {{ form.processing ? 'Memproses...' : 'Login' }}
                                </span>

                                <ArrowRight v-if="!form.processing"
                                    class="h-5 w-5 transition duration-300 group-hover:translate-x-1"
                                    stroke-width="2.5" />
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </section>
    </main>
</template>
