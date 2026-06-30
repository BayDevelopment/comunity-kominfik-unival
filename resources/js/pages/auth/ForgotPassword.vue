<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import {
    ArrowLeft,
    ArrowRight,
    LoaderCircle,
    Mail,
    ShieldCheck,
    Sparkles,
} from 'lucide-vue-next';

defineProps<{
    status?: string;
}>();

const form = useForm({
    email: '',
});

const submit = () => {
    form.post('/forgot-password', {
        onFinish: () => form.reset('email'),
    });
};
</script>

<template>

    <Head title="Lupa Password" />

    <main
        class="min-h-screen w-full overflow-x-hidden bg-gradient-to-br from-orange-50 via-white to-amber-50 font-sans text-slate-900">
        <section class="flex min-h-screen items-center justify-center px-3 py-6 sm:px-4 sm:py-8">
            <div
                class="grid w-full max-w-md overflow-hidden rounded-2xl border border-orange-100 bg-white shadow-2xl shadow-orange-900/10 sm:rounded-[2rem] lg:max-w-5xl lg:grid-cols-2">
                <!-- Left Panel -->
                <div
                    class="hidden min-w-0 overflow-hidden bg-gradient-to-br from-orange-500 via-orange-500 to-amber-400 p-8 text-white lg:block">
                    <div class="flex h-full min-h-[520px] flex-col justify-between">
                        <div>
                            <div
                                class="inline-flex items-center gap-2 rounded-full border border-white/25 bg-white/15 px-4 py-2 text-xs font-bold text-white shadow-sm backdrop-blur">
                                <span class="h-2 w-2 rounded-full bg-white"></span>
                                KOMINFIK Security
                            </div>

                            <h1 class="mt-8 max-w-sm text-3xl font-black leading-tight tracking-tight xl:text-4xl">
                                Pulihkan akses akun KOMINFIK kamu.
                            </h1>

                            <p class="mt-5 max-w-sm text-sm leading-7 text-orange-50">
                                Masukkan email akun kamu, lalu sistem akan mengirimkan link untuk reset password.
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
                                        <h3 class="font-black">Reset Aman</h3>
                                        <p class="mt-1 text-sm leading-6 text-orange-50">
                                            Link reset hanya dikirim ke email akun yang terdaftar.
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
                                        <h3 class="font-black">Akses Cepat</h3>
                                        <p class="mt-1 text-sm leading-6 text-orange-50">
                                            Setelah menerima email, kamu bisa membuat password baru.
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
                            <div
                                class="mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-2xl bg-gradient-to-br from-orange-500 to-amber-400 text-white shadow-xl shadow-orange-500/25 sm:mb-5 sm:h-16 sm:w-16 sm:rounded-3xl">
                                <Mail class="h-7 w-7 sm:h-8 sm:w-8" stroke-width="2.5" />
                            </div>

                            <h2 class="text-xl font-black tracking-tight text-slate-950 sm:text-2xl lg:text-3xl">
                                Lupa Password
                            </h2>

                            <p class="mt-2 text-sm leading-6 text-slate-500 sm:mt-3">
                                Masukkan email akun kamu untuk menerima link reset password.
                            </p>
                        </div>

                        <!-- Status -->
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
                                        autocomplete="email" placeholder="email@example.com"
                                        class="h-12 w-full rounded-2xl border border-orange-100 bg-white pl-12 pr-4 text-sm font-semibold text-slate-800 shadow-sm outline-none transition placeholder:text-slate-400 focus:border-orange-300 focus:ring-4 focus:ring-orange-100 sm:h-14" />
                                </div>

                                <p v-if="form.errors.email" class="mt-2 text-sm font-semibold text-red-600">
                                    {{ form.errors.email }}
                                </p>
                            </div>

                            <!-- Submit -->
                            <button type="submit" :disabled="form.processing"
                                class="group inline-flex h-12 w-full items-center justify-center gap-2 rounded-2xl bg-gradient-to-r from-orange-500 to-amber-400 px-6 text-sm font-black text-white shadow-xl shadow-orange-500/25 transition duration-300 hover:-translate-y-0.5 hover:shadow-2xl hover:shadow-orange-500/30 disabled:pointer-events-none disabled:opacity-70 sm:h-14">
                                <LoaderCircle v-if="form.processing" class="h-5 w-5 animate-spin" stroke-width="2.5" />

                                <span>
                                    {{ form.processing ? 'Mengirim...' : 'Kirim Link Reset Password' }}
                                </span>

                                <ArrowRight v-if="!form.processing"
                                    class="h-5 w-5 transition duration-300 group-hover:translate-x-1"
                                    stroke-width="2.5" />
                            </button>
                        </form>

                        <!-- Back Login -->
                        <div class="mt-6 text-center sm:mt-8">
                            <Link href="/login"
                                class="inline-flex items-center justify-center gap-2 text-sm font-black text-orange-600 transition hover:text-orange-700 hover:underline">
                                <ArrowLeft class="h-4 w-4" stroke-width="2.5" />
                                Kembali ke Login
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>
</template>
