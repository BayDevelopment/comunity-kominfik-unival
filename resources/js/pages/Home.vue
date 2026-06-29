<script setup lang="ts">
import { Link } from '@inertiajs/vue3';

import PublicLayout from '@/layouts/PublicLayout.vue';
</script>

<template>
    <PublicLayout>
        <section class="mx-auto grid max-w-7xl grid-cols-1 items-center gap-10 px-6 py-20 lg:grid-cols-2">
            <div>
                <p class="mb-3 font-semibold text-blue-600">
                    Komunitas Informatika Kreatif
                </p>

                <h1 class="text-4xl font-extrabold leading-tight md:text-5xl">
                    Bangun Ekosistem Digital Bersama KOMINFIK
                </h1>

                <p class="mt-5 max-w-xl text-lg text-gray-600">
                    KOMINFIK menjadi wadah kolaborasi untuk mahasiswa, anggota komunitas,
                    perusahaan, dan universitas dalam pengembangan teknologi dan inovasi digital.
                </p>

                <div class="mt-8 flex flex-wrap gap-3">
                    <Link href="/join"
                        class="rounded-xl bg-blue-600 px-6 py-3 font-semibold text-white hover:bg-blue-700">
                        Join KOMINFIK
                    </Link>

                    <Link href="/kerjasama"
                        class="rounded-xl border border-gray-300 px-6 py-3 font-semibold text-gray-800 hover:bg-gray-100">
                        Ajukan Kerjasama
                    </Link>
                </div>
            </div>

            <div class="rounded-3xl bg-blue-50 p-8">
                <div class="rounded-2xl bg-white p-6 shadow-sm">
                    <h2 class="text-xl font-bold">Program Utama</h2>

                    <div class="mt-5 space-y-4">
                        <div class="rounded-xl border border-gray-100 p-4">
                            <h3 class="font-semibold">Project Digital</h3>
                            <p class="text-sm text-gray-500">
                                Kolaborasi pembuatan aplikasi, website, dan sistem informasi.
                            </p>
                        </div>

                        <div class="rounded-xl border border-gray-100 p-4">
                            <h3 class="font-semibold">Anggota Komunitas</h3>
                            <p class="text-sm text-gray-500">
                                Wadah belajar, networking, dan pengembangan skill teknologi.
                            </p>
                        </div>

                        <div class="rounded-xl border border-gray-100 p-4">
                            <h3 class="font-semibold">Kerjasama</h3>
                            <p class="text-sm text-gray-500">
                                Kolaborasi dengan perusahaan, kampus, dan organisasi.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </PublicLayout>
</template>
