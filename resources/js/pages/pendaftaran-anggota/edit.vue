<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import {
    UserPlus,
    ArrowLeft,
    Loader2,
    Upload,
    FileText,
    X,
    ShieldCheck,
} from 'lucide-vue-next';
import { ref } from 'vue';
import { dashboard } from '@/routes';

interface PendaftaranAnggota {
    id: number;
    nama: string;
    nim_nis: string;
    asal_instansi: string;
    jenjang: 'mahasiswa' | 'sma' | 'smk';
    jurusan_prodi: string | null;
    angkatan: string | null;
    email: string;
    no_telepon: string;
    alamat: string | null;
    alasan_bergabung: string | null;
    file_cv: string | null;
    foto: string | null;
    status: 'pending' | 'diterima' | 'ditolak';
    catatan_admin: string | null;
}

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Dashboard',
                href: dashboard(),
            },
            {
                title: 'Pendaftaran Anggota',
                href: '/pendaftaran-anggota',
            },
            {
                title: 'Edit Pendaftar',
                href: '/pendaftaran-anggota', // statis, tanpa id
            },
        ],
    },
});

const props = defineProps<{
    pendaftaran: PendaftaranAnggota;
}>();

const form = useForm({
    _method: 'put',
    nama: props.pendaftaran.nama,
    nim_nis: props.pendaftaran.nim_nis,
    asal_instansi: props.pendaftaran.asal_instansi,
    jenjang: props.pendaftaran.jenjang,
    jurusan_prodi: props.pendaftaran.jurusan_prodi ?? '',
    angkatan: props.pendaftaran.angkatan ?? '',
    email: props.pendaftaran.email,
    no_telepon: props.pendaftaran.no_telepon,
    alamat: props.pendaftaran.alamat ?? '',
    alasan_bergabung: props.pendaftaran.alasan_bergabung ?? '',
    status: props.pendaftaran.status,
    catatan_admin: props.pendaftaran.catatan_admin ?? '',
    // ⬇️ FIELD BARU — wajib diisi kalau status = diterima
    jabatan: '',
    divisi: '',
    tanggal_bergabung: '',
    file_cv: null as File | null,
    foto: null as File | null,
});

const fotoPreview = ref<string | null>(
    props.pendaftaran.foto ? `/storage/${props.pendaftaran.foto}` : null,
);
const fotoInput = ref<HTMLInputElement | null>(null);
const cvInput = ref<HTMLInputElement | null>(null);

function onFotoChange(e: Event) {
    const file = (e.target as HTMLInputElement).files?.[0] ?? null;
    form.foto = file;
    fotoPreview.value = file ? URL.createObjectURL(file) : null;
}

function onCvChange(e: Event) {
    const file = (e.target as HTMLInputElement).files?.[0] ?? null;
    form.file_cv = file;
}

function removeFoto() {
    form.foto = null;
    fotoPreview.value = null;

    if (fotoInput.value) {
        fotoInput.value.value = '';
    }
}

function removeCv() {
    form.file_cv = null;

    if (cvInput.value) {
        cvInput.value.value = '';
    }
}

const statusOptions: { value: PendaftaranAnggota['status']; label: string }[] = [
    { value: 'pending', label: 'Pending' },
    { value: 'diterima', label: 'Diterima' },
    { value: 'ditolak', label: 'Ditolak' },
];

function submit() {
    form.post(`/pendaftaran-anggota/${props.pendaftaran.id}`, {
        forceFormData: true,
        preserveScroll: true,
    });
}
</script>

