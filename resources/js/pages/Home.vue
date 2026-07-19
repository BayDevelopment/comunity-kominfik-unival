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
    ImageOff,
    LayoutDashboard,
    Palette,
    Sparkles,
} from 'lucide-vue-next';
import { computed, ref } from 'vue';

import PublicLayout from '@/layouts/PublicLayout.vue';

interface Stats {
    project: number;
    anggota: number;
    layanan: number;
    mitra: number;
}

interface AnggotaItem {
    id: number;
    nama: string;
    foto_url: string | null;
    email: string | null;
    no_telepon: string | null;
    jabatan: string | null;
    divisi: string | null;
    tanggal_bergabung: string | null;
    status: 'aktif' | 'nonaktif';
}

interface ProjectItem {
    id: number;
    nama: string;
    gambar_url: string | null;
    deskripsi: string | null;
    klien: string | null;
    pic: string | null;
    teknologi: string | null;
    status: 'aktif' | 'selesai' | 'ditunda' | 'dibatalkan';
    progress: number;
    mulai: string | null;
    selesai: string | null;
}

interface LayananItem {
    biaya_formatted: string;
    id: number;
    nama: string;
    gambar_url: string | null;
    kategori: string | null;
    deskripsi: string | null;
    syarat: string[] | null;
    estimasi_waktu: string | null;
    biaya: number | null;
    status: 'aktif' | 'nonaktif';
}

const props = defineProps<{
    stats: Stats;
    projects: ProjectItem[];
    layanans: LayananItem[];
    anggotas: AnggotaItem[];
}>();

// Tampilan angka statistik: kasih akhiran "+" kalau datanya lebih dari 0
function formatCount(value: number): string {
    return value > 0 ? `${value}+` : '0';
}

const statCards = computed(() => [
    { value: formatCount(props.stats.project), label: 'Project Digital' },
    { value: formatCount(props.stats.anggota), label: 'Anggota Aktif' },
    { value: formatCount(props.stats.layanan), label: 'Layanan Tersedia' },
]);

// Pemetaan kategori layanan -> icon. Kalau kategori tidak dikenali, pakai Sparkles.
const iconByKategori: Record<string, typeof Code2> = {
    website: Code2,
    'pengembangan website': Code2,
    'sistem informasi': LayoutDashboard,
    aplikasi: LayoutDashboard,
    desain: Palette,
    'ui/ux': Palette,
    pelatihan: GraduationCap,
    workshop: GraduationCap,
    dokumentasi: FileText,
    kerjasama: Handshake,
};

function getServiceIcon(kategori: string | null) {
    if (!kategori) {
        return Sparkles;
    }

    const key = kategori.trim().toLowerCase();
    const matched = Object.keys(iconByKategori).find((k) => key.includes(k));

    return matched ? iconByKategori[matched] : Sparkles;
}


const projectStatusLabel: Record<ProjectItem['status'], string> = {
    aktif: 'Sedang Berjalan',
    selesai: 'Selesai',
    ditunda: 'Ditunda',
    dibatalkan: 'Dibatalkan',
};

const projectStatusClass: Record<ProjectItem['status'], string> = {
    aktif: 'bg-orange-50 text-orange-700 ring-orange-600/20',
    selesai: 'bg-emerald-50 text-emerald-700 ring-emerald-600/20',
    ditunda: 'bg-amber-50 text-amber-700 ring-amber-600/20',
    dibatalkan: 'bg-rose-50 text-rose-700 ring-rose-600/20',
};

// --- Anggota carousel (dari tabel anggotas) ---
const membersCarousel = ref<HTMLElement | null>(null);

function formatJoinDate(date: string | null): string {
    if (!date) {
        return '—';
    }

    return new Date(date).toLocaleDateString('id-ID', {
        month: 'long',
        year: 'numeric',
    });
}

