<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import {
    BriefcaseBusiness,
    ChevronLeft,
    ChevronRight,
    Code2,
    FileText,
    GraduationCap,
    Handshake,
    LayoutDashboard,
    Palette,
    Sparkles,
} from 'lucide-vue-next';
import { ref } from 'vue';

import PublicLayout from '@/layouts/PublicLayout.vue';

const stats = [
    { value: '25+', label: 'Project Digital' },
    { value: '100+', label: 'Anggota Aktif' },
    { value: '10+', label: 'Mitra Kolaborasi' },
];

const projects = [
    {
        title: 'Website & Landing Page',
        description: 'Pembuatan website profil, company profile, sistem informasi, dan landing page modern.',
    },
    {
        title: 'Aplikasi Digital',
        description: 'Kolaborasi pengembangan aplikasi berbasis web untuk kebutuhan komunitas, kampus, dan organisasi.',
    },
    {
        title: 'Branding Teknologi',
        description: 'Membantu penguatan identitas digital melalui desain, konten, dan solusi teknologi kreatif.',
    },
];

const services = [
    {
        title: 'Pengembangan Website',
        description: 'Website modern, responsif, dan profesional untuk kebutuhan profil, bisnis, atau komunitas.',
        icon: Code2,
    },
    {
        title: 'Sistem Informasi',
        description: 'Pembuatan sistem berbasis web untuk membantu pengelolaan data dan aktivitas organisasi.',
        icon: LayoutDashboard,
    },
    {
        title: 'Desain UI/UX',
        description: 'Desain antarmuka yang bersih, nyaman digunakan, dan menarik secara visual.',
        icon: Palette,
    },
    {
        title: 'Pelatihan Teknologi',
        description: 'Kegiatan belajar, workshop, dan mentoring untuk meningkatkan skill digital.',
        icon: GraduationCap,
    },
    {
        title: 'Dokumentasi Project',
        description: 'Penyusunan dokumentasi project agar rapi, jelas, dan mudah dikembangkan.',
        icon: FileText,
    },
    {
        title: 'Kerjasama Digital',
        description: 'Kolaborasi dengan kampus, organisasi, perusahaan, dan komunitas teknologi.',
        icon: Handshake,
    },
];

const membersCarousel = ref<HTMLElement | null>(null);

const members = [
    {
        name: 'Bay',
        prodi: 'Teknik Informatika',
        skill: 'Fullstack Web Developer',
        position: 'Ketua',
        image: '/images/members/bay.png',
    },
    {
        name: 'Arya',
        prodi: 'Teknik Informatika',
        skill: 'Fullstack Web Developer',
        position: 'Wakil Ketua',
        image: '/images/members/arya.jpeg',
    }

];

const scrollMembers = (direction: 'prev' | 'next') => {
    const el = membersCarousel.value;

    if (!el) {
        return;
    }

    const scrollAmount = el.clientWidth * 0.9;

    el.scrollBy({
        left: direction === 'next' ? scrollAmount : -scrollAmount,
        behavior: 'smooth',
    });
};
</script>

