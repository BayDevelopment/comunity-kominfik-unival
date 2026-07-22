<script setup lang="ts">
import { Link, useForm } from '@inertiajs/vue3';
import {
    UserPlus,
    ArrowLeft,
    UploadCloud,
    FileText,
    Image as ImageIcon,
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
    nama: '',
    nim_nis: '',
    asal_instansi: '',
    jenjang: '' as '' | 'mahasiswa' | 'sma' | 'smk',
    jurusan_prodi: '',
    angkatan: '',
    email: '',
    no_telepon: '',
    alamat: '',
    alasan_bergabung: '',
    file_cv: null as File | null,
    foto: null as File | null,
});

const cvFileName = ref('');
const fotoFileName = ref('');

function onCvChange(e: Event) {
    const target = e.target as HTMLInputElement;
    const file = target.files?.[0] ?? null;
    form.file_cv = file;
    cvFileName.value = file ? file.name : '';
}

function onFotoChange(e: Event) {
    const target = e.target as HTMLInputElement;
    const file = target.files?.[0] ?? null;
    form.foto = file;
    fotoFileName.value = file ? file.name : '';
}

function submit() {
    if (!isPendaftaranOpen.value) {
        return;
    }

    form.post('/join/anggota', {
        forceFormData: true,
        preserveScroll: true,

        onSuccess: () => {
            // Kosongkan semua data form setelah berhasil disimpan
            form.reset();
            form.clearErrors();

            // Kosongkan nama file yang tampil
            cvFileName.value = '';
            fotoFileName.value = '';
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
                        Pendaftaran Anggota
                    </div>

                    <h1 class="text-4xl font-black leading-tight tracking-tight text-slate-950 md:text-5xl">
                        Formulir
                        <span class="bg-gradient-to-r from-orange-500 to-amber-500 bg-clip-text text-transparent">
                            Pendaftaran Anggota
                        </span>
                    </h1>

                    <p class="mx-auto mt-6 max-w-2xl text-lg leading-8 text-slate-600">
                        Lengkapi data dirimu di bawah ini untuk bergabung sebagai anggota KOMINFIK.
                    </p>

                    <div v-if="props.periode && (props.periode.tanggal_mulai || props.periode.tanggal_selesai)"
                        class="mx-auto mt-6 inline-flex items-center gap-2 rounded-full bg-orange-50/70 px-4 py-2 text-xs font-semibold text-orange-700">
                        <CalendarRange class="h-3.5 w-3.5 shrink-0" />
                        <span>
                            Periode pendaftaran: {{ formatDate(props.periode.tanggal_mulai) }} – {{
                                formatDate(props.periode.tanggal_selesai) }}
                        </span>
                    </div>
                </div>
            </section>

            <!-- Form -->
            <section class="px-6 pb-24">
                <div class="mx-auto max-w-3xl">
                    <!-- Pendaftaran ditutup -->
                    <div v-if="!isPendaftaranOpen"
                        class="flex flex-col items-center rounded-[2rem] border border-orange-100 bg-orange-50/60 p-12 text-center shadow-sm shadow-orange-100/50">
                        <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-slate-200 text-slate-500">
                            <Lock class="h-7 w-7" stroke-width="2.4" />
                        </div>
                        <h2 class="mt-6 text-xl font-black text-slate-950">Pendaftaran Anggota Ditutup</h2>
                        <p class="mt-3 max-w-md text-sm leading-6 text-slate-500">
                            Saat ini periode pendaftaran anggota belum dibuka atau sudah berakhir. Pantau terus
                            informasi terbaru untuk mengetahui jadwal pembukaan berikutnya.
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
                            Pendaftaranmu berhasil dikirim! Tim kami akan meninjau data kamu dan menghubungi
                            melalui email yang terdaftar.
                        </div>
                    </div>

                    <form v-if="isPendaftaranOpen" @submit.prevent="submit"
                        class="relative rounded-[2rem] border border-orange-100 bg-white p-8 shadow-sm shadow-orange-100/50 md:p-10">
                        <div class="flex items-center gap-4 border-b border-orange-100 pb-6">
                            <div
                                class="flex h-12 w-12 items-center justify-center rounded-2xl bg-gradient-to-br from-orange-500 to-amber-400 text-white shadow-lg shadow-orange-500/20">
                                <UserPlus class="h-6 w-6" stroke-width="2.4" />
                            </div>
                            <div>
                                <p class="text-xs font-black uppercase tracking-widest text-orange-600">Individu</p>
                                <h2 class="text-xl font-black text-slate-950">Data Pendaftar</h2>
                            </div>
                        </div>

                        <!-- Honeypot: field jebakan bot, disembunyikan dari manusia lewat CSS -->
                        <div class="absolute -left-[9999px] top-0" aria-hidden="true">
                            <label for="website">Jangan diisi</label>
                            <input id="website" v-model="form.website" type="text" tabindex="-1" autocomplete="off" />
                        </div>

                        <div class="mt-8 grid grid-cols-1 gap-6 md:grid-cols-2">
                            <!-- Nama -->
                            <div class="md:col-span-2">
                                <label class="mb-2 block text-sm font-bold text-slate-800">Nama Lengkap <span class="text-rose-600" aria-hidden="true">*</span></label>
                                <input v-model="form.nama" type="text" placeholder="Nama sesuai identitas" required
                                    class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-orange-400 focus:ring-4 focus:ring-orange-100" />
                                <p v-if="form.errors.nama" class="mt-1.5 text-xs font-semibold text-rose-600">{{
                                    form.errors.nama }}</p>
                            </div>

                            <!-- NIM/NIS -->
                            <div>
                                <label class="mb-2 block text-sm font-bold text-slate-800">NIM / NIS <span class="text-rose-600" aria-hidden="true">*</span></label>
                                <input v-model="form.nim_nis" type="text" placeholder="Nomor induk" required
                                    class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-orange-400 focus:ring-4 focus:ring-orange-100" />
                                <p v-if="form.errors.nim_nis" class="mt-1.5 text-xs font-semibold text-rose-600">{{
                                    form.errors.nim_nis }}</p>
                            </div>

                            <!-- Jenjang -->
                            <div>
                                <label class="mb-2 block text-sm font-bold text-slate-800">Jenjang <span class="text-rose-600" aria-hidden="true">*</span></label>
                                <select v-model="form.jenjang" required
                                    class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-orange-400 focus:ring-4 focus:ring-orange-100">
                                    <option value="" disabled>Pilih jenjang</option>
                                    <option value="mahasiswa">Mahasiswa</option>
                                    <option value="sma">SMA</option>
                                    <option value="smk">SMK</option>
                                </select>
                                <p v-if="form.errors.jenjang" class="mt-1.5 text-xs font-semibold text-rose-600">{{
                                    form.errors.jenjang }}</p>
                            </div>

                            <!-- Asal Instansi -->
                            <div>
                                <label class="mb-2 block text-sm font-bold text-slate-800">Asal Instansi <span class="text-rose-600" aria-hidden="true">*</span></label>
                                <input v-model="form.asal_instansi" type="text" placeholder="Nama kampus/sekolah" required
                                    class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-orange-400 focus:ring-4 focus:ring-orange-100" />
                                <p v-if="form.errors.asal_instansi" class="mt-1.5 text-xs font-semibold text-rose-600">
                                    {{ form.errors.asal_instansi }}</p>
                            </div>

                            <!-- Jurusan/Prodi -->
                            <div>
                                <label class="mb-2 block text-sm font-bold text-slate-800">Jurusan / Prodi</label>
                                <input v-model="form.jurusan_prodi" type="text" placeholder="Opsional"
                                    class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-orange-400 focus:ring-4 focus:ring-orange-100" />
                                <p v-if="form.errors.jurusan_prodi" class="mt-1.5 text-xs font-semibold text-rose-600">
                                    {{ form.errors.jurusan_prodi }}</p>
                            </div>

                            <!-- Angkatan -->
                            <div>
                                <label class="mb-2 block text-sm font-bold text-slate-800">Angkatan</label>
                                <input v-model="form.angkatan" type="text" placeholder="Opsional, contoh: 2023"
                                    class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-orange-400 focus:ring-4 focus:ring-orange-100" />
                                <p v-if="form.errors.angkatan" class="mt-1.5 text-xs font-semibold text-rose-600">{{
                                    form.errors.angkatan }}</p>
                            </div>

                            <!-- Email -->
                            <div>
                                <label class="mb-2 block text-sm font-bold text-slate-800">Email <span class="text-rose-600" aria-hidden="true">*</span></label>
                                <input v-model="form.email" type="email" placeholder="nama@email.com" required
                                    class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-orange-400 focus:ring-4 focus:ring-orange-100" />
                                <p v-if="form.errors.email" class="mt-1.5 text-xs font-semibold text-rose-600">{{
                                    form.errors.email }}</p>
                            </div>

                            <!-- No Telepon -->
                            <div>
                                <label class="mb-2 block text-sm font-bold text-slate-800">No. Telepon <span class="text-rose-600" aria-hidden="true">*</span></label>
                                <input v-model="form.no_telepon" type="text" placeholder="08xxxxxxxxxx" required
                                    class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-orange-400 focus:ring-4 focus:ring-orange-100" />
                                <p v-if="form.errors.no_telepon" class="mt-1.5 text-xs font-semibold text-rose-600">{{
                                    form.errors.no_telepon }}</p>
                            </div>

                            <!-- Alamat -->
                            <div class="md:col-span-2">
                                <label class="mb-2 block text-sm font-bold text-slate-800">Alamat</label>
                                <textarea v-model="form.alamat" rows="3" placeholder="Opsional"
                                    class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-orange-400 focus:ring-4 focus:ring-orange-100"></textarea>
                                <p v-if="form.errors.alamat" class="mt-1.5 text-xs font-semibold text-rose-600">{{
                                    form.errors.alamat }}</p>
                            </div>

                            <!-- Alasan Bergabung -->
                            <div class="md:col-span-2">
                                <label class="mb-2 block text-sm font-bold text-slate-800">Alasan Bergabung</label>
                                <textarea v-model="form.alasan_bergabung" rows="4"
                                    placeholder="Ceritakan alasan dan motivasimu bergabung dengan KOMINFIK"
                                    class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-orange-400 focus:ring-4 focus:ring-orange-100"></textarea>
                                <p v-if="form.errors.alasan_bergabung"
                                    class="mt-1.5 text-xs font-semibold text-rose-600">{{ form.errors.alasan_bergabung
                                    }}</p>
                            </div>

                            <!-- File CV -->
                            <div>
                                <label class="mb-2 block text-sm font-bold text-slate-800">Upload CV</label>
                                <label
                                    class="flex cursor-pointer items-center gap-3 rounded-xl border border-dashed border-orange-200 bg-orange-50/50 px-4 py-3.5 text-sm font-semibold text-orange-700 transition hover:bg-orange-50">
                                    <UploadCloud class="h-5 w-5 shrink-0" />
                                    <span class="truncate">{{ cvFileName || 'Pilih file PDF/DOC (opsional)' }}</span>
                                    <input type="file" class="hidden" accept=".pdf,.doc,.docx" @change="onCvChange" />
                                </label>
                                <p class="mt-1.5 text-xs text-slate-400">Format: PDF, DOC, DOCX • Maks. 1 MB</p>
                                <p v-if="cvFileName" class="mt-1.5 flex items-center gap-1 text-xs text-slate-500">
                                    <FileText class="h-3.5 w-3.5" /> {{ cvFileName }}
                                </p>
                                <p v-if="form.errors.file_cv" class="mt-1.5 text-xs font-semibold text-rose-600">{{
                                    form.errors.file_cv }}</p>
                            </div>

                            <!-- Foto -->
                            <div>
                                <label class="mb-2 block text-sm font-bold text-slate-800">Upload Foto</label>
                                <label
                                    class="flex cursor-pointer items-center gap-3 rounded-xl border border-dashed border-orange-200 bg-orange-50/50 px-4 py-3.5 text-sm font-semibold text-orange-700 transition hover:bg-orange-50">
                                    <UploadCloud class="h-5 w-5 shrink-0" />
                                    <span class="truncate">{{ fotoFileName || 'Pilih file foto (opsional)' }}</span>
                                    <input type="file" class="hidden" accept="image/jpeg,image/png,image/webp"
                                        @change="onFotoChange" />
                                </label>
                                <p class="mt-1.5 text-xs text-slate-400">Format: JPG, PNG • Maks. 1 MB</p>
                                <p v-if="fotoFileName" class="mt-1.5 flex items-center gap-1 text-xs text-slate-500">
                                    <ImageIcon class="h-3.5 w-3.5" /> {{ fotoFileName }}
                                </p>
                                <p v-if="form.errors.foto" class="mt-1.5 text-xs font-semibold text-rose-600">{{
                                    form.errors.foto }}</p>
                            </div>

                        </div>

                        <button type="submit" :disabled="form.processing"
                            class="mt-10 inline-flex w-full items-center justify-center gap-2 rounded-2xl bg-gradient-to-r from-orange-500 to-amber-400 px-6 py-4 text-sm font-black text-white shadow-lg shadow-orange-500/25 transition duration-300 hover:-translate-y-0.5 hover:shadow-xl hover:shadow-orange-500/30 disabled:cursor-not-allowed disabled:opacity-60 disabled:hover:translate-y-0">
                            <Loader2 v-if="form.processing" class="h-4 w-4 animate-spin" />
                            {{ form.processing ? 'Mengirim...' : 'Kirim Pendaftaran' }}
                        </button>
                    </form>
                </div>
            </section>
        </main>
    </PublicLayout>
</template>
