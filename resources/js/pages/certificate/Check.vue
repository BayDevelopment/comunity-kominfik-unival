<script setup lang="ts">
import { useForm } from '@inertiajs/vue3';
import {
    Award,
    Search,
    Loader2,
    ShieldCheck,
    ShieldX,
    Download,
    CalendarDays,
    Hash,
    UserRound,
    BadgeCheck,
    XCircle,
} from 'lucide-vue-next';
import { computed } from 'vue';
import PublicLayout from '@/layouts/PublicLayout.vue';

interface CertificateResult {
    recipient_name: string;
    certificate_number: string;
    program_name: string | null;
    description: string | null;
    issued_at: string;
    status: 'draft' | 'published' | 'revoked';
    is_expired: boolean;
    verification_code: string;
}

interface SearchResult {
    found: boolean;
    data: CertificateResult | null;
}

const props = defineProps<{
    searchResult?: SearchResult | null;
}>();

const form = useForm({
    recipient_name: '',
    recipient_email: '',
});

function formatDate(date: string): string {
    return new Date(date).toLocaleDateString('id-ID', {
        day: 'numeric',
        month: 'long',
        year: 'numeric',
    });
}

function submitSearch() {
    form.post('/sertifikat/cari', {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => {
            // Berhasil mengambil respons pencarian dari server
        },
    });
}

function downloadUrl(code: string): string {
    return `/sertifikat/${code}/download`;
}

const found = computed(() => props.searchResult?.found === true && props.searchResult.data !== null);
const notFound = computed(() => props.searchResult?.found === false);
</script>