<template>
    <PublicLayout>
        <main class="overflow-hidden bg-white">
            <!-- Hero -->
            <section id="beranda" class="relative scroll-mt-28 bg-gradient-to-b from-orange-50 via-white to-white">
                <div class="absolute -left-24 top-20 h-72 w-72 rounded-full bg-orange-200/50 blur-3xl"></div>
                <div class="absolute -right-24 top-10 h-80 w-80 rounded-full bg-amber-200/50 blur-3xl"></div>

                <div
                    class="relative mx-auto grid max-w-7xl grid-cols-1 items-center gap-12 px-6 py-20 lg:grid-cols-2 lg:py-24">
                    <div>
                        <div
                            class="mb-5 inline-flex items-center gap-2 rounded-full border border-orange-100 bg-white px-4 py-2 text-sm font-bold text-orange-700 shadow-sm shadow-orange-100/60">
                            <span class="h-2 w-2 rounded-full bg-orange-500"></span>
                            Komunitas Informatika Kreatif
                        </div>

                        <h1
                            class="max-w-3xl text-4xl font-black leading-tight tracking-tight text-slate-950 md:text-6xl">
                            Bangun Ekosistem Digital Bersama
                            <span class="bg-gradient-to-r from-orange-500 to-amber-500 bg-clip-text text-transparent">
                                KOMINFIK
                            </span>
                        </h1>

                        <p class="mt-6 max-w-xl text-lg leading-8 text-slate-600">
                            KOMINFIK menjadi wadah kolaborasi untuk mahasiswa, anggota komunitas,
                            perusahaan, dan universitas dalam pengembangan teknologi, kreativitas,
                            serta inovasi digital.
                        </p>

                        <div class="mt-9 flex flex-wrap gap-3">
                            <Link href="/join"
                                class="rounded-2xl bg-gradient-to-r from-orange-500 to-amber-400 px-6 py-3.5 text-sm font-black text-white shadow-xl shadow-orange-500/25 transition duration-300 hover:-translate-y-0.5 hover:shadow-2xl hover:shadow-orange-500/30">
                                Join
                            </Link>

                            <a href="#kerjasama"
                                class="rounded-2xl border border-orange-100 bg-white px-6 py-3.5 text-sm font-black text-slate-800 shadow-sm transition duration-300 hover:-translate-y-0.5 hover:border-orange-200 hover:bg-orange-50 hover:text-orange-700 hover:shadow-md hover:shadow-orange-100">
                                Ajukan Kerjasama
                            </a>
                        </div>

                        <div class="mt-10 grid max-w-xl grid-cols-3 gap-4">
                            <div v-for="item in stats" :key="item.label"
                                class="rounded-2xl border border-orange-100 bg-white/90 p-4 shadow-sm shadow-orange-100/50 transition hover:-translate-y-1 hover:shadow-lg hover:shadow-orange-100">
                                <h3 class="text-2xl font-black text-slate-950">
                                    {{ item.value }}
                                </h3>
                                <p class="mt-1 text-xs font-semibold text-slate-500">
                                    {{ item.label }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Hero Card -->
                    <div class="relative">
                        <div
                            class="rounded-[2rem] border border-orange-100 bg-white p-5 shadow-2xl shadow-orange-900/10">
                            <div class="rounded-[1.5rem] bg-slate-950 p-5 text-white">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-sm text-slate-400">KOMINFIK Dashboard</p>
                                        <h2 class="mt-1 text-xl font-black">Program Utama</h2>
                                    </div>

                                    <div
                                        class="rounded-full bg-orange-500/10 px-3 py-1 text-xs font-bold text-orange-300">
                                        Active
                                    </div>
                                </div>

                                <div class="mt-6 space-y-4">
                                    <div class="rounded-2xl bg-white/10 p-4 backdrop-blur">
                                        <div class="flex items-center justify-between">
                                            <h3 class="font-bold">Project Digital</h3>
                                            <span class="text-xs text-orange-300">85%</span>
                                        </div>

                                        <div class="mt-3 h-2 rounded-full bg-white/10">
                                            <div
                                                class="h-2 w-[85%] rounded-full bg-gradient-to-r from-orange-500 to-amber-400">
                                            </div>
                                        </div>

                                        <p class="mt-3 text-sm text-slate-300">
                                            Kolaborasi pembuatan aplikasi, website, dan sistem informasi.
                                        </p>
                                    </div>

                                    <div class="rounded-2xl bg-white p-4 text-slate-900">
                                        <h3 class="font-black">Anggota Komunitas</h3>
                                        <p class="mt-2 text-sm leading-6 text-slate-500">
                                            Wadah belajar, networking, mentoring, dan pengembangan skill teknologi.
                                        </p>
                                    </div>

                                    <div class="grid grid-cols-2 gap-4">
                                        <div class="rounded-2xl bg-orange-500 p-4">
                                            <p class="text-xs font-bold text-orange-100">Kerjasama</p>
                                            <h4 class="mt-2 text-2xl font-black">10+</h4>
                                        </div>

                                        <div class="rounded-2xl bg-amber-400 p-4 text-slate-950">
                                            <p class="text-xs font-bold text-amber-900/70">Layanan</p>
                                            <h4 class="mt-2 text-2xl font-black">6+</h4>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div
                            class="absolute -bottom-6 -left-6 hidden rounded-3xl border border-orange-100 bg-white p-4 shadow-xl shadow-orange-100 lg:block">
                            <p class="text-xs font-bold text-slate-400">Status Komunitas</p>
                            <p class="mt-1 text-lg font-black text-slate-900">Siap Kolaborasi</p>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Project -->
            <section id="project" class="scroll-mt-28 px-6 py-20">
                <div class="mx-auto max-w-7xl">
                    <div class="max-w-2xl">
                        <p class="font-bold text-orange-600">Project</p>

                        <h2 class="mt-3 text-3xl font-black tracking-tight text-slate-950 md:text-4xl">
                            Project digital yang bisa dikembangkan bersama
                        </h2>

                        <p class="mt-4 text-slate-600">
                            KOMINFIK membuka ruang kolaborasi untuk membuat produk digital yang bermanfaat,
                            modern, dan sesuai kebutuhan pengguna.
                        </p>
                    </div>

                    <div class="mt-10 grid grid-cols-1 gap-5 md:grid-cols-3">
                        <div v-for="project in projects" :key="project.title"
                            class="group rounded-3xl border border-orange-100 bg-white p-6 shadow-sm transition duration-300 hover:-translate-y-1 hover:border-orange-200 hover:shadow-xl hover:shadow-orange-900/10">
                            <div
                                class="mb-5 flex h-12 w-12 items-center justify-center rounded-2xl bg-orange-50 text-xl font-black text-orange-600 transition group-hover:bg-orange-500 group-hover:text-white">
                                #
                            </div>

                            <h3 class="text-lg font-black text-slate-950">
                                {{ project.title }}
                            </h3>

                            <p class="mt-3 text-sm leading-7 text-slate-500">
                                {{ project.description }}
                            </p>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Anggota -->
            <section id="anggota" class="relative scroll-mt-28 overflow-hidden bg-orange-50/60 px-6 py-20">
                <div class="absolute -left-24 top-10 h-72 w-72 rounded-full bg-orange-200/40 blur-3xl"></div>
                <div class="absolute -right-24 bottom-10 h-72 w-72 rounded-full bg-amber-200/40 blur-3xl"></div>

                <div class="relative mx-auto max-w-7xl">
                    <div class="flex flex-col gap-6 md:flex-row md:items-end md:justify-between">
                        <div class="max-w-2xl">
                            <p class="font-bold text-orange-600">Anggota</p>

                            <h2 class="mt-3 text-3xl font-black tracking-tight text-slate-950 md:text-4xl">
                                Talenta kreatif di balik KOMINFIK
                            </h2>

                            <p class="mt-5 leading-8 text-slate-600">
                                Anggota KOMINFIK terdiri dari mahasiswa dengan berbagai skill digital,
                                mulai dari pengembangan website, desain UI/UX, content creator, hingga
                                manajemen project teknologi.
                            </p>
                        </div>

                        <!-- Control Button -->
                        <div class="flex items-center gap-3">
                            <button type="button"
                                class="flex h-12 w-12 items-center justify-center rounded-2xl border border-orange-100 bg-white text-slate-700 shadow-sm shadow-orange-100 transition duration-300 hover:-translate-y-0.5 hover:border-orange-200 hover:bg-orange-50 hover:text-orange-700"
                                aria-label="Anggota sebelumnya" @click="scrollMembers('prev')">
                                <ChevronLeft class="h-5 w-5" stroke-width="2.5" />
                            </button>

                            <button type="button"
                                class="flex h-12 w-12 items-center justify-center rounded-2xl bg-gradient-to-br from-orange-500 to-amber-400 text-white shadow-lg shadow-orange-500/25 transition duration-300 hover:-translate-y-0.5 hover:shadow-xl hover:shadow-orange-500/30"
                                aria-label="Anggota berikutnya" @click="scrollMembers('next')">
                                <ChevronRight class="h-5 w-5" stroke-width="2.5" />
                            </button>
                        </div>
                    </div>

                    <!-- Carousel -->
                    <div ref="membersCarousel"
                        class="mt-10 flex snap-x snap-mandatory gap-5 overflow-x-auto scroll-smooth pb-4 [scrollbar-width:none] [&::-webkit-scrollbar]:hidden">
                        <article v-for="member in members" :key="member.name"
                            class="group min-w-full snap-start overflow-hidden rounded-[2rem] border border-orange-100 bg-white shadow-sm shadow-orange-100/60 transition duration-300 hover:-translate-y-1 hover:border-orange-200 hover:shadow-2xl hover:shadow-orange-900/10 sm:min-w-[calc(50%-10px)] lg:min-w-[calc(33.333%-14px)]">
                            <!-- Image -->
                            <div class="relative h-72 overflow-hidden bg-orange-100">
                                <img :src="member.image" :alt="member.name"
                                    class="h-full w-full object-cover transition duration-500 group-hover:scale-105" />

                                <div
                                    class="absolute inset-0 bg-gradient-to-t from-slate-950/70 via-slate-950/10 to-transparent">
                                </div>

                                <div
                                    class="absolute left-5 top-5 rounded-full bg-white/90 px-4 py-2 text-xs font-black text-orange-700 shadow-sm backdrop-blur">
                                    {{ member.position }}
                                </div>

                                <div class="absolute bottom-5 left-5 right-5">
                                    <h3 class="text-2xl font-black text-white">
                                        {{ member.name }}
                                    </h3>

                                    <p class="mt-1 text-sm font-semibold text-orange-100">
                                        {{ member.skill }}
                                    </p>
                                </div>
                            </div>

                            <!-- Content -->
                            <div class="p-6">
                                <div class="space-y-3">
                                    <div class="flex items-start gap-3">
                                        <div
                                            class="flex h-10 w-10 shrink-0 items-center justify-center rounded-2xl bg-orange-50 text-orange-600">
                                            <GraduationCap class="h-5 w-5" stroke-width="2.4" />
                                        </div>

                                        <div>
                                            <p class="text-xs font-bold uppercase tracking-widest text-slate-400">
                                                Program Studi
                                            </p>
                                            <p class="mt-1 text-sm font-black text-slate-900">
                                                {{ member.prodi }}
                                            </p>
                                        </div>
                                    </div>

                                    <div class="flex items-start gap-3">
                                        <div
                                            class="flex h-10 w-10 shrink-0 items-center justify-center rounded-2xl bg-orange-50 text-orange-600">
                                            <Sparkles class="h-5 w-5" stroke-width="2.4" />
                                        </div>

                                        <div>
                                            <p class="text-xs font-bold uppercase tracking-widest text-slate-400">
                                                Skill
                                            </p>
                                            <p class="mt-1 text-sm font-black text-slate-900">
                                                {{ member.skill }}
                                            </p>
                                        </div>
                                    </div>

                                    <div class="flex items-start gap-3">
                                        <div
                                            class="flex h-10 w-10 shrink-0 items-center justify-center rounded-2xl bg-orange-50 text-orange-600">
                                            <BriefcaseBusiness class="h-5 w-5" stroke-width="2.4" />
                                        </div>

                                        <div>
                                            <p class="text-xs font-bold uppercase tracking-widest text-slate-400">
                                                Jabatan
                                            </p>
                                            <p class="mt-1 text-sm font-black text-slate-900">
                                                {{ member.position }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </article>
                    </div>

                    <!-- Bottom Info -->
                    <div
                        class="mt-6 flex flex-col gap-3 rounded-3xl border border-orange-100 bg-white/80 p-5 shadow-sm shadow-orange-100/60 backdrop-blur md:flex-row md:items-center md:justify-between">
                        <div>
                            <p class="text-sm font-black text-slate-950">
                                Ingin menjadi bagian dari KOMINFIK?
                            </p>
                            <p class="mt-1 text-sm text-slate-500">
                                Gabung bersama anggota lain dan bangun portofolio digitalmu.
                            </p>
                        </div>

                        <Link href="/join"
                            class="inline-flex items-center justify-center rounded-2xl bg-gradient-to-r from-orange-500 to-amber-400 px-5 py-3 text-sm font-black text-white shadow-lg shadow-orange-500/25 transition hover:-translate-y-0.5">
                            Gabung Anggota
                        </Link>
                    </div>
                </div>
            </section>

            <!-- Layanan -->
            <section id="layanan" class="scroll-mt-28 px-6 py-20">
                <div class="mx-auto max-w-7xl">
                    <div class="text-center">
                        <p class="font-bold text-orange-600">Layanan</p>

                        <h2 class="mt-3 text-3xl font-black tracking-tight text-slate-950 md:text-4xl">
                            Layanan digital KOMINFIK
                        </h2>

                        <p class="mx-auto mt-4 max-w-2xl text-slate-600">
                            Kami membantu kebutuhan digital melalui pendekatan kolaboratif,
                            kreatif, dan profesional.
                        </p>
                    </div>

                    <div class="mt-10 grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3">
                        <div v-for="service in services" :key="service.title"
                            class="group rounded-3xl border border-orange-100 bg-white p-6 shadow-sm transition duration-300 hover:-translate-y-1 hover:border-orange-200 hover:bg-orange-50/60 hover:shadow-xl hover:shadow-orange-100">
                            <div
                                class="mb-5 flex h-14 w-14 items-center justify-center rounded-2xl bg-gradient-to-br from-orange-500 to-amber-400 text-white shadow-lg shadow-orange-500/20 transition duration-300 group-hover:scale-110 group-hover:rotate-3">
                                <component :is="service.icon" class="h-7 w-7" stroke-width="2.4" />
                            </div>

                            <h3 class="text-lg font-black text-slate-950">
                                {{ service.title }}
                            </h3>

                            <p class="mt-3 text-sm leading-7 text-slate-500">
                                {{ service.description }}
                            </p>

                            <div
                                class="mt-5 flex items-center gap-2 text-sm font-bold text-orange-600 opacity-0 transition duration-300 group-hover:opacity-100">
                                <span>Pelajari layanan</span>
                                <span class="transition duration-300 group-hover:translate-x-1">→</span>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Kerjasama -->
            <section id="kerjasama" class="scroll-mt-28 px-6 py-20">
                <div class="mx-auto max-w-7xl">
                    <div
                        class="overflow-hidden rounded-[2rem] bg-gradient-to-br from-orange-500 via-orange-500 to-amber-400 p-8 text-white shadow-2xl shadow-orange-500/25 md:p-12">
                        <div class="grid grid-cols-1 items-center gap-8 lg:grid-cols-2">
                            <div>
                                <p class="font-bold text-orange-100">Kerjasama</p>

                                <h2 class="mt-3 text-3xl font-black tracking-tight md:text-4xl">
                                    Ingin berkolaborasi dengan KOMINFIK?
                                </h2>

                                <p class="mt-4 max-w-xl leading-8 text-orange-50">
                                    Kami terbuka untuk kerjasama project digital, pelatihan teknologi,
                                    kegiatan kampus, komunitas, perusahaan, dan organisasi.
                                </p>
                            </div>

                            <div class="flex flex-wrap gap-3 lg:justify-end">
                                <Link href="/kerjasama"
                                    class="rounded-2xl bg-white px-6 py-3.5 text-sm font-black text-orange-700 shadow-lg transition duration-300 hover:-translate-y-0.5 hover:bg-orange-50">
                                    Ajukan Kerjasama
                                </Link>

                                <Link href="/join"
                                    class="rounded-2xl border border-white/30 px-6 py-3.5 text-sm font-black text-white transition duration-300 hover:-translate-y-0.5 hover:bg-white/10">
                                    Gabung Anggota
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </main>
    </PublicLayout>
</template>