function getInitials(name: string): string {
    return name
        .split(' ')
        .filter(Boolean)
        .slice(0, 2)
        .map((word) => word[0]?.toUpperCase())
        .join('');
}

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

                            <a href="/join/kerjasama"
                                class="rounded-2xl border border-orange-100 bg-white px-6 py-3.5 text-sm font-black text-slate-800 shadow-sm transition duration-300 hover:-translate-y-0.5 hover:border-orange-200 hover:bg-orange-50 hover:text-orange-700 hover:shadow-md hover:shadow-orange-100">
                                Ajukan Kerjasama
                            </a>
                        </div>

                        <!-- Stats dari database -->
                        <div class="mt-10 grid max-w-xl grid-cols-3 gap-4">
                            <div v-for="item in statCards" :key="item.label"
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
                            <div
                                class="relative overflow-hidden rounded-[1.5rem] bg-gradient-to-b from-slate-950 via-[#0b0f2a] to-slate-950 p-5 text-white">
                                <!-- Starfield -->
                                <div class="pointer-events-none absolute inset-0 opacity-70" style="background-image:
                                        radial-gradient(1.5px 1.5px at 20px 30px, white, transparent),
                                        radial-gradient(1px 1px at 90px 80px, white, transparent),
                                        radial-gradient(1px 1px at 150px 20px, white, transparent),
                                        radial-gradient(1.5px 1.5px at 50px 120px, white, transparent),
                                        radial-gradient(1px 1px at 180px 140px, white, transparent),
                                        radial-gradient(1.5px 1.5px at 220px 60px, white, transparent),
                                        radial-gradient(1px 1px at 260px 110px, white, transparent),
                                        radial-gradient(1px 1px at 10px 160px, white, transparent);
                                        background-repeat: repeat; background-size: 280px 180px;"></div>

                                <!-- Nebula glow -->
                                <div
                                    class="pointer-events-none absolute -right-10 -top-10 h-40 w-40 rounded-full bg-orange-500/20 blur-3xl">
                                </div>
                                <div
                                    class="pointer-events-none absolute -bottom-10 -left-10 h-40 w-40 rounded-full bg-indigo-500/20 blur-3xl">
                                </div>

                                <!-- Meteors -->
                                <span class="meteor" style="top: 5%; left: 75%; animation-delay: 0s;"></span>
                                <span class="meteor" style="top: 35%; left: 15%; animation-delay: 1.4s;"></span>
                                <span class="meteor" style="top: 60%; left: 85%; animation-delay: 2.8s;"></span>
                                <span class="meteor" style="top: 15%; left: 45%; animation-delay: 4.2s;"></span>

                                <!-- Konten -->
                                <div class="relative z-10">
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
                                                <span class="text-xs text-orange-300">{{ stats.project }} project</span>
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
                                                <h4 class="mt-2 text-2xl font-black">{{ formatCount(stats.mitra) }}</h4>
                                            </div>

                                            <div class="rounded-2xl bg-amber-400 p-4 text-slate-950">
                                                <p class="text-xs font-bold text-amber-900/70">Layanan</p>
                                                <h4 class="mt-2 text-2xl font-black">{{ formatCount(stats.layanan) }}
                                                </h4>
                                            </div>
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

                    <!-- State kosong -->
                    <div v-if="projects.length === 0"
                        class="mt-10 rounded-3xl border border-dashed border-orange-200 bg-orange-50/40 p-10 text-center text-sm font-semibold text-slate-500">
                        Belum ada project yang ditampilkan saat ini.
                    </div>

                    <div v-else class="mt-10 grid grid-cols-1 gap-5 md:grid-cols-2 lg:grid-cols-3">
                        <article v-for="project in projects" :key="project.id"
                            class="group overflow-hidden rounded-3xl border border-orange-100 bg-white shadow-sm transition duration-300 hover:-translate-y-1 hover:border-orange-200 hover:shadow-xl hover:shadow-orange-900/10">
                            <!-- Gambar project -->
                            <div class="relative h-44 w-full overflow-hidden bg-orange-50">
                                <img v-if="project.gambar_url" :src="project.gambar_url" :alt="project.nama"
                                    class="h-full w-full object-cover transition duration-500 group-hover:scale-105" />
                                <div v-else class="flex h-full w-full items-center justify-center text-orange-300">
                                    <ImageOff class="h-8 w-8" stroke-width="1.8" />
                                </div>

                                <span
                                    class="absolute right-3 top-3 rounded-full px-3 py-1 text-xs font-bold ring-1 ring-inset"
                                    :class="projectStatusClass[project.status]">
                                    {{ projectStatusLabel[project.status] }}
                                </span>
                            </div>

                            <div class="p-6">
                                <h3 class="text-lg font-black text-slate-950">
                                    {{ project.nama }}
                                </h3>

                                <p v-if="project.deskripsi" class="mt-3 line-clamp-3 text-sm leading-7 text-slate-500">
                                    {{ project.deskripsi }}
                                </p>

                                <div class="mt-4 flex flex-wrap gap-2 text-xs font-semibold text-slate-500">
                                    <span v-if="project.klien" class="rounded-full bg-slate-50 px-3 py-1">
                                        Klien: {{ project.klien }}
                                    </span>
                                    <span v-if="project.teknologi" class="rounded-full bg-slate-50 px-3 py-1">
                                        {{ project.teknologi }}
                                    </span>
                                </div>

                                <!-- Progress -->
                                <div class="mt-5">
                                    <div class="flex items-center justify-between text-xs font-bold text-slate-500">
                                        <span>Progress</span>
                                        <span>{{ project.progress }}%</span>
                                    </div>
                                    <div class="mt-2 h-2 rounded-full bg-orange-50">
                                        <div class="h-2 rounded-full bg-gradient-to-r from-orange-500 to-amber-400"
                                            :style="{ width: `${project.progress}%` }"></div>
                                    </div>
                                </div>
                            </div>
                        </article>
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

                    <!-- State kosong -->
                    <div v-if="anggotas.length === 0"
                        class="mt-10 rounded-3xl border border-dashed border-orange-200 bg-white/60 p-10 text-center text-sm font-semibold text-slate-500">
                        Belum ada anggota yang ditampilkan saat ini.
                    </div>

                    <!-- Carousel -->
                    <div v-else ref="membersCarousel"
                        class="mt-10 flex snap-x snap-mandatory gap-5 overflow-x-auto scroll-smooth pb-4 [scrollbar-width:none] [&::-webkit-scrollbar]:hidden">
                        <article v-for="member in anggotas" :key="member.id"
                            class="group min-w-full snap-start overflow-hidden rounded-[2rem] border border-orange-100 bg-white shadow-sm shadow-orange-100/60 transition duration-300 hover:-translate-y-1 hover:border-orange-200 hover:shadow-2xl hover:shadow-orange-900/10 sm:min-w-[calc(50%-10px)] lg:min-w-[calc(33.333%-14px)]">
                            <div class="relative h-72 overflow-hidden bg-orange-100">
                                <img v-if="member.foto_url" :src="member.foto_url" :alt="member.nama"
                                    class="h-full w-full object-cover transition duration-500 group-hover:scale-105" />
                                <div v-else
                                    class="flex h-full w-full items-center justify-center bg-gradient-to-br from-orange-400 to-amber-300 text-5xl font-black text-white">
                                    {{ getInitials(member.nama) }}
                                </div>

                                <div
                                    class="absolute inset-0 bg-gradient-to-t from-slate-950/70 via-slate-950/10 to-transparent">
                                </div>

                                <div
                                    class="absolute left-5 top-5 rounded-full bg-white/90 px-4 py-2 text-xs font-black text-orange-700 shadow-sm backdrop-blur">
                                    {{ member.jabatan || 'Anggota' }}
                                </div>

                                <div class="absolute bottom-5 left-5 right-5">
                                    <h3 class="text-2xl font-black text-white">
                                        {{ member.nama }}
                                    </h3>

                                    <p v-if="member.divisi" class="mt-1 text-sm font-semibold text-orange-100">
                                        {{ member.divisi }}
                                    </p>
                                </div>
                            </div>

                            <div class="p-6">
                                <div class="space-y-3">
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
                                                {{ member.jabatan || '—' }}
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
                                                Divisi
                                            </p>
                                            <p class="mt-1 text-sm font-black text-slate-900">
                                                {{ member.divisi || '—' }}
                                            </p>
                                        </div>
                                    </div>

                                    <div class="flex items-start gap-3">
                                        <div
                                            class="flex h-10 w-10 shrink-0 items-center justify-center rounded-2xl bg-orange-50 text-orange-600">
                                            <GraduationCap class="h-5 w-5" stroke-width="2.4" />
                                        </div>

                                        <div>
                                            <p class="text-xs font-bold uppercase tracking-widest text-slate-400">
                                                Bergabung Sejak
                                            </p>
                                            <p class="mt-1 text-sm font-black text-slate-900">
                                                {{ formatJoinDate(member.tanggal_bergabung) }}
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

                    <!-- State kosong -->
                    <div v-if="layanans.length === 0"
                        class="mt-10 rounded-3xl border border-dashed border-orange-200 bg-orange-50/40 p-10 text-center text-sm font-semibold text-slate-500">
                        Belum ada layanan yang tersedia saat ini.
                    </div>

                    <div v-else class="mt-10 grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3">
                        <article v-for="layanan in layanans" :key="layanan.id"
                            class="group overflow-hidden rounded-3xl border border-orange-100 bg-white shadow-sm transition duration-300 hover:-translate-y-1 hover:border-orange-200 hover:shadow-xl hover:shadow-orange-100">
                            <!-- Gambar layanan (kalau ada) -->
                            <div v-if="layanan.gambar_url" class="h-40 w-full overflow-hidden bg-orange-50">
                                <img :src="layanan.gambar_url" :alt="layanan.nama"
                                    class="h-full w-full object-cover transition duration-500 group-hover:scale-105" />
                            </div>

                            <div class="p-6">
                                <div
                                    class="mb-5 flex h-14 w-14 items-center justify-center rounded-2xl bg-gradient-to-br from-orange-500 to-amber-400 text-white shadow-lg shadow-orange-500/20 transition duration-300 group-hover:scale-110 group-hover:rotate-3">
                                    <component :is="getServiceIcon(layanan.kategori)" class="h-7 w-7"
                                        stroke-width="2.4" />
                                </div>

                                <p v-if="layanan.kategori"
                                    class="text-xs font-black uppercase tracking-widest text-orange-600">
                                    {{ layanan.kategori }}
                                </p>

                                <h3 class="mt-2 text-lg font-black text-slate-950">
                                    {{ layanan.nama }}
                                </h3>

                                <p v-if="layanan.deskripsi" class="mt-3 line-clamp-3 text-sm leading-7 text-slate-500">
                                    {{ layanan.deskripsi }}
                                </p>

                                <!-- Syarat -->
                                <div v-if="layanan.syarat && layanan.syarat.length"
                                    class="mt-4 border-t border-orange-50 pt-4">
                                    <p class="text-xs font-black uppercase tracking-widest text-slate-400">Syarat</p>
                                    <ul class="mt-2 space-y-1.5 text-sm text-slate-600">
                                        <li v-for="(syarat, index) in layanan.syarat.slice(0, 3)" :key="index"
                                            class="flex items-start gap-2">
                                            <span class="mt-1.5 h-1 w-1 shrink-0 rounded-full bg-orange-400"></span>
                                            <span>{{ syarat }}</span>
                                        </li>
                                    </ul>
                                    <p v-if="layanan.syarat.length > 3"
                                        class="mt-1.5 text-xs font-semibold text-orange-500">
                                        +{{ layanan.syarat.length - 3 }} syarat lainnya
                                    </p>
                                </div>

                                <div class="mt-5 flex flex-wrap items-center gap-2 text-xs font-bold">
                                    <span v-if="layanan.estimasi_waktu"
                                        class="rounded-full bg-slate-50 px-3 py-1.5 text-slate-500">
                                        ⏱ {{ layanan.estimasi_waktu }}
                                    </span>
                                    <span class="rounded-full bg-orange-50 px-3 py-1.5 text-orange-700">
                                        {{ layanan.biaya_formatted || 'Gratis / Nego' }}
                                    </span>
                                </div>
                            </div>
                        </article>
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
                                <Link href="/join/kerjasama"
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

<style scoped>
.meteor {
    position: absolute;
    width: 2px;
    height: 2px;
    border-radius: 9999px;
    background: linear-gradient(90deg, rgba(251, 191, 36, 0) 0%, rgba(251, 191, 36, 0.9) 60%, #ffffff 100%);
    opacity: 0;
    animation: meteor-fall 3.5s linear infinite;
}

.meteor::before {
    content: '';
    position: absolute;
    top: 50%;
    right: 1px;
    width: 70px;
    height: 1px;
    transform: translateY(-50%);
    background: linear-gradient(90deg, transparent, rgba(251, 191, 36, 0.6));
}

@keyframes meteor-fall {
    0% {
        transform: rotate(215deg) translateX(0);
        opacity: 0;
    }

    8% {
        opacity: 1;
    }

    65% {
        opacity: 1;
    }

    100% {
        transform: rotate(215deg) translateX(-260px);
        opacity: 0;
    }
}
</style>
