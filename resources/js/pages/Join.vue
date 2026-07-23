<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import {
    ArrowRight,
    CalendarRange,
    GraduationCap,
    UserPlus,
    Lock
} from 'lucide-vue-next';
import { computed } from 'vue';
import PublicLayout from '@/layouts/PublicLayout.vue';

interface PeriodePendaftaran {
    id: number;
    jenis: 'anggota' | 'kerjasama';
    nama_periode: string | null;
    tanggal_mulai: string | null;
    tanggal_selesai: string | null;
    status: 'active' | 'nonactive';
    created_at: string;
}

const props = defineProps<{
    periodes: PeriodePendaftaran[];
}>();

const options = computed(() => {
    return props.periodes.map((periode) => {
        if (periode.jenis === 'anggota') {
            return {
                id: periode.id,
                eyebrow: 'Individu',
                title: periode.nama_periode || 'Pendaftaran Anggota',
                description: 'Bergabung sebagai anggota KOMINFIK, belajar bersama, ikut project digital, dan kembangkan portofoliomu.',
                points: [
                    'Akses project & pelatihan komunitas',
                    'Mentoring dari anggota berpengalaman',
                    'Networking dengan talenta digital lain',
                ],
                icon: UserPlus,
                href: '/join/anggota',
                cta: 'Daftar sebagai anggota',
                periode: periode,
            };
        }

        return {
            id: periode.id,
            eyebrow: 'Institusi',
            title: periode.nama_periode || 'Kerjasama Institusi',
            description: 'Kolaborasi dengan institusi pendidikan untuk program magang, riset, kegiatan kampus, hingga pengembangan aplikasi bersama.',
            points: [
                'Program magang & studi independen',
                'Kolaborasi riset dan project bersama',
                'Workshop & seminar teknologi',
            ],
            icon: GraduationCap,
            href: '/join/kerjasama',
            cta: 'Ajukan kerjasama',
            periode: periode,
        };
    });
});

// Mengamankan parameter agar menerima string/null/undefined tanpa crash
function isOpen(status: 'active' | 'nonactive' | string | null | undefined): boolean {
    return status === 'active';
}

function formatDate(date: string | null): string {
    if (!date) {
        return '—';
    }

    return new Date(date).toLocaleDateString('id-ID', {
        day: 'numeric',
        month: 'short',
        year: 'numeric',
    });
}
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
            <!-- Options -->
            <section class="scroll-mt-28 px-6 pb-24">
                <div class="mx-auto max-w-7xl">

                    <!-- Alert jika data kosong -->
                    <div v-if="options.length === 0"
                        class="flex flex-col items-center justify-center gap-3 rounded-[2rem] border border-dashed border-orange-200 bg-orange-50/50 px-6 py-16 text-center">
                        <div
                            class="flex h-14 w-14 items-center justify-center rounded-full bg-orange-100 text-orange-500">
                            <CalendarRange class="h-7 w-7" stroke-width="2" />
                        </div>
                        <p class="text-lg font-black text-slate-800">
                            Belum Ada Periode Pendaftaran
                        </p>
                        <p class="max-w-md text-sm leading-6 text-slate-500">
                            Saat ini belum ada data periode pendaftaran anggota maupun kerjasama yang tersedia untuk
                            ditampilkan. Silakan cek kembali nanti.
                        </p>
                    </div>

                    <!-- Grid jika data tersedia -->
                    <div v-else class="grid grid-cols-1 gap-6 md:grid-cols-3">
                        <article v-for="option in options" :key="option.id"
                            class="group relative flex flex-col rounded-[2rem] border border-orange-100 bg-white p-8 shadow-sm shadow-orange-100/50 transition duration-300 hover:-translate-y-1 hover:border-orange-200 hover:shadow-2xl hover:shadow-orange-900/10">

                            <!-- Badge status -->
                            <span v-if="option.periode"
                                class="absolute right-6 top-6 inline-flex items-center gap-1.5 rounded-full px-3 py-1 text-xs font-bold ring-1 ring-inset"
                                :class="isOpen(option.periode?.status)
                                    ? 'bg-emerald-50 text-emerald-700 ring-emerald-600/20'
                                    : 'bg-rose-50 text-rose-700 ring-rose-600/20'">
                                {{ isOpen(option.periode?.status) ? 'Dibuka' : 'Ditutup' }}
                            </span>

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

                            <div v-if="option.periode && (option.periode.tanggal_mulai || option.periode.tanggal_selesai)"
                                class="mt-4 flex items-center gap-2 rounded-xl bg-orange-50/70 px-3 py-2 text-xs font-semibold text-orange-700">
                                <CalendarRange class="h-3.5 w-3.5 shrink-0" />
                                <span>
                                    {{ formatDate(option.periode.tanggal_mulai) }} – {{
                                        formatDate(option.periode.tanggal_selesai) }}
                                </span>
                            </div>

                            <ul class="mt-6 flex-1 space-y-3">
                                <li v-for="point in option.points" :key="point"
                                    class="flex items-start gap-3 text-sm text-slate-600">
                                    <span class="mt-1.5 h-1.5 w-1.5 shrink-0 rounded-full bg-orange-500"></span>
                                    <span>{{ point }}</span>
                                </li>
                            </ul>

                            <Link v-if="isOpen(option.periode?.status)" :href="option.href"
                                class="mt-8 inline-flex items-center justify-center gap-2 rounded-2xl bg-gradient-to-r from-orange-500 to-amber-400 px-6 py-3.5 text-sm font-black text-white shadow-lg shadow-orange-500/25 transition duration-300 hover:-translate-y-0.5 hover:shadow-xl hover:shadow-orange-500/30">
                                {{ option.cta }}
                                <ArrowRight class="h-4 w-4 transition duration-300 group-hover:translate-x-1"
                                    stroke-width="2.5" />
                            </Link>
                            <div v-else
                                class="mt-8 inline-flex cursor-not-allowed items-center justify-center gap-2 rounded-2xl bg-slate-100 px-6 py-3.5 text-sm font-black text-slate-400">
                                <Lock class="h-4 w-4" />
                                Pendaftaran Ditutup
                            </div>
                        </article>
                    </div>
                </div>
            </section>
        </main>
    </PublicLayout>
</template>