<template>

    <Head title="Edit Pendaftar" />

    <div class="space-y-6 p-6">
        <!-- Header -->
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center gap-3">
                <Link href="/pendaftaran-anggota"
                    class="flex h-9 w-9 items-center justify-center rounded-lg border transition-colors hover:bg-muted">
                    <ArrowLeft class="h-4 w-4" />
                </Link>
                <div>
                    <h1 class="text-2xl font-bold">Edit Pendaftar</h1>
                    <p class="text-sm text-muted-foreground">
                        Perbarui data pendaftaran {{ pendaftaran.nama }}.
                    </p>
                </div>
            </div>
        </div>

        <form class="space-y-6" @submit.prevent="submit">
            <!-- ⬇️ BANNER ERROR UMUM -->
            <div v-if="Object.keys(form.errors).length > 0"
                class="rounded-lg border border-rose-300 bg-rose-50 p-4 dark:border-rose-500/30 dark:bg-rose-500/10">
                <p class="mb-2 text-sm font-semibold text-rose-700 dark:text-rose-400">
                    Gagal menyimpan, ada {{ Object.keys(form.errors).length }} error:
                </p>
                <ul class="list-disc space-y-1 pl-5 text-sm text-rose-600 dark:text-rose-400">
                    <li v-for="(msg, field) in form.errors" :key="field">
                        <strong>{{ field }}</strong>: {{ msg }}
                    </li>
                </ul>
            </div>
            <!-- ⬆️ SAMPAI SINI -->

            <!-- Data Diri -->
            <div class="rounded-xl border bg-background p-6 shadow-sm">
                <div class="mb-5 flex items-center gap-2">
                    <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-blue-50 dark:bg-blue-500/10">
                        <UserPlus class="h-4 w-4 text-blue-500" />
                    </div>
                    <div>
                        <h2 class="font-semibold">Data Diri</h2>
                        <p class="text-xs text-muted-foreground">
                            Informasi identitas pendaftar
                        </p>
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                    <div class="sm:col-span-2">
                        <label class="mb-1.5 block text-sm font-medium">Nama Lengkap <span
                                class="text-rose-500">*</span></label>
                        <input v-model="form.nama" type="text" placeholder="Masukkan nama lengkap"
                            class="w-full rounded-lg border bg-background px-3 py-2.5 text-sm outline-none transition-shadow focus:ring-2 focus:ring-ring"
                            :class="{ 'border-rose-500': form.errors.nama }" />
                        <p v-if="form.errors.nama" class="mt-1 text-xs text-rose-500">
                            {{ form.errors.nama }}
                        </p>
                    </div>

                    <div>
                        <label class="mb-1.5 block text-sm font-medium">NIM / NIS <span
                                class="text-rose-500">*</span></label>
                        <input v-model="form.nim_nis" type="text" placeholder="Nomor induk"
                            class="w-full rounded-lg border bg-background px-3 py-2.5 text-sm outline-none transition-shadow focus:ring-2 focus:ring-ring"
                            :class="{ 'border-rose-500': form.errors.nim_nis }" />
                        <p v-if="form.errors.nim_nis" class="mt-1 text-xs text-rose-500">
                            {{ form.errors.nim_nis }}
                        </p>
                    </div>

                    <div>
                        <label class="mb-1.5 block text-sm font-medium">Jenjang <span
                                class="text-rose-500">*</span></label>
                        <select v-model="form.jenjang"
                            class="w-full rounded-lg border bg-background px-3 py-2.5 text-sm outline-none transition-shadow focus:ring-2 focus:ring-ring"
                            :class="{ 'border-rose-500': form.errors.jenjang }">
                            <option value="mahasiswa">Mahasiswa</option>
                            <option value="sma">SMA</option>
                            <option value="smk">SMK</option>
                        </select>
                        <p v-if="form.errors.jenjang" class="mt-1 text-xs text-rose-500">
                            {{ form.errors.jenjang }}
                        </p>
                    </div>

                    <div class="sm:col-span-2">
                        <label class="mb-1.5 block text-sm font-medium">Asal Instansi <span
                                class="text-rose-500">*</span></label>
                        <input v-model="form.asal_instansi" type="text" placeholder="Nama kampus / sekolah"
                            class="w-full rounded-lg border bg-background px-3 py-2.5 text-sm outline-none transition-shadow focus:ring-2 focus:ring-ring"
                            :class="{ 'border-rose-500': form.errors.asal_instansi }" />
                        <p v-if="form.errors.asal_instansi" class="mt-1 text-xs text-rose-500">
                            {{ form.errors.asal_instansi }}
                        </p>
                    </div>

                    <div>
                        <label class="mb-1.5 block text-sm font-medium">Jurusan / Prodi</label>
                        <input v-model="form.jurusan_prodi" type="text" placeholder="Contoh: Teknik Informatika"
                            class="w-full rounded-lg border bg-background px-3 py-2.5 text-sm outline-none transition-shadow focus:ring-2 focus:ring-ring"
                            :class="{ 'border-rose-500': form.errors.jurusan_prodi }" />
                        <p v-if="form.errors.jurusan_prodi" class="mt-1 text-xs text-rose-500">
                            {{ form.errors.jurusan_prodi }}
                        </p>
                    </div>

                    <div>
                        <label class="mb-1.5 block text-sm font-medium">Angkatan</label>
                        <input v-model="form.angkatan" type="text" placeholder="Contoh: 2023"
                            class="w-full rounded-lg border bg-background px-3 py-2.5 text-sm outline-none transition-shadow focus:ring-2 focus:ring-ring"
                            :class="{ 'border-rose-500': form.errors.angkatan }" />
                        <p v-if="form.errors.angkatan" class="mt-1 text-xs text-rose-500">
                            {{ form.errors.angkatan }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Kontak -->
            <div class="rounded-xl border bg-background p-6 shadow-sm">
                <div class="mb-5">
                    <h2 class="font-semibold">Kontak & Alamat</h2>
                    <p class="text-xs text-muted-foreground">
                        Informasi untuk menghubungi pendaftar
                    </p>
                </div>

                <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                    <div>
                        <label class="mb-1.5 block text-sm font-medium">Email <span
                                class="text-rose-500">*</span></label>
                        <input v-model="form.email" type="email" placeholder="nama@email.com"
                            class="w-full rounded-lg border bg-background px-3 py-2.5 text-sm outline-none transition-shadow focus:ring-2 focus:ring-ring"
                            :class="{ 'border-rose-500': form.errors.email }" />
                        <p v-if="form.errors.email" class="mt-1 text-xs text-rose-500">
                            {{ form.errors.email }}
                        </p>
                    </div>

                    <div>
                        <label class="mb-1.5 block text-sm font-medium">No. Telepon <span
                                class="text-rose-500">*</span></label>
                        <input v-model="form.no_telepon" type="text" placeholder="08xxxxxxxxxx"
                            class="w-full rounded-lg border bg-background px-3 py-2.5 text-sm outline-none transition-shadow focus:ring-2 focus:ring-ring"
                            :class="{ 'border-rose-500': form.errors.no_telepon }" />
                        <p v-if="form.errors.no_telepon" class="mt-1 text-xs text-rose-500">
                            {{ form.errors.no_telepon }}
                        </p>
                    </div>

                    <div class="sm:col-span-2">
                        <label class="mb-1.5 block text-sm font-medium">Alamat</label>
                        <textarea v-model="form.alamat" rows="3" placeholder="Alamat lengkap"
                            class="w-full rounded-lg border bg-background px-3 py-2.5 text-sm outline-none transition-shadow focus:ring-2 focus:ring-ring"
                            :class="{ 'border-rose-500': form.errors.alamat }"></textarea>
                        <p v-if="form.errors.alamat" class="mt-1 text-xs text-rose-500">
                            {{ form.errors.alamat }}
                        </p>
                    </div>

                    <div class="sm:col-span-2">
                        <label class="mb-1.5 block text-sm font-medium">Alasan Bergabung</label>
                        <textarea v-model="form.alasan_bergabung" rows="4"
                            placeholder="Ceritakan alasan ingin bergabung..."
                            class="w-full rounded-lg border bg-background px-3 py-2.5 text-sm outline-none transition-shadow focus:ring-2 focus:ring-ring"
                            :class="{ 'border-rose-500': form.errors.alasan_bergabung }"></textarea>
                        <p v-if="form.errors.alasan_bergabung" class="mt-1 text-xs text-rose-500">
                            {{ form.errors.alasan_bergabung }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Berkas -->
            <div class="rounded-xl border bg-background p-6 shadow-sm">
                <div class="mb-5">
                    <h2 class="font-semibold">Berkas Pendukung</h2>
                    <p class="text-xs text-muted-foreground">
                        Kosongkan jika tidak ingin mengganti berkas
                    </p>
                </div>

                <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                    <!-- Foto -->
                    <div>
                        <label class="mb-1.5 block text-sm font-medium">Foto</label>
                        <div v-if="fotoPreview"
                            class="relative flex items-center gap-3 rounded-lg border bg-muted/30 p-3">
                            <img :src="fotoPreview" alt="Preview foto" class="h-14 w-14 rounded-lg object-cover" />
                            <div class="flex-1 text-sm text-muted-foreground">
                                {{ form.foto?.name ?? 'Foto saat ini' }}
                            </div>
                            <button type="button"
                                class="rounded-md p-1.5 text-muted-foreground transition-colors hover:bg-rose-50 hover:text-rose-600 dark:hover:bg-rose-500/10"
                                @click="removeFoto">
                                <X class="h-4 w-4" />
                            </button>
                        </div>
                        <label v-else
                            class="flex cursor-pointer flex-col items-center justify-center gap-2 rounded-lg border border-dashed px-4 py-6 text-center transition-colors hover:bg-muted/40">
                            <Upload class="h-5 w-5 text-muted-foreground" />
                            <span class="text-sm text-muted-foreground">Klik untuk unggah foto</span>
                            <input ref="fotoInput" type="file" accept="image/*" class="hidden" @change="onFotoChange" />
                        </label>
                        <p v-if="form.errors.foto" class="mt-1 text-xs text-rose-500">
                            {{ form.errors.foto }}
                        </p>
                    </div>

                    <!-- CV -->
                    <div>
                        <label class="mb-1.5 block text-sm font-medium">File CV</label>
                        <div v-if="form.file_cv"
                            class="relative flex items-center gap-3 rounded-lg border bg-muted/30 p-3">
                            <div
                                class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-blue-50 dark:bg-blue-500/10">
                                <FileText class="h-5 w-5 text-blue-500" />
                            </div>
                            <div class="flex-1 truncate text-sm text-muted-foreground">
                                {{ form.file_cv.name }}
                            </div>
                            <button type="button"
                                class="rounded-md p-1.5 text-muted-foreground transition-colors hover:bg-rose-50 hover:text-rose-600 dark:hover:bg-rose-500/10"
                                @click="removeCv">
                                <X class="h-4 w-4" />
                            </button>
                        </div>
                        <div v-else-if="pendaftaran.file_cv"
                            class="relative flex items-center gap-3 rounded-lg border bg-muted/30 p-3">
                            <div
                                class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-blue-50 dark:bg-blue-500/10">
                                <FileText class="h-5 w-5 text-blue-500" />
                            </div>
                            <a :href="`/storage/${pendaftaran.file_cv}`" target="_blank"
                                class="flex-1 truncate text-sm text-primary underline-offset-2 hover:underline">
                                Lihat CV saat ini
                            </a>
                            <label
                                class="cursor-pointer rounded-md p-1.5 text-muted-foreground transition-colors hover:bg-muted">
                                <Upload class="h-4 w-4" />
                                <input ref="cvInput" type="file" accept=".pdf,.doc,.docx" class="hidden"
                                    @change="onCvChange" />
                            </label>
                        </div>
                        <label v-else
                            class="flex cursor-pointer flex-col items-center justify-center gap-2 rounded-lg border border-dashed px-4 py-6 text-center transition-colors hover:bg-muted/40">
                            <Upload class="h-5 w-5 text-muted-foreground" />
                            <span class="text-sm text-muted-foreground">Klik untuk unggah CV (PDF)</span>
                            <input ref="cvInput" type="file" accept=".pdf,.doc,.docx" class="hidden"
                                @change="onCvChange" />
                        </label>
                        <p v-if="form.errors.file_cv" class="mt-1 text-xs text-rose-500">
                            {{ form.errors.file_cv }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Status & Verifikasi (Admin) -->
            <div class="rounded-xl border bg-background p-6 shadow-sm">
                <div class="mb-5 flex items-center gap-2">
                    <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-amber-50 dark:bg-amber-500/10">
                        <ShieldCheck class="h-4 w-4 text-amber-500" />
                    </div>
                    <div>
                        <h2 class="font-semibold">Status & Verifikasi</h2>
                        <p class="text-xs text-muted-foreground">
                            Khusus admin — status pendaftaran dan catatan
                        </p>
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                    <div>
                        <label class="mb-1.5 block text-sm font-medium">Status</label>
                        <select v-model="form.status"
                            class="w-full rounded-lg border bg-background px-3 py-2.5 text-sm outline-none transition-shadow focus:ring-2 focus:ring-ring"
                            :class="{ 'border-rose-500': form.errors.status }">
                            <option v-for="opt in statusOptions" :key="opt.value" :value="opt.value">
                                {{ opt.label }}
                            </option>
                        </select>
                        <p v-if="form.errors.status" class="mt-1 text-xs text-rose-500">
                            {{ form.errors.status }}
                        </p>
                    </div>

                    <div class="sm:col-span-2">
                        <label class="mb-1.5 block text-sm font-medium">Catatan Admin</label>
                        <textarea v-model="form.catatan_admin" rows="3"
                            placeholder="Catatan internal terkait pendaftar ini..."
                            class="w-full rounded-lg border bg-background px-3 py-2.5 text-sm outline-none transition-shadow focus:ring-2 focus:ring-ring"
                            :class="{ 'border-rose-500': form.errors.catatan_admin }"></textarea>
                        <p v-if="form.errors.catatan_admin" class="mt-1 text-xs text-rose-500">
                            {{ form.errors.catatan_admin }}
                        </p>
                    </div>

                    <!-- ⬇️ FIELD KHUSUS SAAT STATUS "DITERIMA" -->
                    <template v-if="form.status === 'diterima'">
                        <div
                            class="sm:col-span-2 rounded-lg border border-amber-200 bg-amber-50 p-4 dark:border-amber-500/20 dark:bg-amber-500/10">
                            <p class="mb-4 text-xs font-medium text-amber-700 dark:text-amber-400">
                                Data ini wajib diisi karena pendaftar akan langsung dibuat menjadi Anggota
                            </p>

                            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                                <div>
                                    <label class="mb-1.5 block text-sm font-medium">Jabatan <span
                                            class="text-rose-500">*</span></label>
                                    <input v-model="form.jabatan" type="text" placeholder="Contoh: Staff"
                                        class="w-full rounded-lg border bg-background px-3 py-2.5 text-sm outline-none transition-shadow focus:ring-2 focus:ring-ring"
                                        :class="{ 'border-rose-500': form.errors.jabatan }" />
                                    <p v-if="form.errors.jabatan" class="mt-1 text-xs text-rose-500">
                                        {{ form.errors.jabatan }}
                                    </p>
                                </div>

                                <div>
                                    <label class="mb-1.5 block text-sm font-medium">Divisi <span
                                            class="text-rose-500">*</span></label>
                                    <input v-model="form.divisi" type="text" placeholder="Contoh: Humas"
                                        class="w-full rounded-lg border bg-background px-3 py-2.5 text-sm outline-none transition-shadow focus:ring-2 focus:ring-ring"
                                        :class="{ 'border-rose-500': form.errors.divisi }" />
                                    <p v-if="form.errors.divisi" class="mt-1 text-xs text-rose-500">
                                        {{ form.errors.divisi }}
                                    </p>
                                </div>

                                <div class="sm:col-span-2">
                                    <label class="mb-1.5 block text-sm font-medium">Tanggal Bergabung <span
                                            class="text-rose-500">*</span></label>
                                    <input v-model="form.tanggal_bergabung" type="date"
                                        class="w-full rounded-lg border bg-background px-3 py-2.5 text-sm outline-none transition-shadow focus:ring-2 focus:ring-ring"
                                        :class="{ 'border-rose-500': form.errors.tanggal_bergabung }" />
                                    <p v-if="form.errors.tanggal_bergabung" class="mt-1 text-xs text-rose-500">
                                        {{ form.errors.tanggal_bergabung }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </template>
                    <!-- ⬆️ SAMPAI SINI -->
                </div>
            </div>

            <!-- Actions -->
            <div class="flex items-center justify-end gap-3">
                <Link href="/pendaftaran-anggota"
                    class="rounded-lg border px-4 py-2.5 text-sm font-medium transition-colors hover:bg-muted">
                    Batal
                </Link>
                <button type="submit" :disabled="form.processing"
                    class="inline-flex items-center gap-2 rounded-lg bg-primary px-4 py-2.5 text-sm font-medium text-primary-foreground shadow-sm transition-colors hover:bg-primary/90 disabled:pointer-events-none disabled:opacity-60">
                    <Loader2 v-if="form.processing" class="h-4 w-4 animate-spin" />
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</template>
