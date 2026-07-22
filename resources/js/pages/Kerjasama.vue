<script setup lang="ts">
import { Link, useForm } from '@inertiajs/vue3';
import {
    GraduationCap,
    ArrowLeft,
    UploadCloud,
    FileText,
    CheckCircle2,
    Loader2,
    Lock,
    CalendarRange,
} from 'lucide-vue-next';
import { computed, ref } from 'vue';
import PublicLayout from '@/layouts/PublicLayout.vue';

interface Periode {
    nama_periode: string | null;
    tanggal_mulai: string | null;
    tanggal_selesai: string | null;
    status: 'active' | 'nonactive';
}

const props = defineProps<{
    periode?: Periode | null;
}>();

// Periode dianggap terbuka jika: ada datanya, statusnya "active",
// dan tanggal hari ini masih berada di antara tanggal_mulai - tanggal_selesai (jika diisi).
const isPendaftaranOpen = computed(() => {
    const periode = props.periode;

    if (!periode || periode.status !== 'active') {
        return false;
    }

    const now = new Date();
    now.setHours(0, 0, 0, 0);

    if (periode.tanggal_mulai) {
        const mulai = new Date(periode.tanggal_mulai);
        mulai.setHours(0, 0, 0, 0);

        if (now < mulai) {
            return false;
        }
    }

    if (periode.tanggal_selesai) {
        const selesai = new Date(periode.tanggal_selesai);
        selesai.setHours(0, 0, 0, 0);

        if (now > selesai) {
            return false;
        }
    }

    return true;
});

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

const form = useForm({
    // Honeypot anti-bot: TIDAK BOLEH diisi manusia. Kalau field ini terisi
    // saat submit, controller akan menolak request (kemungkinan besar bot).
    website: '',
    jenis_instansi: '' as '' | 'kampus' | 'sma' | 'smk' | 'perusahaan' | 'lainnya',
    nama_instansi: '',
    alamat: '',
    nama_pic: '',
    jabatan_pic: '',
    email_pic: '',
    no_hp_pic: '',
    jenis_kerjasama: '',
    deskripsi_kerjasama: '',
    file_proposal: null as File | null,
    file_mou: null as File | null,
});

const proposalFileName = ref('');
const mouFileName = ref('');

function onProposalChange(e: Event) {
    const target = e.target as HTMLInputElement;
    const file = target.files?.[0] ?? null;
    form.file_proposal = file;
    proposalFileName.value = file ? file.name : '';
}

function onMouChange(e: Event) {
    const target = e.target as HTMLInputElement;
    const file = target.files?.[0] ?? null;
    form.file_mou = file;
    mouFileName.value = file ? file.name : '';
}

