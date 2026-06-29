<script setup lang="ts">
import { Link } from '@inertiajs/vue3';

const currentYear = new Date().getFullYear();

const navigationLinks = [
    { label: 'Beranda', href: '/' },
    { label: 'Project', href: '/project' },
    { label: 'Anggota', href: '/anggota' },
    { label: 'Layanan', href: '/layanan' },
    { label: 'Kerjasama', href: '/kerjasama' },
];
</script>

<template>
    <footer class="bg-slate-950 text-white">
        <div class="mx-auto max-w-7xl px-6 py-12">
            <div class="grid gap-10 md:grid-cols-3">
                <div>
                    <Link href="/" class="text-2xl font-extrabold text-white">
                        KOMINFIK
                    </Link>

                    <p class="mt-4 max-w-md text-sm leading-6 text-slate-400">
                        Komunitas Informatika Kreatif sebagai wadah kolaborasi digital,
                        pengembangan skill, project teknologi, dan kerja sama bersama
                        perusahaan maupun universitas.
                    </p>
                </div>

                <div>
                    <h3 class="text-sm font-bold uppercase tracking-widest text-slate-300">
                        Navigasi
                    </h3>

                    <ul class="mt-4 space-y-3">
                        <li v-for="link in navigationLinks" :key="link.href">
                            <Link
                                :href="link.href"
                                class="text-sm text-slate-400 hover:text-blue-400"
                            >
                                {{ link.label }}
                            </Link>
                        </li>
                    </ul>
                </div>

                <div>
                    <h3 class="text-sm font-bold uppercase tracking-widest text-slate-300">
                        Gabung Bersama Kami
                    </h3>

                    <p class="mt-4 text-sm leading-6 text-slate-400">
                        Daftar sebagai anggota KOMINFIK atau ajukan kerja sama untuk
                        membangun project digital bersama.
                    </p>

                    <div class="mt-6 flex flex-wrap gap-3">
                        <Link
                            href="/join"
                            class="rounded-xl bg-blue-600 px-5 py-3 text-sm font-bold text-white hover:bg-blue-700"
                        >
                            Join Sekarang
                        </Link>

                        <Link
                            href="/kerjasama"
                            class="rounded-xl border border-white/10 px-5 py-3 text-sm font-bold text-white hover:bg-white/10"
                        >
                            Kerjasama
                        </Link>
                    </div>
                </div>
            </div>

            <div class="mt-10 border-t border-white/10 pt-6 text-sm text-slate-400">
                © {{ currentYear }} KOMINFIK. All rights reserved.
            </div>
        </div>
    </footer>
</template>
