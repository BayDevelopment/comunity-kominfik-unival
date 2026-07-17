<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import logoSrc from '@/images/logo-kominfik.png';

const currentYear = new Date().getFullYear();

const navigationLinks = [
    { label: 'Beranda', href: '#beranda' },
    { label: 'Project', href: '#project' },
    { label: 'Anggota', href: '#anggota' },
    { label: 'Layanan', href: '#layanan' },
    { label: 'Kerjasama', href: '#kerjasama' },
];

const programLinks = [
    'Project Digital',
    'Pengembangan Website',
    'Sistem Informasi',
    'UI/UX Design',
];
</script>

<template>
    <footer class="relative overflow-hidden bg-slate-950 text-white">
        <!-- Background Glow Orange -->
        <div class="absolute -left-24 top-10 h-72 w-72 rounded-full bg-orange-500/20 blur-3xl"></div>
        <div class="absolute -right-24 bottom-0 h-80 w-80 rounded-full bg-amber-400/20 blur-3xl"></div>
        <div
            class="absolute left-1/2 top-0 h-px w-full -translate-x-1/2 bg-gradient-to-r from-transparent via-orange-400/40 to-transparent">
        </div>

        <div class="relative mx-auto max-w-7xl px-6 py-14">
            <div class="grid gap-10 lg:grid-cols-4">
                <!-- Brand -->
                <div class="lg:col-span-2">
                    <a href="#beranda" class="group inline-flex items-center gap-3">
                        <img :src="logoSrc" alt="Logo KOMINFIK" class="h-12 w-12" />

                        <div>
                            <h2 class="text-xl font-black tracking-tight text-white">
                                KOMINFIK
                            </h2>
                            <p class="text-[11px] font-semibold uppercase tracking-[0.22em] text-orange-300">
                                Creative Tech
                            </p>
                        </div>
                    </a>

                    <p class="mt-5 max-w-xl text-sm leading-7 text-slate-400">
                        Komunitas Informatika Kreatif sebagai wadah kolaborasi digital,
                        pengembangan skill, project teknologi, dan kerja sama bersama
                        perusahaan, organisasi, komunitas, maupun universitas.
                    </p>

                    <div class="mt-6 flex flex-wrap gap-3">
                        <Link href="/join"
                            class="rounded-2xl bg-gradient-to-r from-orange-500 to-amber-400 px-5 py-3 text-sm font-black text-white shadow-lg shadow-orange-500/25 transition duration-300 hover:-translate-y-0.5 hover:shadow-xl hover:shadow-orange-500/30">
                            Join Sekarang
                        </Link>

                        <Link href="/kerjasama"
                            class="rounded-2xl border border-orange-300/20 bg-white/5 px-5 py-3 text-sm font-black text-white transition duration-300 hover:-translate-y-0.5 hover:border-orange-300/40 hover:bg-orange-400/10 hover:text-orange-100">
                            Ajukan Kerjasama
                        </Link>
                    </div>
                </div>

                <!-- Navigation -->
                <div>
                    <h3 class="text-sm font-black uppercase tracking-[0.2em] text-orange-200">
                        Navigasi
                    </h3>

                    <ul class="mt-5 space-y-3">
                        <li v-for="link in navigationLinks" :key="link.href">
                            <a :href="link.href"
                                class="group inline-flex items-center gap-2 text-sm font-semibold text-slate-400 transition duration-300 hover:translate-x-1 hover:text-orange-300">
                                <span
                                    class="h-1.5 w-1.5 rounded-full bg-slate-700 transition duration-300 group-hover:bg-orange-400 group-hover:shadow-[0_0_12px_rgba(251,146,60,0.8)]"></span>
                                {{ link.label }}
                            </a>
                        </li>
                    </ul>
                </div>

                <!-- Program -->
                <div>
                    <h3 class="text-sm font-black uppercase tracking-[0.2em] text-orange-200">
                        Program
                    </h3>

                    <ul class="mt-5 space-y-3">
                        <li v-for="item in programLinks" :key="item">
                            <span class="inline-flex items-center gap-2 text-sm font-semibold text-slate-400">
                                <span class="h-1.5 w-1.5 rounded-full bg-orange-400"></span>
                                {{ item }}
                            </span>
                        </li>
                    </ul>

                    <div
                        class="mt-6 rounded-3xl border border-orange-300/15 bg-white/[0.04] p-5 shadow-lg shadow-orange-950/20 backdrop-blur">
                        <p class="text-sm font-black text-white">
                            Siap Kolaborasi?
                        </p>

                        <p class="mt-2 text-sm leading-6 text-slate-400">
                            Bangun project digital bersama KOMINFIK.
                        </p>

                        <a href="#kerjasama"
                            class="mt-4 inline-flex items-center gap-2 text-sm font-black text-orange-300 transition hover:gap-3 hover:text-orange-200">
                            Mulai sekarang
                            <span>→</span>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Bottom -->
            <div
                class="mt-12 flex flex-col gap-4 border-t border-orange-300/10 pt-6 text-sm text-slate-500 md:flex-row md:items-center md:justify-between">
                <p>
                    © {{ currentYear }} KOMINFIK. All rights reserved.
                </p>

                <p class="font-medium text-slate-500">
                    Built with
                    <span class="font-bold text-orange-300">passion</span>
                    for digital collaboration.
                </p>
            </div>
        </div>
    </footer>
</template>