function submit() {
    if (!isPendaftaranOpen.value) {
        return;
    }

    form.post('/join/kerjasama', {
        forceFormData: true,
        preserveScroll: true,

        onSuccess: () => {
            // Kosongkan semua data form setelah berhasil disimpan
            form.reset();
            form.clearErrors();

            // Kosongkan nama file yang tampil
            proposalFileName.value = '';
            mouFileName.value = '';
        },
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

                <div class="relative mx-auto max-w-4xl px-6 py-16 text-center lg:py-20">
                    <Link href="/join"
                        class="mb-6 inline-flex items-center gap-2 text-sm font-bold text-orange-700 hover:text-orange-800">
                        <ArrowLeft class="h-4 w-4" />
                        Kembali ke pilihan bergabung
                    </Link>

                    <div
                        class="mb-5 inline-flex items-center gap-2 rounded-full border border-orange-100 bg-white px-4 py-2 text-sm font-bold text-orange-700 shadow-sm shadow-orange-100/60">
                        <span class="h-2 w-2 rounded-full bg-orange-500"></span>
                        Pengajuan Kerjasama
                    </div>

                    <h1 class="text-4xl font-black leading-tight tracking-tight text-slate-950 md:text-5xl">
                        Formulir
                        <span class="bg-gradient-to-r from-orange-500 to-amber-500 bg-clip-text text-transparent">
                            Kerjasama Institusi
                        </span>
                    </h1>

                    <p class="mx-auto mt-6 max-w-2xl text-lg leading-8 text-slate-600">
                        Ajukan kolaborasi antara institusi kamu dengan KOMINFIK, mulai dari magang, riset,
                        hingga kegiatan bersama.
                    </p>

                    <div v-if="props.periode && (props.periode.tanggal_mulai || props.periode.tanggal_selesai)"
                        class="mx-auto mt-6 inline-flex items-center gap-2 rounded-full bg-orange-50/70 px-4 py-2 text-xs font-semibold text-orange-700">
                        <CalendarRange class="h-3.5 w-3.5 shrink-0" />
                        <span>
                            Periode pengajuan: {{ formatDate(props.periode.tanggal_mulai) }} – {{
                                formatDate(props.periode.tanggal_selesai) }}
                        </span>
                    </div>
                </div>
            </section>

            <!-- Form -->
            <section class="px-6 pb-24">
                <div class="mx-auto max-w-3xl">
                    <!-- Pengajuan ditutup -->
                    <div v-if="!isPendaftaranOpen"
                        class="flex flex-col items-center rounded-[2rem] border border-orange-100 bg-orange-50/60 p-12 text-center shadow-sm shadow-orange-100/50">
                        <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-slate-200 text-slate-500">
                            <Lock class="h-7 w-7" stroke-width="2.4" />
                        </div>
                        <h2 class="mt-6 text-xl font-black text-slate-950">Pengajuan Kerjasama Ditutup</h2>
                        <p class="mt-3 max-w-md text-sm leading-6 text-slate-500">
                            Saat ini periode pengajuan kerjasama belum dibuka atau sudah berakhir. Silakan
                            pantau informasi terbaru untuk mengetahui jadwal pembukaan berikutnya.
                        </p>
                        <Link href="/join"
                            class="mt-8 inline-flex items-center justify-center gap-2 rounded-2xl border border-orange-200 bg-white px-6 py-3 text-sm font-black text-orange-700 shadow-sm transition hover:-translate-y-0.5 hover:bg-orange-50">
                            <ArrowLeft class="h-4 w-4" />
                            Kembali ke Halaman Bergabung
                        </Link>
                    </div>

                    <div v-if="form.recentlySuccessful"
                        class="mb-6 flex items-start gap-3 rounded-2xl border border-emerald-100 bg-emerald-50 p-5 text-emerald-800">
                        <CheckCircle2 class="h-5 w-5 shrink-0" />
                        <div class="text-sm font-semibold">
                            Pengajuan kerjasama berhasil dikirim! Tim kami akan meninjau pengajuan dan
                            menghubungi PIC yang tercantum.
                        </div>
                    </div>

                    <form v-if="isPendaftaranOpen" @submit.prevent="submit"
                        class="relative rounded-[2rem] border border-orange-100 bg-white p-8 shadow-sm shadow-orange-100/50 md:p-10">
                        <!-- Honeypot: field jebakan bot, disembunyikan dari manusia lewat CSS -->
                        <div class="absolute -left-[9999px] top-0" aria-hidden="true">
                            <label for="website">Jangan diisi</label>
                            <input id="website" v-model="form.website" type="text" tabindex="-1" autocomplete="off" />
                        </div>

                        <div class="flex items-center gap-4 border-b border-orange-100 pb-6">
                            <div
                                class="flex h-12 w-12 items-center justify-center rounded-2xl bg-gradient-to-br from-orange-500 to-amber-400 text-white shadow-lg shadow-orange-500/20">
                                <GraduationCap class="h-6 w-6" stroke-width="2.4" />
                            </div>
                            <div>
                                <p class="text-xs font-black uppercase tracking-widest text-orange-600">Institusi</p>
                                <h2 class="text-xl font-black text-slate-950">Data Instansi & PIC</h2>
                            </div>
                        </div>

                        <div class="mt-8 grid grid-cols-1 gap-6 md:grid-cols-2">
                            <!-- Jenis Instansi -->
                            <div>
                                <label class="mb-2 block text-sm font-bold text-slate-800">
                                    Jenis Instansi <span class="text-rose-600">*</span>
                                </label>
                                <select v-model="form.jenis_instansi" required
                                    class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-orange-400 focus:ring-4 focus:ring-orange-100">
                                    <option value="" disabled>Pilih jenis instansi</option>
                                    <option value="kampus">Kampus</option>
                                    <option value="sma">SMA</option>
                                    <option value="smk">SMK</option>
                                    <option value="perusahaan">Perusahaan</option>
                                    <option value="lainnya">Lainnya</option>
                                </select>
                                <p v-if="form.errors.jenis_instansi" class="mt-1.5 text-xs font-semibold text-rose-600">
                                    {{ form.errors.jenis_instansi }}</p>
                            </div>

                            <!-- Nama Instansi -->
                            <div>
                                <label class="mb-2 block text-sm font-bold text-slate-800">
                                    Nama Instansi <span class="text-rose-600">*</span>
                                </label>
                                <input v-model="form.nama_instansi" type="text" required
                                    placeholder="Nama institusi/perusahaan"
                                    class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-orange-400 focus:ring-4 focus:ring-orange-100" />
                                <p v-if="form.errors.nama_instansi" class="mt-1.5 text-xs font-semibold text-rose-600">
                                    {{ form.errors.nama_instansi }}</p>
                            </div>

                            <!-- Alamat -->
                            <div class="md:col-span-2">
                                <label class="mb-2 block text-sm font-bold text-slate-800">Alamat Instansi</label>
                                <textarea v-model="form.alamat" rows="3" placeholder="Opsional"
                                    class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-orange-400 focus:ring-4 focus:ring-orange-100"></textarea>
                                <p v-if="form.errors.alamat" class="mt-1.5 text-xs font-semibold text-rose-600">{{
                                    form.errors.alamat }}</p>
                            </div>

                            <!-- Nama PIC -->
                            <div>
                                <label class="mb-2 block text-sm font-bold text-slate-800">
                                    Nama PIC <span class="text-rose-600">*</span>
                                </label>
                                <input v-model="form.nama_pic" type="text" required placeholder="Nama penanggung jawab"
                                    class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-orange-400 focus:ring-4 focus:ring-orange-100" />
                                <p v-if="form.errors.nama_pic" class="mt-1.5 text-xs font-semibold text-rose-600">{{
                                    form.errors.nama_pic }}</p>
                            </div>

                            <!-- Jabatan PIC -->
                            <div>
                                <label class="mb-2 block text-sm font-bold text-slate-800">Jabatan PIC</label>
                                <input v-model="form.jabatan_pic" type="text" placeholder="Opsional"
                                    class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-orange-400 focus:ring-4 focus:ring-orange-100" />
                                <p v-if="form.errors.jabatan_pic" class="mt-1.5 text-xs font-semibold text-rose-600">{{
                                    form.errors.jabatan_pic }}</p>
                            </div>

                            <!-- Email PIC -->
                            <div>
                                <label class="mb-2 block text-sm font-bold text-slate-800">
                                    Email PIC <span class="text-rose-600">*</span>
                                </label>
                                <input v-model="form.email_pic" type="email" required placeholder="nama@instansi.com"
                                    class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-orange-400 focus:ring-4 focus:ring-orange-100" />
                                <p v-if="form.errors.email_pic" class="mt-1.5 text-xs font-semibold text-rose-600">{{
                                    form.errors.email_pic }}</p>
                            </div>

                            <!-- No HP PIC -->
                            <div>
                                <label class="mb-2 block text-sm font-bold text-slate-800">
                                    No. HP PIC <span class="text-rose-600">*</span>
                                </label>
                                <input v-model="form.no_hp_pic" type="text" required placeholder="08xxxxxxxxxx"
                                    class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-orange-400 focus:ring-4 focus:ring-orange-100" />
                                <p v-if="form.errors.no_hp_pic" class="mt-1.5 text-xs font-semibold text-rose-600">{{
                                    form.errors.no_hp_pic }}</p>
                            </div>

                            <!-- Jenis Kerjasama -->
                            <div>
                                <label class="mb-2 block text-sm font-bold text-slate-800">Jenis Kerjasama</label>
                                <input v-model="form.jenis_kerjasama" type="text"
                                    placeholder="Contoh: Magang, Riset, Studi Independen"
                                    class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-orange-400 focus:ring-4 focus:ring-orange-100" />
                                <p v-if="form.errors.jenis_kerjasama"
                                    class="mt-1.5 text-xs font-semibold text-rose-600">{{ form.errors.jenis_kerjasama }}
                                </p>
                            </div>

                            <!-- Deskripsi Kerjasama -->
                            <div class="md:col-span-2">
                                <label class="mb-2 block text-sm font-bold text-slate-800">Deskripsi Kerjasama</label>
                                <textarea v-model="form.deskripsi_kerjasama" rows="4"
                                    placeholder="Jelaskan rencana atau lingkup kerjasama yang diajukan"
                                    class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-orange-400 focus:ring-4 focus:ring-orange-100"></textarea>
                                <p v-if="form.errors.deskripsi_kerjasama"
                                    class="mt-1.5 text-xs font-semibold text-rose-600">{{
                                        form.errors.deskripsi_kerjasama }}</p>
                            </div>

                            <!-- File Proposal -->
                            <div>
                                <label class="mb-2 block text-sm font-bold text-slate-800">Upload Proposal</label>
                                <label
                                    class="flex cursor-pointer items-center gap-3 rounded-xl border border-dashed border-orange-200 bg-orange-50/50 px-4 py-3.5 text-sm font-semibold text-orange-700 transition hover:bg-orange-50">
                                    <UploadCloud class="h-5 w-5 shrink-0" />
                                    <span class="truncate">{{ proposalFileName || 'Pilih file PDF/DOC (opsional)'
                                        }}</span>
                                    <input type="file" class="hidden" accept=".pdf,.doc,.docx"
                                        @change="onProposalChange" />
                                </label>
                                <p class="mt-1.5 text-xs text-slate-400">Format: PDF, DOC, DOCX • Maks. 5 MB</p>
                                <p v-if="proposalFileName"
                                    class="mt-1.5 flex items-center gap-1 text-xs text-slate-500">
                                    <FileText class="h-3.5 w-3.5" /> {{ proposalFileName }}
                                </p>
                                <p v-if="form.errors.file_proposal" class="mt-1.5 text-xs font-semibold text-rose-600">
                                    {{ form.errors.file_proposal }}</p>
                            </div>

                            <!-- File MoU -->
                            <div>
                                <label class="mb-2 block text-sm font-bold text-slate-800">Upload Draft MoU</label>
                                <label
                                    class="flex cursor-pointer items-center gap-3 rounded-xl border border-dashed border-orange-200 bg-orange-50/50 px-4 py-3.5 text-sm font-semibold text-orange-700 transition hover:bg-orange-50">
                                    <UploadCloud class="h-5 w-5 shrink-0" />
                                    <span class="truncate">{{ mouFileName || 'Pilih file PDF/DOC (opsional)' }}</span>
                                    <input type="file" class="hidden" accept=".pdf,.doc,.docx" @change="onMouChange" />
                                </label>
                                <p class="mt-1.5 text-xs text-slate-400">Format: PDF, DOC, DOCX • Maks. 5 MB</p>
                                <p v-if="mouFileName" class="mt-1.5 flex items-center gap-1 text-xs text-slate-500">
                                    <FileText class="h-3.5 w-3.5" /> {{ mouFileName }}
                                </p>
                                <p v-if="form.errors.file_mou" class="mt-1.5 text-xs font-semibold text-rose-600">{{
                                    form.errors.file_mou }}</p>
                            </div>

                        </div>

                        <button type="submit" :disabled="form.processing"
                            class="mt-10 inline-flex w-full items-center justify-center gap-2 rounded-2xl bg-gradient-to-r from-orange-500 to-amber-400 px-6 py-4 text-sm font-black text-white shadow-lg shadow-orange-500/25 transition duration-300 hover:-translate-y-0.5 hover:shadow-xl hover:shadow-orange-500/30 disabled:cursor-not-allowed disabled:opacity-60 disabled:hover:translate-y-0">
                            <Loader2 v-if="form.processing" class="h-4 w-4 animate-spin" />
                            {{ form.processing ? 'Mengirim...' : 'Ajukan Kerjasama' }}
                        </button>
                    </form>
                </div>
            </section>
        </main>
    </PublicLayout>
</template>