<template>
    <PublicLayout>
        <main class="overflow-hidden bg-white">
            <!-- Hero -->
            <section class="relative bg-gradient-to-b from-orange-50 via-white to-white">
                <div class="absolute -left-24 top-20 h-72 w-72 rounded-full bg-orange-200/50 blur-3xl"></div>
                <div class="absolute -right-24 top-10 h-80 w-80 rounded-full bg-amber-200/50 blur-3xl"></div>

                <div class="relative mx-auto max-w-3xl px-6 py-16 text-center lg:py-20">
                    <div
                        class="mb-5 inline-flex items-center gap-2 rounded-full border border-orange-100 bg-white px-4 py-2 text-sm font-bold text-orange-700 shadow-sm shadow-orange-100/60">
                        <span class="h-2 w-2 rounded-full bg-orange-500"></span>
                        Verifikasi Sertifikat
                    </div>

                    <h1 class="text-4xl font-black leading-tight tracking-tight text-slate-950 md:text-5xl">
                        Cetak dan Cek Keaslian
                        <span class="bg-gradient-to-r from-orange-500 to-amber-500 bg-clip-text text-transparent">
                            Sertifikat Pelatihan
                        </span>
                        Anda
                    </h1>

                    <p class="mx-auto mt-6 max-w-xl text-lg leading-8 text-slate-600">
                        Masukkan nama dan email yang digunakan saat pendaftaran untuk menemukan dan
                        mengunduh sertifikat resmi Anda.
                    </p>
                </div>
            </section>

            <!-- Search -->
            <section class="px-6 pb-24">
                <div class="mx-auto max-w-xl">
                    <form
                        @submit.prevent="submitSearch"
                        class="rounded-[2rem] border border-orange-100 bg-white p-8 shadow-sm shadow-orange-100/50 md:p-10"
                    >
                        <div class="flex items-center gap-4 border-b border-orange-100 pb-6">
                            <div
                                class="flex h-12 w-12 items-center justify-center rounded-2xl bg-gradient-to-br from-orange-500 to-amber-400 text-white shadow-lg shadow-orange-500/20"
                            >
                                <Award class="h-6 w-6" stroke-width="2.4" />
                            </div>
                            <div>
                                <p class="text-xs font-black uppercase tracking-widest text-orange-600">Pencarian</p>
                                <h2 class="text-xl font-black text-slate-950">Temukan Sertifikat Anda</h2>
                            </div>
                        </div>

                        <div class="mt-8 space-y-6">
                            <div>
                                <label class="mb-2 block text-sm font-bold text-slate-800">
                                    Nama Lengkap <span class="text-rose-600">*</span>
                                </label>
                                <input
                                    v-model="form.recipient_name"
                                    type="text"
                                    required
                                    placeholder="Sesuai nama saat pendaftaran"
                                    class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-orange-400 focus:ring-4 focus:ring-orange-100"
                                />
                                <p v-if="form.errors.recipient_name" class="mt-1.5 text-xs font-semibold text-rose-600">
                                    {{ form.errors.recipient_name }}
                                </p>
                            </div>

                            <div>
                                <label class="mb-2 block text-sm font-bold text-slate-800">
                                    Email <span class="text-rose-600">*</span>
                                </label>
                                <input
                                    v-model="form.recipient_email"
                                    type="email"
                                    required
                                    placeholder="nama@email.com"
                                    class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-orange-400 focus:ring-4 focus:ring-orange-100"
                                />
                                <p v-if="form.errors.recipient_email" class="mt-1.5 text-xs font-semibold text-rose-600">
                                    {{ form.errors.recipient_email }}
                                </p>
                            </div>
                        </div>

                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="mt-8 inline-flex w-full items-center justify-center gap-2 rounded-2xl bg-gradient-to-r from-orange-500 to-amber-400 px-6 py-4 text-sm font-black text-white shadow-lg shadow-orange-500/25 transition duration-300 hover:-translate-y-0.5 hover:shadow-xl hover:shadow-orange-500/30 disabled:cursor-not-allowed disabled:opacity-60 disabled:hover:translate-y-0"
                        >
                            <Loader2 v-if="form.processing" class="h-4 w-4 animate-spin" />
                            <Search v-else class="h-4 w-4" />
                            {{ form.processing ? 'Mencari...' : 'Cari Sertifikat' }}
                        </button>
                    </form>

                    <!-- Hasil: ditemukan & valid -->
                    <div
                        v-if="found && searchResult?.data && searchResult.data.status === 'published' && !searchResult.data.is_expired"
                        class="mt-8 overflow-hidden rounded-[2rem] border border-emerald-100 bg-emerald-50/60 shadow-sm shadow-emerald-100/50"
                    >
                        <div class="flex items-center gap-3 border-b border-emerald-100 bg-emerald-50 px-8 py-5">
                            <ShieldCheck class="h-5 w-5 text-emerald-600" />
                            <p class="text-sm font-black text-emerald-800">Sertifikat Valid & Terverifikasi</p>
                        </div>

                        <div class="space-y-4 px-8 py-6">
                            <div class="flex items-start gap-3">
                                <UserRound class="mt-0.5 h-4 w-4 shrink-0 text-slate-400" />
                                <div>
                                    <p class="text-xs font-bold uppercase tracking-wide text-slate-400">Nama Penerima</p>
                                    <p class="text-sm font-bold text-slate-900">{{ searchResult.data.recipient_name }}</p>
                                </div>
                            </div>

                            <div v-if="searchResult.data.program_name" class="flex items-start gap-3">
                                <BadgeCheck class="mt-0.5 h-4 w-4 shrink-0 text-slate-400" />
                                <div>
                                    <p class="text-xs font-bold uppercase tracking-wide text-slate-400">Program</p>
                                    <p class="text-sm font-bold text-slate-900">{{ searchResult.data.program_name }}</p>
                                </div>
                            </div>

                            <div class="flex items-start gap-3">
                                <Hash class="mt-0.5 h-4 w-4 shrink-0 text-slate-400" />
                                <div>
                                    <p class="text-xs font-bold uppercase tracking-wide text-slate-400">Nomor Sertifikat</p>
                                    <p class="text-sm font-bold text-slate-900">{{ searchResult.data.certificate_number }}</p>
                                </div>
                            </div>

                            <div class="flex items-start gap-3">
                                <CalendarDays class="mt-0.5 h-4 w-4 shrink-0 text-slate-400" />
                                <div>
                                    <p class="text-xs font-bold uppercase tracking-wide text-slate-400">Diterbitkan</p>
                                    <p class="text-sm font-bold text-slate-900">{{ formatDate(searchResult.data.issued_at) }}</p>
                                </div>
                            </div>

                            <p v-if="searchResult.data.description" class="pt-2 text-sm leading-6 text-slate-600">
                                {{ searchResult.data.description }}
                            </p>
                        </div>

                        <div class="border-t border-emerald-100 px-8 py-6">
                            <a
                                :href="downloadUrl(searchResult.data.verification_code)"
                                class="inline-flex w-full items-center justify-center gap-2 rounded-2xl bg-gradient-to-r from-orange-500 to-amber-400 px-6 py-4 text-sm font-black text-white shadow-lg shadow-orange-500/25 transition duration-300 hover:-translate-y-0.5 hover:shadow-xl hover:shadow-orange-500/30"
                            >
                                <Download class="h-4 w-4" />
                                Unduh Sertifikat (PDF)
                            </a>
                        </div>
                    </div>

                    <!-- Hasil: sertifikat dicabut/expired -->
                    <div
                        v-else-if="found && searchResult?.data && (searchResult.data.status === 'revoked' || searchResult.data.is_expired)"
                        class="mt-8 flex flex-col items-center rounded-[2rem] border border-rose-100 bg-rose-50/60 p-10 text-center shadow-sm shadow-rose-100/50"
                    >
                        <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-rose-100 text-rose-600">
                            <ShieldX class="h-7 w-7" stroke-width="2.4" />
                        </div>
                        <h3 class="mt-5 text-lg font-black text-slate-950">
                            {{ searchResult.data.status === 'revoked' ? 'Sertifikat Telah Dicabut' : 'Sertifikat Telah Kedaluwarsa' }}
                        </h3>
                        <p class="mt-2 max-w-sm text-sm leading-6 text-slate-500">
                            Nomor sertifikat {{ searchResult.data.certificate_number }} tidak lagi berlaku. Hubungi panitia
                            jika Anda merasa ini keliru.
                        </p>
                    </div>

                    <!-- Hasil: tidak ditemukan -->
                    <div
                        v-else-if="notFound"
                        class="mt-8 flex flex-col items-center rounded-[2rem] border border-slate-100 bg-slate-50 p-10 text-center shadow-sm"
                    >
                        <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-slate-200 text-slate-500">
                            <XCircle class="h-7 w-7" stroke-width="2.4" />
                        </div>
                        <h3 class="mt-5 text-lg font-black text-slate-950">Sertifikat Tidak Ditemukan</h3>
                        <p class="mt-2 max-w-sm text-sm leading-6 text-slate-500">
                            Pastikan nama dan email sesuai dengan data saat pendaftaran. Coba periksa kembali
                            ejaan atau hubungi panitia jika masalah berlanjut.
                        </p>
                    </div>
                </div>
            </section>
        </main>
    </PublicLayout>
</template>