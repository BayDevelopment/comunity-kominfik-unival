<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import {
    ArrowRight,
    BookOpenCheck,
    Building2,
    GraduationCap,
    UserPlus,
} from 'lucide-vue-next';
import type { Component } from 'vue'; // Menggunakan tipe Component bawaan Vue

import PublicLayout from '@/layouts/PublicLayout.vue';

interface JoinOption {
    key: string;
    eyebrow: string;
    title: string;
    description: string;
    points: string[];
    icon: Component; // Lebih fleksibel dan aman untuk semua ikon Lucide
    href: string;
    cta: string;
}

const options: JoinOption[] = [
    {
        key: 'anggota',
        eyebrow: 'Individu',
        title: 'Daftar Anggota',
        description:
            'Bergabung sebagai anggota KOMINFIK, belajar bersama, ikut project digital, dan kembangkan portofoliomu.',
        points: [
            'Akses project & pelatihan komunitas',
            'Mentoring dari anggota berpengalaman',
            'Networking dengan talenta digital lain',
        ],
        icon: UserPlus,
        href: '/join/anggota',
        cta: 'Daftar sebagai anggota',
    },
    {
        key: 'university',
        eyebrow: 'Institusi Pendidikan Tinggi',
        title: 'Kerjasama Universitas',
        description:
            'Kolaborasi dengan universitas untuk program magang, riset, kegiatan kampus, hingga pengembangan aplikasi bersama.',
        points: [
            'Program magang & studi independen',
            'Kolaborasi riset dan project kampus',
            'Workshop & seminar teknologi',
        ],
        icon: GraduationCap,
        href: '/join/kerjasama/university',
        cta: 'Ajukan kerjasama universitas',
    },
    {
        key: 'sekolah',
        eyebrow: 'Sekolah Menengah',
        title: 'Kerjasama Sekolah',
        description:
            'Kerjasama dengan sekolah untuk pengenalan teknologi, pelatihan dasar coding, hingga pendampingan project siswa.',
        points: [
            'Pelatihan dasar coding & digital skill',
            'Pendampingan project siswa',
            'Kunjungan & sharing session teknologi',
        ],
        icon: BookOpenCheck,
        href: '/join/kerjasama/sekolah',
        cta: 'Ajukan kerjasama sekolah',
    },
];
</script>

<template>
    <PublicLayout>
        <main class="overflow-hidden bg-white">
            <!-- Hero -->
            <section class="relative bg-gradient-to-b from-orange-50 via-white to-white">
                <div class="absolute -left-24 top-20 h-72 w-72 rounded-full bg-orange-200/50 blur-3xl"></div>
                <div class="absolute -right-24 top-10 h-80 w-80 rounded-full bg-amber-200/50 blur-3xl"></div>

                <div class="relative mx-auto max-w-4xl px-6 py-20 text-center lg:py-24">
                    <div
                        class="mb-5 inline-flex items-center gap-2 rounded-full border border-orange-100 bg-white px-4 py-2 text-sm font-bold text-orange-700 shadow-sm shadow-orange-100/60">
                        <span class="h-2 w-2 rounded-full bg-orange-500"></span>
                        Bergabung dengan KOMINFIK
                    </div>

                    <h1 class="text-4xl font-black leading-tight tracking-tight text-slate-950 md:text-6xl">
                        Pilih Jalur
                        <span class="bg-gradient-to-r from-orange-500 to-amber-500 bg-clip-text text-transparent">
                            Bergabungmu
                        </span>
                    </h1>

                    <p class="mx-auto mt-6 max-w-2xl text-lg leading-8 text-slate-600">
                        Baik kamu ingin jadi anggota, atau mewakili universitas maupun sekolah untuk
                        berkolaborasi, KOMINFIK membuka ruang untuk tumbuh bersama.
                    </p>
                </div>
            </section>

            <!-- Options -->
            <section class="scroll-mt-28 px-6 pb-24">
                <div class="mx-auto grid max-w-7xl grid-cols-1 gap-6 md:grid-cols-3">
                    <article v-for="option in options" :key="option.key"
                        class="group flex flex-col rounded-[2rem] border border-orange-100 bg-white p-8 shadow-sm shadow-orange-100/50 transition duration-300 hover:-translate-y-1 hover:border-orange-200 hover:shadow-2xl hover:shadow-orange-900/10">
                        <div
                            class="flex h-14 w-14 items-center justify-center rounded-2xl bg-gradient-to-br from-orange-500 to-amber-400 text-white shadow-lg shadow-orange-500/20 transition duration-300 group-hover:scale-110 group-hover:rotate-3">
                            <component :is="option.icon" class="h-7 w-7" stroke-width="2.4" />
                        </div>

                        <p class="mt-6 text-xs font-black uppercase tracking-widest text-orange-600">
                            {{ option.eyebrow }}
                        </p>

                        <h2 class="mt-2 text-2xl font-black text-slate-950">
                            {{ option.title }}
                        </h2>

                        <p class="mt-4 text-sm leading-7 text-slate-500">
                            {{ option.description }}
                        </p>

                        <ul class="mt-6 flex-1 space-y-3">
                            <li v-for="point in option.points" :key="point"
                                class="flex items-start gap-3 text-sm text-slate-600">
                                <span class="mt-1.5 h-1.5 w-1.5 shrink-0 rounded-full bg-orange-500"></span>
                                <span>{{ point }}</span>
                            </li>
                        </ul>

                        <Link :href="option.href"
                            class="mt-8 inline-flex items-center justify-center gap-2 rounded-2xl bg-gradient-to-r from-orange-500 to-amber-400 px-6 py-3.5 text-sm font-black text-white shadow-lg shadow-orange-500/25 transition duration-300 hover:-translate-y-0.5 hover:shadow-xl hover:shadow-orange-500/30">
                            {{ option.cta }}
                            <ArrowRight class="h-4 w-4 transition duration-300 group-hover:translate-x-1"
                                stroke-width="2.5" />
                        </Link>
                    </article>
                </div>

                <!-- Bottom Info -->
                <div
                    class="mx-auto mt-10 flex max-w-7xl flex-col gap-3 rounded-3xl border border-orange-100 bg-orange-50/60 p-6 shadow-sm shadow-orange-100/60 md:flex-row md:items-center md:justify-between">
                    <div class="flex items-center gap-3">
                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-2xl bg-white text-orange-600">
                            <Building2 class="h-5 w-5" stroke-width="2.4" />
                        </div>
                        <div>
                            <p class="text-sm font-black text-slate-950">
                                Mewakili perusahaan atau organisasi lain?
                            </p>
                            <p class="mt-1 text-sm text-slate-500">
                                Kami juga terbuka untuk kerjasama di luar universitas dan sekolah.
                            </p>
                        </div>
                    </div>

                    <Link href="/kerjasama"
                        class="inline-flex shrink-0 items-center justify-center rounded-2xl border border-orange-200 bg-white px-5 py-3 text-sm font-black text-orange-700 shadow-sm transition hover:-translate-y-0.5 hover:bg-orange-50">
                        Ajukan Kerjasama Lainnya
                    </Link>
                </div>
            </section>
        </main>
    </PublicLayout>
</template>
